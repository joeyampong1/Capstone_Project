<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Complaint;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index()
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $stats = [
            'total_users'         => User::count(),
            'user_growth'         => User::where('created_at', '>=', $thisMonth)->count()
                                     - User::whereBetween('created_at', [$lastMonth, $thisMonth])->count(),
            'total_bookings'      => Booking::count(),
            'active_bookings'     => Booking::whereIn('status', ['active', 'accepted', 'ongoing'])->count(),
            'completed_bookings'  => Booking::where('status', 'completed')->count(),
            'total_complaints'    => Complaint::count(),
            'pending_complaints'  => Complaint::where('status', 'pending')->count(),
        ];

        $bookingStats = [
            'total'           => Booking::count(),
            'completed'       => Booking::where('status', 'completed')->count(),
            'cancelled'       => Booking::where('status', 'cancelled')->count(),
            'pending'         => Booking::where('status', 'pending')->count(),
            'top_sitter_name' => null,
        ];

        $userStats = [
            'total'                => User::count(),
            'verified'             => User::where('id_validation_status', 'verified')->count(),
            'sitters'              => User::where('is_sitter', true)->count(),
            'suspended'            => User::where('status', 'suspended')->count(),
            'pending_verification' => User::where('id_validation_status', 'pending')->count(),
        ];

        $hasTypeColumn = Schema::hasColumn('complaints', 'type');

        $complaintStats = [
            'total'       => Complaint::count(),
            'pending'     => Complaint::where('status', 'pending')->count(),
            'resolved'    => Complaint::where('status', 'resolved')->count(),
            'dismissed'   => Complaint::where('status', 'dismissed')->count(),
            'common_type' => null,
        ];

        if ($hasTypeColumn) {
            $row = Complaint::selectRaw('type, COUNT(*) as total')
                ->groupBy('type')
                ->orderByDesc('total')
                ->first();
            $complaintStats['common_type'] = $row?->type
                ? ucwords(str_replace('_', ' ', $row->type))
                : null;
        }

        $bookingTrend = collect(range(6, 0))->map(function ($i) {
            $month = Carbon::now()->subMonths($i);
            return [
                'month' => $month->format('M'),
                'count' => Booking::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        })->all();

        $totalUsers = max(1, User::count());
        $owners  = User::where('role', 'owner')->where('is_sitter', false)->count();
        $sitters = User::where('is_sitter', true)->count();
        $admins  = User::where('role', 'admin')->count();

        $userGrowth = [
            ['label' => 'Owners',      'count' => $owners,  'pct' => round(($owners  / $totalUsers) * 100, 1), 'color' => 'bg-blue-500'],
            ['label' => 'Pet Sitters', 'count' => $sitters, 'pct' => round(($sitters / $totalUsers) * 100, 1), 'color' => 'bg-amber-500'],
            ['label' => 'Admins',      'count' => $admins,  'pct' => round(($admins  / $totalUsers) * 100, 1), 'color' => 'bg-primary'],
        ];

        $complaintDistribution = [];

        if ($hasTypeColumn) {
            $totalComplaints = max(1, Complaint::count());
            $typeLabels = [
                'missed_visit'  => ['label' => 'Missed Visit',  'color' => 'bg-amber-500'],
                'poor_service'  => ['label' => 'Poor Service',  'color' => 'bg-red-500'],
                'no_proof'      => ['label' => 'No Proof',      'color' => 'bg-blue-500'],
                'rude_behavior' => ['label' => 'Rude Behavior', 'color' => 'bg-purple-500'],
                'others'        => ['label' => 'Others',        'color' => 'bg-neutral-400'],
            ];

            $complaintDistribution = Complaint::selectRaw('type, COUNT(*) as total')
                ->groupBy('type')
                ->orderByDesc('total')
                ->get()
                ->map(function ($row) use ($typeLabels, $totalComplaints) {
                    $meta = $typeLabels[$row->type] ?? ['label' => ucfirst($row->type), 'color' => 'bg-neutral-400'];
                    return [
                        'label' => $meta['label'],
                        'color' => $meta['color'],
                        'pct'   => round(($row->total / $totalComplaints) * 100, 1),
                    ];
                })
                ->all();
        }

        $recentReports = [];
        $reportHistory = collect();

        return view('admin.reports.reports', compact(
            'stats',
            'bookingStats',
            'userStats',
            'complaintStats',
            'bookingTrend',
            'userGrowth',
            'complaintDistribution',
            'recentReports',
            'reportHistory',
        ));
    }

    /* ==============================================================
     |  EXPORT — PDF / CSV (Excel)
     |==============================================================*/

    public function export(Request $request)
    {
        $format = $request->query('format', 'pdf');   // pdf | excel | csv
        $type   = $request->query('type', 'all');     // all | booking | user | complaint
        $from   = $request->query('from');
        $to     = $request->query('to');

        if (in_array($format, ['excel', 'csv'])) {
            return $this->exportCsv($type, $from, $to);
        }

        // PDF — use a print-optimized HTML view (browser can Save as PDF)
        return $this->exportPdfView($type, $from, $to);
    }

    /* --------------------------------------------------------------
     |  CSV (opens in Excel / Google Sheets)
     |--------------------------------------------------------------*/
    private function exportCsv(string $type, ?string $from, ?string $to): StreamedResponse
    {
        $filename = 'report_' . $type . '_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($type, $from, $to) {
            $out = fopen('php://output', 'w');

            // UTF-8 BOM so Excel detects encoding properly (important for ₱ symbol)
            fwrite($out, "\xEF\xBB\xBF");

            // ---- HEADER ----
            fputcsv($out, ['PetNanny — System Report']);
            fputcsv($out, ['Type',      ucfirst($type)]);
            fputcsv($out, ['From',      $from ?: '—']);
            fputcsv($out, ['To',        $to   ?: '—']);
            fputcsv($out, ['Generated', now()->format('F j, Y g:i A')]);
            fputcsv($out, []);

            // ---- USERS ----
            if (in_array($type, ['all', 'user'])) {
                fputcsv($out, ['=== USERS ===']);
                fputcsv($out, ['ID', 'Name', 'Email', 'Role', 'ID Verified', 'Status', 'Joined']);

                User::query()
                    ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
                    ->when($to,   fn ($q) => $q->where('created_at', '<=', $to . ' 23:59:59'))
                    ->orderBy('id')
                    ->chunk(500, function ($users) use ($out) {
                        foreach ($users as $u) {
                            fputcsv($out, [
                                $u->id,
                                trim(($u->f_name ?? '') . ' ' . ($u->l_name ?? '')),
                                $u->email,
                                $u->is_sitter ? 'Sitter' : ucfirst($u->role ?? 'owner'),
                                $u->id_validation_status === 'verified' ? 'Yes' : 'No',
                                $u->status,
                                optional($u->created_at)->format('Y-m-d'),
                            ]);
                        }
                    });

                fputcsv($out, []);
            }

            // ---- BOOKINGS ----
            if (in_array($type, ['all', 'booking'])) {
                fputcsv($out, ['=== BOOKINGS ===']);
                fputcsv($out, ['ID', 'Owner', 'Sitter', 'Pet', 'Status', 'Total', 'Created']);

                Booking::with(['owner:id,f_name,l_name', 'sitter:id,f_name,l_name', 'pet:id,name'])
                    ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
                    ->when($to,   fn ($q) => $q->where('created_at', '<=', $to . ' 23:59:59'))
                    ->orderBy('id')
                    ->chunk(500, function ($bookings) use ($out) {
                        foreach ($bookings as $b) {
                            fputcsv($out, [
                                $b->id,
                                trim(($b->owner->f_name  ?? '') . ' ' . ($b->owner->l_name  ?? '')),
                                trim(($b->sitter->f_name ?? '') . ' ' . ($b->sitter->l_name ?? '')),
                                $b->pet->name ?? '—',
                                $b->status,
                                $b->subtotal ?? $b->total_amount ?? 0,
                                optional($b->created_at)->format('Y-m-d'),
                            ]);
                        }
                    });

                fputcsv($out, []);
            }

            // ---- COMPLAINTS ----
            if (in_array($type, ['all', 'complaint'])) {
                fputcsv($out, ['=== COMPLAINTS ===']);
                fputcsv($out, ['ID', 'Description', 'Status', 'Created']);

                Complaint::query()
                    ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
                    ->when($to,   fn ($q) => $q->where('created_at', '<=', $to . ' 23:59:59'))
                    ->orderBy('id')
                    ->chunk(500, function ($complaints) use ($out) {
                        foreach ($complaints as $c) {
                            fputcsv($out, [
                                $c->id,
                                $c->description ?? $c->subject ?? '—',
                                $c->status,
                                optional($c->created_at)->format('Y-m-d'),
                            ]);
                        }
                    });
            }

            fclose($out);
        }, 200, $headers);
    }

    /* --------------------------------------------------------------
     |  PDF — print-optimized HTML view
     |--------------------------------------------------------------*/
    private function exportPdfView(string $type, ?string $from, ?string $to)
    {
        $users      = in_array($type, ['all', 'user'])      ? User::query()
                        ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
                        ->when($to,   fn ($q) => $q->where('created_at', '<=', $to . ' 23:59:59'))
                        ->orderBy('id')->get() : collect();

        $bookings   = in_array($type, ['all', 'booking'])   ? Booking::with(['owner', 'sitter', 'pet'])
                        ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
                        ->when($to,   fn ($q) => $q->where('created_at', '<=', $to . ' 23:59:59'))
                        ->orderBy('id')->get() : collect();

        $complaints = in_array($type, ['all', 'complaint']) ? Complaint::query()
                        ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
                        ->when($to,   fn ($q) => $q->where('created_at', '<=', $to . ' 23:59:59'))
                        ->orderBy('id')->get() : collect();

        return view('admin.reports.pdf', compact(
            'type', 'from', 'to', 'users', 'bookings', 'complaints'
        ));
    }
}