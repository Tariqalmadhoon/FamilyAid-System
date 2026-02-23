<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.account_security.title') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <section class="bg-white shadow-sm sm:rounded-xl border border-gray-100 p-6 sm:p-8">
                <header class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('messages.account_security.change_password') }}</h3>
                    <p class="mt-1 text-sm text-gray-600">{{ __('messages.account_security.subtitle') }}</p>
                </header>

                <form method="post" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    @method('put')

                    <div>
                        <x-input-label for="current_password" :value="__('messages.account_security.current_password')" />
                        <div class="relative mt-1">
                            <x-text-input
                                id="current_password"
                                name="current_password"
                                type="password"
                                class="block w-full {{ app()->getLocale() === 'ar' ? 'pl-11' : 'pr-11' }}"
                                autocomplete="current-password"
                                required
                            />
                            <button
                                type="button"
                                data-toggle-password="#current_password"
                                class="absolute inset-y-0 z-10 {{ app()->getLocale() === 'ar' ? 'left-0 pl-3' : 'right-0 pr-3' }} flex items-center text-gray-400 hover:text-gray-600"
                                aria-label="{{ __('messages.account_security.show_password') }}"
                                title="{{ __('messages.account_security.show_password') }}"
                            >
                                <svg style="margin: 5px" data-eye-open class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg  data-eye-off class="hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.956 9.956 0 012.043-3.368m3.099-2.46A9.96 9.96 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.964 9.964 0 01-4.293 5.151M15 12a3 3 0 00-3-3m0 0a3 3 0 00-2.236 5.003M3 3l18 18"></path>
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('messages.account_security.new_password')" />
                        <div class="relative mt-1">
                            <x-text-input
                                id="password"
                                name="password"
                                type="password"
                                class="block w-full {{ app()->getLocale() === 'ar' ? 'pl-11' : 'pr-11' }}"
                                autocomplete="new-password"
                                required
                            />
                            <button
                                type="button"
                                data-toggle-password="#password"
                                class="absolute inset-y-0 z-10 {{ app()->getLocale() === 'ar' ? 'left-0 pl-3' : 'right-0 pr-3' }} flex items-center text-gray-400 hover:text-gray-600"
                                aria-label="{{ __('messages.account_security.show_password') }}"
                                title="{{ __('messages.account_security.show_password') }}"
                            >
                                <svg style="margin: 5px" data-eye-open class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg data-eye-off class="hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.956 9.956 0 012.043-3.368m3.099-2.46A9.96 9.96 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.964 9.964 0 01-4.293 5.151M15 12a3 3 0 00-3-3m0 0a3 3 0 00-2.236 5.003M3 3l18 18"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="mt-3">
                            <div class="flex items-center justify-between text-xs text-gray-600">
                                <span>{{ __('messages.account_security.password_strength') }}</span>
                                <span id="password-strength-label" class="font-semibold">{{ __('messages.account_security.strength_empty') }}</span>
                            </div>
                            <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-gray-100">
                                <div id="password-strength-bar" class="h-2 w-0 rounded-full bg-gray-300 transition-all duration-300"></div>
                            </div>
                        </div>

                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('messages.account_security.confirm_new_password')" />
                        <div class="relative mt-1">
                            <x-text-input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                class="block w-full {{ app()->getLocale() === 'ar' ? 'pl-11' : 'pr-11' }}"
                                autocomplete="new-password"
                                required
                            />
                            <button
                                type="button"
                                data-toggle-password="#password_confirmation"
                                class="absolute inset-y-0 z-10 {{ app()->getLocale() === 'ar' ? 'left-0 pl-3' : 'right-0 pr-3' }} flex items-center text-gray-400 hover:text-gray-600"
                                aria-label="{{ __('messages.account_security.show_password') }}"
                                title="{{ __('messages.account_security.show_password') }}"
                            >
                                <svg style="margin: 5px" data-eye-open class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg data-eye-off class="hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.956 9.956 0 012.043-3.368m3.099-2.46A9.96 9.96 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.964 9.964 0 01-4.293 5.151M15 12a3 3 0 00-3-3m0 0a3 3 0 00-2.236 5.003M3 3l18 18"></path>
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-3 mt-4">
                        <x-primary-button>{{ __('messages.actions.save') }}</x-primary-button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>

<script>
    (function () {
        const run = function () {
            const showPasswordText = @json(__('messages.account_security.show_password'));
            const hidePasswordText = @json(__('messages.account_security.hide_password'));

            document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
                const selector = button.getAttribute('data-toggle-password');
                const input = document.querySelector(selector);
                const eyeOpen = button.querySelector('[data-eye-open]');
                const eyeOff = button.querySelector('[data-eye-off]');

                if (!input || !eyeOpen || !eyeOff) {
                    return;
                }

                button.addEventListener('click', function () {
                    const isHidden = input.type === 'password';
                    input.type = isHidden ? 'text' : 'password';
                    eyeOpen.classList.toggle('hidden', isHidden);
                    eyeOff.classList.toggle('hidden', !isHidden);
                    button.setAttribute('aria-label', isHidden ? hidePasswordText : showPasswordText);
                    button.setAttribute('title', isHidden ? hidePasswordText : showPasswordText);
                });
            });

            const passwordInput = document.getElementById('password');
            const strengthLabel = document.getElementById('password-strength-label');
            const strengthBar = document.getElementById('password-strength-bar');

            const strengthTexts = {
                0: @json(__('messages.account_security.strength_empty')),
                1: @json(__('messages.account_security.strength_very_weak')),
                2: @json(__('messages.account_security.strength_weak')),
                3: @json(__('messages.account_security.strength_medium')),
                4: @json(__('messages.account_security.strength_strong')),
                5: @json(__('messages.account_security.strength_very_strong')),
            };

            const strengthColors = {
                0: 'bg-gray-300',
                1: 'bg-red-500',
                2: 'bg-orange-500',
                3: 'bg-amber-500',
                4: 'bg-teal-500',
                5: 'bg-emerald-600',
            };

            const allStrengthClasses = Object.values(strengthColors);

            const evaluateStrength = function () {
                if (!passwordInput || !strengthLabel || !strengthBar) {
                    return;
                }

                const password = passwordInput.value || '';
                let score = 0;

                if (password.length > 0) {
                    if (password.length >= 8) score++;
                    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
                    if (/\d/.test(password)) score++;
                    if (/[^A-Za-z0-9]/.test(password)) score++;
                    if (password.length >= 12) score++;
                }

                strengthLabel.textContent = strengthTexts[score];
                strengthBar.style.width = (score * 20) + '%';
                strengthBar.classList.remove(...allStrengthClasses);
                strengthBar.classList.add(strengthColors[score]);
            };

            if (passwordInput) {
                passwordInput.addEventListener('input', evaluateStrength);
                evaluateStrength();
            }

            const showProfessionalSuccess = function (message) {
                if (typeof showToast === 'function') {
                    showToast(message, 'success');
                    return;
                }

                const notice = document.createElement('div');
                notice.className = 'fixed top-5 z-50 rounded-xl border border-emerald-200 bg-white px-4 py-3 text-sm text-emerald-700 shadow-xl';
                notice.style.maxWidth = '360px';
                notice.style[document.documentElement.dir === 'rtl' ? 'left' : 'right'] = '20px';
                notice.innerHTML = `
                    <div class="flex items-start gap-2">
                        <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">✓</span>
                        <span>${message}</span>
                    </div>
                `;
                document.body.appendChild(notice);
                setTimeout(() => notice.remove(), 3500);
            };

            @if (session('status') === 'password-updated')
                showProfessionalSuccess(@json(__('messages.account_security.password_updated')));
            @endif
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', run);
        } else {
            run();
        }
    })();
</script>
