<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Menu Kopi Tembalang - Meja {{ $table->table_number }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #D4E971;
            /* Hijau Lime Logo */
            --dark: #1a1a1a;
            --bg: #ffffff;
            --gray: #f8f9fa;
            --border: #eeeeee;
        }

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            margin: 0;
            padding: 0;
            color: var(--dark);
            padding-bottom: 110px;
            /* Space untuk floating button */
        }

        /* HEADER */
        .header {
            padding: 20px;
            background: white;
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--gray);
        }

        .brand-logo {
            font-weight: 800;
            font-size: 18px;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-tag {
            background: var(--dark);
            color: var(--primary);
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* CATEGORIES */
        .categories {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            padding: 15px 20px;
            scrollbar-width: none;
        }

        .categories::-webkit-scrollbar {
            display: none;
        }

        .cat {
            padding: 10px 22px;
            background: var(--gray);
            border-radius: 16px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            color: #888;
            white-space: nowrap;
            transition: all 0.2s ease;
            border: 1.5px solid transparent;
        }

        .cat.active {
            background: var(--primary);
            color: var(--dark);
            border-color: var(--primary);
            box-shadow: 0 4px 15px rgba(212, 233, 113, 0.35);
        }

        /* GRID MENU */
        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            padding: 10px 20px;
        }

        .card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            border: 1px solid var(--border);
            transition: all 0.2s ease;
            position: relative;
        }

        .card:active {
            transform: scale(0.96);
            border-color: var(--primary);
        }

        .img-container {
            width: 100%;
            height: 150px;
            position: relative;
            background: var(--gray);
        }

        .img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .content {
            padding: 14px;
            flex-grow: 1;
        }

        .name {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 6px;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .price {
            font-size: 15px;
            color: var(--dark);
            font-weight: 800;
        }

        .add-btn {
            position: absolute;
            bottom: 12px;
            right: 12px;
            background: var(--dark);
            color: var(--primary);
            width: 32px;
            height: 32px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        /* FLOATING CART BUTTON */
        .cart-footer {
            position: fixed;
            bottom: 25px;
            left: 20px;
            right: 20px;
            z-index: 200;
        }

        .cart-btn {
            background: var(--dark);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            border-radius: 22px;
            text-decoration: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
            font-weight: 700;
            transition: transform 0.2s;
        }

        .cart-btn:active {
            transform: scale(0.98);
        }

        .cart-count {
            background: var(--primary);
            color: var(--dark);
            padding: 4px 12px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .empty-state {
            grid-column: span 2;
            text-align: center;
            padding: 80px 20px;
            color: #ccc;
        }

        .empty-state span {
            font-size: 50px;
            display: block;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="brand-logo">
            <img src="{{ asset('assets/images/logo_bengkod.svg') }}" style="height: 35px;" alt="Logo">
            <span>Kopi Tembalang</span>
        </div>
        <div class="table-tag">MEJA {{ $table->table_number }}</div>
    </div>

    {{-- FILTER KATEGORI --}}
    <div class="categories">
        <a href="{{ route('customer.menu', $table->id) }}"
            class="cat {{ !request('category') ? 'active' : '' }}">
            Semua
        </a>

        @foreach($categories as $cat)
        <a href="{{ route('customer.menu', ['table' => $table->id, 'category' => $cat->id]) }}"
            class="cat {{ request('category') == $cat->id ? 'active' : '' }}">
            {{ $cat->name }}
        </a>
        @endforeach
    </div>

    {{-- LIST MENU --}}
    <div class="grid">
        @forelse($menus as $menu)
        <a href="{{ route('customer.menu.show', ['table' => $table->id, 'menu' => $menu->id]) }}" class="card">
            <div class="img-container">
                @if($menu->image && file_exists(public_path('images/menu/'.$menu->image)))
                <img src="{{ asset('images/menu/'.$menu->image) }}" class="img" alt="{{ $menu->name }}">
                @else
                <div style="display:flex; align-items:center; justify-content:center; height:100%; background:#f1f1f1; color:#bbb; font-size: 30px;">☕</div>
                @endif
                <div class="add-btn">+</div>
            </div>

            <div class="content">
                <div class="name">{{ $menu->name }}</div>
                <div class="price">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
            </div>
        </a>
        @empty
        <div class="empty-state">
            <span>🍃</span>
            <p>Menu belum tersedia untuk kategori ini.</p>
        </div>
        @endforelse
    </div>

    {{-- FOOTER CART --}}
    <div class="cart-footer">
        <a href="{{ route('customer.cart.index', ['table' => $table->id]) }}" class="cart-btn">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 20px;">🛒</span>
                <span>Lihat Pesanan</span>
            </div>
            <div class="cart-count" id="js-cart-count">Kosong</div>
        </a>
    </div>

    <script>
        /**
         * Update jumlah item di tombol keranjang secara real-time
         */
        function updateCartUI() {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            const countBadge = document.getElementById('js-cart-count');

            if (cart.length > 0) {
                countBadge.innerText = `${cart.length} Item`;
                countBadge.style.background = 'var(--primary)';
            } else {
                countBadge.innerText = 'Kosong';
                countBadge.style.background = '#eeeeee';
                countBadge.style.color = '#888';
            }
        }

        // Jalankan saat halaman dimuat
        document.addEventListener('DOMContentLoaded', updateCartUI);
    </script>

</body>

</html>