<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class SitterVerificationController extends Controller
{
    // ==========================================
    // INDEX — List sitter applications
    // ==========================================
    public function index(Request $request)
    {
        $status     = $request->query('status', 'pending');
        $search     = $request->query('search');
        $dateSort   = $request->query('date', 'latest');
        $experience = $request->query('experience', 'all');

        // Base query — users with a sitter profile (applicants)
        $query = User::query()
            ->whereNotNull('sitter_applied_at')
            ->whereHas('sitterProfile')
            ->with('sitterProfile')
            ->withCount([
                'bookingsAsSitter as completed_bookings_count' => fn ($q) => $q->where('status', 'completed'),
                'bookingsAsSitter as cancelled_bookings_count' => fn ($q) => $q->where('status', 'cancelled'),
            ]);

        // Filter by status
        if ($status !== 'all') {
            if ($status === 'pending') {
                // pending OR NULL (because it is not set yet)
                $query->where(function ($q) {
                    $q->where('sitter_status', 'pending')
                    ->orWhereNull('sitter_status');
                });
            } else {
                $query->where('sitter_status', $status);
            }
        }

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('f_name', 'like', "%{$search}%")
                ->orWhere('l_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Experience filter
        if ($experience !== 'all') {
            $query->whereHas('sitterProfile', function ($q) use ($experience) {
                match ($experience) {
                    '3+'    => $q->where('experience_years', '>=', 3),
                    '1-3'   => $q->whereBetween('experience_years', [1, 2]),
                    '<1'    => $q->where('experience_years', '<', 1),
                    'none'  => $q->where('experience_years', 0),
                    default => null,
                };
            });
        }

        // Sort
        $query->orderByRaw('COALESCE(sitter_applied_at, updated_at) ' . ($dateSort === 'oldest' ? 'asc' : 'desc'));

        $applicants = $query->paginate(10)->withQueryString();

        // ==========================================
        // STATS
        // ==========================================
        $stats = [
            'pending'  => User::whereNotNull('sitter_applied_at') // ✅ FIX ISSUE #43
                            ->whereHas('sitterProfile')
                            ->where(function ($q) {
                                $q->where('sitter_status', 'pending')
                                ->orWhereNull('sitter_status');
                            })->count(),
            'approved' => User::whereNotNull('sitter_applied_at')
                            ->where('sitter_status', 'approved')->count(),
            'rejected' => User::whereNotNull('sitter_applied_at')
                            ->where('sitter_status', 'rejected')->count(),
            'total'    => User::whereNotNull('sitter_applied_at')
                            ->whereHas('sitterProfile')->count(),
        ];

        // ==========================================
        // MODAL PAYLOAD
        // ==========================================
        $applicantsData = $applicants->map(function ($user) {
            $profile = $user->sitterProfile;

            // 👇 1. INITIALIZE CLOUDINARY SERVICE HERE 👇
            $cloudinary = app(\App\Services\CloudinaryService::class);

            // Documents — handle both string and array (JSON)
            $documents = [];
            if ($profile?->certificates_path) {
                $paths = is_array($profile->certificates_path)
                    ? $profile->certificates_path
                    : [$profile->certificates_path];

                foreach ($paths as $index => $path) {
                    if (!$path) continue;
                    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    $documents[] = [
                        'name' => 'Sitter Certificate ' . ($index + 1),
                        'type' => strtoupper($ext ?: 'FILE'),
                        'url'  => $cloudinary->getSignedUrl($path), // 👈 ADDED SIGNED URL FOR CERTIFICATES TOO
                    ];
                }
            }

            $petsAccepted = [];
            if ($profile?->preferred_pet_types) {
                $decoded = is_array($profile->preferred_pet_types)
                    ? $profile->preferred_pet_types
                    : json_decode($profile->preferred_pet_types, true);
                $petsAccepted = is_array($decoded) ? $decoded : [];
            }

            return [
                'id'                => $user->id,
                'name'              => $user->full_name ?: 'User',
                'initials'          => strtoupper(substr($user->f_name ?? 'U', 0, 1)),
                'email'             => $user->email,
                'phone'             => $user->contact_number ?? 'N/A',
                'location'          => $user->location ?? 'N/A',
                'registered_at'     => $user->created_at?->format('M d, Y') ?? 'N/A',
                'applied_at'        => ($user->sitter_applied_at ?? $user->updated_at)?->format('M d, Y') ?? 'N/A',
                'status'            => $user->sitter_status ?? 'pending',
                'profile_photo'     => $user->profile_photo,

                // ✅ FIX ISSUE #43: Use signed URLs for gov_id and selfie
                'gov_id_url'        => $cloudinary->getSignedUrl($user->gov_id_public_id),
                'selfie_url'        => $cloudinary->getSignedUrl($user->selfie_photo_public_id),

                'experience_years'  => $profile?->experience_years ?? 0,
                'bio'               => $profile?->bio ?? '',
                'base_rate'         => $profile?->base_rate ?? 0,
                'sitter_type'       => $profile?->sitter_type ?? 'small_pets',
                'sitter_type_label' => match($profile?->sitter_type) {
                    'small_pets'  => 'Small Pets',
                    'large_pets'  => 'Large Pets',
                    'exotic_pets' => 'Exotic Pets',
                    'all_pets'    => 'All Pets',
                    default       => 'Small Pets',
                },
                'sitter_type_icon'  => match($profile?->sitter_type) {
                    'small_pets'  => '🐱',
                    'large_pets'  => '🐕',
                    'exotic_pets' => '🦜',
                    'all_pets'    => '🐾',
                    default       => '🐱',
                },
                'food_preference'   => $profile?->food_preference ?? 'owner_provides',
                'food_budget'       => $profile?->food_budget ?? 0,
                'average_rating'    => $profile?->average_ratings ?? 3.0,
                'total_bookings'    => $profile?->total_bookings ?? 0,
                'pets_accepted'     => $petsAccepted,
                'pet_sizes'         => $profile?->preferred_pet_sizes
                                        ? (is_array($profile->preferred_pet_sizes)
                                            ? $profile->preferred_pet_sizes
                                            : json_decode($profile->preferred_pet_sizes, true))
                                        : [],
                'min_pets_capacity' => $profile?->min_pets_capacity,
                'max_pets_capacity' => $profile?->max_pets_capacity,

                'completed_bookings'=> $user->completed_bookings_count ?? 0,
                'cancelled_count'   => $user->cancelled_bookings_count ?? 0,

                'completed_visits'  => 0,
                'missed_visits'     => 0,
                'complaints_count'  => 0,

                'services'          => [],
                'documents'         => $documents,
                'admin_remarks'     => $user->admin_notes ?? '',
            ];
        })->values()->toArray();

        return view('admin.sitter_verification.sitter', compact(
            'applicants',
            'stats',
            'status',
            'search',
            'dateSort',
            'experience',
            'applicantsData'
        ));
    }

    // ==========================================
    // APPROVE
    // ==========================================
    public function approve(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        // ✅ FIX ISSUE #43: Ensure they actually submitted an application
        abort_if(
            !$user->sitter_applied_at,
            400,
            'This user has not submitted a sitter application.'
        );

        abort_if(
            ! in_array($user->sitter_status, ['pending', 'rejected']),
            400,
            'This application cannot be approved.'
        );

        DB::beginTransaction();
        try {
            // ✅ DIRECT ASSIGNMENT
            $user->sitter_status      = 'approved';
            $user->sitter_approved_at = now();
            $user->is_sitter          = true;

            if (Schema::hasColumn('users', 'admin_notes')) {
                $user->admin_notes = $request->input('remarks');
            }

            $user->save();

            DB::commit();

            // ✅ Send notification AFTER DB is committed
            NotificationService::sitterApproved($user);

            return back()->with('status', "Approved! {$user->f_name} is now a verified sitter.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sitter approval failed', ['user_id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to approve: ' . $e->getMessage());
        }
    }

    // ==========================================
    // REJECT
    // ==========================================
    public function reject(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        // ✅ FIX ISSUE #43: Ensure they actually submitted an application
        abort_if(
            !$user->sitter_applied_at,
            400,
            'This user has not submitted a sitter application.'
        );

        abort_if(
            ! in_array($user->sitter_status, ['pending', 'approved']),
            400,
            'This application cannot be rejected.'
        );

        $reason = $request->input('reason', 'Application did not meet requirements.');

        DB::beginTransaction();
        try {
            // ✅ DIRECT ASSIGNMENT
            $user->sitter_status = 'rejected';
            $user->is_sitter     = false;

            if (Schema::hasColumn('users', 'admin_notes')) {
                $user->admin_notes = $reason;
            }

            $user->save();

            DB::commit();

            // ✅ Send notification AFTER DB is committed
            NotificationService::sitterRejected($user, $reason);

            return back()->with('status', "Rejected. {$user->f_name} has been notified.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sitter rejection failed', ['user_id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to reject: ' . $e->getMessage());
        }
    }

    // ==========================================
    // DOWNLOAD — single file or ZIP bundle
    // ==========================================
    public function download(int $id)
    {
        $user = User::with('sitterProfile')->findOrFail($id);
        $profile = $user->sitterProfile;

        if (! $profile?->certificates_path) {
            return back()->with('error', 'No documents found for this application.');
        }

        $paths = is_array($profile->certificates_path)
            ? $profile->certificates_path
            : [$profile->certificates_path];

        // Remove empty values
        $paths = array_values(array_filter($paths));

        if (empty($paths)) {
            return back()->with('error', 'File not found.');
        }

        // Single file — download directly
        if (count($paths) === 1) {
            $path = $paths[0];

            // ✅ Check if Cloudinary URL
            if (str_starts_with($path, 'http')) {
                $content = @file_get_contents($path);
                if ($content === false) {
                    return back()->with('error', 'Failed to download file from Cloudinary.');
                }

                $ext = pathinfo(parse_url($path, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                return response($content, 200)
                    ->header('Content-Type', 'application/octet-stream')
                    ->header('Content-Disposition', 'attachment; filename="sitter_' . $user->id . '_certificate.' . $ext . '"');
            }
            // ✅ Local storage fallback
            elseif (Storage::disk('public')->exists($path)) {
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                return Storage::disk('public')->download($path, 'sitter_' . $user->id . '_certificate.' . $ext);
            }

            return back()->with('error', 'File not found on storage or Cloudinary.');
        }

        // Multiple files — create ZIP
        $zipFileName = 'sitter_' . $user->id . '_certificates_' . time() . '.zip';
        $zipDir = storage_path('app/temp');

        if (!file_exists($zipDir)) {
            mkdir($zipDir, 0755, true);
        }

        $zipPath = $zipDir . '/' . $zipFileName;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($paths as $index => $path) {
                $fileName = 'certificate_' . ($index + 1);

                // ✅ Check if Cloudinary URL
                if (str_starts_with($path, 'http')) {
                    $content = @file_get_contents($path);
                    if ($content !== false) {
                        $ext = pathinfo(parse_url($path, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                        $zip->addFromString($fileName . '.' . $ext, $content);
                    }
                }
                // ✅ Local storage fallback
                elseif (Storage::disk('public')->exists($path)) {
                    $ext = pathinfo($path, PATHINFO_EXTENSION);
                    $zip->addFromString($fileName . '.' . $ext, Storage::disk('public')->get($path));
                }
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
