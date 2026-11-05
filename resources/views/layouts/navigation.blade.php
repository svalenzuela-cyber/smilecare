<nav x-data="{ open: false }" class="navbar-pattern border-b border-blue-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <x-application-logo class="block h-9 w-auto fill-current text-blue-600" />
                        <span class="font-bold text-lg text-blue-900">SmileCare</span>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-nav-link>

                    @if (Auth::user()->role === 'admin')
                        <x-nav-link :href="route('admin.appointments.index')" :active="request()->routeIs('admin.appointments.*')">Appointments</x-nav-link>
                        <x-nav-link :href="route('admin.services.index')" :active="request()->routeIs('admin.services.*')">Services</x-nav-link>
                        <x-nav-link :href="route('admin.dentists.index')" :active="request()->routeIs('admin.dentists.*')">Dentists</x-nav-link>
                    @endif

                    @if (Auth::user()->role === 'patient')
                        <x-nav-link :href="route('appointments.create')" :active="request()->routeIs('appointments.create')">Book appointment</x-nav-link>
                        <x-nav-link :href="route('appointments.index')" :active="request()->routeIs('appointments.index')">My appointments</x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-900 bg-transparent hover:text-blue-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (mobile menu button) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-blue-800 hover:bg-blue-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-responsive-nav-link>

            @if (Auth::user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.appointments.index')" :active="request()->routeIs('admin.appointments.*')">Appointments</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.services.index')" :active="request()->routeIs('admin.services.*')">Services</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.dentists.index')" :active="request()->routeIs('admin.dentists.*')">Dentists</x-responsive-nav-link>
            @endif

            @if (Auth::user()->role === 'patient')
                <x-responsive-nav-link :href="route('appointments.create')" :active="request()->routeIs('appointments.create')">Book appointment</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('appointments.index')" :active="request()->routeIs('appointments.index')">My appointments</x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-blue-200">
            <div class="px-4">
                <div class="font-medium text-base text-blue-900">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-blue-700">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>