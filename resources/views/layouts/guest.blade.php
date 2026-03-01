<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FamilyAid') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Tajawal', sans-serif; }

        /* Page enter animation */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-enter { animation: fadeSlideUp 0.5s ease-out forwards; }

        /* Input focus transition */
        .input-focus-transition {
            transition: all 0.2s ease;
        }
        .input-focus-transition:focus {
            transform: scale(1.01);
        }

        /* Button loading */
        .btn-loading {
            position: relative;
            pointer-events: none;
        }
        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            right: 50%;
            margin-top: -8px;
            margin-right: -8px;
            border: 2px solid transparent;
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* RTL adjustments */
        [dir="rtl"] .rtl\:text-right { text-align: right; }
        [dir="rtl"] .rtl\:mr-auto { margin-right: auto; margin-left: 0; }
        [dir="rtl"] .rtl\:ml-0 { margin-left: 0; }
        [dir="rtl"] .rtl\:space-x-reverse > :not([hidden]) ~ :not([hidden]) {
            --tw-space-x-reverse: 1;
        }
    </style>
</head>
<body class="antialiased bg-gradient-to-br from-slate-50 via-white to-teal-50/30 min-h-screen">
    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 {{ app()->getLocale() === 'ar' ? 'left-4' : 'right-4' }} z-50 space-y-2"></div>

    <!-- Background Pattern -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute top-0 {{ app()->getLocale() === 'ar' ? 'left-0' : 'right-0' }} w-96 h-96 bg-teal-100/40 rounded-full blur-3xl -translate-y-1/2 {{ app()->getLocale() === 'ar' ? '-translate-x-1/2' : 'translate-x-1/2' }}"></div>
        <div class="absolute bottom-0 {{ app()->getLocale() === 'ar' ? 'right-0' : 'left-0' }} w-96 h-96 bg-slate-100/50 rounded-full blur-3xl translate-y-1/2 {{ app()->getLocale() === 'ar' ? 'translate-x-1/2' : '-translate-x-1/2' }}"></div>
    </div>

    <!-- Language Switcher -->
    <div class="absolute top-4 {{ app()->getLocale() === 'ar' ? 'left-4' : 'right-4' }}">
        <form action="{{ route('language.switch') }}" method="POST" class="inline">
            @csrf
            <input type="hidden" name="locale" value="{{ app()->getLocale() === 'ar' ? 'en' : 'ar' }}">
            <button type="submit" class="px-3 py-1.5 text-sm font-medium text-slate-600 bg-white/80 backdrop-blur rounded-lg border border-slate-200 hover:bg-white hover:border-slate-300 transition-all shadow-sm">
                {{ app()->getLocale() === 'ar' ? __('messages.language.en') : __('messages.language.ar') }}
            </button>
        </form>
    </div>

    <div class="min-h-screen flex flex-col justify-center items-center px-4 py-8">
        <!-- Logo -->
        <div class="mb-8 animate-enter">
            <a href="/" class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-teal-500/25">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
                <span class="text-2xl font-bold text-slate-800">{{ __('messages.app_name') }}</span>
            </a>
        </div>

        <!-- Auth Card -->
        <div class="w-full max-w-md animate-enter" style="animation-delay: 0.1s;">
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl shadow-slate-200/50 border border-white/50 overflow-hidden">
                {{ $slot }}
            </div>
        </div>

        <!-- Footer -->
        <p class="mt-8 text-sm text-slate-400 animate-enter" style="animation-delay: 0.2s;">
© {{ date('Y') }} تجمع مخيمات العائدين – القرارة
جميع الحقوق محفوظة
        </p>
    </div>

    <!-- Toast Script -->
    <script>
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-teal-500' : type === 'error' ? 'bg-red-500' : 'bg-slate-700';
            toast.className = `${bgColor} text-white px-4 py-3 rounded-lg shadow-lg text-sm font-medium transform transition-all duration-300 translate-x-full opacity-0`;
            toast.textContent = message;
            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            }, 10);

            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif
        @if(session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif
        @if($errors->any())
            showToast('{{ __('messages.error.validation') }}', 'error');
        @endif
    </script>

    @stack('scripts')
</body>
</html>
