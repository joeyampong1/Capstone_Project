<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\PetType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PetController extends Controller
{
    // ==========================================
    // INDEX — List user's pets
    // ==========================================
    public function index()
    {
        $pets = auth()->user()->pets()->with('petType')->latest()->get();

        return view('mypets.index', compact('pets'));
    }

    // ==========================================
    // CREATE — Show create form
    // ==========================================
    public function create()
    {
        $petTypes = PetType::all();

        return view('mypets.create', compact('petTypes'));
    }

    // ==========================================
    // STORE — Save new pet
    // ==========================================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'pet_type_id'          => 'required|exists:pet_types,id',
            'breed'                => 'nullable|string|max:255',
            'age'                  => 'required|integer|min:0|max:50',
            'size'                 => 'required|in:small,medium,large',
            'temperament'          => 'required|in:calm,friendly,playful,energetic,shy,independent',
            'weight'               => 'nullable|numeric|min:0',
            'weight_unit'          => 'nullable|string|max:10',
            'height'               => 'nullable|numeric|min:0',
            'height_unit'          => 'nullable|string|max:10',
            'length'               => 'nullable|numeric|min:0',
            'length_unit'          => 'nullable|string|max:10',
            'width'                => 'nullable|numeric|min:0',
            'width_unit'           => 'nullable|string|max:10',
            'special_needs'        => 'nullable|string',
            'medical_conditions'   => 'nullable|string',
            'dietary_restrictions' => 'nullable|string',
            'photo_path'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        unset($validated['photo_path']);

        if ($request->hasFile('photo_path')) {
            $validated['photo_path'] = $request->file('photo_path')->store('pet-photos', 'public');
        }

        $validated['user_id'] = auth()->id();

        Pet::create($validated);

        return redirect()
            ->route('mypets.index')
            ->with('status', 'Pet added successfully!');
    }

    // ==========================================
    // EDIT — Show edit form
    // ==========================================
    public function edit($id)
    {
        $pet      = auth()->user()->pets()->findOrFail($id);
        $petTypes = PetType::all();

        return view('mypets.edit', compact('pet', 'petTypes'));
    }

    // ==========================================
    // UPDATE — Save changes
    // ==========================================
    public function update(Request $request, $id)
    {
        $pet = auth()->user()->pets()->findOrFail($id);

        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'pet_type_id'          => 'required|exists:pet_types,id',
            'breed'                => 'nullable|string|max:255',
            'age'                  => 'required|integer|min:0|max:50',
            'size'                 => 'required|in:small,medium,large',
            'temperament'          => 'required|in:calm,friendly,playful,energetic,shy,independent',
            'weight'               => 'nullable|numeric|min:0',
            'weight_unit'          => 'nullable|string|max:10',
            'height'               => 'nullable|numeric|min:0',
            'height_unit'          => 'nullable|string|max:10',
            'length'               => 'nullable|numeric|min:0',
            'length_unit'          => 'nullable|string|max:10',
            'width'                => 'nullable|numeric|min:0',
            'width_unit'           => 'nullable|string|max:10',
            'special_needs'        => 'nullable|string',
            'medical_conditions'   => 'nullable|string',
            'dietary_restrictions' => 'nullable|string',
            'photo_path'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        unset($validated['photo_path']);

        if ($request->hasFile('photo_path')) {
            // Delete old photo
            if ($pet->photo_path) {
                Storage::disk('public')->delete($pet->photo_path);
            }
            $validated['photo_path'] = $request->file('photo_path')->store('pet-photos', 'public');
        }

        $pet->update($validated);

        return redirect()
            ->route('mypets.index')
            ->with('status', 'Pet updated successfully!');
    }

    // ==========================================
    // DESTROY — Delete pet
    // ==========================================
    public function destroy($id)
    {
        $pet = auth()->user()->pets()->findOrFail($id);

        // Delete photo from storage
        if ($pet->photo_path) {
            Storage::disk('public')->delete($pet->photo_path);
        }

        $pet->delete();

        return redirect()
            ->route('mypets.index')
            ->with('status', 'Pet deleted successfully!');
    }
}