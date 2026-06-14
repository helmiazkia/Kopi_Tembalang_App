@php use Illuminate\Support\Str; @endphp

<x-layouts.admin title="Manajemen Kategori">

    {{-- Toast Notifikasi --}}
    @if(session('success'))
        <div class="toast toast-top toast-end z-[100]">
            <div class="alert alert-success shadow-lg border-none bg-[#D4E971] text-black">
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
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Daftar Kategori</h1>
                <p class="text-slate-500 text-sm">Kelola kategori produk Kopi Tembalang.</p>
            </div>
            <button
                onclick="add_modal.showModal()"
                class="btn border-none bg-[#D4E971] hover:bg-black hover:text-[#D4E971] text-black shadow-xl shadow-[#D4E971]/30 px-8 rounded-2xl font-black transition-all duration-500 group"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" class="mr-2 group-hover:rotate-90 transition-transform duration-500">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Tambah Kategori Baru
            </button>
        </div>

        {{-- Tabel --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-400 uppercase text-[11px] tracking-[0.15em]">
                            <th class="py-5 pl-8">No</th>
                            <th>Nama Kategori</th>
                            <th>Gambar</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-600">
                        @forelse($categories as $index => $category)
                            <tr class="hover:bg-slate-50/80 transition-colors">

                                <td class="pl-8 font-medium opacity-50">{{ $index + 1 }}</td>

                                {{-- Nama --}}
                                <td class="font-bold text-slate-800">{{ $category->name }}</td>

                                {{-- Gambar --}}
                                <td>
                                    @if($category->image)
                                        <div class="avatar">
                                            <div class="mask mask-squircle w-12 h-12">
                                                <img src="{{ asset('images/category/' . $category->image) }}" alt="{{ $category->name }}">
                                            </div>
                                        </div>
                                    @else
                                        <div class="mask mask-squircle w-12 h-12 bg-slate-100 flex items-center justify-center text-slate-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                                                <circle cx="9" cy="9" r="2"/>
                                                <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                            </svg>
                                        </div>
                                    @endif
                                </td>

                                {{-- Deskripsi --}}
                                <td>
                                    <div class="text-[11px] opacity-50 max-w-xs">
                                        {{ Str::limit($category->description, 60) ?: '—' }}
                                    </div>
                                </td>

                                {{-- Aksi --}}
                                <td>
                                    <div class="flex justify-center gap-2">
                                        <button
                                            onclick="openEditModal(this)"
                                            data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}"
                                            data-description="{{ $category->description }}"
                                            class="btn btn-square btn-ghost btn-sm hover:bg-[#D4E971]/20 hover:text-black transition-colors"
                                            title="Edit Kategori"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                                                <path d="m15 5 4 4"/>
                                            </svg>
                                        </button>
                                        <button
                                            onclick="openDeleteModal(this)"
                                            data-id="{{ $category->id }}"
                                            class="btn btn-square btn-ghost btn-sm hover:bg-red-50 hover:text-red-500 transition-colors"
                                            title="Hapus Kategori"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 6h18"/>
                                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                                <line x1="10" x2="10" y1="11" y2="17"/>
                                                <line x1="14" x2="14" y1="11" y2="17"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-32 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <span class="text-5xl opacity-10">📁</span>
                                        <p class="font-black text-slate-300 uppercase tracking-[0.3em] text-xs">Belum ada kategori tersedia</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Shared Modal Styles --}}
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

    {{-- MODAL: Tambah --}}
    <dialog id="add_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box max-w-lg bg-white">
            <form method="POST" enctype="multipart/form-data" action="{{ route('admin.categories.store') }}">
                @csrf

                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-black text-2xl text-slate-800 tracking-tight">Tambah Kategori Baru</h3>
                    <button type="button" onclick="add_modal.close()" class="btn btn-sm btn-circle btn-ghost">✕</button>
                </div>

                <div class="flex flex-col gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-slate-600">Nama Kategori</span></label>
                        <input type="text" name="name" placeholder="Contoh: Minuman Dingin" class="input input-bordered w-full" required>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-slate-600">Deskripsi</span></label>
                        <textarea name="description" class="textarea textarea-bordered h-24" placeholder="Jelaskan kategori ini..."></textarea>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-slate-600">Foto Kategori</span></label>
                        <input type="file" name="image" class="file-input file-input-bordered w-full">
                    </div>
                </div>

                <div class="modal-action mt-8">
                    <button type="button" class="btn btn-ghost px-8" onclick="add_modal.close()">Batal</button>
                    <button type="submit" class="btn border-none bg-[#D4E971] hover:bg-[#c2d75f] text-black px-10 rounded-xl font-bold">
                        Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- MODAL: Edit --}}
    <dialog id="edit_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box max-w-lg bg-white text-slate-800">
            <form method="POST" enctype="multipart/form-data" id="editForm">
                @csrf
                @method('PUT')

                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-black text-2xl tracking-tight">Edit Kategori</h3>
                    <button type="button" onclick="edit_modal.close()" class="btn btn-sm btn-circle btn-ghost">✕</button>
                </div>

                <div class="flex flex-col gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-slate-600">Nama Kategori</span></label>
                        <input type="text" id="edit_name" name="name" class="input input-bordered w-full" required>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-slate-600">Deskripsi</span></label>
                        <textarea id="edit_description" name="description" class="textarea textarea-bordered h-24"></textarea>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-slate-600">Ganti Foto (Opsional)</span></label>
                        <input type="file" name="image" class="file-input file-input-bordered w-full">
                    </div>
                </div>

                <div class="modal-action mt-8">
                    <button type="button" class="btn btn-ghost px-8" onclick="edit_modal.close()">Batal</button>
                    <button type="submit" class="btn border-none bg-[#D4E971] hover:bg-[#c2d75f] text-black px-10 rounded-xl font-bold shadow-lg shadow-[#D4E971]/20">
                        Perbarui Kategori
                    </button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- MODAL: Hapus --}}
    <dialog id="delete_modal" class="modal">
        <div class="modal-box bg-white max-w-sm text-center">
            <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h18"/>
                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                    <line x1="10" x2="10" y1="11" y2="17"/>
                    <line x1="14" x2="14" y1="11" y2="17"/>
                </svg>
            </div>
            <h3 class="font-black text-xl text-slate-800">Hapus Kategori?</h3>
            <p class="text-slate-500 mt-2">Tindakan ini tidak bisa dibatalkan. Kategori akan hilang dari daftar.</p>
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

    @push('scripts')
    <script>
        function openEditModal(btn) {
            document.getElementById('edit_name').value        = btn.dataset.name;
            document.getElementById('edit_description').value = btn.dataset.description;
            document.getElementById('editForm').action        = `/admin/categories/${btn.dataset.id}`;
            edit_modal.showModal();
        }

        function openDeleteModal(btn) {
            document.getElementById('deleteForm').action = `/admin/categories/${btn.dataset.id}`;
            delete_modal.showModal();
        }
    </script>
    @endpush

</x-layouts.admin>