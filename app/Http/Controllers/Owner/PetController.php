<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\PetType;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PetController extends Controller
{
    public function __construct(
        private CloudinaryService $cloudinary
    ) {}

    // ==========================================
    // Shared validation rules
    // ==========================================
    private function validationRules(): array
    {
        return [
            'name'                 => 'required|string|max:255',
            'pet_type_id'          => 'required|exists:pet_types,id',
            'breed'                => 'nullable|string|max:255',
            'age'                  => 'required|integer|min:0|max:50',
            'size'                 => 'required|in:small,medium,large,giant',
            'temperament'          => 'required|in:friendly,shy,aggressive,playful,anxious',
            'vaccination_status'   => 'required|in:complete,partial,none',
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
            'dietary_restrictions' => 'nullable|in:none,dry_food,wet_food,raw,prescription',
            'photo_path'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }

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
    // STORE — Save new pet (with Cloudinary)
    // ==========================================
    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules());

        unset($validated['photo_path']);

        if ($request->hasFile('photo_path')) {
            $upload = $this->cloudinary->upload(
                $request->file('photo_path'),
                'pets',
                [
                    'transformation' => [
                        'width'  => 800,
                        'height' => 800,
                        'crop'   => 'limit',
                    ],
                ]
            );

            if ($upload['success']) {
                $validated['photo_path']      = $upload['url'];
                $validated['photo_public_id'] = $upload['public_id'];
            } else {
                \Log::error('Pet photo upload failed', ['error' => $upload['error']]);
            }
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
    // UPDATE — Save changes (with Cloudinary)
    // ==========================================
    public function update(Request $request, $id)
    {
        $pet = auth()->user()->pets()->findOrFail($id);

        $validated = $request->validate($this->validationRules());

        unset($validated['photo_path']);

        if ($request->hasFile('photo_path')) {
            // Delete old photo (Cloudinary or local)
            $this->deleteOldPhoto($pet->photo_path, $pet->photo_public_id);

            // Upload new photo to Cloudinary
            $upload = $this->cloudinary->upload(
                $request->file('photo_path'),
                'pets',
                [
                    'transformation' => [
                        'width'  => 800,
                        'height' => 800,
                        'crop'   => 'limit',
                    ],
                ]
            );

            if ($upload['success']) {
                $validated['photo_path']      = $upload['url'];
                $validated['photo_public_id'] = $upload['public_id'];
            } else {
                \Log::error('Pet photo update failed', ['error' => $upload['error']]);
            }
        }

        $pet->update($validated);

        return redirect()
            ->route('mypets.index')
            ->with('status', 'Pet updated successfully!');
    }

    // ==========================================
    // DESTROY — Delete pet (with Cloudinary)
    // ==========================================
    public function destroy($id)
    {
        $pet = auth()->user()->pets()->findOrFail($id);

        // Delete photo (Cloudinary or local)
        $this->deleteOldPhoto($pet->photo_path, $pet->photo_public_id);

        $pet->delete();

        return redirect()
            ->route('mypets.index')
            ->with('status', 'Pet deleted successfully!');
    }

    // ==========================================
    // PRIVATE HELPER — Delete old photo
    // ==========================================
    private function deleteOldPhoto(?string $path, ?string $publicId): void
    {
        if (!$path) return;

        // Cloudinary image
        if (str_starts_with($path, 'http') && $publicId) {
            $this->cloudinary->delete($publicId);
            return;
        }

        // Local storage image
        if (!str_starts_with($path, 'http') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
