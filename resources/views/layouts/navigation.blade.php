<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(Auth::user()->role == 'admin')
                        <x-nav-link :href="route('admin.pegawai.index')" :active="request()->routeIs('admin.pegawai.*')">
                            {{ __('Data Pegawai') }}
                        </x-nav-link>
                        
                        <x-nav-link :href="route('admin.laporan.index')" :active="request()->routeIs('admin.laporan.*')">
                            {{ __('Laporan Cuti') }}
                        </x-nav-link>
                    @endif

                    @if(Auth::user()->role == 'pegawai')
                        <x-nav-link :href="route('cuti.create')" :active="request()->routeIs('cuti.create')">
                            {{ __('Ajukan Cuti') }}
                        </x-nav-link>
                        <x-nav-link :href="route('cuti.index')" :active="request()->routeIs('cuti.index')">
                            {{ __('Riwayat Cuti') }}
                        </x-nav-link>
                    @endif

                    @if(Auth::user()->role == 'pimpinan' || Auth::user()->bawahan()->exists())
                        <x-nav-link :href="route('pimpinan.persetujuan.index')" :active="request()->routeIs('pimpinan.persetujuan.*')">
                            {{ __('Persetujuan Cuti') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Notifications Dropdown -->
                <x-dropdown align="right" width="64">
                    <x-slot name="trigger">
                        <button class="relative inline-flex items-center p-2 mr-2 border border-transparent text-sm leading-4 font-medium rounded-full text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            @if(Auth::user()->unreadNotifications->count() > 0)
                                <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full">{{ Auth::user()->unreadNotifications->count() }}</span>
                            @endif
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="p-2 border-b border-gray-100 font-semibold text-gray-700">Notifikasi</div>
                        @forelse(Auth::user()->unreadNotifications->take(5) as $notification)
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition duration-150 ease-in-out">
                                <p class="text-sm font-medium text-gray-800">{{ $notification->data['pesan'] ?? 'Notifikasi Baru' }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</p>
                            </a>
                        @empty
                            <div class="px-4 py-3 text-sm text-gray-500 text-center">Belum ada notifikasi baru.</div>
                        @endforelse
                    </x-slot>
                </x-dropdown>

                <!-- User Profile Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if(Auth::user()->role == 'admin')
                <x-responsive-nav-link :href="route('admin.pegawai.index')" :active="request()->routeIs('admin.pegawai.*')">
                    {{ __('Data Pegawai') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.laporan.index')" :active="request()->routeIs('admin.laporan.*')">
                    {{ __('Laporan Cuti') }}
                </x-responsive-nav-link>
            @endif

            @if(Auth::user()->role == 'pegawai')
                <x-responsive-nav-link :href="route('cuti.create')" :active="request()->routeIs('cuti.create')">
                    {{ __('Ajukan Cuti') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('cuti.index')" :active="request()->routeIs('cuti.index')">
                    {{ __('Riwayat Cuti') }}
                </x-responsive-nav-link>
            @endif

            @if(Auth::user()->role == 'pimpinan' || Auth::user()->bawahan()->exists())
                <x-responsive-nav-link :href="route('pimpinan.persetujuan.index')" :active="request()->routeIs('pimpinan.persetujuan.*')">
                    {{ __('Persetujuan Cuti') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>