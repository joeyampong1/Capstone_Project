<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Complaint;
use App\Models\User;

class AdminPrescriptiveService
{
    /**
     * Generates prescriptive recommendations and sitter risk rankings for Admin.
     *
     * Risk Score (0 - 100, higher = needs more attention):
     * = (CancellationRate * 0.45) + (ComplaintRatio * 0.40) + (UnverifiedPenalty * 0.15)
     */
    public function generateRecommendations(array $kpis, array $verification, array $complaintStatus): array
    {
        $recommendations = [];

        // 1. Sitter Risk Ranking (Prescriptive prioritization)
        $sitterRiskList = $this->rankSittersByRisk();
        $highRiskSitters = array_filter($sitterRiskList, fn ($s) => $s['risk_score'] >= 50);

        if (!empty($highRiskSitters)) {
            $topRisk = array_values($highRiskSitters)[0];
            $recommendations[] = [
                'priority' => 'High',
                'title'    => 'Review High-Risk Sitter: ' . $topRisk['name'],
                'reason'   => "Risk score {$topRisk['risk_score']}/100 ({$topRisk['cancelled']} cancelled, {$topRisk['complaints']} complaints).",
                'action'   => 'Inspect sitter bookings, verification status, and recent complaints.',
            ];
        }

        // 2. Pending Complaints Action
        if (($complaintStatus['pending'] ?? 0) > 0) {
            $recommendations[] = [
                'priority' => ($complaintStatus['pending'] >= 5) ? 'High' : 'Medium',
                'title'    => 'Resolve ' . $complaintStatus['pending'] . ' Pending Complaint(s)',
                'reason'   => 'Unresolved complaints can lower platform trust and booking completion.',
                'action'   => 'Prioritize oldest pending complaints in Complaint Management.',
            ];
        }

        // 3. Verification Bottleneck Action
        if (($verification['pending'] ?? 0) > 0) {
            $recommendations[] = [
                'priority' => 'Medium',
                'title'    => 'Process ' . $verification['pending'] . ' Pending ID/Sitter Verification(s)',
                'reason'   => 'Verified accounts boost sitter supply and owner confidence.',
                'action'   => 'Review pending ID/sitter applications to expand active sitter pool.',
            ];
        }

        // 4. Cancellation Rate Check
        if (($kpis['cancellation_rate'] ?? 0) >= 20) {
            $recommendations[] = [
                'priority' => 'High',
                'title'    => 'Address Elevated Booking Cancellation Rate (' . $kpis['cancellation_rate'] . '%)',
                'reason'   => 'Cancellation rate is at or above the 20% warning threshold.',
                'action'   => 'Review schedule conflicts, pet compatibility, and sitter availability.',
            ];
        }

        // Fallback if everything looks healthy
        if (empty($recommendations)) {
            $recommendations[] = [
                'priority' => 'Low',
                'title'    => 'Platform Operations Healthy',
                'reason'   => 'No critical spikes in cancellations, complaints, or verification backlogs.',
                'action'   => 'Maintain routine monitoring and encourage active sitter availability.',
            ];
        }

        return [
            'recommendations' => $recommendations,
            'sitter_risk_list' => array_slice($sitterRiskList, 0, 5),
        ];
    }

    private function rankSittersByRisk(): array
    {
        $sitters = User::where('is_sitter', true)
            ->with('sitterProfile')
            ->get();

        $ranked = [];

        foreach ($sitters as $sitter) {
            $completed = Booking::where('sitter_id', $sitter->id)->where('status', 'completed')->count();
            $cancelled = Booking::where('sitter_id', $sitter->id)->where('status', 'cancelled')->count();
            $total     = max(1, $completed + $cancelled);

            $complaints = Complaint::whereHas('booking', fn ($q) => $q->where('sitter_id', $sitter->id))->count();

            $cancelRate        = min(100.0, ($cancelled / $total) * 100.0);
            $complaintRatio    = min(100.0, ($complaints / $total) * 100.0);
            $unverifiedPenalty = ($sitter->id_validation_status === 'verified') ? 0.0 : 100.0;

            // Weighted Risk Formula (Total weight = 1.00)
            $riskScore = round(
                ($cancelRate * 0.45) +
                ($complaintRatio * 0.40) +
                ($unverifiedPenalty * 0.15),
                1
            );

            $ranked[] = [
                'id'         => $sitter->id,
                'name'       => trim(($sitter->f_name ?? '') . ' ' . ($sitter->l_name ?? '')) ?: $sitter->email,
                'completed'  => $completed,
                'cancelled'  => $cancelled,
                'complaints' => $complaints,
                'risk_score' => $riskScore,
            ];
        }

        usort($ranked, fn ($a, $b) => $b['risk_score'] <=> $a['risk_score']);

        return $ranked;
    }
}
