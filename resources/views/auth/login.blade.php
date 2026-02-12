<x-guest-layout>
    <!-- Header -->
    <div class="px-8 pt-8 pb-2 text-center">
        <h1 class="text-2xl font-bold text-slate-800">{{ __('auth.welcome_back') }}</h1>
        <p class="mt-1.5 text-sm text-slate-400">{{ __('auth.login_subtitle') }}</p>
    </div>

    <!-- Guidance Toggle Section -->
    <div  style="margin: 20px; margin-bottom:0;" class="px-8 pt-2 pb-0 mt-4 " x-data="{ open: false }">
        <!-- Toggle Button -->
        <button @click="open = !open" type="button"
            class="w-full flex items-center justify-center gap-2 py-2 px-4 rounded-xl text-sm font-medium transition-all duration-300"
            :class="open ? 'bg-teal-50 text-teal-700 border border-teal-200' : 'bg-slate-50 text-slate-500 border border-slate-200 hover:bg-teal-50 hover:text-teal-600 hover:border-teal-200'">
            <span>ⓘ</span>
            <span x-text="open ? '{{ __('auth.hide_guidance') }}' : '{{ __('auth.show_guidance') }}'"></span>
            <span class="text-[10px] transition-transform duration-300" :class="open ? 'rotate-180' : ''">▼</span>
        </button>

        <!-- Collapsible Content -->
        <div x-show="open"
             x-transition:enter="transition-all ease-out duration-400"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition-all ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             x-cloak
             class="mt-3">

            <!-- Bullet Points -->
            <div class="bg-slate-50/80 border border-slate-100 rounded-xl p-4 mb-4">
                <ul class="space-y-2 text-[13px] text-slate-600">
                    <li class="flex items-center gap-2">
                        <span class="text-teal-500 text-xs">●</span>
                        {{ __('auth.guidance_1') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-teal-500 text-xs">●</span>
                        {{ __('auth.guidance_2') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-teal-500 text-xs">●</span>
                        {{ __('auth.guidance_3') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-teal-500 text-xs">●</span>
                        {{ __('auth.guidance_4') }}
                    </li>
                </ul>
            </div>

            <!-- 4-Step Process Indicator -->
            <div class="flex items-start justify-between mb-4 px-2">
                @php $steps = ['step_1', 'step_2', 'step_3', 'step_4']; @endphp
                @foreach($steps as $i => $step)
                    <div class="flex flex-col items-center text-center" style="flex: 0 0 auto; max-width: 68px;">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                            {{ $i === 0
                                ? 'bg-teal-500 text-white'
                                : 'bg-slate-100 text-slate-400' }}">
                            {{ $i + 1 }}
                        </div>
                        <span class="mt-1 text-[10px] leading-tight {{ $i === 0 ? 'text-teal-600 font-semibold' : 'text-slate-400' }}">{{ __('auth.' . $step) }}</span>
                    </div>
                    @if($i < 3)
                        <div class="flex-1 mt-3.5 mx-1">
                            <div class="h-px {{ $i === 0 ? 'bg-teal-300' : 'bg-slate-200' }}"></div>
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Disclaimer -->
            <p class="text-[11px] text-slate-400 text-center leading-relaxed mb-1">
                ⚠️ {{ __('auth.disclaimer') }}
            </p>
        </div>
    </div>

    <!-- Divider -->
    <div class="px-8 pt-4">
        <div class="h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('login') }}" x-data="{ loading: false }" @submit="loading = true" class="p-8 pt-5">
        @csrf

        <!-- National ID -->
        <div class="mb-5">
            <label for="national_id" class="block text-sm font-medium text-slate-700 mb-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                {{ __('auth.national_id') }}
            </label>
            <div class="relative">
                <input
                    id="national_id"
                    type="tel"
                    name="national_id"
                    value="{{ old('national_id') }}"
                    required
                    autofocus
                    autocomplete="username"
                    maxlength="9"
                    inputmode="numeric"
                    class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                    placeholder="{{ __('auth.enter_national_id') }}"
                    dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
                    oninput="this.value=this.value.replace(/[٠-٩]/g,d=>'٠١٢٣٤٥٦٧٨٩'.indexOf(d)).replace(/\\D/g,'').slice(0,9)"
                >
                <div class="absolute inset-y-0 {{ app()->getLocale() === 'ar' ? 'left-4' : 'right-4' }} flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                    </svg>
                </div>
            </div>
            @error('national_id')
                <p class="mt-2 text-sm text-red-500 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-5">
            <label for="password" class="block text-sm font-medium text-slate-700 mb-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                {{ __('auth.password_label') }}
            </label>
            <div class="relative">
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                    dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
                >
                <div class="absolute inset-y-0 {{ app()->getLocale() === 'ar' ? 'left-4' : 'right-4' }} flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
            </div>
            @error('password')
                <p class="mt-2 text-sm text-red-500 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between mb-6 flex-wrap gap-2">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500/20">
                <span class="text-sm text-slate-600 {{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }}">{{ __('auth.remember_me') }}</span>
            </label>
            <a href="{{ route('password.otp.request') }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium transition-colors">
                {{ __('auth.forgot_password') }}
            </a>
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            :class="{ 'btn-loading opacity-70': loading }"
            :disabled="loading"
            class="w-full py-3.5 px-4 bg-gradient-to-r from-teal-500 to-teal-600 text-white font-semibold rounded-xl shadow-lg shadow-teal-500/25 hover:shadow-xl hover:shadow-teal-500/30 hover:from-teal-600 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-all duration-200 disabled:cursor-not-allowed"
        >
            <span x-show="!loading">{{ __('auth.login_btn') }}</span>
            <span x-show="loading" class="opacity-0">{{ __('messages.loading') }}</span>
        </button>

        <!-- Register Link -->
        <p class="mt-6 text-center text-sm text-slate-500">
            {{ __('auth.no_account') }}
            <a href="{{ route('register') }}" class="text-teal-600 hover:text-teal-700 font-semibold transition-colors">
                {{ __('auth.register') }}
            </a>
        </p>
    </form>

</x-guest-layout>
