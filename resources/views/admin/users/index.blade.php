@php use Illuminate\Support\Str; @endphp

<x-layouts.admin title="Manajemen User">

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
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Daftar User</h1>
            <p class="text-slate-500 text-sm">Kelola daftar pengguna Kopi Tembalang.</p>
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
            Tambah User Baru
        </button>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-400 uppercase text-[11px] tracking-[0.15em]">
                        <th class="py-5 pl-8">No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No. HP</th>
                        <th>Role</th>
                        <th>Bergabung</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-slate-600">
                    @forelse ($users as $index => $user)
                        <tr class="hover:bg-slate-50/80 transition-colors">

                            {{-- No --}}
                            <td class="pl-8 font-medium opacity-50">{{ $index + 1 }}</td>

                            {{-- Nama --}}
                            <td class="font-bold text-slate-800">{{ $user->name }}</td>

                            {{-- Email --}}
                            <td>{{ $user->email }}</td>

                            {{-- No. HP --}}
                            <td>{{ $user->phone ?? '-' }}</td>

                            {{-- Role --}}
                            <td>
                                @php
                                    $roleStyle = match($user->role) {
                                        'admin'    => 'bg-red-50 text-red-600',
                                        'cashier'  => 'bg-blue-50 text-blue-600',
                                        'kitchen'  => 'bg-orange-50 text-orange-600',
                                        default    => 'bg-slate-100 text-slate-500',
                                    };
                                @endphp
                                <span class="badge border-none font-bold text-[10px] px-3 uppercase tracking-wider {{ $roleStyle }}">
                                    {{ $user->role }}
                                </span>
                            </td>

                            {{-- Bergabung --}}
                            <td class="text-slate-400 text-sm">{{ $user->created_at->format('d M Y') }}</td>

                            {{-- Aksi --}}
                            <td>
                                <div class="flex justify-center gap-2">
                                    <button
                                        class="btn btn-square btn-ghost btn-sm hover:bg-[#D4E971]/20 hover:text-black transition-colors"
                                        onclick="openEditModal(this)"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-email="{{ $user->email }}"
                                        data-phone="{{ $user->phone }}"
                                        data-role="{{ $user->role }}"
                                        title="Edit User">
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
                                        data-id="{{ $user->id }}"
                                        title="Hapus User">
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
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-300 italic text-sm">
                                Belum ada user terdaftar.
                            </td>
                        </tr>
                    @endforelse
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
    .input, .select, .textarea {
        border-radius: 0.75rem;
        border-color: #e2e8f0;
    }
    .input:focus, .select:focus, .textarea:focus {
        outline: 2px solid #D4E971;
        outline-offset: 1px;
        border-color: transparent;
    }
</style>

{{-- ===================== MODAL: TAMBAH USER ===================== --}}
<dialog id="add_modal" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box max-w-2xl bg-white">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="flex justify-between items-center mb-6">
                <h3 class="font-black text-2xl text-slate-800 tracking-tight">Tambah User Baru</h3>
                <button type="button" onclick="add_modal.close()" class="btn btn-sm btn-circle btn-ghost">✕</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label"><span class="label-text font-bold text-slate-600">Nama User</span></label>
                    <input type="text" name="name" placeholder="Contoh: John Doe" class="input input-bordered w-full" required>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text font-bold text-slate-600">Email</span></label>
                    <input type="email" name="email" placeholder="john@example.com" class="input input-bordered w-full" required>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text font-bold text-slate-600">No. HP</span></label>
                    <input type="text" name="phone" placeholder="081234567890" class="input input-bordered w-full">
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text font-bold text-slate-600">Role</span></label>
                    <select name="role" class="select select-bordered w-full">
                        <option value="admin">Admin</option>
                        <option value="cashier">Cashier</option>
                        <option value="kitchen">Kitchen</option>
                    </select>
                </div>
                <div class="form-control md:col-span-2">
                    <label class="label"><span class="label-text font-bold text-slate-600">Password</span></label>
                    <input type="password" name="password" class="input input-bordered w-full" placeholder="••••••••" required>
                </div>
            </div>

            <div class="modal-action mt-8">
                <button type="button" class="btn btn-ghost px-8" onclick="add_modal.close()">Batal</button>
                <button type="submit" class="btn border-none bg-[#D4E971] hover:bg-[#c2d75f] text-black px-10 rounded-xl font-bold">
                    Simpan User
                </button>
            </div>
        </form>
    </div>
</dialog>

{{-- ===================== MODAL: EDIT USER ===================== --}}
<dialog id="edit_modal" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box max-w-2xl bg-white text-slate-800">
        <form method="POST" id="editForm">
            @csrf
            @method('PUT')

            <div class="flex justify-between items-center mb-6">
                <h3 class="font-black text-2xl text-slate-800 tracking-tight">Edit User</h3>
                <button type="button" onclick="edit_modal.close()" class="btn btn-sm btn-circle btn-ghost">✕</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label"><span class="label-text font-bold text-slate-600">Nama User</span></label>
                    <input type="text" id="edit_name" name="name" class="input input-bordered w-full" required>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text font-bold text-slate-600">Email</span></label>
                    <input type="email" id="edit_email" name="email" class="input input-bordered w-full" required>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text font-bold text-slate-600">No. HP</span></label>
                    <input type="text" id="edit_phone" name="phone" class="input input-bordered w-full">
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text font-bold text-slate-600">Role</span></label>
                    <select id="edit_role" name="role" class="select select-bordered w-full">
                        <option value="admin">Admin</option>
                        <option value="cashier">Cashier</option>
                        <option value="kitchen">Kitchen</option>
                    </select>
                </div>
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text font-bold text-slate-600">Password Baru</span>
                        <span class="label-text-alt text-slate-400">Kosongkan jika tidak ingin mengubah</span>
                    </label>
                    <input type="password" id="edit_password" name="password" class="input input-bordered w-full" placeholder="••••••••">
                </div>
            </div>

            <div class="modal-action mt-8">
                <button type="button" class="btn btn-ghost px-8" onclick="edit_modal.close()">Batal</button>
                <button type="submit" class="btn border-none bg-[#D4E971] hover:bg-[#c2d75f] text-black px-10 rounded-xl font-bold shadow-lg shadow-[#D4E971]/20">
                    Perbarui Data User
                </button>
            </div>
        </form>
    </div>
</dialog>

{{-- ===================== MODAL: HAPUS USER ===================== --}}
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
        <h3 class="font-black text-xl text-slate-800">Hapus User?</h3>
        <p class="text-slate-500 mt-2">Tindakan ini tidak bisa dibatalkan. User akan hilang dari daftar.</p>
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
        const { id, name, email, role, phone } = btn.dataset;

        document.getElementById('edit_name').value     = name  ?? '';
        document.getElementById('edit_email').value    = email ?? '';
        document.getElementById('edit_phone').value    = phone ?? '';
        document.getElementById('edit_role').value     = role  ?? 'user';
        document.getElementById('edit_password').value = '';

        document.getElementById('editForm').action = `/admin/users/${id}`;
        edit_modal.showModal();
    }

    function openDeleteModal(btn) {
        document.getElementById('deleteForm').action = `/admin/users/${btn.dataset.id}`;
        delete_modal.showModal();
    }
</script>

</x-layouts.admin>      