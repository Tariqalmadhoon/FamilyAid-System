<x-guest-layout>
    <!-- Header -->
    <div class="p-8 pb-0">
        <h1 class="text-2xl font-bold text-slate-800 text-center">{{ __('auth.security_question') }}</h1>
        <p class="mt-2 text-sm text-slate-500 text-center">{{ __('auth.answer_security') }}</p>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('password.security.verify-answer') }}" x-data="{ loading: false }" @submit="loading = true" class="p-8">
        @csrf
        <input type="hidden" name="national_id" value="{{ $national_id }}">

        <!-- Security Question Display -->
        <div class="mb-4 p-4 bg-slate-50 rounded-xl border border-slate-200">
            <p class="text-sm text-slate-500 mb-1">{{ __('auth.security_question') }}</p>
            <p class="font-medium text-slate-800 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ $security_question }}</p>
        </div>

        <!-- Security Answer -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 mb-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                {{ __('auth.security_answer') }}
            </label>
            <input 
                type="text" 
                name="security_answer" 
                required 
                autofocus
                class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
            >
            @error('security_answer')
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit -->
        <button 
            type="submit" 
            :class="{ 'btn-loading opacity-70': loading }"
            :disabled="loading"
            class="w-full py-3.5 px-4 bg-gradient-to-r from-teal-500 to-teal-600 text-white font-semibold rounded-xl shadow-lg shadow-teal-500/25 hover:shadow-xl hover:from-teal-600 hover:to-teal-700 transition-all"
        >
            <span x-show="!loading">{{ __('auth.verify_btn') }}</span>
            <span x-show="loading" class="opacity-0">{{ __('messages.loading') }}</span>
        </button>

        <!-- Back -->
        <p class="mt-6 text-center text-sm text-slate-500">
            <a href="{{ route('password.security.request') }}" class="text-teal-600 hover:text-teal-700 font-medium transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('messages.actions.back') }}
            </a>
        </p>
    </form>
</x-guest-layout>
