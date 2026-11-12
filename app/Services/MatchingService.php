<?php

namespace App\Services;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * ═══════════════════════════════════════════════════════════════════
 * WEIGHTED MATCHING ALGORITHM (Multi-Criteria Decision-Making - MCDM)
 * ═══════════════════════════════════════════════════════════════════
 *
 * CAPSTONE FORMULA (Table 7):
 * Score = (Wv × V) + (We × E) + (Wr × R) + (Wc × C) + (Wd × D) + (Wh × H) + (Wt × T)
 *
 * NORMALIZATION RULES:
 * D = minDistance / actualDistance        (Haversine-based)
 * E = experienceYears / maxExperience     (Scale: 0.0 to 1.0)
 * R = BayesianRating / maxRating          (Scale: 0.0 to 1.0)
 * V, C, H, T = binary (1.0 = yes, 0.0 = no)
 */
class MatchingService
{
    // ═══════════════════════════════════════════════════
    // WEIGHTS — Table 7 (Total = 1.00 = 100%)
    // ═══════════════════════════════════════════════════
    const W_VERIFIED     = 0.20; // 20% — Verified ID (V)
    const W_DISTANCE     = 0.20; // 20% — Distance (D)
    const W_RATING       = 0.15; // 15% — Ratings & Reviews (R)
    const W_CAPABILITY   = 0.15; // 15% — Capability to Handle the Pet (C)
    const W_EXPERIENCE   = 0.15; // 15% — Experience (E)
    const W_HEALTH       = 0.10; // 10% — Pet Health & Vaccination Compatibility (H)
    const W_TEMPERAMENT  = 0.05; // 5%  — Temperament Compatibility (T)

    private const MAX_RATING = 5.0;

    // ═══════════════════════════════════════════════════
    // BAYESIAN AVERAGE PARAMETERS
    // ═══════════════════════════════════════════════════
    private const CONFIDENCE_THRESHOLD = 5.0; // (m) Minimum reviews needed to fully trust the rating
    private const FALLBACK_GLOBAL_MEAN = 3.0; // (C) Default mean if no ratings exist in the pool

    public function findMatches(Pet $pet, User $owner, int $limit = 30): Collection
    {
        // Exclude the owner from their own matching pool
        $candidates = $this->getEligibleSitters($owner->id);

        if ($candidates->isEmpty()) return collect();

        $ctx = $this->buildNormalizationContext($candidates, $owner);

        return $candidates
            ->map(fn (User $s) => $this->scoreWithPet($s, $pet, $ctx))
            ->sortByDesc('match_percentage')
            ->take($limit)
            ->values();
    }

    public function findGeneralMatches(User $owner, int $limit = 30): Collection
    {
        $candidates = $this->getEligibleSitters($owner->id);

        if ($candidates->isEmpty()) return collect();

        $ctx = $this->buildNormalizationContext($candidates, $owner);

        return $candidates
            ->map(fn (User $s) => $this->scoreGeneral($s, $ctx))
            ->sortByDesc('match_percentage')
            ->take($limit)
            ->values();
    }

    /**
     * Filters eligible sitters.
     * Excludes:
     * 1. The owner themselves (if dual-role).
     * 2. Suspended or banned users.
     */
    private function getEligibleSitters(int $ownerId): Collection
    {
        return User::query()
            ->where('id', '!=', $ownerId) // Exclude Owner (Issue #56)
            ->where('is_sitter', true)
            ->where('sitter_status', 'approved')
            ->whereNotIn('status', ['suspended', 'banned']) // Exclude Inactive (Issue #56)
            ->with('sitterProfile')
            ->whereHas('setting', function ($query) {
                $query->where('profile_visibility', 'public');
            })
            ->get()
            ->filter(fn ($u) => $u->sitterProfile !== null)
            ->values();
    }

    /**
     * Prepares normalization constants (Min Distance, Max Experience, Global Rating Mean).
     */
    private function buildNormalizationContext(Collection $candidates, User $owner): array
    {
        $distances = [];
        $ratings = [];

        foreach ($candidates as $s) {
            // FORMULA: Haversine Distance (See haversineKm method)
            $distances[$s->id] = $this->haversineKm(
                $owner->latitude, $owner->longitude,
                $s->latitude, $s->longitude
            );

            // Collect valid ratings for Global Mean (C) calculation
            if ($s->sitterProfile->average_ratings > 0) {
                $ratings[] = $s->sitterProfile->average_ratings;
            }
        }

        $validDists = array_filter($distances, fn ($d) => $d !== null);

        // FORMULA: minDistance (Smallest distance in the dataset)
        $minDist = !empty($validDists) ? max(0.1, min($validDists)) : 1.0;

        // FORMULA: maxExperience (Highest experience in the dataset)
        $maxExp = max(
            1.0,
            $candidates->max(fn ($u) => (float) ($u->sitterProfile->experience_years ?? 0))
        );

        // FORMULA: Global Mean (C) - Average rating of all candidates
        $globalMean = count($ratings) > 0 ? array_sum($ratings) / count($ratings) : self::FALLBACK_GLOBAL_MEAN;

        return [
            'distances'   => $distances,
            'min_dist'    => $minDist,
            'max_exp'     => $maxExp,
            'global_mean' => $globalMean,
        ];
    }

    /**
     * Calculates final score for a SPECIFIC pet (Uses all 7 factors).
     */
    private function scoreWithPet(User $sitter, Pet $pet, array $ctx): User
    {
        $p = $sitter->sitterProfile;

        // [V] Verified ID (Binary)
        $V = $sitter->id_validation_status === 'verified' ? 1.0 : 0.0;

        // [D] Distance Normalization
        $distKm = $ctx['distances'][$sitter->id] ?? null;
        $D = $this->normalizeDistance($distKm, $ctx['min_dist']);

        // [R] Bayesian Rating Calculation
        $R = $this->calculateBayesianRating($p, $ctx['global_mean']);

        // [E] Experience Normalization
        $E = min(1.0, ((float) ($p->experience_years ?? 0)) / $ctx['max_exp']);

        // [C], [H], [T] Binary Compatibility Checks
        $C = $this->scoreCapability($p, $pet);
        $H = $this->scoreHealth($p, $pet);
        $T = $this->scoreTemperament($p, $pet);

        // ═══════════════════════════════════════════════════
        // FINAL WEIGHTED SCORE CALCULATION (Capstone Formula)
        // ═══════════════════════════════════════════════════
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

    /**
     * Calculates score when no specific pet is selected (Uses neutral values for C, H, T).
     */
    private function scoreGeneral(User $sitter, array $ctx): User
    {
        $p = $sitter->sitterProfile;

        // [V] Verified ID (Binary)
        $V = $sitter->id_validation_status === 'verified' ? 1.0 : 0.0;

        // [D] Distance Normalization
        $distKm = $ctx['distances'][$sitter->id] ?? null;
        $D = $this->normalizeDistance($distKm, $ctx['min_dist']);

        // [R] Bayesian Rating Calculation
        $R = $this->calculateBayesianRating($p, $ctx['global_mean']);

        // [E] Experience Normalization
        $E = min(1.0, ((float) ($p->experience_years ?? 0)) / $ctx['max_exp']);

        // [C], [H], [T] Neutral values (1.0) since no pet context is provided
        $C = 1.0;
        $H = 1.0;
        $T = 1.0;

        // ═══════════════════════════════════════════════════
        // FINAL WEIGHTED SCORE CALCULATION (Capstone Formula)
        // ═══════════════════════════════════════════════════
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

    /**
     * ═══════════════════════════════════════════════════════════════════
     * BAYESIAN AVERAGE RATING FORMULA
     * ═══════════════════════════════════════════════════════════════════
     *
     * Bayesian Rating (C_adj) = (v / (v + m)) * actual_rating + (m / (v + m)) * global_mean
     *
     * Where:
     * v = Number of reviews (reviews_count)
     * m = Confidence threshold (CONFIDENCE_THRESHOLD = 5)
     * global_mean = Average rating of all candidates in the pool
     *
     * Purpose: Prevents new sitters with a single 5-star review from outranking
     * veterans with hundreds of 4.8-star reviews.
     */
    private function calculateBayesianRating(\App\Models\SitterProfile $profile, float $globalMean): float
    {
        $v = (float) ($profile->reviews_count ?? 0);
        $actualRating = (float) ($profile->average_ratings ?? 0);
        $m = self::CONFIDENCE_THRESHOLD;

        // If no reviews exist, fallback to Global Mean (prevents zero score for new sitters)
        if ($actualRating <= 0) {
            $bayesianRating = $globalMean;
        } else {
            // Apply Bayesian Average Formula
            $bayesianRating = (($v / ($v + $m)) * $actualRating) + (($m / ($v + $m)) * $globalMean);
        }

        // Normalize to 0.0 - 1.0 scale for the Weighted Algorithm
        return min(1.0, $bayesianRating / self::MAX_RATING);
    }

    /**
     * Attaches the computed scores to the User model object.
     */
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

    /**
     * ═══════════════════════════════════════════════════════════════════
     * DISTANCE NORMALIZATION FORMULA
     * ═══════════════════════════════════════════════════════════════════
     * Formula: D = minDistance / actualDistance
     *
     * If actual distance is very close (<= 0.1 km), it gets a perfect score (1.0).
     * If unknown (null), it gets a neutral score (0.5).
     */
    private function normalizeDistance(?float $actualKm, float $minKm): float
    {
        if ($actualKm === null) return 0.5;
        if ($actualKm <= 0.1)   return 1.0;
        return min(1.0, $minKm / $actualKm);
    }

    /**
     * [C] Capability to Handle the Pet (Binary Check)
     * Returns 1.0 if sitter accepts BOTH pet type AND size. Returns 0.0 otherwise.
     */
    private function scoreCapability(\App\Models\SitterProfile $p, Pet $pet): float
    {
        $types = $p->preferred_pet_types ?? [];
        $sizes = $p->preferred_pet_sizes ?? [];

        // Empty list means no restriction (accepts all)
        $typeOk = empty($types) || in_array($pet->pet_type_id, $types);
        $sizeOk = empty($sizes) || in_array($pet->size, $sizes);

        return ($typeOk && $sizeOk) ? 1.0 : 0.0;
    }

    /**
     * [H] Pet Health & Vaccination Compatibility (Binary Check)
     * Returns 1.0 if sitter can handle the pet's health conditions. Returns 0.0 otherwise.
     */
    private function scoreHealth(\App\Models\SitterProfile $p, Pet $pet): float
    {
        $accepted = $p->accepted_health_conditions ?? [];
        $vax      = $pet->vaccination_status ?? 'none';
        $hasCond  = !empty($pet->special_needs) || !empty($pet->medical_conditions);

        if (!$hasCond && $vax === 'complete') return 1.0;
        if ($hasCond && in_array('special_needs', $accepted)) return 1.0;
        if ($vax === 'partial' && in_array('partial_vax', $accepted)) return 1.0;

        return 0.0;
    }

    /**
     * [T] Temperament Compatibility (Binary Check)
     * Returns 1.0 if sitter accepts the pet's temperament. Returns 0.0 otherwise.
     */
    private function scoreTemperament(\App\Models\SitterProfile $p, Pet $pet): float
    {
        $accepted = $p->accepted_temperaments ?? [];

        if (empty($accepted)) return 1.0; // No restrictions

        return in_array($pet->temperament, $accepted) ? 1.0 : 0.0;
    }

    /**
     * ═══════════════════════════════════════════════════════════════════
     * HAVERSINE FORMULA (Geospatial Distance)
     * ═══════════════════════════════════════════════════════════════════
     * Calculates the great-circle distance between two points on a sphere.
     *
     * Formula:
     * a = sin²(Δφ/2) + cos φ1 ⋅ cos φ2 ⋅ sin²(Δλ/2)
     * c = 2 ⋅ atan2( √a, √(1−a) )
     * d = R ⋅ c
     *
     * Where:
     * φ = Latitude, λ = Longitude, R = Earth's radius (6371 km)
     */
    private function haversineKm(?float $lat1, ?float $lng1, ?float $lat2, ?float $lng2): ?float
    {
        if ($lat1 === null || $lng1 === null || $lat2 === null || $lng2 === null) {
            return null;
        }

        $R    = 6371; // Earth radius in km
        $dLat = deg2rad((float) $lat2 - (float) $lat1);
        $dLng = deg2rad((float) $lng2 - (float) $lng1);

        $a = sin($dLat / 2) ** 2
           + cos(deg2rad((float) $lat1)) * cos(deg2rad((float) $lat2))
           * sin($dLng / 2) ** 2;

        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
