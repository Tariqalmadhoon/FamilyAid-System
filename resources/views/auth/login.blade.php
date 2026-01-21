<x-guest-layout>
    <!-- Header -->
    <div class="p-8 pb-0">
        <h1 class="text-2xl font-bold text-slate-800 text-center">{{ __('auth.welcome_back') }}</h1>
        <p class="mt-2 text-sm text-slate-500 text-center">{{ __('auth.login_subtitle') }}</p>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('login') }}" x-data="{ loading: false }" @submit="loading = true" class="p-8">
        @csrf

        <!-- National ID -->
        <div class="mb-5">
            <label for="national_id" class="block text-sm font-medium text-slate-700 mb-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                {{ __('auth.national_id') }}
            </label>
            <div class="relative">
                <input 
                    id="national_id" 
                    type="text" 
                    name="national_id" 
                    value="{{ old('national_id') }}" 
                    required 
                    autofocus 
                    autocomplete="username"
                    class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                    placeholder="{{ __('auth.enter_national_id') }}"
                    dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
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
            <a href="{{ route('password.security.request') }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium transition-colors">
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
