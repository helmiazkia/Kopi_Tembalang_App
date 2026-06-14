<x-layouts.cashier title="Ketersediaan Menu">

    {{-- Toast Notifikasi --}}
    @if(session('success'))
        <div class="toast toast-top toast-end z-[100]">
            <div class="alert shadow-lg border-none bg-[#D4E971] text-black">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        </div>
        <script>
            setTimeout(() => {
                const toast = document.querySelector('.toast');
                if (!toast) return;
                toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        </script>
    @endif

    <div class="py-6 px-4">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Ketersediaan Menu</h1>
                <p class="text-slate-500 text-sm">Tandai menu yang tersedia atau habis hari ini.</p>
            </div>
            <div class="flex items-center gap-3 bg-white px-5 py-3 rounded-2xl border border-slate-200 shadow-sm">
                <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                <span class="text-xs font-black text-slate-500 uppercase tracking-widest">
                    {{ $menus->where('is_available', true)->count() }} / {{ $menus->count() }} Tersedia
                </span>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-400 uppercase text-[11px] tracking-[0.15em]">
                            <th class="py-5 pl-8">No</th>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Toggle</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-600">
                        @forelse($menus as $index => $menu)
                            <tr class="hover:bg-slate-50/80 transition-colors {{ !$menu->is_available ? 'opacity-60' : '' }}">

                                <td class="pl-8 font-medium opacity-50">{{ $index + 1 }}</td>

                                {{-- Produk --}}
                                <td>
                                    <div class="flex items-center gap-4">
                                        <div class="avatar">
                                            <div class="mask mask-squircle w-12 h-12 bg-slate-100">
                                                @if($menu->image)
                                                    <img src="{{ asset('images/menu/' . $menu->image) }}" alt="{{ $menu->name }}">
                                                @else
                                                    <div class="flex items-center justify-center h-full text-slate-300">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                                                            <circle cx="9" cy="9" r="2"/>
                                                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800">{{ $menu->name }}</div>
                                            <div class="text-[11px] opacity-50 truncate max-w-[150px]">{{ $menu->description }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kategori --}}
                                <td>
                                    <span class="badge badge-ghost border-slate-200 text-slate-500 font-medium">
                                        {{ $menu->category->name }}
                                    </span>
                                </td>

                                {{-- Harga --}}
                                <td class="font-black text-slate-800">
                                    <span class="text-[10px] font-normal opacity-40">Rp</span>
                                    {{ number_format($menu->price, 0, ',', '.') }}
                                </td>

                                {{-- Status --}}
                                <td class="text-center">
                                    @if($menu->is_available)
                                        <div class="badge bg-[#D4E971]/20 border-none text-green-700 font-bold text-[10px] px-3">TERSEDIA</div>
                                    @else
                                        <div class="badge bg-red-50 border-none text-red-500 font-bold text-[10px] px-3">HABIS</div>
                                    @endif
                                </td>

                                {{-- Toggle --}}
                                <td class="text-center">
                                    <form method="POST" action="{{ route('cashier.menus.toggle', $menu) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="btn btn-sm rounded-xl font-black text-[10px] tracking-widest border-none transition-all duration-300
                                                {{ $menu->is_available
                                                    ? 'bg-red-50 text-red-500 hover:bg-red-500 hover:text-white'
                                                    : 'bg-[#D4E971]/20 text-green-700 hover:bg-[#D4E971] hover:text-black' }}"
                                        >
                                            {{ $menu->is_available ? 'Tandai Habis' : 'Tandai Tersedia' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-32 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <span class="text-5xl opacity-10">☕</span>
                                        <p class="font-black text-slate-300 uppercase tracking-[0.3em] text-xs">Belum ada menu terdaftar</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-layouts.cashier>