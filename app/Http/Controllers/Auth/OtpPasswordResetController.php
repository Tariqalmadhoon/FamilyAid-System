<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordOtp;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class OtpPasswordResetController extends Controller
{
    public function __construct(private SmsService $sms)
    {
    }

    /**
     * Show the request form (enter national ID or phone).
     */
    public function showRequestForm(): View
    {
        return view('auth.forgot-password-security');
    }

    /**
     * Send OTP to the registered phone.
     */
    public function sendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'identifier' => ['required', 'digits_between:9,10'],
        ]);

        $user = User::where('national_id', $request->identifier)
            ->orWhere('phone', $request->identifier)
            ->first();

        if (!$user || !$user->phone) {
            return back()->withErrors(['identifier' => __('auth.failed')]);
        }

        $rateKey = 'otp-send:' . $user->id . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($rateKey, 3)) {
            $seconds = RateLimiter::availableIn($rateKey);
            return back()->withErrors(['identifier' => __('auth.throttle', ['seconds' => $seconds])]);
        }
        RateLimiter::hit($rateKey, 3600);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        PasswordOtp::updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => $user->phone,
                'code_hash' => Hash::make($code),
                'expires_at' => now()->addMinutes(5),
                'attempts' => 0,
            ]
        );

        $this->sms->send($user->phone, __('auth.code_sent') . ' ' . $code);

        return redirect()->route('password.otp.verify', ['user' => $user->id])
            ->with('status', __('auth.code_sent'));
    }

    /**
     * Show verify form.
     */
    public function showVerifyForm(Request $request): View|RedirectResponse
    {
        $userId = $request->query('user');
        $user = $userId ? User::find($userId) : null;
        if (!$user) {
            return redirect()->route('password.otp.request')->withErrors(['identifier' => __('auth.failed')]);
        }

        return view('auth.verify-otp', [
            'user' => $user,
        ]);
    }

    /**
     * Verify OTP and reset password.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'code' => ['required', 'digits:6'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $otp = PasswordOtp::where('user_id', $request->user_id)->first();

        if (!$otp || $otp->isExpired()) {
            return back()->withErrors(['code' => __('auth.code_invalid')]);
        }

        if ($otp->attempts >= 5) {
            return back()->withErrors(['code' => __('auth.throttle', ['seconds' => 300])]);
        }

        if (!Hash::check($request->code, $otp->code_hash)) {
            $otp->increment('attempts');
            return back()->withErrors(['code' => __('auth.code_invalid')]);
        }

        $user = User::find($request->user_id);
        if (!$user) {
            return back()->withErrors(['code' => __('auth.failed')]);
        }

        $user->update(['password' => Hash::make($request->password)]);
        $otp->delete();

        RateLimiter::clear('otp-send:' . $user->id . '|' . $request->ip());

        return redirect()->route('login')->with('status', __('auth.password_reset_success'));
    }
}
