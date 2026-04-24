<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Kasir Dashboard' }}</title>

    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">

    <div class="drawer lg:drawer-open flex-1">
        <input id="my-drawer-4" type="checkbox" class="drawer-toggle" />

        <div class="drawer-content flex flex-col min-h-screen">

            {{-- Navbar --}}
            <nav class="sticky top-0 z-30 flex items-center gap-3 px-4 py-3
                        bg-white border-b border-gray-100">

                {{-- Hamburger (mobile only) --}}
                <label for="my-drawer-4" aria-label="open sidebar"
                    class="lg:hidden btn btn-ghost btn-sm btn-square rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <line x1="3" y1="6" x2="21" y2="6" />
                        <line x1="3" y1="12" x2="21" y2="12" />
                        <line x1="3" y1="18" x2="21" y2="18" />
                    </svg>
                </label>

                {{-- Page title --}}
                <h1 class="text-sm font-semibold text-gray-800 tracking-tight">
                    {{ $title ?? 'Dashboard' }}
                </h1>

                <div class="flex-1"></div>

                {{-- User badge --}}
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-violet-100 flex items-center justify-center
                                text-xs font-semibold text-violet-700">
                        {{ strtoupper(substr(Auth::user()->name ?? 'K', 0, 2)) }}
                    </div>
                    <span class="hidden sm:block text-sm text-gray-700 font-medium">
                        {{ Auth::user()->name ?? 'Kasir' }}
                    </span>
                </div>
            </nav>

            {{-- Page content --}}
            <main class="flex-1 p-5 md:p-6">
                {{ $slot }}
            </main>

            {{-- Footer --}}
            <footer class="px-6 py-4 border-t border-gray-100 bg-white">
                <p class="text-xs text-gray-400 text-center">
                    © {{ date('Y') }} BengKod. All rights reserved.
                </p>
            </footer>

        </div>

        @include('components.cashier.sidebar')
    </div>

    @stack('scripts')
</body>

</html>