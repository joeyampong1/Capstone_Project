<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'f_name' => ['required', 'string', 'max:50'],
            'l_name' => ['required', 'string', 'max:50'],
            'm_name' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::min(12)],
        ]);

        // ✅ Create with only fillable fields
        $user = User::create([
            'f_name'   => $request->f_name,
            'l_name'   => $request->l_name,
            'm_name'   => $request->m_name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // ✅ Direct assignment for non-fillable fields (fresh account defaults)
        $user->role                  = 'owner';
        $user->status                = 'active';
        $user->is_sitter             = false;
        $user->sitter_level          = 1;
        $user->sitter_status         = null;
        $user->id_validation_status  = 'unverified';
        $user->pet_count             = 0;
        $user->save();

        event(new Registered($user));

        return redirect()->route('login')
                        ->with('status', 'Your account has been created successfully! Please log in.');
    }
}
