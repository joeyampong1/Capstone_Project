<?php

namespace App\Services;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Weighted Matching Algorithm — Multi-Criteria Decision-Making (MCDM)
 *
 * Formula (per capstone Table 7):
 *   Score = (Wv × V) + (We × E) + (Wr × R) + (Wc × C)
 *         + (Wd × D) + (Wh × H) + (Wt × T)
 *
 * Normalization:
 *   D = minDistance / actualDistance
 *   E = experience / maxExperience
 *   R = rating / maxRating
 *   V, C, H, T = binary (1 = yes, 0 = no)
 */
class MatchingService
{
    // ═══════════════════════════════════════════════════
    // WEIGHTS — Table 7 (total = 1.00 = 100%)
    // ═══════════════════════════════════════════════════
    const W_VERIFIED     = 0.20; // V — Verified ID
    const W_DISTANCE     = 0.20; // D — Distance
    const W_RATING       = 0.15; // R — Ratings & Reviews
    const W_CAPABILITY   = 0.15; // C — Capability to Handle the Pet
    const W_EXPERIENCE   = 0.15; // E — Experience
    const W_HEALTH       = 0.10; // H — Pet Health & Vaccination Compatibility
    const W_TEMPERAMENT  = 0.05; // T — Temperament Compatibility

    /** Maximum rating scale (per docs: R = rating / 5.0) */
    private const MAX_RATING = 5.0;

    /**
     * Run matching for a SPECIFIC pet (uses all 7 factors).
     */
    public function findMatches(Pet $pet, User $owner, int $limit = 30): Collection
    {
        $candidates = $this->getEligibleSitters();

        if ($candidates->isEmpty()) return collect();

        $ctx = $this->buildNormalizationContext($candidates, $owner);

        return $candidates
            ->map(fn (User $s) => $this->scoreWithPet($s, $pet, $ctx))
            ->sortByDesc('match_percentage')
            ->take($limit)
            ->values();
    }

    /**
     * Run general ranking when NO pet is selected.
     * C, H, T cannot be evaluated without pet context → neutral values used.
     */
    public function findGeneralMatches(User $owner, int $limit = 30): Collection
    {
        $candidates = $this->getEligibleSitters();

        if ($candidates->isEmpty()) return collect();

        $ctx = $this->buildNormalizationContext($candidates, $owner);

        return $candidates
            ->map(fn (User $s) => $this->scoreGeneral($s, $ctx))
            ->sortByDesc('match_percentage')
            ->take($limit)
            ->values();
    }

    // ═══════════════════════════════════════════════════
    // CANDIDATE SELECTION
    // ═══════════════════════════════════════════════════
    /**
     * Business rule: only approved sitters with a profile.
     *
     * NOTE: ID verification is NOT hard-filtered — it is scored as
     * the V factor (20% weight). This allows verified sitters to
     * rank higher than unverified ones, making V meaningful in the
     * ranking instead of a constant offset.
     */
    private function getEligibleSitters(): Collection
    {
        return User::query()
            ->where('is_sitter', true)
            ->where('sitter_status', 'approved')
            ->with('sitterProfile')
            ->get()
            ->filter(fn ($u) => $u->sitterProfile !== null)
            ->values();
    }

    // ═══════════════════════════════════════════════════
    // NORMALIZATION CONSTANTS (computed once per run)
    // ═══════════════════════════════════════════════════
    private function buildNormalizationContext(Collection $candidates, User $owner): array
    {
        // Distances for each candidate
        $distances = [];
        foreach ($candidates as $s) {
            $distances[$s->id] = $this->haversineKm(
                $owner->latitude, $owner->longitude,
                $s->latitude, $s->longitude
            );
        }

        $validDists = array_filter($distances, fn ($d) => $d !== null);
        $minDist    = !empty($validDists) ? max(0.1, min($validDists)) : 1.0;

        // Max experience in dataset (per docs: E = exp / maxExp)
        $maxExp = max(
            1.0,
            $candidates->max(fn ($u) => (float) ($u->sitterProfile->experience_years ?? 0))
        );

        return [
            'distances' => $distances,
            'min_dist'  => $minDist,
            'max_exp'   => $maxExp,
        ];
    }

    // ═══════════════════════════════════════════════════
    // SCORING — with pet (all 7 factors)
    // ═══════════════════════════════════════════════════
    private function scoreWithPet(User $sitter, Pet $pet, array $ctx): User
    {
        $p = $sitter->sitterProfile;

        // ─── V — Verified ID (binary) ───
        $V = $sitter->id_validation_status === 'verified' ? 1.0 : 0.0;

        // ─── D — Distance (minDist / actualDist) ───
        $distKm = $ctx['distances'][$sitter->id] ?? null;
        $D = $this->normalizeDistance($distKm, $ctx['min_dist']);

        // ─── R — Rating (rating / 5.0) ───
        $R = min(1.0, ((float) ($p->average_ratings ?? 0)) / self::MAX_RATING);

        // ─── E — Experience (exp / maxExp) ───
        $E = min(1.0, ((float) ($p->experience_years ?? 0)) / $ctx['max_exp']);

        // ─── C, H, T — binary per docs ───
        $C = $this->scoreCapability($p, $pet);
        $H = $this->scoreHealth($p, $pet);
        $T = $this->scoreTemperament($p, $pet);

        // ─── Weighted sum ───
        $raw =
              self::W_VERIFIED    * $V
            + self::W_DISTANCE    * $D
            + self::W_RATING      * $R
            + self::W_CAPABILITY  * $C
            + self::W_EXPERIENCE  * $E
            + self::W_HEALTH      * $H
            + self::W_TEMPERAMENT * $T;

        return $this->attachScore($sitter, $raw, $V, $D, $R, $C, $E, $H, $T, $distKm);
    }

    // ═══════════════════════════════════════════════════
    // SCORING — general (no pet context)
    // ═══════════════════════════════════════════════════
    private function scoreGeneral(User $sitter, array $ctx): User
    {
        $p = $sitter->sitterProfile;

        $V = $sitter->id_validation_status === 'verified' ? 1.0 : 0.0;

        $distKm = $ctx['distances'][$sitter->id] ?? null;
        $D = $this->normalizeDistance($distKm, $ctx['min_dist']);

        $R = min(1.0, ((float) ($p->average_ratings ?? 0)) / self::MAX_RATING);
        $E = min(1.0, ((float) ($p->experience_years ?? 0)) / $ctx['max_exp']);

        // No pet → assume neutral compatibility (do not penalize)
        $C = 1.0;
        $H = 1.0;
        $T = 1.0;

        $raw =
              self::W_VERIFIED    * $V
            + self::W_DISTANCE    * $D
            + self::W_RATING      * $R
            + self::W_CAPABILITY  * $C
            + self::W_EXPERIENCE  * $E
            + self::W_HEALTH      * $H
            + self::W_TEMPERAMENT * $T;

        return $this->attachScore($sitter, $raw, $V, $D, $R, $C, $E, $H, $T, $distKm);
    }

    // ═══════════════════════════════════════════════════
    // ATTACH RESULT TO MODEL
    // ═══════════════════════════════════════════════════
    private function attachScore(
        User $sitter, float $raw,
        float $V, float $D, float $R, float $C, float $E, float $H, float $T,
        ?float $distKm
    ): User {
        $sitter->match_percentage = round($raw * 100, 2);
        $sitter->match_breakdown  = [
            'V' => round($V, 3),
            'D' => round($D, 3),
            'R' => round($R, 3),
            'C' => round($C, 3),
            'E' => round($E, 3),
            'H' => round($H, 3),
            'T' => round($T, 3),
        ];
        $sitter->distance_km = $distKm !== null ? round($distKm, 2) : null;

        return $sitter;
    }

    // ═══════════════════════════════════════════════════
    // D — Distance normalization
    // ═══════════════════════════════════════════════════
    private function normalizeDistance(?float $actualKm, float $minKm): float
    {
        if ($actualKm === null) return 0.5;   // unknown → neutral
        if ($actualKm <= 0.1)   return 1.0;   // very close
        return min(1.0, $minKm / $actualKm);  // per docs: D = minDist / actualDist
    }

    // ═══════════════════════════════════════════════════
    // C — Capability to Handle the Pet (binary)
    // Sitter must accept BOTH pet type AND size
    // ═══════════════════════════════════════════════════
    private function scoreCapability($p, Pet $pet): float
    {
        $types = $p->preferred_pet_types ?? [];
        $sizes = $p->preferred_pet_sizes ?? [];

        // Empty list = no restriction (accepts any)
        $typeOk = empty($types) || in_array($pet->pet_type_id, $types);
        $sizeOk = empty($sizes) || in_array($pet->size, $sizes);

        return ($typeOk && $sizeOk) ? 1.0 : 0.0;
    }

    // ═══════════════════════════════════════════════════
    // H — Pet Health & Vaccination Compatibility (binary)
    // ═══════════════════════════════════════════════════
    private function scoreHealth($p, Pet $pet): float
    {
        $accepted = $p->accepted_health_conditions ?? [];
        $vax      = $pet->vaccination_status ?? 'none';
        $hasCond  = !empty($pet->special_needs) || !empty($pet->medical_conditions);

        // Healthy + complete vaccination → perfect
        if (!$hasCond && $vax === 'complete') return 1.0;

        // Has medical condition and sitter accepts special needs
        if ($hasCond && in_array('special_needs', $accepted)) return 1.0;

        // Partial vaccination and sitter accepts partial
        if ($vax === 'partial' && in_array('partial_vax', $accepted)) return 1.0;

        return 0.0;
    }

    // ═══════════════════════════════════════════════════
    // T — Temperament Compatibility (binary)
    // ═══════════════════════════════════════════════════
    private function scoreTemperament($p, Pet $pet): float
    {
        $accepted = $p->accepted_temperaments ?? [];

        // No restriction → compatible
        if (empty($accepted)) return 1.0;

        return in_array($pet->temperament, $accepted) ? 1.0 : 0.0;
    }

    // ═══════════════════════════════════════════════════
    // HAVERSINE — distance in km
    // ═══════════════════════════════════════════════════
    private function haversineKm($lat1, $lng1, $lat2, $lng2): ?float
    {
        if ($lat1 === null || $lng1 === null || $lat2 === null || $lng2 === null) {
            return null;
        }

        $R    = 6371; // earth radius in km
        $dLat = deg2rad((float) $lat2 - (float) $lat1);
        $dLng = deg2rad((float) $lng2 - (float) $lng1);

        $a = sin($dLat / 2) ** 2
           + cos(deg2rad((float) $lat1)) * cos(deg2rad((float) $lat2))
           * sin($dLng / 2) ** 2;

        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
