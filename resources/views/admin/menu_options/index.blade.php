<x-layouts.admin title="Master Menu Option">

    <div class="py-6 px-4">
        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Master Menu Option</h1>
                <p class="text-slate-500 text-sm">Atur kategori pilihan tambahan seperti Ukuran, Topping, atau Level Gula.</p>
            </div>

            <button class="btn border-none bg-[#D4E971] hover:bg-[#c2d75f] text-black shadow-lg shadow-[#D4E971]/20 px-8 rounded-xl font-bold"
                onclick="add_modal.showModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Tambah Option
            </button>
        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-400 uppercase text-[11px] tracking-[0.15em]">
                            <th class="py-5 pl-8">No</th>
                            <th>Nama Option</th>
                            <th>Tipe Pilihan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-600">
                        @foreach($menu_options as $index => $opt)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="pl-8 font-medium opacity-50">{{ $index+1 }}</td>
                            <td>
                                <div class="font-bold text-slate-800 text-base">{{ $opt->name }}</div>
                            </td>
                            <td>
                                @if($opt->type == 'select')
                                    <div class="badge bg-blue-50 border-none text-blue-600 font-bold text-[10px] px-3 tracking-wider uppercase">Single Choice</div>
                                @else
                                    <div class="badge bg-purple-50 border-none text-purple-600 font-bold text-[10px] px-3 tracking-wider uppercase">Multiple Choice</div>
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-center gap-2">
                                    <button class="btn btn-square btn-ghost btn-sm hover:bg-[#D4E971]/20 hover:text-black transition-colors"
                                        onclick="openEditModal(this)"
                                        data-id="{{ $opt->id }}"
                                        data-name="{{ $opt->name }}"
                                        data-type="{{ $opt->type }}"
                                        title="Edit Option">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                    </button>
                                    
                                    <button class="btn btn-square btn-ghost btn-sm hover:bg-red-50 hover:text-red-500 transition-colors"
                                        onclick="openDeleteModal({{ $opt->id }})"
                                        title="Hapus Option">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
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

    {{-- ================= MODAL STYLING (SHARED) ================= --}}
    <style>
        .modal-box { border-radius: 1.5rem; border: none; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15); }
        .input, .select { border-radius: 0.75rem; border-color: #e2e8f0; }
        .input:focus, .select:focus { outline: 2px solid #D4E971; outline-offset: 1px; border-color: transparent; }
    </style>

    {{-- ADD MODAL --}}
    <dialog id="add_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white">
            <form method="POST" action="{{ route('admin.menu_options.store') }}">
                @csrf
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-black text-2xl text-slate-800 tracking-tight">Tambah Option</h3>
                    <button type="button" onclick="add_modal.close()" class="btn btn-sm btn-circle btn-ghost">✕</button>
                </div>

                <div class="form-control mb-4">
                    <label class="label"><span class="label-text font-bold text-slate-600">Nama Option</span></label>
                    <input type="text" name="name" placeholder="Contoh: Ukuran atau Topping" class="input input-bordered w-full" required>
                </div>

                <div class="form-control mb-6">
                    <label class="label"><span class="label-text font-bold text-slate-600">Tipe Pemilihan</span></label>
                    <select name="type" class="select select-bordered w-full">
                        <option value="select">Radio / Select (Hanya 1 pilihan)</option>
                        <option value="checkbox">Checkbox (Bisa pilih banyak)</option>
                    </select>
                </div>

                <div class="modal-action">
                    <button type="button" class="btn btn-ghost px-8" onclick="add_modal.close()">Batal</button>
                    <button class="btn border-none bg-[#D4E971] hover:bg-[#c2d75f] text-black px-10 rounded-xl font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- EDIT MODAL --}}
    <dialog id="edit_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white text-slate-800">
            <form method="POST" id="editForm">
                @csrf
                @method('PUT')
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-black text-2xl tracking-tight text-slate-800">Edit Option</h3>
                    <button type="button" onclick="edit_modal.close()" class="btn btn-sm btn-circle btn-ghost">✕</button>
                </div>

                <div class="form-control mb-4">
                    <label class="label"><span class="label-text font-bold text-slate-600">Nama Option</span></label>
                    <input type="text" id="edit_name" name="name" class="input input-bordered w-full text-slate-800" required>
                </div>

                <div class="form-control mb-6">
                    <label class="label"><span class="label-text font-bold text-slate-600 text-slate-800">Tipe Pemilihan</span></label>
                    <select id="edit_type" name="type" class="select select-bordered w-full text-slate-800">
                        <option value="select">Radio / Select (Hanya 1 pilihan)</option>
                        <option value="checkbox">Checkbox (Bisa pilih banyak)</option>
                    </select>
                </div>

                <div class="modal-action">
                    <button type="button" class="btn btn-ghost px-8" onclick="edit_modal.close()">Batal</button>
                    <button class="btn border-none bg-[#D4E971] hover:bg-[#c2d75f] text-black px-10 rounded-xl font-bold shadow-lg shadow-[#D4E971]/20">Update</button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- DELETE MODAL --}}
    <dialog id="delete_modal" class="modal">
        <div class="modal-box bg-white max-w-sm text-center">
            <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
            </div>
            <h3 class="font-black text-xl text-slate-800">Hapus Option?</h3>
            <p class="text-slate-500 mt-2">Menghapus kategori opsi ini juga akan melepaskan kaitannya dari semua menu yang menggunakan ini.</p>
            <form method="POST" id="deleteForm">
                @csrf
                @method('DELETE')
                <div class="flex flex-col gap-2 mt-6">
                    <button class="btn btn-error text-white font-bold rounded-xl border-none">Ya, Hapus</button>
                    <button type="button" class="btn btn-ghost" onclick="delete_modal.close()">Batalkan</button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- SCRIPT --}}
    <script>
        function openEditModal(btn) {
            document.getElementById('edit_name').value = btn.dataset.name;
            document.getElementById('edit_type').value = btn.dataset.type;
            document.getElementById('editForm').action = `/admin/menu_options/${btn.dataset.id}`;
            edit_modal.showModal();
        }

        function openDeleteModal(id) {
            document.getElementById('deleteForm').action = `/admin/menu_options/${id}`;
            delete_modal.showModal();
        }
    </script>

</x-layouts.admin>