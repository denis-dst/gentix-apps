<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $isRegistrationEnabled = (bool) (Setting::where('key', 'tenant_registration_enabled')->value('value') ?? true);

        return view('auth.register', compact('isRegistrationEnabled'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $isRegistrationEnabled = (bool) (Setting::where('key', 'tenant_registration_enabled')->value('value') ?? true);

        if (!$isRegistrationEnabled) {
            return redirect()->route('login')->with('error', 'Pendaftaran partner/tenant mandiri saat ini sedang dinonaktifkan oleh administrator.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'organization_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $tenant = Tenant::create([
            'name' => $request->organization_name,
            'slug' => Str::slug($request->organization_name) . '-' . rand(1000, 9999),
            'email' => $request->email,
            'status' => 'active',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tenant_id' => $tenant->id,
        ]);

        $user->assignRole('Penyedia Event');

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
