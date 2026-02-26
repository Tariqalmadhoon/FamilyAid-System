<x-guest-layout>
    @php
        $registerInitialStep = 1;
        if ($errors->hasAny(['first_name', 'father_name', 'grandfather_name', 'last_name', 'birth_date', 'national_id', 'phone_country_code', 'phone', 'website'])) {
            $registerInitialStep = 1;
        } elseif ($errors->hasAny(['password', 'password_confirmation'])) {
            $registerInitialStep = 2;
        }
    @endphp
    <!-- Header -->
    <div class="p-8 pb-0">
        <h1 class="text-2xl font-bold text-slate-800 text-center">{{ __('auth.register') }}</h1>
        <p class="mt-2 text-sm text-slate-500 text-center">{{ __('auth.register_subtitle') }}</p>
    </div>

    <!-- Form -->
<form method="POST" action="{{ route('register') }}" x-data="registerForm({ initialStep: {{ $registerInitialStep }} })" @submit="loading = true" class="p-8">
    @csrf

        <!-- Honeypot -->
        <div style="display: none;">
            <input type="text" name="website" value="">
        </div>

        <!-- Step 1: Basic Info -->
        <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- First Name -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                        {{ __('auth.first_name') }} <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="first_name"
                        x-model="form.first_name"
                        value="{{ old('first_name') }}"
                        required
                        maxlength="120"
                        class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                        dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
                    >
                    @error('first_name')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <!-- Father Name -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                        {{ __('auth.father_name') }}
                    </label>
                    <input
                        type="text"
                        name="father_name"
                        x-model="form.father_name"
                        value="{{ old('father_name') }}"
                        maxlength="120"
                        class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                        dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
                    >
                    @error('father_name')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <!-- Grandfather Name -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                        {{ __('auth.grandfather_name') }}
                    </label>
                    <input
                        type="text"
                        name="grandfather_name"
                        x-model="form.grandfather_name"
                        value="{{ old('grandfather_name') }}"
                        maxlength="120"
                        class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                        dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
                    >
                    @error('grandfather_name')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <!-- Last Name -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                        {{ __('auth.last_name') }} <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="last_name"
                        x-model="form.last_name"
                        value="{{ old('last_name') }}"
                        required
                        maxlength="120"
                        class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                        dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
                    >
                    @error('last_name')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <!-- Birth Date -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                        {{ __('auth.birth_date') }} <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="date"
                        name="birth_date"
                        x-model="form.birth_date"
                        value="{{ old('birth_date') }}"
                        required
                        max="{{ now()->subDay()->toDateString() }}"
                        class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                    >
                    @error('birth_date')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- National ID -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-slate-700 mb-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                    {{ __('auth.national_id') }} <span class="text-red-500">*</span>
                </label>
                <input
                    type="tel"
                    name="national_id"
                    x-model="form.national_id"
                    value="{{ old('national_id') }}"
                    required
                    maxlength="9"
                    inputmode="numeric"
                    pattern="[0-9]{9}"
                    class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                    dir="ltr"
                    @input="filterDigits($event, 9)"
                >
                @error('national_id')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                <p class="mt-1 text-xs text-slate-500">{{ __('auth.national_id_hint') }}</p>
            </div>

            <!-- Phone -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-slate-700 mb-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                    {{ __('auth.phone') }} <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-2" dir="ltr">
                    <div class="relative w-32 sm:w-36">
                        <select
                            name="phone_country_code"
                            x-model="form.phone_country_code"
                            class="input-focus-transition w-full appearance-none pl-3 pr-9 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all"
                            dir="ltr"
                        >
                            <option value="+970">+970</option>
                            <option value="+972">+972</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-500">

                        </div>
                    </div>
                    <div class="flex-1">
                        <input
                            type="tel"
                            name="phone"
                            x-model="form.phone"
                            value="{{ old('phone') }}"
                            required
                            maxlength="9"
                            inputmode="numeric"
                            pattern="[0-9]{9}"
                            placeholder="590000000"
                            class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all"
                            dir="ltr"
                            @input="filterDigits($event, 9)"
                        >
                    </div>
                </div>
                @error('phone_country_code')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                @error('phone')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                <p class="mt-1 text-xs text-slate-500">{{ __('auth.phone_hint') }}</p>
            </div>

            <!-- Next Button -->
            <button style="margin-top: 10px" type="button" @click="step = 2" :disabled="!step1Valid" class="w-full py-3.5 px-4 bg-gradient-to-r from-teal-500 to-teal-600 text-white font-semibold rounded-xl shadow-lg shadow-teal-500/25 hover:shadow-xl hover:from-teal-600 hover:to-teal-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                {{ __('messages.actions.next') }}
            </button>
        </div>

        <!-- Step 2: Security & Password -->
        <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">

            <!-- Password -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                    {{ __('auth.password_label') }}
                </label>
                <div class="relative">
                    <input
                        :type="showPassword ? 'text' : 'password'"
                        name="password"
                        required
                        x-model="form.password"
                        @input="evaluateStrength()"
                        class="input-focus-transition w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all"
                        dir="ltr"
                    >
                    <button
                    style="margin:8px"
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 left-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors"
                        :aria-label="showPassword ? 'Hide password' : 'Show password'"
                    >
                        <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 3C5 3 1.73 6.11.46 9.17a2.3 2.3 0 000 1.66C1.73 13.89 5 17 10 17s8.27-3.11 9.54-6.17a2.3 2.3 0 000-1.66C18.27 6.11 15 3 10 3zm0 11a4 4 0 110-8 4 4 0 010 8z" />
                            <path d="M10 8a2 2 0 100 4 2 2 0 000-4z" />
                        </svg>
                        <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M3.28 2.22a.75.75 0 10-1.06 1.06l2.1 2.1A11.34 11.34 0 00.46 9.17a2.3 2.3 0 000 1.66C1.73 13.89 5 17 10 17a10.5 10.5 0 005.01-1.2l1.71 1.71a.75.75 0 101.06-1.06L3.28 2.22zM10 15.5c-4.3 0-7.2-2.67-8.13-5 .69-1.73 2.42-3.65 5.15-4.52l1.42 1.42A4 4 0 0010 14a3.9 3.9 0 002.08-.58l1.79 1.79A8.8 8.8 0 0110 15.5z" />
                            <path d="M10 5c5 0 8.27 3.11 9.54 6.17a2.3 2.3 0 010 1.66c-.28.67-.66 1.32-1.12 1.93l-1.08-1.08c.3-.42.55-.86.74-1.32-.94-2.33-3.83-5-8.08-5-.75 0-1.45.08-2.1.23L6.71 6.4A8.7 8.7 0 0110 5z" />
                        </svg>



                    </button>
                </div>
                @error('password')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror

                <!-- Strength indicator -->
                <div class="mt-2">
                    <div class="h-2 rounded-full bg-slate-200 overflow-hidden">
                        <div class="h-full transition-all" :class="strengthBar.class" :style="`width: ${strengthBar.width}%`"></div>
                    </div>
                    <p class="mt-1 text-xs" :class="strengthBar.textClass" x-text="strengthBar.label"></p>
                    <p class="mt-1 text-[11px] text-slate-500" x-text="strengthHint"></p>
                </div>
            </div>

            <!-- Password Confirmation -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                    {{ __('auth.password_confirmation') }}
                </label>
                <div class="relative">
                    <input
                        :type="showPasswordConfirmation ? 'text' : 'password'"
                        name="password_confirmation"
                        required
                        x-model="form.password_confirmation"
                        class="input-focus-transition w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all"
                        dir="ltr"
                    >
                    <button
                                        style="margin:8px"

                        type="button"
                        @click="showPasswordConfirmation = !showPasswordConfirmation"
                        class="absolute inset-y-0 left-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors"
                        :aria-label="showPasswordConfirmation ? 'Hide password confirmation' : 'Show password confirmation'"
                    >
                        <svg x-show="!showPasswordConfirmation" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 3C5 3 1.73 6.11.46 9.17a2.3 2.3 0 000 1.66C1.73 13.89 5 17 10 17s8.27-3.11 9.54-6.17a2.3 2.3 0 000-1.66C18.27 6.11 15 3 10 3zm0 11a4 4 0 110-8 4 4 0 010 8z" />
                            <path d="M10 8a2 2 0 100 4 2 2 0 000-4z" />
                        </svg>
                        <svg x-show="showPasswordConfirmation" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M3.28 2.22a.75.75 0 10-1.06 1.06l2.1 2.1A11.34 11.34 0 00.46 9.17a2.3 2.3 0 000 1.66C1.73 13.89 5 17 10 17a10.5 10.5 0 005.01-1.2l1.71 1.71a.75.75 0 101.06-1.06L3.28 2.22zM10 15.5c-4.3 0-7.2-2.67-8.13-5 .69-1.73 2.42-3.65 5.15-4.52l1.42 1.42A4 4 0 0010 14a3.9 3.9 0 002.08-.58l1.79 1.79A8.8 8.8 0 0110 15.5z" />
                            <path d="M10 5c5 0 8.27 3.11 9.54 6.17a2.3 2.3 0 010 1.66c-.28.67-.66 1.32-1.12 1.93l-1.08-1.08c.3-.42.55-.86.74-1.32-.94-2.33-3.83-5-8.08-5-.75 0-1.45.08-2.1.23L6.71 6.4A8.7 8.7 0 0110 5z" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="mb-6"></div>

            <!-- Buttons -->
            <div class="flex gap-3">
                <button type="button" @click="step = 1" class="flex-1 py-3 px-4 bg-slate-100 text-slate-700 font-medium rounded-xl hover:bg-slate-200 transition-all">
                    {{ __('messages.actions.back') }}
                </button>
                <button
                    type="submit"
                    :class="{ 'btn-loading opacity-70': loading }"
                    :disabled="loading"
                    class="flex-1 py-3 px-4 bg-gradient-to-r from-teal-500 to-teal-600 text-white font-semibold rounded-xl shadow-lg shadow-teal-500/25 hover:shadow-xl hover:from-teal-600 hover:to-teal-700 transition-all"
                >
                    <span x-show="!loading">{{ __('auth.register_btn') }}</span>
                    <span x-show="loading" class="opacity-0">{{ __('messages.loading') }}</span>
                </button>
            </div>
        </div>

        <!-- Login Link -->
        <p class="mt-6 text-center text-sm text-slate-500">
            {{ __('auth.have_account') }}
            <a href="{{ route('login') }}" class="text-teal-600 hover:text-teal-700 font-semibold transition-colors">
                {{ __('auth.login') }}
            </a>
        </p>
    </form>

    @push('scripts')
    <script>
        function registerForm(config = {}) {
            return {
                loading: false,
                step: Number(config.initialStep ?? 1),
                showPassword: false,
                showPasswordConfirmation: false,
                form: {
                    first_name: @json(old('first_name')),
                    father_name: @json(old('father_name')),
                    grandfather_name: @json(old('grandfather_name')),
                    last_name: @json(old('last_name')),
                    birth_date: @json(old('birth_date')),
                    national_id: @json(old('national_id')),
                    phone_country_code: @json(old('phone_country_code', '+970')),
                    phone: @json(old('phone')),
                    password: '',
                    password_confirmation: '',
                },
                filterDigits(event, max) {
                    const eastern = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
                    const western = ['0','1','2','3','4','5','6','7','8','9'];
                    const normalized = event.target.value.split('').map(ch => {
                        const idx = eastern.indexOf(ch);
                        return idx !== -1 ? western[idx] : ch;
                    }).join('');
                    const digits = normalized.replace(/\\D/g, '').slice(0, max);
                    event.target.value = digits;
                    if (event.target.name === 'national_id') this.form.national_id = digits;
                    if (event.target.name === 'phone') this.form.phone = digits;
                },
                get step1Valid() {
                    const first = (this.form.first_name || '').trim();
                    const last = (this.form.last_name || '').trim();
                    const birthDate = (this.form.birth_date || '').trim();
                    const nid = (this.form.national_id || '').trim();
                    const phoneCountryCode = (this.form.phone_country_code || '').trim();
                    const phone = (this.form.phone || '').trim();
                    return first.length >= 2
                        && last.length >= 2
                        && birthDate.length > 0
                        && nid.length === 9
                        && ['+970', '+972'].includes(phoneCountryCode)
                        && phone.length === 9;
                },
                strengthBar: { width: 0, class: 'bg-red-500', label: '', textClass: 'text-red-600' },
                strengthHint: '',
                evaluateStrength() {
                    const pwd = this.form.password || '';
                    let score = 0;
                    if (pwd.length >= 8) score++;
                    if (pwd.length >= 12) score++;
                    if (/[0-9]/.test(pwd)) score++;
                    if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) score++;
                    if (/[^A-Za-z0-9]/.test(pwd)) score++;

                    const levels = [
                        { label: '{{ __("auth.strength_weak") }}', width: 20, class: 'bg-red-500', textClass: 'text-red-600', hint: '{{ __("auth.strength_hint_basic") }}' },
                        { label: '{{ __("auth.strength_fair") }}', width: 40, class: 'bg-orange-500', textClass: 'text-orange-600', hint: '{{ __("auth.strength_hint_numbers") }}' },
                        { label: '{{ __("auth.strength_ok") }}', width: 60, class: 'bg-amber-500', textClass: 'text-amber-600', hint: '{{ __("auth.strength_hint_upper") }}' },
                        { label: '{{ __("auth.strength_good") }}', width: 80, class: 'bg-green-500', textClass: 'text-green-600', hint: '{{ __("auth.strength_hint_symbols") }}' },
                        { label: '{{ __("auth.strength_strong") }}', width: 100, class: 'bg-emerald-600', textClass: 'text-emerald-600', hint: '{{ __("auth.strength_hint_strong") }}' },
                    ];

                    const level = levels[Math.min(score, levels.length - 1)];
                    this.strengthBar = { width: level.width, class: level.class, label: level.label, textClass: level.textClass };
                    this.strengthHint = level.hint;
                },
            };
        }
    </script>
    @endpush
</x-guest-layout>
