<x-layouts.customer :title="'Menu Kopi Tembalang - Meja ' . $table->table_number">

    {{-- HEADER --}}
    <div class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-100 px-6 py-5 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/assets/images/Logo_Kopi_Tembalang.jpeg') }}" class="h-10 w-10 rounded-full object-cover shadow-sm" alt="Logo">
            <span class="font-black tracking-tighter text-lg uppercase italic text-slate-800">Kopi Tembalang</span>
        </div>
        <div class="bg-slate-900 text-[#D4E971] px-4 py-2 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg">
            Meja {{ $table->table_number }}
        </div>
    </div>

    {{-- FILTER KATEGORI (Sticky & Scrollable) --}}
    <div class="sticky top-[81px] z-40 bg-white/80 backdrop-blur-sm border-b border-slate-50 py-3">
        <div class="flex gap-2 overflow-x-auto px-6 no-scrollbar">
            @foreach($categories as $cat)
            <button onclick="scrollToCategory('cat-{{ $cat->id }}')"
                class="cat-pill whitespace-nowrap px-5 py-2.5 bg-slate-100 rounded-xl text-[11px] font-black uppercase tracking-widest text-slate-400 transition-all active:scale-95 touch-manipulation border border-transparent"
                id="pill-cat-{{ $cat->id }}">
                {{ $cat->name }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- MENU CONTENT (Single Page Scroll) --}}
    <div class="p-6 space-y-12 pb-32">
        @foreach($categories as $cat)
        <div id="cat-{{ $cat->id }}" class="category-section">
            {{-- Judul Pembatas Kategori --}}
            <div class="flex items-center gap-4 mb-6">
                <h2 class="font-black uppercase italic text-sm tracking-widest text-slate-800">{{ $cat->name }}</h2>
                <div class="h-[2px] flex-1 bg-gradient-to-r from-slate-100 to-transparent"></div>
            </div>

            {{-- Grid Menu dalam Kategori --}}
            <div class="grid grid-cols-2 gap-4">
                @php
                    $categoryMenus = $menus->where('category_id', $cat->id);
                @endphp

                @forelse($categoryMenus as $menu)
                    @php
                        // Logika: Sinkron dengan status is_available dari database
                        $isReady = $menu->is_available; 
                    @endphp

                    @if($isReady)
                        {{-- TAMPILAN MENU TERSEDIA (NORMAL) --}}
                        <a href="{{ route('customer.menu.show', ['table' => $table->id, 'menu' => $menu->id]) }}"
                            class="group relative bg-white rounded-[2rem] border border-slate-100 p-2 transition-all active:scale-95 shadow-sm">

                            <div class="relative h-40 w-full rounded-[1.6rem] overflow-hidden bg-slate-50">
                                @if($menu->image && file_exists(public_path('images/menu/'.$menu->image)))
                                    <img src="{{ asset('images/menu/'.$menu->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="{{ $menu->name }}">
                                @else
                                    <div class="flex items-center justify-center h-full text-3xl opacity-20">☕</div>
                                @endif

                                <div class="absolute bottom-3 right-3 bg-slate-900 text-[#D4E971] w-8 h-8 rounded-xl flex items-center justify-center font-black shadow-xl">
                                    +
                                </div>
                            </div>

                            <div class="p-3">
                                <h3 class="font-bold text-xs text-slate-800 line-clamp-2 leading-tight uppercase mb-1">{{ $menu->name }}</h3>
                                <p class="font-black text-sm text-slate-900 italic">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                            </div>
                        </a>
                    @else
                        {{-- TAMPILAN MENU HABIS (ABU-ABU & MATI) --}}
                        <div class="relative bg-slate-50 rounded-[2rem] border border-slate-100 p-2 opacity-60 grayscale cursor-not-allowed overflow-hidden">
                            
                            <div class="relative h-40 w-full rounded-[1.6rem] overflow-hidden bg-slate-200">
                                @if($menu->image && file_exists(public_path('images/menu/'.$menu->image)))
                                    <img src="{{ asset('images/menu/'.$menu->image) }}" class="w-full h-full object-cover" alt="{{ $menu->name }}">
                                @else
                                    <div class="flex items-center justify-center h-full text-3xl opacity-10">☕</div>
                                @endif
                                
                                {{-- Overlay Label Habis --}}
                                <div class="absolute inset-0 bg-black/10 flex items-center justify-center">
                                    <span class="bg-slate-900 text-white px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-tighter shadow-lg">
                                        HABIS
                                    </span>
                                </div>
                            </div>

                            <div class="p-3">
                                <h3 class="font-bold text-xs text-slate-400 line-clamp-2 leading-tight uppercase mb-1">{{ $menu->name }}</h3>
                                <p class="font-black text-sm text-slate-400 italic">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endif
                @empty
                    <p class="col-span-2 text-[10px] font-bold text-slate-300 uppercase italic">Segera Hadir...</p>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>

    {{-- FLOATING CART --}}
    <div class="fixed bottom-8 left-0 right-0 px-6 z-50 max-w-md mx-auto">
        <a href="{{ route('customer.cart.index', ['table' => $table->id]) }}"
            class="flex items-center justify-between bg-slate-900 text-white p-5 rounded-[2rem] shadow-2xl shadow-slate-400 active:scale-95 transition-all">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-[#D4E971] rounded-xl flex items-center justify-center text-xl">🛒</div>
                <div class="text-left">
                    <p class="text-[9px] font-black uppercase text-slate-400 tracking-widest leading-none">Keranjang</p>
                    <p class="text-xs font-bold">Lihat Pesanan</p>
                </div>
            </div>
            <div id="js-cart-count" class="bg-[#D4E971] text-slate-900 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-tighter">
                Kosong
            </div>
        </a>
    </div>

    @push('scripts')
    <script>
        // Fungsi Scroll ke Kategori
        function scrollToCategory(id) {
            const element = document.getElementById(id);
            const headerOffset = 160; 
            const elementPosition = element.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

            window.scrollTo({
                top: offsetPosition,
                behavior: "smooth"
            });
        }

        // Fungsi Update UI Cart (dari LocalStorage)
        function updateCartUI() {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            const countBadge = document.getElementById('js-cart-count');

            if (cart.length > 0) {
                countBadge.innerText = `${cart.length} Item`;
                countBadge.classList.replace('bg-slate-700', 'bg-[#D4E971]');
                countBadge.classList.replace('text-slate-400', 'text-slate-900');
            } else {
                countBadge.innerText = 'Kosong';
                countBadge.classList.replace('bg-[#D4E971]', 'bg-slate-700');
                countBadge.classList.add('text-slate-400');
            }
        }

        // Highlight Tombol Kategori saat Scroll
        window.addEventListener('scroll', () => {
            let current = "";
            const sections = document.querySelectorAll(".category-section");

            sections.forEach((section) => {
                const sectionTop = section.offsetTop;
                if (window.pageYOffset >= sectionTop - 180) {
                    current = section.getAttribute("id");
                }
            });

            document.querySelectorAll(".cat-pill").forEach((pill) => {
                pill.classList.remove("bg-[#D4E971]", "text-slate-900");
                pill.classList.add("bg-slate-100", "text-slate-400");
                if (pill.id === `pill-${current}`) {
                    pill.classList.replace("bg-slate-100", "bg-[#D4E971]");
                    pill.classList.replace("text-slate-400", "text-slate-900");
                }
            });
        });

        document.addEventListener('DOMContentLoaded', updateCartUI);
    </script>
    @endpush
</x-layouts.customer>