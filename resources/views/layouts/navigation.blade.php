<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <div class="w-9 h-9 bg-gradient-to-br from-teal-500 to-teal-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                        <span class="font-bold text-gray-800">{{ __('messages.app_name') }}</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:flex {{ app()->getLocale() === 'ar' ? 'sm:mr-10 sm:space-x-reverse' : 'sm:ml-10' }}">
                    @if(auth()->user()->hasRole('citizen'))
                        <x-nav-link :href="route('citizen.dashboard')" :active="request()->routeIs('citizen.dashboard')">
                            {{ __('messages.nav.dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('citizen.members.index')" :active="request()->routeIs('citizen.members.*')">
                            {{ __('messages.nav.members') }}
                        </x-nav-link>
                    @endif

                    @if(auth()->user()->hasAnyRole(['admin', 'data_entry', 'auditor', 'distributor']))
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('messages.nav.dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.households.index')" :active="request()->routeIs('admin.households.*')">
                            {{ __('messages.nav.households') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.programs.index')" :active="request()->routeIs('admin.programs.*')">
                            {{ __('messages.nav.programs') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.distributions.index')" :active="request()->routeIs('admin.distributions.*')">
                            {{ __('messages.nav.distributions') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.import-export.index')" :active="request()->routeIs('admin.import-export.*')">
                            {{ __('messages.nav.import_export') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center {{ app()->getLocale() === 'ar' ? 'sm:mr-6' : 'sm:ml-6' }} gap-3">
                <!-- Language Switcher -->
                <form action="{{ route('language.switch') }}" method="POST">
                    @csrf
                    <input type="hidden" name="locale" value="{{ app()->getLocale() === 'ar' ? 'en' : 'ar' }}">
                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                        {{ app()->getLocale() === 'ar' ? __('messages.language.short_en') : __('messages.language.short_ar') }}
                    </button>
                </form>

                <x-dropdown :align="app()->getLocale() === 'ar' ? 'left' : 'right'" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="{{ app()->getLocale() === 'ar' ? 'mr-1' : 'ml-1' }}">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('messages.nav.logout') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(auth()->user()->hasRole('citizen'))
                <x-responsive-nav-link :href="route('citizen.dashboard')" :active="request()->routeIs('citizen.dashboard')">
                    {{ __('messages.nav.dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('citizen.members.index')" :active="request()->routeIs('citizen.members.*')">
                    {{ __('messages.nav.members') }}
                </x-responsive-nav-link>
            @endif

            @if(auth()->user()->hasAnyRole(['admin', 'data_entry', 'auditor', 'distributor']))
                <x-responsive-nav-link :href="route('admin.dashboard')">{{ __('messages.nav.dashboard') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.households.index')">{{ __('messages.nav.households') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.programs.index')">{{ __('messages.nav.programs') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.distributions.index')">{{ __('messages.nav.distributions') }}</x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->national_id }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('messages.nav.logout') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
