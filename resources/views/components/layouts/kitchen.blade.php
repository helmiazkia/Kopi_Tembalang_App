<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Kitchen System' }} - Lodo Kopi</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,800;1,400;1,800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        /* Menghilangkan scrollbar agar tampilan seperti aplikasi native */
        ::-webkit-scrollbar {
            display: none;
        }

        body {
            -ms-overflow-style: none;
            scrollbar-width: none;
            overscroll-behavior-y: contain;
            /* Mencegah pull-to-refresh di mobile */
        }

        /* Animasi masuk untuk kartu pesanan baru */
        .grid>div {
            animation: slideIn 0.5s ease-out forwards;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="antialiased font-sans h-full bg-slate-900">

    @if(session('success'))
    <div id="notif-toast" class="fixed top-5 right-5 z-[9999] bg-[#D4E971] text-slate-900 px-6 py-4 rounded-2xl font-black shadow-2xl flex items-center gap-3 animate-bounce">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
        </svg>
        {{ session('success') }}
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('notif-toast').remove();
        }, 3000);
    </script>
    @endif

    <main>
        {{ $slot }}
    </main>

    <script>
        // Mencegah zoom saat double tap di tablet
        document.addEventListener('touchstart', function(event) {
            if (event.touches.length > 1) {
                event.preventDefault();
            }
        }, {
            passive: false
        });
    </script>
</body>

</html>