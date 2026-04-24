<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Dashboard' }} | Kopi Tembalang</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Menghilangkan scrollbar pada level body karena drawer sudah mengaturnya */
        body {
            overflow-x: hidden;
        }
    </style>
</head>

<body class="bg-slate-50 antialiased">
    <div class="drawer lg:drawer-open h-screen">
        <input id="my-drawer-4" type="checkbox" class="drawer-toggle" />

        <div class="drawer-content flex flex-col h-screen overflow-y-auto">
            <nav class="navbar sticky top-0 z-30 w-full bg-white/80 backdrop-blur-md border-b border-slate-200 px-4">
                <div class="flex-none lg:hidden">
                    <label for="my-drawer-4" class="btn btn-square btn-ghost">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-6 h-6 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </label>
                </div>

                <div class="flex-1 px-2 mx-2 font-bold text-slate-700">
                    <span class="hidden lg:inline text-xs opacity-50 uppercase tracking-widest mr-2">Halaman:</span>
                    {{ $title ?? 'Dashboard' }}
                </div>

                <div class="flex-none gap-2">
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                            <div class="w-10 rounded-full border-2 border-[#D4E971]">
                                <img alt="Admin" src="https://ui-avatars.com/api/?name=Admin+Kopi&background=D4E971&color=000" />
                            </div>
                        </div>
                        <ul tabindex="0" class="mt-3 z-[1] p-2 shadow-xl menu menu-sm dropdown-content bg-base-100 rounded-box w-52 border border-slate-100">
                            <li><a>Profil</a></li>
                            <li><a>Pengaturan</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <main class="p-6 lg:p-8 flex-grow">
                <div class="max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </main>

            <footer class="footer footer-center p-6 bg-white border-t border-slate-200 text-slate-400">
                <aside>
                    <p class="text-xs font-medium">© {{ date('Y') }} <span class="text-slate-900 font-bold">Kopi Tembalang</span>. Built with ❤️ for better coffee experience.</p>
                </aside>
            </footer>
        </div>

        @include('components.admin.sidebar')
    </div>

    @stack('scripts')
</body>

</html>