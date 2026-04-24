<div class="drawer-side is-drawer-close:overflow-visible">
    <label for="my-drawer-4" aria-label="close sidebar" class="drawer-overlay"></label>

    <div class="flex min-h-full flex-col bg-white border-r border-gray-100
                w-64 is-drawer-close:w-16 is-drawer-open:w-72
                transition-all duration-300 ease-in-out">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-4 py-5 border-b border-gray-100">
            <div class="w-9 h-9 rounded-xl bg-violet-600 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"
                        stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <span class="is-drawer-close:hidden font-semibold text-gray-900 text-[15px] tracking-tight">
                BengKod
            </span>
        </div>

        {{-- Section label --}}
        <div class="px-4 pt-5 pb-1 is-drawer-close:hidden">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest">Menu</p>
        </div>

        {{-- Navigation --}}
        <ul class="flex flex-col gap-1 px-2 pt-2 flex-1">

            {{-- Order --}}
            <li>
                <a href="{{ route('cashier.orders.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150
                          is-drawer-close:tooltip is-drawer-close:tooltip-right
                          is-drawer-close:justify-center
                          {{ request()->routeIs('cashier.orders.*')
                              ? 'bg-violet-50 text-violet-700'
                              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    data-tip="Order">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        class="flex-shrink-0 {{ request()->routeIs('cashier.orders.*') ? 'text-violet-600' : '' }}">
                        <path fill="currentColor" d="M6 19h3v-5q0-.425.288-.712T10 13h4q.425 0 .713.288T15 14v5h3v-9l-6-4.5L6 10zm-2 0v-9q0-.475.213-.9t.587-.7l6-4.5q.525-.4 1.2-.4t1.2.4l6 4.5q.375.275.588.7T20 10v9q0 .825-.588 1.413T18 21h-4q-.425 0-.712-.288T13 20v-5h-2v5q0 .425-.288.713T10 21H6q-.825 0-1.412-.587T4 19m8-6.75" />
                    </svg>
                    <span class="is-drawer-close:hidden text-sm font-medium">Order</span>
                </a>
            </li>

            {{-- Laporan --}}
            <li>
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150
                          is-drawer-close:tooltip is-drawer-close:tooltip-right
                          is-drawer-close:justify-center
                          text-gray-600 hover:bg-gray-50 hover:text-gray-900"
                    data-tip="Laporan">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        class="flex-shrink-0">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="1.75"
                            d="M15 5v2m0 4v2m0 4v2M5 5h14a2 2 0 0 1 2 2v3a2 2 0 0 0 0 4v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-3a2 2 0 0 0 0-4V7a2 2 0 0 1 2-2" />
                    </svg>
                    <span class="is-drawer-close:hidden text-sm font-medium">Laporan</span>
                </a>
            </li>

        </ul>

        {{-- User info + Logout --}}
        <div class="border-t border-gray-100 p-3 space-y-2">

            {{-- User card --}}
            <div class="flex items-center gap-3 px-2 py-2 is-drawer-close:justify-center">
                <div class="w-8 h-8 rounded-full bg-violet-100 flex items-center justify-center
                            text-xs font-semibold text-violet-700 flex-shrink-0">
                    {{ strtoupper(substr(Auth::user()->name ?? 'K', 0, 2)) }}
                </div>
                <div class="is-drawer-close:hidden overflow-hidden">
                    <p class="text-sm font-medium text-gray-800 truncate leading-tight">
                        {{ Auth::user()->name ?? 'Kasir' }}
                    </p>
                    <p class="text-xs text-gray-400 truncate leading-tight">
                        {{ Auth::user()->email ?? '' }}
                    </p>
                </div>
            </div>

            {{-- Logout --}}
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl transition-all duration-150
                               is-drawer-close:tooltip is-drawer-close:tooltip-right
                               is-drawer-close:justify-center
                               text-red-500 hover:bg-red-50 hover:text-red-600"
                    data-tip="Logout">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        class="flex-shrink-0">
                        <path fill="currentColor"
                            d="M10 17v-2h4v-2h-4v-2l-5 3 5 3m9-12H5q-.825 0-1.413.588T3 7v10q0 .825.587 1.413T5 19h14q.825 0 1.413-.587T21 17v-3h-2v3H5V7h14v3h2V7q0-.825-.587-1.413T19 5z" />
                    </svg>
                    <span class="is-drawer-close:hidden text-sm font-medium">Logout</span>
                </button>
            </form>
        </div>

    </div>
</div>