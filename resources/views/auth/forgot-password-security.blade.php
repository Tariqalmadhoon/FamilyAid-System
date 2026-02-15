<x-guest-layout>
    <!-- Header -->
    <div class="p-8 pb-0">
        <h1 class="text-2xl font-bold text-slate-800 text-center">{{ __('auth.forgot_password') }}</h1>
        <p class="mt-2 text-sm text-slate-500 text-center">{{ __('auth.enter_phone_or_id') }}</p>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('password.otp.send') }}" x-data="{ loading: false, phone_country_code: '{{ old('phone_country_code', '+970') }}' }" @submit="loading = true" class="p-8">
        @csrf

        @if(config('services.sms.driver') === 'log')
            <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                {{ __('auth.sms_log_mode_notice') }}
            </div>
        @endif

        <!-- National ID or Phone -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 mb-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                {{ __('auth.enter_phone_or_id') }}
            </label>
            <div class="flex items-center gap-2" dir="ltr">
                <div class="relative w-32 sm:w-36">
                    <select
                        name="phone_country_code"
                        x-model="phone_country_code"
                        class="input-focus-transition w-full appearance-none pl-3 pr-9 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all"
                        dir="ltr"
                    >
                        <option value="+970">+970</option>
                        <option value="+972">+972</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-500">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="flex-1 relative">
                    <input
                        type="tel"
                        name="identifier"
                        value="{{ old('identifier') }}"
                        required
                        autofocus
                        maxlength="9"
                        inputmode="numeric"
                        placeholder="590000000"
                        class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                        dir="ltr"
                        oninput="this.value=this.value.replace(/\\D/g,'').slice(0,9)"
                    >
                    <div class="absolute inset-y-0 {{ app()->getLocale() === 'ar' ? 'left-4' : 'right-4' }} flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                        </svg>
                    </div>
                </div>
            </div>
            @error('phone_country_code')
                <p class="mt-2 text-sm text-red-500 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
            @enderror
            @error('identifier')
                <p class="mt-2 text-sm text-red-500 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
            @enderror

            @if(session('show_support_whatsapp'))
                <div class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50/80 px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                    <p class="text-sm text-emerald-800">{{ __('auth.support_contact_notice') }}</p>
                    <a
                        href="https://wa.me/972595199423"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition-colors"
                        dir="ltr"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M19.05 4.94A9.94 9.94 0 0012.02 2C6.5 2 2 6.48 2 12a9.9 9.9 0 001.35 4.98L2 22l5.2-1.33A9.96 9.96 0 0012.02 22C17.54 22 22 17.52 22 12a9.93 9.93 0 00-2.95-7.06zM12.02 20.3a8.2 8.2 0 01-4.18-1.14l-.3-.18-3.09.79.82-3.01-.2-.31A8.22 8.22 0 013.7 12c0-4.59 3.74-8.33 8.32-8.33A8.3 8.3 0 0120.35 12a8.34 8.34 0 01-8.33 8.3zm4.57-6.24c-.25-.12-1.48-.73-1.71-.81-.23-.09-.39-.12-.56.12-.16.24-.64.81-.78.97-.14.17-.28.18-.53.06-.25-.13-1.05-.39-2-1.24-.74-.66-1.24-1.48-1.38-1.73-.14-.24-.01-.37.11-.49.11-.11.25-.28.37-.41.13-.14.17-.24.25-.4.08-.17.04-.31-.02-.43-.06-.12-.56-1.35-.77-1.84-.2-.49-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.23.24-.87.85-.87 2.08s.89 2.42 1.01 2.59c.13.16 1.75 2.67 4.25 3.74.59.25 1.05.4 1.41.51.59.19 1.13.16 1.56.1.48-.07 1.48-.6 1.69-1.18.21-.58.21-1.08.15-1.18-.06-.1-.22-.16-.46-.28z"/>
                        </svg>
                        <span>{{ __('auth.contact_support_whatsapp') }} (+972595199423)</span>
                    </a>
                </div>
            @endif
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
