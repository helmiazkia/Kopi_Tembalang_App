<style>
    /* ── Hilangkan marker details/summary ── */
    .menu details > summary::marker,
    .menu details > summary::-webkit-details-marker {
        display: none !important;
        content: "";
    }

    /* ── Scrollbar tipis & Custom ── */
    .scrollbar-custom::-webkit-scrollbar {
        width: 4px;
    }
    .scrollbar-custom::-webkit-scrollbar-track {
        background: transparent;
    }
    .scrollbar-custom::-webkit-scrollbar-thumb {
        background: rgba(0,0,0,0.1);
        border-radius: 10px;
    }
    .scrollbar-custom:hover::-webkit-scrollbar-thumb {
        background: rgba(0,0,0,0.2);
    }

    /* ── Indikator aktif ── */
    .active-indicator { position: relative; }
    .active-indicator::after {
        content: "";
        position: absolute;
        right: 12px;
        width: 6px;
        height: 6px;
        background: #D4E971;
        border-radius: 50%;
    }

    /* Animasi Dropdown */
    details[open] summary ~ * {
        animation: fadeIn 0.3s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

@php
    $isMenuOpen = request()->routeIs('admin.menus.*', 'admin.menu_options.*', 'admin.menu_option_items.*');
@endphp

<div class="drawer-side is-drawer-close:overflow-visible shadow-2xl z-[50]">
    <label for="my-drawer-4" aria-label="close sidebar" class="drawer-overlay"></label>

    <!-- Container Utama: h-screen + flex-col -->
    <div class="sticky top-0 h-screen flex flex-col items-start bg-base-100 text-base-content
                w-64 is-drawer-close:w-20 is-drawer-open:w-80
                transition-all duration-500 ease-in-out border-r border-base-200">

        {{-- ── Logo (Fixed at Top) ── --}}
        <div class="w-full flex flex-col items-center justify-center py-10 px-6
                    border-b border-base-200/50 shrink-0 bg-white">
            <img src="{{ asset('images/assets/images/Logo_Kopi_Tembalang.jpeg') }}"
                alt="Kopi Tembalang"
                class="h-24 w-auto object-contain transition-transform duration-500 hover:scale-105">
            <span class="mt-2 text-[10px] font-black tracking-[0.3em] text-gray-400 uppercase
                         is-drawer-close:hidden">
                Administrator
            </span>
        </div>

        {{-- ── Navigation (Scrollable Area) ── --}}
        <!-- flex-1 agar mengambil sisa ruang, min-h-0 agar overflow-y-auto berfungsi -->
        <ul class="menu w-full flex-1 px-4 gap-1 overflow-y-auto overflow-x-hidden scrollbar-custom py-6 min-h-0">

            {{-- Dashboard --}}
            <li>
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-4 p-3 rounded-2xl transition-all duration-300
                           is-drawer-close:tooltip is-drawer-close:tooltip-right
                           {{ request()->routeIs('admin.dashboard')
                               ? 'bg-[#D4E971] text-black shadow-lg shadow-[#D4E971]/30 active-indicator'
                               : 'hover:bg-base-200 text-base-content/80' }}"
                    data-tip="Dashboard">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <path d="M9 22V12h6v10"/>
                    </svg>
                    <span class="is-drawer-close:hidden font-bold text-[14px]">Dashboard</span>
                </a>
            </li>

            {{-- Manajemen User --}}
            <li>
                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center gap-4 p-3 rounded-2xl transition-all duration-300
                           is-drawer-close:tooltip is-drawer-close:tooltip-right
                           {{ request()->routeIs('admin.users.*')
                               ? 'bg-[#D4E971] text-black shadow-lg shadow-[#D4E971]/30 active-indicator'
                               : 'hover:bg-base-200 text-base-content/80' }}"
                    data-tip="Manajemen Users">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span class="is-drawer-close:hidden font-bold text-[14px]">Manajemen Users</span>
                </a>
            </li>

            {{-- Manajemen Menu (Dropdown) --}}
            <li>
                <details {{ $isMenuOpen ? 'open' : '' }}>
                    <summary class="flex items-center gap-4 p-3 rounded-2xl cursor-pointer list-none
                                   hover:bg-base-200 transition-all duration-300
                                   is-drawer-close:tooltip is-drawer-close:tooltip-right
                                   {{ $isMenuOpen ? 'text-black font-bold' : 'text-base-content/80' }}"
                        data-tip="Manajemen Menu">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="7" height="7" x="3" y="3" rx="1"/>
                            <rect width="7" height="7" x="14" y="3" rx="1"/>
                            <rect width="7" height="7" x="14" y="14" rx="1"/>
                            <rect width="7" height="7" x="3" y="14" rx="1"/>
                        </svg>
                        <span class="is-drawer-close:hidden text-[14px] flex-1">Manajemen Menu</span>
                    </summary>

                    <ul class="is-drawer-close:hidden mt-1 ml-8 border-l-2 border-base-200 gap-1 flex flex-col">
                        @foreach([
                            ['admin.menus.index',           'Daftar Menu',  'admin.menus.*'],
                            ['admin.menu_options.index',    'Menu Option',  'admin.menu_options.*'],
                            ['admin.menu_option_items.index', 'Option Item', 'admin.menu_option_items.*'],
                        ] as [$routeName, $label, $routePattern])
                            <li>
                                <a href="{{ route($routeName) }}"
                                    class="py-2 px-4 rounded-lg text-sm transition-colors block
                                           {{ request()->routeIs($routePattern)
                                               ? 'text-black font-bold bg-[#D4E971]/20'
                                               : 'hover:bg-base-200' }}">
                                    {{ $label }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </details>
            </li>

            {{-- Section: Operasional --}}
            <div class="is-drawer-close:hidden px-5 mt-4 mb-2
                        text-[10px] uppercase font-black text-gray-400 tracking-[0.2em] shrink-0">
                Operasional
            </div>

            {{-- Kategori --}}
            <li>
                <a href="{{ route('admin.categories.index') }}"
                    class="flex items-center gap-4 p-3 rounded-2xl transition-all duration-300
                           is-drawer-close:tooltip is-drawer-close:tooltip-right
                           {{ request()->routeIs('admin.categories.*')
                               ? 'bg-[#D4E971] text-black shadow-lg shadow-[#D4E971]/30 active-indicator'
                               : 'hover:bg-base-200 text-base-content/80' }}"
                    data-tip="Kategori">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/>
                        <line x1="4" x2="4" y1="22" y2="15"/>
                    </svg>
                    <span class="is-drawer-close:hidden font-bold text-[14px]">Kategori</span>
                </a>
            </li>

            {{-- Data Meja --}}
            <li>
                <a href="{{ route('admin.tables.index') }}"
                    class="flex items-center gap-4 p-3 rounded-2xl transition-all duration-300
                           is-drawer-close:tooltip is-drawer-close:tooltip-right
                           {{ request()->routeIs('admin.tables.*')
                               ? 'bg-[#D4E971] text-black shadow-lg shadow-[#D4E971]/30 active-indicator'
                               : 'hover:bg-base-200 text-base-content/80' }}"
                    data-tip="Meja">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 3h18v18H3z"/>
                        <path d="M3 9h18"/>
                        <path d="M9 3v18"/>
                    </svg>
                    <span class="is-drawer-close:hidden font-bold text-[14px]">Data Meja</span>
                </a>
            </li>

            {{-- Pesanan --}}
            <li>
                <a href="{{ route('admin.reports.index') }}"
                    class="flex items-center gap-4 p-3 rounded-2xl transition-all duration-300
                           is-drawer-close:tooltip is-drawer-close:tooltip-right
                           {{ request()->routeIs('admin.reports.*')
                               ? 'bg-[#D4E971] text-black shadow-lg shadow-[#D4E971]/30 active-indicator'
                               : 'hover:bg-base-200 text-base-content/80' }}"
                    data-tip="Laporan">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                        <path d="M3 6h18"/>
                        <path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                    <span class="is-drawer-close:hidden font-bold text-[14px]">Laporan</span>
                </a>
            </li>
        </ul>

        {{-- ── Logout (Fixed at Bottom) ── --}}
        <div class="w-full p-6 border-t border-base-200/50 shrink-0">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="flex items-center justify-start gap-4 w-full p-3.5 rounded-2xl
                           text-gray-500 hover:text-error hover:bg-error/10
                           transition-all duration-300
                           is-drawer-close:tooltip is-drawer-close:tooltip-right"
                    data-tip="Logout">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" x2="9" y1="12" y2="12"/>
                    </svg>
                    <span class="is-drawer-close:hidden font-bold text-[14px]">Log Out</span>
                </button>
            </form>
        </div>

    </div>
</div>