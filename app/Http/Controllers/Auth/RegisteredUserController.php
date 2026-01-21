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
     * Security questions for password reset.
     */
    public static array $securityQuestions = [
        'What is your mother\'s maiden name?',
        'What was the name of your first pet?',
        'What city were you born in?',
        'What is your favorite book?',
        'What was the name of your elementary school?',
        'What is your father\'s middle name?',
    ];

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register', [
            'securityQuestions' => self::$securityQuestions,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'national_id' => ['required', 'string', 'max:20', 'unique:users,national_id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'security_question' => ['required', 'string', 'max:255'],
            'security_answer' => ['required', 'string', 'min:2', 'max:100'],
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
            $user = User::create([
                'name' => $request->name,
                'national_id' => $request->national_id,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'security_question' => $request->security_question,
                'security_answer_hash' => Hash::make(strtolower(trim($request->security_answer))),
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
}
