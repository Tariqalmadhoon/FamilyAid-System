<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class SecurityQuestionController extends Controller
{
    /**
     * Display the forgot password form.
     */
    public function showForm(): View
    {
        return view('auth.forgot-password-security');
    }

    /**
     * Verify national_id and show security question.
     */
    public function verifyNationalId(Request $request): View|RedirectResponse
    {
        $request->validate([
            'national_id' => ['required', 'string', 'max:20'],
        ]);

        // Rate limiting
        $key = 'forgot-password:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'national_id' => "Too many attempts. Please try again in {$seconds} seconds.",
            ]);
        }
        RateLimiter::hit($key, 300); // 5 minute decay

        $user = User::where('national_id', $request->national_id)->first();

        if (!$user || !$user->security_question) {
            return back()->withErrors([
                'national_id' => 'No account found with this National ID or no security question set.',
            ]);
        }

        return view('auth.answer-security-question', [
            'national_id' => $request->national_id,
            'security_question' => $user->security_question,
        ]);
    }

    /**
     * Verify security answer and show reset form.
     */
    public function verifyAnswer(Request $request): View|RedirectResponse
    {
        $request->validate([
            'national_id' => ['required', 'string', 'max:20'],
            'security_answer' => ['required', 'string'],
        ]);

        // Rate limiting
        $key = 'security-answer:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'security_answer' => "Too many attempts. Please try again in {$seconds} seconds.",
            ]);
        }
        RateLimiter::hit($key, 300);

        $user = User::where('national_id', $request->national_id)->first();

        if (!$user || !$user->verifySecurityAnswer($request->security_answer)) {
            return back()
                ->withInput(['national_id' => $request->national_id])
                ->withErrors([
                    'security_answer' => 'Incorrect security answer.',
                ]);
        }

        // Generate a temp token for password reset
        $token = bin2hex(random_bytes(32));
        cache()->put('password-reset:' . $token, $user->id, now()->addMinutes(15));

        RateLimiter::clear($key);

        return view('auth.reset-password-security', [
            'token' => $token,
        ]);
    }

    /**
     * Reset the password.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $userId = cache()->get('password-reset:' . $request->token);

        if (!$userId) {
            return redirect()->route('password.security.request')->withErrors([
                'national_id' => 'Password reset link has expired. Please try again.',
            ]);
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('password.security.request')->withErrors([
                'national_id' => 'User not found.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        cache()->forget('password-reset:' . $request->token);

        return redirect()->route('login')->with('status', 'Your password has been reset successfully!');
    }
}
