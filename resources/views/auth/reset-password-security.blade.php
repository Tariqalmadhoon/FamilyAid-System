<x-guest-layout>
    <!-- Header -->
    <div class="p-8 pb-0">
        <h1 class="text-2xl font-bold text-slate-800 text-center">{{ __('auth.reset_password') }}</h1>
        <p class="mt-2 text-sm text-slate-500 text-center">{{ __('auth.set_new_password') }}</p>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('password.security.update') }}" x-data="{ loading: false }" @submit="loading = true" class="p-8">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <!-- New Password -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                {{ __('auth.password_label') }}
            </label>
            <input 
                type="password" 
                name="password" 
                required 
                autofocus
                class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all"
                dir="ltr"
            >
            @error('password')
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 mb-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                {{ __('auth.password_confirmation') }}
            </label>
            <input 
                type="password" 
                name="password_confirmation" 
                required
                class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all"
                dir="ltr"
            >
        </div>

        <!-- Submit -->
        <button 
            type="submit" 
            :class="{ 'btn-loading opacity-70': loading }"
            :disabled="loading"
            class="w-full py-3.5 px-4 bg-gradient-to-r from-teal-500 to-teal-600 text-white font-semibold rounded-xl shadow-lg shadow-teal-500/25 hover:shadow-xl hover:from-teal-600 hover:to-teal-700 transition-all"
        >
            <span x-show="!loading">{{ __('auth.reset_btn') }}</span>
            <span x-show="loading" class="opacity-0">{{ __('messages.loading') }}</span>
        </button>
    </form>
</x-guest-layout>
