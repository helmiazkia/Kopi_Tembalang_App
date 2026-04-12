<div class="bg-white p-3 rounded-xl shadow mb-6">

    <div class="tabs tabs-bordered gap-3">

        <a href="{{ route('admin.menus.index') }}"
           class="tab flex items-center gap-2
           {{ request()->routeIs('admin.menus.*') ? 'tab-active font-semibold text-primary' : '' }}">

            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                <path fill="currentColor"
                d="M4 4h6v6H4zm10 0h6v6h-6zM4 14h6v6H4zm10 3a3 3 0 1 0 6 0a3 3 0 1 0-6 0"/>
            </svg>

            Menu
        </a>


        <a href="{{ route('admin.menu_options.index') }}"
           class="tab flex items-center gap-2
           {{ request()->routeIs('admin.menu_options.*') ? 'tab-active font-semibold text-primary' : '' }}">

            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                <path fill="currentColor"
                d="M10 4h4v2h-4zm0 7h4v2h-4zm0 7h4v2h-4M4 4h2v2H4zm0 7h2v2H4zm0 7h2v2H4"/>
            </svg>

            Menu Option
        </a>


        <a href="{{ route('admin.menu_option_items.index') }}"
           class="tab flex items-center gap-2
           {{ request()->routeIs('admin.menu_option_items.*') ? 'tab-active font-semibold text-primary' : '' }}">

            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                <path fill="currentColor"
                d="M4 6h16v2H4zm0 5h10v2H4zm0 5h16v2H4"/>
            </svg>

            Menu Option Item
        </a>

    </div>

</div>