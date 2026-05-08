@php use Illuminate\Support\Str; @endphp

<x-layouts.admin title="Manajemen Menu">

{{-- ===================== TOAST NOTIFICATION ===================== --}}
@if (session('success'))
    <div class="toast toast-top toast-end z-[100]">
        <div class="alert alert-success shadow-lg border-none bg-[#D4E971] text-black">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
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

{{-- ===================== MAIN CONTENT ===================== --}}
<div class="py-6 px-4">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Daftar Menu</h1>
            <p class="text-slate-500 text-sm">Kelola daftar menu, kategori, dan opsi produk Kopi Tembalang.</p>
        </div>
        <button
            class="btn border-none bg-[#D4E971] hover:bg-black hover:text-[#D4E971] text-black shadow-xl shadow-[#D4E971]/30 px-8 rounded-2xl font-black transition-all duration-500 group"
            onclick="add_modal.showModal()">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"
                class="mr-2 group-hover:rotate-90 transition-transform duration-500">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            Tambah Menu Baru
        </button>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-400 uppercase text-[11px] tracking-[0.15em]">
                        <th class="py-5 pl-8">No</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Opsi</th>
                        <th>Status</th>
                        <th>Harga</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-slate-600">
                    @foreach ($menus as $index => $menu)
                        <tr class="hover:bg-slate-50/80 transition-colors">

                            {{-- No --}}
                            <td class="pl-8 font-medium opacity-50">{{ $index + 1 }}</td>

                            {{-- Produk --}}
                            <td>
                                <div class="flex items-center gap-4">
                                    <div class="avatar">
                                        <div class="mask mask-squircle w-12 h-12 bg-slate-100">
                                            @if ($menu->image)
                                                <img src="{{ asset('images/menu/' . $menu->image) }}" alt="{{ $menu->name }}" />
                                            @else
                                                <div class="flex items-center justify-center h-full text-slate-300">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                                                        <circle cx="9" cy="9" r="2" />
                                                        <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
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

                            {{-- Opsi --}}
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($menu->options as $opt)
                                        <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-bold uppercase tracking-wider border border-slate-200">
                                            {{ $opt->name }}
                                        </span>
                                    @empty
                                        <span class="text-[10px] text-slate-300 italic">Tanpa opsi</span>
                                    @endforelse
                                </div>
                            </td>

                            {{-- Status --}}
                            <td>
                                @if ($menu->is_available)
                                    <div class="badge bg-[#D4E971]/20 border-none text-green-700 font-bold text-[10px] px-3">TERSEDIA</div>
                                @else
                                    <div class="badge bg-red-50 border-none text-red-500 font-bold text-[10px] px-3">HABIS</div>
                                @endif
                            </td>

                            {{-- Harga --}}
                            <td class="font-black text-slate-800">
                                <span class="text-[10px] font-normal opacity-40">Rp</span>
                                {{ number_format($menu->price, 0, ',', '.') }}
                            </td>

                            {{-- Aksi --}}
                            <td>
                                <div class="flex justify-center gap-2">
                                    <button
                                        class="btn btn-square btn-ghost btn-sm hover:bg-[#D4E971]/20 hover:text-black transition-colors"
                                        onclick="openEditModal(this)"
                                        data-id="{{ $menu->id }}"
                                        data-name="{{ $menu->name }}"
                                        data-price="{{ $menu->price }}"
                                        data-category="{{ $menu->category_id }}"
                                        data-description="{{ $menu->description }}"
                                        data-available="{{ $menu->is_available }}"
                                        data-options='@json($menu->options->pluck("id"))'
                                        title="Edit Menu">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                            <path d="m15 5 4 4" />
                                        </svg>
                                    </button>
                                    <button
                                        class="btn btn-square btn-ghost btn-sm hover:bg-red-50 hover:text-red-500 transition-colors"
                                        onclick="openDeleteModal(this)"
                                        data-id="{{ $menu->id }}"
                                        title="Hapus Menu">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18" />
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                            <line x1="10" x2="10" y1="11" y2="17" />
                                            <line x1="14" x2="14" y1="11" y2="17" />
                                        </svg>
                                    </button>
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ===================== SHARED MODAL STYLES ===================== --}}
<style>
    .modal-box {
        border-radius: 1.5rem;
        border: none;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    }
    .input, .select, .textarea, .file-input {
        border-radius: 0.75rem;
        border-color: #e2e8f0;
    }
    .input:focus, .select:focus, .textarea:focus {
        outline: 2px solid #D4E971;
        outline-offset: 1px;
        border-color: transparent;
    }
</style>

{{-- ===================== MODAL: TAMBAH MENU ===================== --}}
<dialog id="add_modal" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box max-w-2xl bg-white">
        <form method="POST" enctype="multipart/form-data" action="{{ route('admin.menus.store') }}">
            @csrf

            <div class="flex justify-between items-center mb-6">
                <h3 class="font-black text-2xl text-slate-800 tracking-tight">Tambah Menu Baru</h3>
                <button type="button" onclick="add_modal.close()" class="btn btn-sm btn-circle btn-ghost">✕</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label"><span class="label-text font-bold text-slate-600">Nama Menu</span></label>
                    <input type="text" name="name" placeholder="Contoh: Es Kopi Susu" class="input input-bordered w-full" required>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text font-bold text-slate-600">Kategori</span></label>
                    <select name="category_id" class="select select-bordered w-full">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text font-bold text-slate-600">Harga (Rp)</span></label>
                    <input type="number" name="price" placeholder="25000" class="input input-bordered w-full" required>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text font-bold text-slate-600">Status Stok</span></label>
                    <select name="is_available" class="select select-bordered w-full">
                        <option value="1">Tersedia</option>
                        <option value="0">Stok Habis</option>
                    </select>
                </div>
            </div>

            <div class="form-control mt-4">
                <label class="label"><span class="label-text font-bold text-slate-600">Deskripsi Singkat</span></label>
                <textarea name="description" class="textarea textarea-bordered h-24" placeholder="Jelaskan rasa atau komposisi..."></textarea>
            </div>

            <div class="form-control mt-4">
                <label class="label"><span class="label-text font-bold text-slate-600">Foto Produk</span></label>
                <input type="file" name="image" class="file-input file-input-bordered w-full">
            </div>

            <div class="mt-6">
                <label class="label"><span class="label-text font-bold text-slate-600">Opsi Menu Tambahan</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach ($options as $opt)
                        <label class="flex items-center gap-3 border border-slate-100 p-3 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:bg-[#D4E971]/10 has-[:checked]:border-[#D4E971]">
                            <input type="checkbox" name="menu_option_ids[]" value="{{ $opt->id }}"
                                class="checkbox checkbox-sm checkbox-primary rounded-md border-slate-300">
                            <span class="text-sm font-medium text-slate-700">{{ $opt->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="modal-action mt-8">
                <button type="button" class="btn btn-ghost px-8" onclick="add_modal.close()">Batal</button>
                <button type="submit" class="btn border-none bg-[#D4E971] hover:bg-[#c2d75f] text-black px-10 rounded-xl font-bold">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
</dialog>

{{-- ===================== MODAL: EDIT MENU ===================== --}}
<dialog id="edit_modal" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box max-w-2xl bg-white text-slate-800">
        <form method="POST" enctype="multipart/form-data" id="editForm">
            @csrf
            @method('PUT')

            <div class="flex justify-between items-center mb-6">
                <h3 class="font-black text-2xl tracking-tight">Edit Menu</h3>
                <button type="button" onclick="edit_modal.close()" class="btn btn-sm btn-circle btn-ghost">✕</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label"><span class="label-text font-bold text-slate-600">Nama Menu</span></label>
                    <input type="text" id="edit_name" name="name" class="input input-bordered w-full">
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text font-bold text-slate-600">Kategori</span></label>
                    <select id="edit_category" name="category_id" class="select select-bordered w-full">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text font-bold text-slate-600">Harga (Rp)</span></label>
                    <input type="number" id="edit_price" name="price" class="input input-bordered w-full">
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text font-bold text-slate-600">Status Stok</span></label>
                    <select id="edit_available" name="is_available" class="select select-bordered w-full">
                        <option value="1">Tersedia</option>
                        <option value="0">Stok Habis</option>
                    </select>
                </div>
            </div>

            <div class="form-control mt-4">
                <label class="label"><span class="label-text font-bold text-slate-600">Deskripsi</span></label>
                <textarea id="edit_description" name="description" class="textarea textarea-bordered h-24"></textarea>
            </div>

            <div class="form-control mt-4">
                <label class="label"><span class="label-text font-bold text-slate-600">Ganti Foto (Opsional)</span></label>
                <input type="file" name="image" class="file-input file-input-bordered w-full">
            </div>

            <div class="mt-6">
                <label class="label"><span class="label-text font-bold text-slate-600">Opsi Menu</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach ($options as $opt)
                        <label class="flex items-center gap-3 border border-slate-100 p-3 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:bg-[#D4E971]/10 has-[:checked]:border-[#D4E971]">
                            <input type="checkbox" name="menu_option_ids[]" value="{{ $opt->id }}"
                                class="edit-option checkbox checkbox-sm checkbox-primary rounded-md">
                            <span class="text-sm font-medium text-slate-700">{{ $opt->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="modal-action mt-8">
                <button type="button" class="btn btn-ghost px-8" onclick="edit_modal.close()">Batal</button>
                <button type="submit" class="btn border-none bg-[#D4E971] hover:bg-[#c2d75f] text-black px-10 rounded-xl font-bold shadow-lg shadow-[#D4E971]/20">
                    Perbarui Menu
                </button>
            </div>
        </form>
    </div>
</dialog>

{{-- ===================== MODAL: HAPUS MENU ===================== --}}
<dialog id="delete_modal" class="modal">
    <div class="modal-box bg-white max-w-sm text-center">
        <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 6h18" />
                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                <line x1="10" x2="10" y1="11" y2="17" />
                <line x1="14" x2="14" y1="11" y2="17" />
            </svg>
        </div>
        <h3 class="font-black text-xl text-slate-800">Hapus Menu?</h3>
        <p class="text-slate-500 mt-2">Tindakan ini tidak bisa dibatalkan. Menu akan hilang dari daftar.</p>
        <form method="POST" id="deleteForm">
            @csrf
            @method('DELETE')
            <div class="flex flex-col gap-2 mt-6">
                <button type="submit" class="btn btn-error text-white font-bold rounded-xl border-none">
                    Ya, Hapus Sekarang
                </button>
                <button type="button" class="btn btn-ghost" onclick="delete_modal.close()">Batalkan</button>
            </div>
        </form>
    </div>
</dialog>

{{-- ===================== SCRIPTS ===================== --}}
<script>
    function openEditModal(btn) {
        const { id, name, price, category, description, available, options } = btn.dataset;

        document.getElementById('edit_name').value        = name;
        document.getElementById('edit_price').value       = price;
        document.getElementById('edit_category').value    = category;
        document.getElementById('edit_description').value = description;
        document.getElementById('edit_available').value   = available;

        const selectedOptions = JSON.parse(options);
        document.querySelectorAll('.edit-option').forEach(cb => {
            cb.checked = selectedOptions.includes(parseInt(cb.value));
        });

        document.getElementById('editForm').action = `/admin/menus/${id}`;
        edit_modal.showModal();
    }

    function openDeleteModal(btn) {
        document.getElementById('deleteForm').action = `/admin/menus/${btn.dataset.id}`;
        delete_modal.showModal();
    }
</script>

</x-layouts.admin>