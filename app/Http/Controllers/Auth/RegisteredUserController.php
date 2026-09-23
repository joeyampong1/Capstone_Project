<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;              
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
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'm_name' => $request->m_name, 
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'owner',
            'status' => 'active',
            'is_sitter' => false,
            'sitter_level' => 1,
            'sitter_status' => 'pending',
            'id_validation_status' => 'pending',
            'pet_count' => 0,
        ]);

      
        Setting::create(['user_id' => $user->id]);

        event(new Registered($user));

        return redirect()->route('login')
                         ->with('status', 'Your account has been created successfully! Please log in.');
    }
}