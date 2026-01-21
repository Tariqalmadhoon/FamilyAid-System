<x-guest-layout>
    <!-- Header -->
    <div class="p-8 pb-0">
        <h1 class="text-2xl font-bold text-slate-800 text-center">{{ __('auth.register') }}</h1>
        <p class="mt-2 text-sm text-slate-500 text-center">{{ __('auth.register_subtitle') }}</p>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('register') }}" x-data="{ loading: false, step: 1 }" @submit="loading = true" class="p-8">
        @csrf
        
        <!-- Honeypot -->
        <div style="display: none;">
            <input type="text" name="website" value="">
        </div>

        <!-- Step 1: Basic Info -->
        <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
            
            <!-- Name -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                    {{ __('auth.name') }}
                </label>
                <input 
                    type="text" 
                    name="name" 
                    value="{{ old('name') }}" 
                    required 
                    class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                    dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
                >
                @error('name')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>

            <!-- National ID -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                    {{ __('auth.national_id') }}
                </label>
                <input 
                    type="text" 
                    name="national_id" 
                    value="{{ old('national_id') }}" 
                    required 
                    class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                    dir="ltr"
                >
                @error('national_id')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>

            <!-- Phone -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                    {{ __('auth.phone') }}
                </label>
                <input 
                    type="tel" 
                    name="phone" 
                    value="{{ old('phone') }}" 
                    class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all"
                    dir="ltr"
                >
            </div>

            <!-- Next Button -->
            <button type="button" @click="step = 2" class="w-full py-3.5 px-4 bg-gradient-to-r from-teal-500 to-teal-600 text-white font-semibold rounded-xl shadow-lg shadow-teal-500/25 hover:shadow-xl hover:from-teal-600 hover:to-teal-700 transition-all">
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
                    class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all"
                    dir="ltr"
                >
                @error('password')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
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
                    class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all"
                    dir="ltr"
                >
            </div>

            <!-- Security Question -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                    {{ __('auth.security_question') }}
                </label>
                <select 
                    name="security_question" 
                    required 
                    class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                    dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
                >
                    <option value="">-- {{ __('messages.actions.select') ?? 'اختر' }} --</option>
                    @foreach(__('auth.questions') as $key => $question)
                        <option value="{{ $question }}" {{ old('security_question') === $question ? 'selected' : '' }}>{{ $question }}</option>
                    @endforeach
                </select>
                @error('security_question')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>

            <!-- Security Answer -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                    {{ __('auth.security_answer') }}
                </label>
                <input 
                    type="text" 
                    name="security_answer" 
                    value="{{ old('security_answer') }}" 
                    required 
                    class="input-focus-transition w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                    dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
                >
                @error('security_answer')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>

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
</x-guest-layout>
