<x-guest-layout>
    <!-- Header -->
    <div class="p-8 pb-0">
        <h1 class="text-2xl font-bold text-slate-800 text-center">{{ __('auth.register') }}</h1>
        <p class="mt-2 text-sm text-slate-500 text-center">{{ __('auth.register_subtitle') }}</p>
    </div>

    <!-- Form -->
<form method="POST" action="{{ route('register') }}" x-data="registerForm()" @submit="loading = true" class="p-8">
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
                <input 
                    type="tel" 
                    name="phone" 
                    x-model="form.phone"
                    value="{{ old('phone') }}" 
                    required
                    maxlength="10"
                    inputmode="numeric"
                    pattern="[0-9]{10}"
                    class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all"
                    dir="ltr"
                    @input="filterDigits($event, 10)"
                >
                @error('phone')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                <p class="mt-1 text-xs text-slate-500">{{ __('auth.phone_hint') }}</p>
            </div>

            <!-- Next Button -->
            <button type="button" @click="step = 2" :disabled="!step1Valid" class="w-full py-3.5 px-4 bg-gradient-to-r from-teal-500 to-teal-600 text-white font-semibold rounded-xl shadow-lg shadow-teal-500/25 hover:shadow-xl hover:from-teal-600 hover:to-teal-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
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
                <input 
                    type="password" 
                    name="password" 
                    required 
                    x-model="form.password"
                    @input="evaluateStrength()"
                    class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all"
                    dir="ltr"
                >
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
                <input 
                    type="password" 
                    name="password_confirmation" 
                    required 
                    x-model="form.password_confirmation"
                    class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all"
                    dir="ltr"
                >
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
        function registerForm() {
            return {
                loading: false,
                step: 1,
                form: {
                    first_name: @json(old('first_name')),
                    father_name: @json(old('father_name')),
                    grandfather_name: @json(old('grandfather_name')),
                    last_name: @json(old('last_name')),
                    national_id: @json(old('national_id')),
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
                    const nid = (this.form.national_id || '').trim();
                    const phone = (this.form.phone || '').trim();
                    return first.length >= 2
                        && last.length >= 2
                        && nid.length === 9
                        && phone.length === 10;
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
