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
use Throwable;

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
        $this->normalizeNumericInputs($request);

        $request->validate([
            'identifier' => ['required', 'digits:9'],
            'phone_country_code' => ['required', 'in:+970,+972'],
        ]);

        $identifierDigits = preg_replace('/\D/', '', (string) $request->identifier) ?? '';
        $phoneCandidates = $this->buildPhoneCandidates(
            $identifierDigits,
            (string) $request->phone_country_code
        );

        $user = User::where('national_id', $identifierDigits)->first();

        if (!$user && !empty($phoneCandidates)) {
            $user = User::whereIn('phone', $phoneCandidates)->first();
        }

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

        try {
            $this->sms->send($user->phone, __('auth.code_sent') . ' ' . $code);
        } catch (Throwable $e) {
            report($e);
            $providerMessage = mb_strtolower($e->getMessage());
            $errorMessage = __('auth.sms_send_failed');

            if (str_contains($providerMessage, 'balance')) {
                $errorMessage = __('auth.sms_balance_low');
            } elseif (str_contains($providerMessage, 'verified sms.to account')) {
                $errorMessage = __('auth.sms_verified_only');
            }

            return back()
                ->withInput()
                ->withErrors(['identifier' => $errorMessage])
                ->with('show_support_whatsapp', true);
        }

        return redirect()->route('password.otp.verify', ['user' => $user->id])
            ->with('status', __('auth.code_sent'));
    }
    

    /**
     * Convert Arabic-Indic digits to western digits for numeric inputs.
     */
    protected function normalizeNumericInputs(Request $request): void
    {
        $map = [
            "\u{0660}" => '0', "\u{0661}" => '1', "\u{0662}" => '2', "\u{0663}" => '3', "\u{0664}" => '4',
            "\u{0665}" => '5', "\u{0666}" => '6', "\u{0667}" => '7', "\u{0668}" => '8', "\u{0669}" => '9',
            "\u{06F0}" => '0', "\u{06F1}" => '1', "\u{06F2}" => '2', "\u{06F3}" => '3', "\u{06F4}" => '4',
            "\u{06F5}" => '5', "\u{06F6}" => '6', "\u{06F7}" => '7', "\u{06F8}" => '8', "\u{06F9}" => '9',
        ];

        $request->merge([
            'identifier' => strtr((string) $request->input('identifier', ''), $map),
        ]);
    }

    /**
     * Build backward-compatible phone candidates (local + E.164).
     */
    protected function buildPhoneCandidates(string $identifierDigits, string $countryCode): array
    {
        $digits = preg_replace('/\D/', '', $identifierDigits) ?? '';
        if ($digits === '') {
            return [];
        }

        $selectedCountry = preg_replace('/\D/', '', $countryCode) ?? '';
        $countries = array_values(array_unique(array_filter([$selectedCountry, '970', '972'])));

        $local = $digits;
        if (str_starts_with($local, '00')) {
            $local = substr($local, 2);
        }

        if (str_starts_with($local, '970') || str_starts_with($local, '972')) {
            $local = substr($local, 3);
        }

        if (str_starts_with($local, '0')) {
            $local = substr($local, 1);
        }

        $candidates = [$digits];

        if ($local !== '') {
            $candidates[] = '0' . $local;
            foreach ($countries as $cc) {
                $candidates[] = $cc . $local;
                $candidates[] = '+' . $cc . $local;
            }
        }

        if (str_starts_with($digits, '00')) {
            $candidates[] = '+' . substr($digits, 2);
        }

        if (strlen($digits) >= 11 && !str_starts_with($digits, '+')) {
            $candidates[] = '+' . $digits;
        }

        return array_values(array_unique(array_filter($candidates)));
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

