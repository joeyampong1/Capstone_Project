<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // ==========================================
    // INDEX — List all users with filters
    // ==========================================
    public function index(Request $request)
    {
        $search             = $request->query('search');
        $roleFilter         = $request->query('role', 'all');
        $statusFilter       = $request->query('status', 'all');
        $verificationFilter = $request->query('verification', 'all');
        $sortFilter         = $request->query('sort', 'newest');
        $showTrashed        = $request->query('trashed') === 'true'; // ✅ FIX ISSUE #42

        // ==========================================
        // BASE QUERY
        // ==========================================
        // ✅ FIX ISSUE #42: Show only trashed if 'trashed' parameter is present
        $query = $showTrashed ? User::onlyTrashed() : User::query();

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('f_name', 'like', "%{$search}%")
                  ->orWhere('l_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($roleFilter === 'admin') {
            $query->where('role', 'admin');
        } elseif ($roleFilter === 'owner') {
            $query->where('role', 'owner');
        } elseif ($roleFilter === 'sitter') {
            $query->where('is_sitter', true);
        }

        // Status filter
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        // Verification filter
        if ($verificationFilter !== 'all') {
            $query->where('id_validation_status', $verificationFilter);
        }

        // Sort
        switch ($sortFilter) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('f_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('f_name', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $users = $query->paginate(15)->withQueryString();

        // ==========================================
        // STATS
        // ==========================================
        $stats = [
            'total'      => User::count(),
            'owners'     => User::where('role', 'owner')->count(),
            'sitters'    => User::where('is_sitter', true)->count(),
            'suspended'  => User::where('status', 'suspended')->count(),
            'banned'     => User::where('status', 'banned')->count(),
        ];

        return view('admin.users.index', compact(
            'users',
            'stats',
            'search',
            'roleFilter',
            'statusFilter',
            'verificationFilter',
            'sortFilter',
            'showTrashed'
        ));
    }

    // ==========================================
    // SHOW — View single user details
    // ==========================================
    public function show(User $user)
    {
        $complaintsAgainst = Complaint::where('respondent_id', $user->id)->count();
        $complaintsFiled   = Complaint::where('complainant_id', $user->id)->count();

        $user->load(['sitterProfile', 'pets.petType']);

        return view('admin.users.show', compact(
            'user',
            'complaintsAgainst',
            'complaintsFiled'
        ));
    }

    // ==========================================
    // SUSPEND — Temporarily suspend user
    // ==========================================
    public function suspend(Request $request, User $user)
    {
        abort_if($user->id === Auth::id(), 400, 'You cannot suspend yourself.');
        abort_if($user->isAdmin(), 400, 'Admins cannot be suspended.');

        $reason = $request->input('reason', 'Suspended by admin.');

        try {
            $user->status = 'suspended';
            $user->save();

            \App\Services\NotificationService::send(
                userId:    $user->id,
                type:      'account_suspended',
                title:     'Account suspended',
                message:   "Your account has been suspended. Reason: {$reason}",
            );

            return back()->with('status', "{$user->f_name}'s account has been suspended.");
        } catch (\Exception $e) {
            Log::error('User Suspend Error: ' . $e->getMessage());
            return back()->with('error', __('messages.db_error_friendly'));
        }
    }

    // ==========================================
    // ACTIVATE — Reactivate suspended user
    // ==========================================
    public function activate(User $user)
    {
        try {
            $user->status = 'active';
            $user->save();

            \App\Services\NotificationService::send(
                userId:    $user->id,
                type:      'account_activated',
                title:     'Account reactivated',
                message:   'Your account has been reactivated. Welcome back!',
            );

            return back()->with('status', "{$user->f_name}'s account has been activated.");
        } catch (\Exception $e) {
            Log::error('User Activate Error: ' . $e->getMessage());
            return back()->with('error', __('messages.db_error_friendly'));
        }
    }

    // ==========================================
    // BAN — Permanently ban user
    // ==========================================
    public function ban(Request $request, User $user)
    {
        abort_if($user->id === Auth::id(), 400, 'You cannot ban yourself.');
        abort_if($user->isAdmin(), 400, 'Admins cannot be banned.');

        $reason = $request->input('reason', 'Banned by admin.');

        DB::beginTransaction();
        try {
            $user->status    = 'banned';
            $user->is_sitter = false;
            $user->save();

            // Optional: cancel tanan active bookings
            \App\Models\Booking::where(function ($q) use ($user) {
                    $q->where('owner_id', $user->id)
                      ->orWhere('sitter_id', $user->id);
                })
                ->whereIn('status', ['pending', 'accepted'])
                ->update([
                    'status'              => 'cancelled',
                    'cancellation_reason' => 'User account banned.',
                    'cancelled_by'        => 'admin',
                ]);

            DB::commit();

            return back()->with('status', "{$user->f_name}'s account has been banned.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User Ban Error: ' . $e->getMessage());
            return back()->with('error', __('messages.db_error_friendly'));
        }
    }

    // ==========================================
    // UNSUSPEND / UNBAN — Restore to active
    // ==========================================
    public function restore(User $user)
    {

        try {
            $user->status = 'active';
            $user->save();

            return back()->with('status', "{$user->f_name}'s account has been restored.");
        } catch (\Exception $e) {
            Log::error('User Restore Error: ' . $e->getMessage());
            return back()->with('error', __('messages.db_error_friendly'));
        }
    }

    // ==========================================
    // SOFT DELETE — Hide user (reversible)
    // ==========================================
    public function destroy(User $user)
    {
        // Safety: cannot delete self
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Safety: cannot delete admins
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot delete admin accounts.');
        }

        // Safety: cannot delete with active bookings
        $hasActiveBookings = $user->bookingsAsOwner()
                ->whereIn('status', ['pending', 'accepted'])
                ->exists()
            || $user->bookingsAsSitter()
                ->whereIn('status', ['pending', 'accepted'])
                ->exists();

        if ($hasActiveBookings) {
            return back()->with('error', 'Cannot delete user with active bookings. Cancel or complete them first.');
        }

        // ✅ FIX ISSUE #42: Free up the email so it can be used again
        $user->update([
            'email' => 'deleted_' . $user->id . '_' . uniqid() . '@petnanny.local',
        ]);

        $name = $user->full_name ?: $user->email;
        $user->delete(); // soft delete (deleted_at timestamp)

        Log::info('User soft-deleted by admin', [
            'admin_id' => Auth::id(),
            'user_id'  => $user->id,
            'email'    => $user->email,
        ]);

        return back()->with('status', "User {$name} has been deleted. Can be restored within 30 days.");
    }

    // ==========================================
    // RESTORE DELETED — Recover soft-deleted user
    // ==========================================
    public function restoreDeleted(int $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        Log::info('User soft-deleted by admin', [
            'admin_id' => Auth::id(),
            'user_id'  => $user->id,
        ]);

        return back()->with('status', "User {$user->full_name} has been restored.");
    }

    // ==========================================
    // FORCE DELETE — Permanently remove user
    // ==========================================
    public function forceDelete(int $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        // Safety: cannot delete self or admins
        if ($user->id === Auth::id() || $user->isAdmin()) {
            return back()->with('error', 'Not allowed.');
        }

        // ✅ FIX ISSUE #20: Check if the user has any bookings (active or history)
        // We don't want to cascade-delete bookings and lose financial records.
        $hasBookings = $user->bookingsAsOwner()->exists() || $user->bookingsAsSitter()->exists();

        if ($hasBookings) {
            return back()->with('error', 'Cannot permanently delete this user because they have booking history. Please use the Soft Delete option instead to preserve data integrity.');
        }

        $name = $user->full_name ?: $user->email;
        $user->forceDelete(); // Permanent deletion — cannot be restored

        Log::warning('User permanently deleted by admin', [
            'admin_id' => Auth::id(),
            'user_id'  => $id,
            'email'    => $user->email,
        ]);

        return back()->with('status', "User {$name} has been permanently deleted.");
    }

    // ==========================================
    // PROMOTE — User → Admin
    // ==========================================
    public function promote(User $user)
    {
        // Safety: cannot promote self
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You are already an admin.');
        }

        // Safety: cannot promote suspended/banned users
        if ($user->status !== 'active') {
            return back()->with('error', 'Cannot promote suspended or banned users. Activate them first.');
        }

        // Safety: already admin
        if ($user->isAdmin()) {
            return back()->with('error', 'User is already an admin.');
        }

        try {
            $user->role = 'admin';
            $user->save();

            Log::warning('User PROMOTED to admin', [
                'admin_id' => Auth::id(),
                'user_id'  => $user->id,
                'email'    => $user->email,
            ]);

            return back()->with('status', "{$user->full_name} has been promoted to Administrator.");
        } catch (\Exception $e) {
            Log::error('User Promote Error: ' . $e->getMessage());
            return back()->with('error', __('messages.db_error_friendly'));
        }
    }

    // ========================================== //
    // DEMOTE — Admin → Owner                     //
    // ========================================== //
    public function demote(User $user)
    {
        // Safety: cannot demote self
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot demote yourself.');
        }

        // Safety: cannot demote if only admin left
        if (User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Cannot demote the only remaining admin.');
        }

        // Safety: not admin
        if (! $user->isAdmin()) {
            return back()->with('error', 'User is not an admin.');
        }

        try {
            $user->role      = 'owner';
            $user->is_sitter = false;
            $user->save();

            Log::warning('Admin DEMOTED to owner', [
                'admin_id' => Auth::id(),
                'user_id'  => $user->id,
                'email'    => $user->email,
            ]);

            return back()->with('status', "{$user->full_name} has been demoted to Pet Owner.");
        } catch (\Exception $e) {
            Log::error('User Demote Error: ' . $e->getMessage());
            return back()->with('error', __('messages.db_error_friendly'));
        }
    }

    /**
     * Export users as CSV (Excel-compatible).
     * Respects the same filters as the index page.
     */
    public function export(Request $request)
    {
        $search             = $request->query('search');
        $roleFilter         = $request->query('role', 'all');
        $statusFilter       = $request->query('status', 'all');
        $verificationFilter = $request->query('verification', 'all');
        $sortFilter         = $request->query('sort', 'newest');

        $query = User::query();

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('f_name', 'like', "%{$search}%")
                  ->orWhere('l_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role
        if ($roleFilter !== 'all') {
            if ($roleFilter === 'admin') {
                $query->where('role', 'admin');
            } elseif ($roleFilter === 'sitter') {
                $query->where('is_sitter', true);
            } elseif ($roleFilter === 'owner') {
                $query->where('role', 'owner')->where('is_sitter', false);
            }
        }

        // Status
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        // Verification
        if ($verificationFilter !== 'all') {
            $query->where('id_validation_status', $verificationFilter);
        }

        // Sort
        match ($sortFilter) {
            'oldest'    => $query->oldest(),
            'name_asc'  => $query->orderBy('f_name')->orderBy('l_name'),
            'name_desc' => $query->orderByDesc('f_name')->orderByDesc('l_name'),
            default     => $query->latest(),
        };

        // ✅ FIX ISSUE #38: Get the count before chunking (without loading all records)
        $totalUsers = $query->count();

        $filename = 'users_export_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ];

        // ✅ FIX ISSUE #38: Pass the query builder (not the loaded data) to the stream
        return response()->stream(function () use ($query, $totalUsers) {
            $out = fopen('php://output', 'w');

            // UTF-8 BOM for Excel to detect encoding (important for ₱ symbol)
            fwrite($out, "\xEF\xBB\xBF");

            // Meta info
            fputcsv($out, ['PetNanny — User Export']);
            fputcsv($out, ['Generated', now()->format('F j, Y g:i A')]);
            fputcsv($out, ['Total Users', $totalUsers]);
            fputcsv($out, []);

            // Header row
            fputcsv($out, [
                'ID',
                'First Name',
                'Last Name',
                'Email',
                'Phone',
                'Role',
                'Sitter Status',
                'ID Verification',
                'Account Status',
                'Joined',
            ]);

            // ✅ FIX ISSUE #38: Process 500 records at a time to save memory
            $query->chunk(500, function ($users) use ($out) {
                foreach ($users as $u) {
                    $role = $u->isAdmin()
                        ? 'Admin'
                        : ($u->is_sitter ? 'Pet Sitter' : 'Pet Owner');

                    fputcsv($out, [
                        $u->id,
                        $this->escapeCsvFormula($u->f_name ?? ''),
                        $this->escapeCsvFormula($u->l_name ?? ''),
                        $this->escapeCsvFormula($u->email ?? ''),
                        $this->escapeCsvFormula($u->contact_number ?? ''),
                        $role,
                        $u->sitter_status ?? '—',
                        $u->id_validation_status ?? '—',
                        $u->status ?? 'active',
                        optional($u->created_at)->format('Y-m-d'),
                    ]);
                }
            });

            fclose($out);
        }, 200, $headers);
    }
    /**
     * Escape potential CSV formula injection.
     * Cells starting with =, +, -, @, \t, or \r are treated as formulas by Excel.
     */
    private function escapeCsvFormula(mixed $value)
    {
        if (is_string($value) && preg_match('/^[=+\-@\t\r]/', $value)) {
            return "'" . $value;
        }
        return $value;
    }
}
