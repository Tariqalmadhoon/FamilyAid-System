<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Household;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $this->normalizeNumericInputs($request);

        $request->validate([
            'first_name' => ['required', 'string', 'max:120', 'min:2'],
            'father_name' => ['nullable', 'string', 'max:120'],
            'grandfather_name' => ['nullable', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120', 'min:2'],
            'national_id' => ['required', 'digits:9', 'unique:users,national_id'],
            'phone_country_code' => ['required', 'in:+970,+972'],
            'phone' => ['required', 'digits:9'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            // Honeypot field - should be empty
            'website' => ['nullable', 'max:0'],
        ]);

        // Rate limiting for registration
        $key = 'register:' . $request->ip();
        if (cache()->get($key, 0) >= 5) {
            return back()->withErrors([
                'national_id' => 'Too many registration attempts. Please try again later.',
            ]);
        }
        cache()->put($key, cache()->get($key, 0) + 1, now()->addHour());

        $user = DB::transaction(function () use ($request) {
            $fullName = collect([
                $request->first_name,
                $request->father_name,
                $request->grandfather_name,
                $request->last_name,
            ])->filter()->implode(' ');
            $phone = $this->normalizePhoneE164(
                (string) $request->phone,
                (string) $request->phone_country_code
            );

            $user = User::create([
                'name' => $fullName,
                'first_name' => $request->first_name,
                'father_name' => $request->father_name,
                'grandfather_name' => $request->grandfather_name,
                'last_name' => $request->last_name,
                'national_id' => $request->national_id,
                'phone' => $phone,
                'password' => Hash::make($request->password),
                'is_staff' => false,
            ]);

            // Assign citizen role
            $user->assignRole('citizen');

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        // Redirect to onboarding wizard since household is not yet created
        return redirect()->route('citizen.onboarding');
    }

    /**
     * Convert Arabic-Indic digits to western digits for numeric fields.
     */
    protected function normalizeNumericInputs(Request $request): void
    {
        $convert = function (?string $value): ?string {
            if ($value === null) return null;
            $eastern = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
            $western = ['0','1','2','3','4','5','6','7','8','9'];
            return str_replace($eastern, $western, $value);
        };

        $request->merge([
            'national_id' => $convert($request->input('national_id')),
            'phone' => $convert($request->input('phone')),
        ]);
    }

    /**
     * Normalize local phone with selected country code to E.164 format.
     */
    protected function normalizePhoneE164(string $phone, string $countryCode): string
    {
        $digits = preg_replace('/\D/', '', $phone) ?? '';
        $countryDigits = preg_replace('/\D/', '', $countryCode) ?? '';

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        if ($countryDigits !== '' && str_starts_with($digits, $countryDigits)) {
            return '+' . $digits;
        }

        return '+' . $countryDigits . $digits;
    }
}
