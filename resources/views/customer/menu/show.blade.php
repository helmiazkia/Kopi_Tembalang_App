<x-layouts.customer :title="$menu->name . ' - Detail Produk'">
    <style>
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
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            font-weight: bold;
            z-index: 10;
        }

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
            background: #f8f9fa;
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            color: #888;
        }

        .price-tag {
            font-size: 22px;
            font-weight: 800;
            color: #1a1a1a;
            margin-top: 5px;
        }

        .description {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
            margin: 20px 0;
        }

        .option-group {
            margin-bottom: 25px;
        }

        .option-title {
            font-size: 16px;
            font-weight: 800;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .option-title span {
            font-size: 10px;
            background: #D4E971;
            padding: 2px 10px;
            border-radius: 6px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .option-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 2px solid #f1f1f1;
            border-radius: 18px;
            margin-bottom: 10px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .option-item input {
            margin-right: 15px;
            width: 18px;
            height: 18px;
            accent-color: #1a1a1a;
        }

        .option-item:has(input:checked) {
            border-color: #D4E971;
            background: rgba(212, 233, 113, 0.05);
        }

        .item-info {
            flex-grow: 1;
            font-weight: 700;
            font-size: 14px;
        }

        .item-price {
            font-size: 12px;
            font-weight: 800;
            color: #888;
        }

        textarea {
            width: 100%;
            border: 2px solid #f1f1f1;
            border-radius: 18px;
            padding: 15px;
            font-family: inherit;
            resize: none;
            background: #f8f9fa;
            margin-bottom: 110px;
            outline: none;
        }

        .footer-action {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            background: white;
            border-top: 1px solid #eee;
            z-index: 100;
            max-width: 28rem;
            /* Setara max-w-md */
            margin: 0 auto;
        }

        .btn-add {
            width: 100%;
            background: #1a1a1a;
            color: #D4E971;
            border: none;
            padding: 20px;
            border-radius: 20px;
            font-family: inherit;
            font-weight: 800;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .btn-add:active {
            transform: scale(0.97);
        }
    </style>

    <div class="product-image-container">
        <a href="javascript:history.back()" class="back-btn">←</a>
        @if($menu->image && file_exists(public_path('images/menu/'.$menu->image)))
        <img src="{{ asset('images/menu/'.$menu->image) }}" alt="{{ $menu->name }}">
        @else
        <div class="flex items-center justify-center h-full bg-slate-800 text-white text-4xl">☕</div>
        @endif
    </div>

    <div class="detail-panel">
        <span class="category-label">{{ $menu->category->name }}</span>
        <h2 class="font-black text-2xl tracking-tighter">{{ $menu->name }}</h2>
        <div class="price-tag">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
        <p class="description">{{ $menu->description }}</p>

        <form id="orderForm">
            <input type="hidden" id="menu_id" value="{{ $menu->id }}">
            <input type="hidden" id="menu_name" value="{{ $menu->name }}">
            <input type="hidden" id="menu_price" value="{{ $menu->price }}">
            {{-- Menyimpan nama file gambar saja untuk diolah di JS keranjang --}}
            <input type="hidden" id="menu_image" value="{{ $menu->image }}">
            <input type="hidden" id="table_id" value="{{ $table->id }}">

            @foreach($menu->options as $option)
            <div class="option-group" data-option-name="{{ $option->name }}">
                <div class="option-title">
                    {{ $option->name }}
                    <span>Wajib</span>
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

            <div class="option-title">Catatan Khusus</div>
            <textarea id="notes" rows="3" placeholder="Contoh: Es sedikit saja, tanpa gula..."></textarea>

            <div class="footer-action">
                <button type="submit" id="submitBtn" class="btn-add">Tambah ke Keranjang</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = document.getElementById('submitBtn');
            const tableId = document.getElementById('table_id').value;

            btn.disabled = true;
            btn.innerText = "Menambahkan...";

            let selectedOptions = {};
            let optionsValid = true;

            // Validasi Opsi
            const optionGroups = document.querySelectorAll('.option-group');
            optionGroups.forEach(group => {
                const checkedRadio = group.querySelector('input[type="radio"]:checked');
                if (!checkedRadio) {
                    optionsValid = false;
                } else {
                    const optionId = checkedRadio.name.replace('option_', '');
                    selectedOptions[optionId] = {
                        name: checkedRadio.dataset.itemName,
                        price: parseInt(checkedRadio.dataset.price)
                    };
                }
            });

            if (!optionsValid) {
                alert('Mohon pilih opsi yang wajib diisi terlebih dahulu 🙏');
                btn.disabled = false;
                btn.innerText = "Tambah ke Keranjang";
                return;
            }

            // Buat Object Item
            let item = {
                id: document.getElementById('menu_id').value,
                name: document.getElementById('menu_name').value,
                price: parseInt(document.getElementById('menu_price').value),
                image: document.getElementById('menu_image').value,
                options: selectedOptions,
                notes: document.getElementById('notes').value,
                timestamp: new Date().getTime()
            };

            // Simpan ke LocalStorage
            try {
                let cart = JSON.parse(localStorage.getItem('cart') || '[]');
                cart.push(item);
                localStorage.setItem('cart', JSON.stringify(cart));

                // Berhasil
                setTimeout(() => {
                    window.location.href = "/menu/" + tableId;
                }, 300);
            } catch (error) {
                console.error("Gagal menyimpan ke cart:", error);
                alert("Terjadi kesalahan sistem.");
                btn.disabled = false;
                btn.innerText = "Tambah ke Keranjang";
            }
        });
    </script>
    @endpush
</x-layouts.customer>