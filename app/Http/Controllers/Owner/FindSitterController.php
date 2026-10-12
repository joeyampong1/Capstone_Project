<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Services\MatchingService;
use Illuminate\Http\Request;

class FindSitterController extends Controller
{
    public function __construct(private MatchingService $matcher) {}

    public function index(Request $request)
    {
        $owner = auth()->user();
        $pets  = Pet::where('user_id', $owner->id)->get();

        // Kung naay pet selected → gamita ang full weighted matching algorithm
        if ($request->filled('pet_id')) {
            $pet = Pet::where('user_id', $owner->id)
                      ->findOrFail($request->integer('pet_id'));

            $sitters = $this->matcher->findMatches($pet, $owner, 30);
        } else {
            // Default: general ranking (walay pet-specific factors)
            $sitters = $this->matcher->findGeneralMatches($owner, 30);
        }

        return view('owner.find_sitter', compact('sitters', 'pets'));
    }
}
