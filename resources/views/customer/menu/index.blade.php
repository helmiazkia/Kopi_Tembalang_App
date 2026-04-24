<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Menu Kopi Tembalang - Meja {{ $table->table_number }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #D4E971; /* Hijau Lime Logo */
            --dark: #1a1a1a;
            --bg: #ffffff;
            --gray: #f8f9fa;
        }

        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            margin: 0;
            padding: 0;
            color: var(--dark);
            padding-bottom: 100px; /* Space untuk floating button */
        }

        /* HEADER */
        .header {
            padding: 25px 20px;
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
            gap: 8px;
        }

        .table-tag {
            background: var(--dark);
            color: var(--primary);
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 800;
        }

        /* CATEGORIES */
        .categories {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            padding: 15px 20px;
            scrollbar-width: none; /* Firefox */
        }
        .categories::-webkit-scrollbar { display: none; }

        .cat {
            padding: 10px 20px;
            background: var(--gray);
            border-radius: 14px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            color: #888;
            white-space: nowrap;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .cat.active {
            background: var(--primary);
            color: var(--dark);
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(212, 233, 113, 0.3);
        }

        /* GRID MENU */
        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            padding: 0 20px;
        }

        .card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            border: 1px solid #eee;
            transition: transform 0.2s;
        }

        .card:active { transform: scale(0.97); }

        .img-container {
            width: 100%;
            height: 140px;
            position: relative;
            background: var(--gray);
        }

        .img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .content {
            padding: 12px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .name {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 4px;
            line-height: 1.3;
        }

        .price {
            font-size: 15px;
            color: var(--dark);
            font-weight: 800;
        }

        .add-btn {
            position: absolute;
            bottom: -15px;
            right: 10px;
            background: var(--dark);
            color: var(--primary);
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* CART BUTTON */
        .cart-footer {
            position: fixed;
            bottom: 20px;
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
            padding: 18px 25px;
            border-radius: 20px;
            text-decoration: none;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            font-weight: 700;
        }

        .cart-count {
            background: var(--primary);
            color: var(--dark);
            padding: 2px 10px;
            border-radius: 8px;
            font-size: 12px;
        }

        .empty-state {
            grid-column: span 2;
            text-align: center;
            padding: 50px 20px;
            color: #ccc;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="brand-logo">
        <img src="{{ asset('assets/images/logo_bengkod.svg') }}" style="height: 30px;" alt="">
        <span>Kopi Tembalang</span>
    </div>
    <div class="table-tag">MEJA {{ $table->table_number }}</div>
</div>

{{-- FILTER KATEGORI --}}
<div class="categories">
    <a href="/menu/{{ $table->id }}"
        class="cat {{ request('category') ? '' : 'active' }}">
        Semua
    </a>

    @foreach($categories as $cat)
        <a href="/menu/{{ $table->id }}?category={{ $cat->id }}"
            class="cat {{ request('category') == $cat->id ? 'active' : '' }}">
            {{ $cat->name }}
        </a>
    @endforeach
</div>

{{-- LIST MENU --}}
<div class="grid">

@forelse($menus as $menu)
    <a href="/menu/{{ $table->id }}/{{ $menu->id }}" class="card">
        <div class="img-container">
            @if($menu->image)
                <img src="{{ asset('images/menu/'.$menu->image) }}" class="img">
            @else
                <div style="display:flex; align-items:center; justify-content:center; height:100%; color:#ddd;">☕</div>
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
        <div style="font-size: 40px; margin-bottom: 10px;">🍃</div>
        <p>Menu belum tersedia untuk kategori ini.</p>
    </div>
@endforelse

</div>

{{-- FOOTER CART --}}
<div class="cart-footer">
    <a href="/cart?table={{ $table->id }}" class="cart-btn">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span>🛒</span>
            <span>Lihat Pesanan</span>
        </div>
        <div class="cart-count">Baru</div>
    </a>
</div>

</body>
</html>