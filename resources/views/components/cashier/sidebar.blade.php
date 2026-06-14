<style>
    .menu details > summary::marker,
    .menu details > summary::-webkit-details-marker {
        display: none !important;
        content: "";
    }

    .scrollbar-hide::-webkit-scrollbar {
        width: 4px;
    }
    .scrollbar-hide::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 10px;
    }

    .active-indicator {
        position: relative;
    }
    .active-indicator::after {
        content: "";
        position: absolute;
        right: 12px;
        width: 6px;
        height: 6px;
        background: #D4E971;
        border-radius: 50%;
    }
</style>

<div class="drawer-side is-drawer-close:overflow-visible shadow-2xl z-[50]">
    <label for="my-drawer-4" aria-label="close sidebar" class="drawer-overlay"></label>

    <div class="sticky top-0 h-screen flex flex-col items-start bg-base-100 text-base-content w-64 is-drawer-close:w-20 is-drawer-open:w-80 transition-all duration-500 ease-in-out border-r border-base-200">

        {{-- Logo --}}
        <div class="w-full flex flex-col items-center justify-center py-10 px-6 border-b border-base-200/50 shrink-0 bg-white">
            <img src="{{ asset('images/assets/images/Logo_Kopi_Tembalang.jpeg') }}" alt="Kopi Tembalang" class="h-24 w-auto object-contain transition-transform duration-500 hover:scale-105">
            <div class="mt-2 is-drawer-close:hidden">
                <span class="text-[10px] font-black tracking-[0.3em] text-gray-400 uppercase">Kasir</span>
            </div>
        </div>

        {{-- Menu Items --}}
        <ul class="menu w-full grow px-4 gap-2 overflow-y-auto overflow-x-hidden scrollbar-hide py-6">

            {{-- Dashboard --}}
            <li>
                <a href="{{ route('cashier.dashboard') }}"
                   class="flex items-center gap-4 p-3.5 rounded-2xl transition-all duration-300 {{ request()->routeIs('cashier.dashboard') ? 'bg-[#D4E971] text-black shadow-lg shadow-[#D4E971]/30 active-indicator' : 'hover:bg-base-200 text-base-content/80' }} is-drawer-close:tooltip is-drawer-close:tooltip-right"
                   data-tip="Dashboard">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    <span class="is-drawer-close:hidden font-bold text-[14px]">Dashboard</span>
                </a>
            </li>

            {{-- Section Label --}}
            <div class="is-drawer-close:hidden px-5 mt-6 mb-2 text-[10px] uppercase font-black text-gray-400 tracking-[0.2em]">Operasional</div>

            {{-- Order --}}
            <li>
                <a href="{{ route('cashier.orders.index') }}"
                   class="flex items-center gap-4 p-3.5 rounded-2xl transition-all duration-300 {{ request()->routeIs('cashier.orders.*') ? 'bg-[#D4E971] text-black shadow-lg shadow-[#D4E971]/30 active-indicator' : 'hover:bg-base-200 text-base-content/80' }} is-drawer-close:tooltip is-drawer-close:tooltip-right"
                   data-tip="Order">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                        <path d="M3 6h18"/>
                        <path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                    <span class="is-drawer-close:hidden font-bold text-[14px]">Order</span>
                </a>
            </li>

            {{-- Order List --}}
            <li>
                <a href="{{ route('cashier.orderList.index') }}"
                   class="flex items-center gap-4 p-3.5 rounded-2xl transition-all duration-300 {{ request()->routeIs('cashier.orderList.*') ? 'bg-[#D4E971] text-black shadow-lg shadow-[#D4E971]/30 active-indicator' : 'hover:bg-base-200 text-base-content/80' }} is-drawer-close:tooltip is-drawer-close:tooltip-right"
                   data-tip="Order List">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 11l3 3L22 4"/>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                    <span class="is-drawer-close:hidden font-bold text-[14px]">Order List</span>
                </a>
            </li>

            {{-- Ketersediaan Menu --}}
            <li>
                <a href="{{ route('cashier.menus.index') }}"
                   class="flex items-center gap-4 p-3.5 rounded-2xl transition-all duration-300 {{ request()->routeIs('cashier.menus.*') ? 'bg-[#D4E971] text-black shadow-lg shadow-[#D4E971]/30 active-indicator' : 'hover:bg-base-200 text-base-content/80' }} is-drawer-close:tooltip is-drawer-close:tooltip-right"
                   data-tip="Ketersediaan Menu">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 2h1a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H3"/>
                        <path d="M9 2h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H9"/>
                        <path d="M9 8h7"/>
                        <path d="M9 12h7"/>
                        <path d="M9 16h4"/>
                    </svg>
                    <span class="is-drawer-close:hidden font-bold text-[14px]">Menu</span>
                </a>
            </li>

        </ul>

        {{-- Logout --}}
        <div class="w-full p-6 border-t border-base-200/50 shrink-0">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center justify-start gap-4 w-full p-3.5 rounded-2xl hover:bg-error/10 text-gray-500 hover:text-error transition-all duration-300 is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="Logout">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
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