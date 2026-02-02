<x-guest-layout>
    <!-- Header -->
    <div class="p-8 pb-0">
        <h1 class="text-2xl font-bold text-slate-800 text-center">{{ __('auth.forgot_password') }}</h1>
        <p class="mt-2 text-sm text-slate-500 text-center">{{ __('auth.enter_phone_or_id') }}</p>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('password.otp.send') }}" x-data="{ loading: false }" @submit="loading = true" class="p-8">
        @csrf

        <!-- National ID or Phone -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 mb-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                {{ __('auth.enter_phone_or_id') }}
            </label>
            <div class="relative">
                <input 
                    type="tel" 
                    name="identifier" 
                    value="{{ old('identifier') }}" 
                    required 
                    autofocus
                    maxlength="10"
                    inputmode="numeric"
                    class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                    dir="ltr"
                    oninput="this.value=this.value.replace(/\\D/g,'').slice(0,10)"
                >
                <div class="absolute inset-y-0 {{ app()->getLocale() === 'ar' ? 'left-4' : 'right-4' }} flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                    </svg>
                </div>
            </div>
            @error('identifier')
                <p class="mt-2 text-sm text-red-500 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit -->
        <button 
            type="submit" 
            :class="{ 'btn-loading opacity-70': loading }"
            :disabled="loading"
            class="w-full py-3.5 px-4 bg-gradient-to-r from-teal-500 to-teal-600 text-white font-semibold rounded-xl shadow-lg shadow-teal-500/25 hover:shadow-xl hover:from-teal-600 hover:to-teal-700 transition-all"
        >
            <span x-show="!loading">{{ __('auth.send_code') }}</span>
            <span x-show="loading" class="opacity-0">{{ __('messages.loading') }}</span>
        </button>

        <!-- Back to Login -->
        <p class="mt-6 text-center text-sm text-slate-500">
            <a href="{{ route('login') }}" class="text-teal-600 hover:text-teal-700 font-medium transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('auth.login') }}
            </a>
        </p>
    </form>
</x-guest-layout>
