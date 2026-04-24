<x-layouts.admin title="Manajemen Meja">

    {{-- ALERT TOAST --}}
    @if (session('success'))
    <div class="toast toast-top toast-end z-[100] p-6">
        <div class="alert shadow-2xl border-none bg-black text-[#D4E971] rounded-2xl p-4 transition-all duration-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-black tracking-tight uppercase text-xs">{{ session('success') }}</span>
        </div>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.querySelector('.toast');
            if (toast) {
                toast.classList.add('opacity-0', 'translate-y-[-20px]');
                setTimeout(() => toast.remove(), 500);
            }
        }, 3000)
    </script>
    @endif

    <div class="py-4 px-4 max-w-7xl mx-auto">
        {{-- ACTION HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Sistem Meja</h1>
                <p class="text-slate-500 text-sm">Total Terdaftar: {{ $tables->count() }} Unit</p>
            </div>

            <button class="btn border-none bg-[#D4E971] hover:bg-black hover:text-[#D4E971] text-black shadow-xl shadow-[#D4E971]/30 px-8 rounded-2xl font-black transition-all duration-500 group"
                onclick="add_modal.showModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" class="mr-2 group-hover:rotate-90 transition-transform duration-500">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Registrasi Meja Baru
            </button>
        </div>

        {{-- TABLE CONTAINER --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-400 uppercase text-[11px] tracking-[0.15em]">
                            <th class="py-5 pl-8">No</th>
                            <th>Identitas Meja</th>
                            <th>Status Operasional</th>
                            <th class="text-center">Akses QR</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-600">
                        @forelse ($tables as $index => $table)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="pl-8 font-medium opacity-50">{{ $index+1 }}</td>
                            </td>
                            <td>
                                <div class="flex items-center gap-4">
                                    <span class="font-black text-slate-800 text-xl tracking-tighter not-italic uppercase group-hover:text-[#D4E971] transition-colors">
                                        Meja {{ $table->table_number }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                @if($table->status == 'available')
                                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-green-50 text-green-600 font-black text-[9px] tracking-widest uppercase border border-green-100">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                    </span>
                                    Available
                                </div>
                                @else
                                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-50 text-red-400 font-black text-[9px] tracking-widest uppercase border border-red-100">
                                    <div class="w-2 h-2 rounded-full bg-red-400"></div> Occupied
                                </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="relative inline-block group/qr">
                                    <div class="p-3 bg-white border-2 border-slate-100 rounded-[1.5rem] group-hover:border-[#D4E971] transition-all duration-500 shadow-sm group-hover:shadow-lg group-hover:shadow-[#D4E971]/20">
                                        {!! QrCode::size(55)->margin(1)->generate($table->qr_code) !!}
                                    </div>
                                    {{-- Tooltip overlay on hover --}}
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/80 rounded-[1.5rem] opacity-0 group-hover/qr:opacity-100 transition-opacity duration-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#D4E971" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M15 3h6v6" />
                                            <path d="M10 14L21 3" />
                                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                                        </svg>
                                    </div>
                                </div>
                            </td>
                            <td class="pr-12">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.tables.qr.download', ['table' => $table->id, 'format' => 'png']) }}"
                                        class="btn btn-square border-none bg-slate-100 hover:bg-[#D4E971] text-slate-400 hover:text-black rounded-xl transition-all duration-300" title="Download QR">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                            <polyline points="7 10 12 15 17 10" />
                                            <line x1="12" x2="12" y1="15" y2="3" />
                                        </svg>
                                    </a>
                                    <button class="btn btn-square border-none bg-slate-100 hover:bg-[#D4E971] text-slate-400 hover:text-black rounded-xl transition-all duration-300"
                                        onclick="openEditModal(this)"
                                        data-id="{{ $table->id }}"
                                        data-table_number="{{ $table->table_number }}"
                                        data-status="{{ $table->status }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                            <path d="m15 5 4 4" />
                                        </svg>
                                    </button>
                                    <button class="btn btn-square border-none bg-slate-100 hover:bg-black hover:text-red-500 rounded-xl transition-all duration-300"
                                        onclick="openDeleteModal(this)"
                                        data-id="{{ $table->id }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18" />
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-32 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mb-6 border border-slate-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 3h18v18H3z" />
                                            <path d="M3 9h18" />
                                            <path d="M9 3v18" />
                                        </svg>
                                    </div>
                                    <p class="font-black uppercase tracking-[0.4em] text-[10px] text-slate-300">No Station Detected</p>
                                    <button class="btn btn-link no-underline text-[#D4E971] font-black text-xs mt-2" onclick="add_modal.showModal()">Create First Table</button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL STYLING --}}
    <style>
        .modal-box {
            border-radius: 2.5rem;
            border: none;
            box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.2);
            padding: 3rem;
        }

        .input,
        .select {
            border-radius: 1.2rem;
            border: 2px solid #f1f5f9;
            background: #f8fafc;
            font-weight: 700;
            height: 3.5rem;
            transition: all 0.3s;
        }

        .input:focus,
        .select:focus {
            border-color: #D4E971;
            outline: none;
            background: white;
            box-shadow: 0 10px 25px -5px rgba(212, 233, 113, 0.2);
        }

        .label-text {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #94a3b8;
            margin-bottom: 0.5rem;
            display: block;
        }
    </style>

    {{-- ADD MODAL --}}
    <dialog id="add_modal" class="modal modal-bottom sm:modal-middle backdrop-blur-md">
        <div class="modal-box bg-white max-w-lg">
            <form method="POST" action="{{ route('admin.tables.store') }}">
                @csrf
                <div class="flex justify-between items-center mb-10">
                    <div>
                        <h3 class="font-black text-3xl text-slate-800 tracking-tighter italic uppercase">New <span class="text-[#D4E971] not-italic">Station</span></h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Registrasi unit meja baru</p>
                    </div>
                    <button type="button" onclick="add_modal.close()" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition-all">✕</button>
                </div>
                <div class="form-control mb-8">
                    <label class="label-text">Nomor Identitas Meja</label>
                    <input type="text" name="table_number" placeholder="CONTOH: 08" class="input w-full text-lg uppercase tracking-widest" required autofocus>
                </div>
                <div class="flex flex-col gap-3">
                    <button class="btn h-[3.5rem] border-none bg-[#D4E971] hover:bg-black hover:text-[#D4E971] text-black rounded-2xl font-black text-sm transition-all duration-500 shadow-xl shadow-[#D4E971]/20">KONFIRMASI REGISTRASI</button>
                    <button type="button" class="btn btn-ghost h-[3.5rem] font-bold text-slate-400 rounded-2xl" onclick="add_modal.close()">BATALKAN</button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- EDIT MODAL --}}
    <dialog id="edit_modal" class="modal modal-bottom sm:modal-middle backdrop-blur-md">
        <div class="modal-box bg-white max-w-lg">
            <form method="POST" id="editForm">
                @csrf
                @method('PUT')
                <div class="flex justify-between items-center mb-10">
                    <div>
                        <h3 class="font-black text-3xl text-slate-800 tracking-tighter italic uppercase">Modify <span class="text-[#D4E971] not-italic">Data</span></h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Perbarui informasi station</p>
                    </div>
                    <button type="button" onclick="edit_modal.close()" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center transition-all">✕</button>
                </div>
                <div class="grid grid-cols-1 gap-6 mb-10">
                    <div class="form-control">
                        <label class="label-text">Nomor Identitas</label>
                        <input type="text" id="edit_table_number" name="table_number" class="input w-full uppercase tracking-widest">
                    </div>
                    <div class="form-control text-slate-800">
                        <label class="label-text">Status Operasional</label>
                        <select id="edit_status" name="status" class="select w-full">
                            <option value="available text-slate-800">AVAILABLE</option>
                            <option value="occupied text-slate-800">OCCUPIED</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-col gap-3">
                    <button class="btn h-[3.5rem] border-none bg-black text-[#D4E971] hover:bg-[#D4E971] hover:text-black rounded-2xl font-black transition-all duration-500 shadow-2xl shadow-black/20 uppercase tracking-widest">Simpan Perubahan</button>
                    <button type="button" class="btn btn-ghost h-[3.5rem] font-bold text-slate-400" onclick="edit_modal.close()">KEMBALI</button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- DELETE MODAL --}}
    <dialog id="delete_modal" class="modal backdrop-blur-sm">
        <div class="modal-box bg-white max-w-sm text-center border-none shadow-2xl p-12">
            <div class="w-20 h-20 bg-red-50 text-red-500 rounded-[2rem] flex items-center justify-center mx-auto mb-8 animate-pulse">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h18" />
                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                </svg>
            </div>
            <h3 class="font-black text-2xl text-slate-800 tracking-tighter uppercase italic">Delete <span class="text-red-500 not-italic">Station?</span></h3>
            <p class="text-slate-400 mt-4 text-xs font-bold leading-relaxed uppercase tracking-widest">Meja & QR Code akan dihapus permanen dari sistem.</p>
            <form method="POST" id="deleteForm">
                @csrf
                @method('DELETE')
                <div class="flex flex-col gap-3 mt-10">
                    <button class="btn btn-error text-white font-black rounded-2xl border-none h-[3.5rem] uppercase tracking-[0.2em] shadow-xl shadow-red-200">Konfirmasi Hapus</button>
                    <button type="button" class="btn btn-ghost font-bold text-slate-300" onclick="delete_modal.close()">Batal</button>
                </div>
            </form>
        </div>
    </dialog>

    <script>
        function openEditModal(btn) {
            const id = btn.dataset.id;
            document.getElementById('edit_table_number').value = btn.dataset.table_number;
            document.getElementById('edit_status').value = btn.dataset.status;
            document.getElementById('editForm').action = `/admin/tables/${id}`;
            edit_modal.showModal();
        }

        function openDeleteModal(btn) {
            document.getElementById('deleteForm').action = `/admin/tables/${btn.dataset.id}`;
            delete_modal.showModal();
        }
    </script>
</x-layouts.admin>