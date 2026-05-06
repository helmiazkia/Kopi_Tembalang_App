<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Kopi Tembalang' }}</title>

    <!-- Fonts & Icons -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,600;0,800;1,800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #D4E971;
            /* Hijau Lime Kopi Tembalang */
            --dark: #1a1a1a;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        /* Menghilangkan scrollbar tapi tetap bisa scroll */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-slate-50 antialiased text-slate-900">

    <!-- Konten Utama -->
    <div class="max-w-md mx-auto min-h-screen bg-white shadow-xl relative">
        {{ $slot }}
    </div>

    <!-- GLOBAL SCRIPTS -->
    <script>
        /**
         * Global Cart Logic
         * Membersihkan keranjang jika ada parameter 'clear_cart' di URL
         */
        document.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);

            if (params.has('clear_cart') || params.has('success')) {
                localStorage.removeItem('cart');
                console.log('🛒 Keranjang otomatis dikosongkan.');

                // Opsional: Update UI badge jika ada di halaman tersebut
                if (typeof updateCartUI === "function") {
                    updateCartUI();
                }
            }
        });
    </script>

    @stack('scripts')
</body>

</html>