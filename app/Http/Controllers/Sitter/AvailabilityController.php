<?php

namespace App\Http\Controllers\Sitter;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\SitterProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailabilityController extends Controller
{
    /**
     * Helper: Get the current user's sitter profile
     */
    private function getSitterProfile()
    {
        return SitterProfile::where('user_id', Auth::id())->first();
    }

    public function index()
    {
        $sitterProfile = $this->getSitterProfile();

        // Safety: Kung wala pay sitter profile, empty data
        if (!$sitterProfile) {
            return view('sitters.sitter_availability', [
                'availabilities' => collect(),
                'totalSlots' => 0,
                'availableSlots' => 0,
                'unavailableSlots' => 0,
                'todaySlots' => collect(),
                'todayAvailable' => 0,
                'todayUnavailable' => 0,
            ]);
        }

        $availabilities = Availability::forSitter($sitterProfile->id)
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        // Stats
        $totalSlots = $availabilities->count();
        $availableSlots = $availabilities->where('is_available', true)->count();
        $unavailableSlots = $availabilities->where('is_available', false)->count();

        // Today's slots
        $todaySlots = $availabilities->filter(function ($slot) {
            return \Carbon\Carbon::parse($slot->date)->isToday();
        });
        $todayAvailable = $todaySlots->where('is_available', true)->count();
        $todayUnavailable = $todaySlots->where('is_available', false)->count();

        return view('sitters.sitter_availability', compact(
            'availabilities',
            'totalSlots',
            'availableSlots',
            'unavailableSlots',
            'todaySlots',
            'todayAvailable',
            'todayUnavailable'
        ));
    }

    public function store(Request $request)
    {
        // STEP 1: Check kung naay sitter profile ang user
        $sitterProfile = $this->getSitterProfile();

        if (!$sitterProfile) {
            return redirect()->route('sitter.application')
                ->with('error', 'Please complete your sitter application first.');
        }

        // STEP 2: Validate
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|in:available,unavailable',
        ]);

        // STEP 3: Check for overlap
        $overlap = Availability::where('sitter_id', $sitterProfile->id)
            ->where('date', $request->date)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                      ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                      });
            })
            ->exists();

        if ($overlap) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['start_time' => 'This time slot overlaps with an existing availability.']);
        }

        // STEP 4: Save
        Availability::create([
            'sitter_id' => $sitterProfile->id,  // <-- sitter_profiles.id na!
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_available' => $request->status === 'available' ? 1 : 0,
        ]);

        return redirect()->route('sitter.availability')
            ->with('status', 'Availability added successfully!');
    }

    public function destroy($id)
    {
        $sitterProfile = $this->getSitterProfile();

        if (!$sitterProfile) {
            return redirect()->route('sitter.availability')
                ->with('error', 'No sitter profile found.');
        }

        $availability = Availability::where('sitter_id', $sitterProfile->id)
            ->findOrFail($id);

        $availability->delete();

        return redirect()->route('sitter.availability')
            ->with('status', 'Availability removed successfully!');
    }
}