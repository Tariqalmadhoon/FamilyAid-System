<x-guest-layout>
    <div class="p-8 pb-0">
        <h1 class="text-2xl font-bold text-slate-800 text-center">{{ __('auth.reset_password') }}</h1>
        <p class="mt-2 text-sm text-slate-500 text-center">{{ __('auth.enter_otp') }}</p>
    </div>

    <form method="POST" action="{{ route('password.otp.update') }}" x-data="otpForm()" @submit="loading=true" class="p-8">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">

        <!-- OTP Code -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('auth.otp_placeholder') }}</label>
            <input type="tel" name="code" x-model="code" required maxlength="6" inputmode="numeric" pattern="\d{6}" @input="filterDigits($event,6)" class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all" dir="ltr">
            @error('code')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('auth.password_label') }}</label>
            <input type="password" name="password" x-model="password" @input="evaluateStrength()" required class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all" dir="ltr">
            @error('password')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            <div class="mt-2">
                <div class="h-2 rounded-full bg-slate-200 overflow-hidden">
                    <div class="h-full transition-all" :class="strengthBar.class" :style="`width: ${strengthBar.width}%`"></div>
                </div>
                <p class="mt-1 text-xs" :class="strengthBar.textClass" x-text="strengthBar.label"></p>
                <p class="mt-1 text-[11px] text-slate-500" x-text="strengthHint"></p>
            </div>
        </div>

        <!-- Password Confirmation -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('auth.password_confirmation') }}</label>
            <input type="password" name="password_confirmation" x-model="password_confirmation" required class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all" dir="ltr">
        </div>

        @if(session('status'))
            <div class="mb-4 p-3 rounded-lg bg-teal-50 text-teal-700 text-sm">{{ session('status') }}</div>
        @endif

        <button type="submit" :class="{ 'btn-loading opacity-70': loading }" :disabled="loading" class="w-full py-3.5 px-4 bg-gradient-to-r from-teal-500 to-teal-600 text-white font-semibold rounded-xl shadow-lg shadow-teal-500/25 hover:shadow-xl hover:from-teal-600 hover:to-teal-700 transition-all">
            <span x-show="!loading">{{ __('auth.reset_btn') }}</span>
            <span x-show="loading" class="opacity-0">{{ __('messages.loading') }}</span>
        </button>

        <p class="mt-6 text-center text-sm text-slate-500">
            <a href="{{ route('password.otp.request') }}" class="text-teal-600 hover:text-teal-700 font-medium transition-colors">{{ __('messages.actions.back') }}</a>
        </p>
    </form>

    @push('scripts')
    <script>
        function otpForm() {
            return {
                loading: false,
                code: '',
                password: '',
                password_confirmation: '',
                strengthBar: { width: 0, class: 'bg-red-500', label: '', textClass: 'text-red-600' },
                strengthHint: '',
                filterDigits(event, max) {
                    const digits = event.target.value.replace(/\D/g, '').slice(0, max);
                    event.target.value = digits;
                    this.code = digits;
                },
                evaluateStrength() {
                    const pwd = this.password || '';
                    let score = 0;
                    if (pwd.length >= 8) score++;
                    if (pwd.length >= 12) score++;
                    if (/[0-9]/.test(pwd)) score++;
                    if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) score++;
                    if (/[^A-Za-z0-9]/.test(pwd)) score++;
                    const levels = [
                        { label: '{{ __('auth.strength_weak') }}', width: 20, class: 'bg-red-500', textClass: 'text-red-600', hint: '{{ __('auth.strength_hint_basic') }}' },
                        { label: '{{ __('auth.strength_fair') }}', width: 40, class: 'bg-orange-500', textClass: 'text-orange-600', hint: '{{ __('auth.strength_hint_numbers') }}' },
                        { label: '{{ __('auth.strength_ok') }}', width: 60, class: 'bg-amber-500', textClass: 'text-amber-600', hint: '{{ __('auth.strength_hint_upper') }}' },
                        { label: '{{ __('auth.strength_good') }}', width: 80, class: 'bg-green-500', textClass: 'text-green-600', hint: '{{ __('auth.strength_hint_symbols') }}' },
                        { label: '{{ __('auth.strength_strong') }}', width: 100, class: 'bg-emerald-600', textClass: 'text-emerald-600', hint: '{{ __('auth.strength_hint_strong') }}' },
                    ];
                    const level = levels[Math.min(score, levels.length - 1)];
                    this.strengthBar = { width: level.width, class: level.class, label: level.label, textClass: level.textClass };
                    this.strengthHint = level.hint;
                }
            }
        }
    </script>
    @endpush
</x-guest-layout>
