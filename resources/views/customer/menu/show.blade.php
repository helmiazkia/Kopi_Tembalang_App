<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>{{ $menu->name }} - Detail Produk</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #D4E971;
            --dark: #1a1a1a;
            --gray-bg: #f8f9fa;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            background-color: var(--dark); /* Background gelap agar gambar produk menonjol */
            color: var(--dark);
        }

        /* IMAGE HEADER */
        .product-image-container {
            width: 100%;
            height: 350px;
            position: relative;
            background: #eee;
        }

        .product-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--dark);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            font-weight: bold;
        }

        /* DETAIL PANEL */
        .detail-panel {
            background: white;
            border-radius: 30px 30px 0 0;
            margin-top: -30px;
            position: relative;
            padding: 30px 20px;
            min-height: 500px;
        }

        .category-label {
            display: inline-block;
            background: var(--gray-bg);
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            color: #888;
        }

        h2 {
            margin: 0;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .price-tag {
            font-size: 22px;
            font-weight: 800;
            color: var(--dark);
            margin-top: 5px;
        }

        .description {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
            margin: 20px 0;
        }

        hr { border: 0; border-top: 1px solid #eee; margin: 25px 0; }

        /* OPTIONS SECTION */
        .option-group { margin-bottom: 25px; }
        
        .option-title {
            font-size: 16px;
            font-weight: 800;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
        }
        
        .option-title span {
            font-size: 11px;
            background: var(--primary);
            padding: 2px 8px;
            border-radius: 5px;
        }

        .option-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 15px;
            margin-bottom: 10px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .option-item input { margin-right: 15px; accent-color: var(--dark); }

        .option-item:has(input:checked) {
            border-color: var(--primary);
            background: rgba(212, 233, 113, 0.05);
        }

        .item-info { flex-grow: 1; font-weight: 600; font-size: 14px; }
        .item-price { font-size: 13px; font-weight: 800; opacity: 0.6; }

        /* NOTES */
        textarea {
            width: 100%;
            border: 1px solid #eee;
            border-radius: 15px;
            padding: 15px;
            font-family: inherit;
            resize: none;
            background: var(--gray-bg);
            margin-bottom: 100px;
        }

        /* BOTTOM NAV */
        .footer-action {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            background: white;
            border-top: 1px solid #eee;
            display: flex;
            gap: 15px;
            z-index: 100;
        }

        .btn-add {
            flex-grow: 1;
            background: var(--dark);
            color: var(--primary);
            border: none;
            padding: 18px;
            border-radius: 18px;
            font-family: inherit;
            font-weight: 800;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn-add:active { transform: scale(0.96); }
    </style>
</head>
<body>

    <div class="product-image-container">
        <a href="javascript:history.back()" class="back-btn">←</a>
        <img src="{{ asset('images/menu/'.$menu->image) }}" alt="{{ $menu->name }}">
    </div>

    <div class="detail-panel">
        <span class="category-label">{{ $menu->category->name }}</span>
        <h2>{{ $menu->name }}</h2>
        <div class="price-tag">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>

        <p class="description">{{ $menu->description }}</p>

        <form id="orderForm" onsubmit="addToCart(event)">
            <input type="hidden" id="menu_id" value="{{ $menu->id }}">
            <input type="hidden" id="menu_name" value="{{ $menu->name }}">
            <input type="hidden" id="menu_price" value="{{ $menu->price }}">

            @foreach($menu->options as $option)
                <div class="option-group">
                    <div class="option-title">
                        {{ $option->name }}
                        <span>Pilih satu</span>
                    </div>

                    @foreach($option->items as $item)
                        <label class="option-item">
                            <input type="radio" 
                                name="option_{{ $option->id }}" 
                                value="{{ $item->id }}" 
                                data-price="{{ $item->price }}"
                                data-item-name="{{ $item->name }}"
                                required>
                            <div class="item-info">{{ $item->name }}</div>
                            <div class="item-price">+Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                        </label>
                    @endforeach
                </div>
            @endforeach

            <div class="option-title">Catatan Pesanan</div>
            <textarea id="notes" rows="3" placeholder="Contoh: Less sugar, es dipisah, dll..."></textarea>

            <div class="footer-action">
                <button type="submit" class="btn-add">Tambah ke Keranjang</button>
            </div>
        </form>
    </div>

    <script>
    function addToCart(e) {
        e.preventDefault();

        let item = {
            id: document.getElementById('menu_id').value,
            name: document.getElementById('menu_name').value,
            price: parseInt(document.getElementById('menu_price').value),
            options: [],
            notes: document.getElementById('notes').value,
            timestamp: new Date().getTime()
        };

        // Ambil pilihan opsi
        document.querySelectorAll('input[type=radio]:checked').forEach(el => {
            item.options.push({
                option_name: el.closest('.option-group').querySelector('.option-title').innerText.replace('Pilih satu', '').trim(),
                item_name: el.dataset.itemName,
                price: parseInt(el.dataset.price)
            });
        });

        // Simpan ke LocalStorage
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        cart.push(item);
        localStorage.setItem('cart', JSON.stringify(cart));

        // Feedback simpel lalu kembali
        alert('✨ ' + item.name + ' berhasil ditambahkan!');
        window.history.back();
    }
    </script>
</body>
</html>