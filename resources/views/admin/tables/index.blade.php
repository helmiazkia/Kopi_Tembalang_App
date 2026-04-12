<x-layouts.admin title="Manajemen Meja">

    @if (session('success'))
    <div class="toast toast-bottom toast-center">
        <div class="alert alert-success">
            <span>{{ session('success') }}</span>
        </div>
    </div>

    <script>
        setTimeout(() => {
            document.querySelector('.toast')?.remove()
        }, 3000)
    </script>
    @endif

    <div class="container mx-auto p-10">

        <div class="flex items-center">
            <h1 class="text-3xl font-semibold">Manajemen Meja</h1>

            <button class="btn btn-primary ml-auto"
                onclick="add_modal.showModal()">
                Tambah Meja
            </button>
        </div>

        <div class="overflow-x-auto rounded-box bg-white p-5 shadow mt-5">

            <table class="table">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor Meja</th>
                        <th>Status</th>
                        <th>QR Code</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($tables as $index => $table)

                    <tr>

                        <td>{{ $index + 1 }}</td>

                        <td>{{ $table->table_number }}</td>

                        <td>
                            @if($table->status == 'available')
                            <span class="badge badge-success">Available</span>
                            @else
                            <span class="badge badge-error">Occupied</span>
                            @endif
                        </td>

                        <td>
                            {!! QrCode::size(100)->generate($table->qr_code) !!}
                        </td>

                        <td class="flex gap-2">

                            <a href="{{ route('admin.tables.qr.download',$table->id) }}"
                                class="btn btn-sm btn-success">
                                Download QR
                            </a>

                            <button
                                class="btn btn-sm btn-primary"
                                onclick="openEditModal(this)"
                                data-id="{{ $table->id }}"
                                data-table_number="{{ $table->table_number }}"
                                data-status="{{ $table->status }}">
                                Edit
                            </button>

                            <button
                                class="btn btn-sm btn-error"
                                onclick="openDeleteModal(this)"
                                data-id="{{ $table->id }}">
                                Hapus
                            </button>

                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="5" class="text-center">
                            Tidak ada meja
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    {{-- ADD MODAL --}}
    <dialog id="add_modal" class="modal">

        <form method="POST"
            action="{{ route('admin.tables.store') }}"
            class="modal-box">

            @csrf

            <h3 class="font-bold text-lg mb-4">Tambah Meja</h3>

            <input type="text"
                name="table_number"
                placeholder="Nomor Meja"
                class="input input-bordered w-full mb-3"
                required>

            <div class="modal-action">

                <button class="btn btn-primary">
                    Simpan
                </button>

                <button type="button"
                    class="btn"
                    onclick="add_modal.close()">
                    Batal
                </button>

            </div>

        </form>

    </dialog>


    {{-- EDIT MODAL --}}
    <dialog id="edit_modal" class="modal">

        <form method="POST"
            id="editForm"
            class="modal-box">

            @csrf
            @method('PUT')

            <h3 class="font-bold text-lg mb-4">Edit Meja</h3>

            <input type="text"
                id="edit_table_number"
                name="table_number"
                class="input input-bordered w-full mb-3">

            <select id="edit_status"
                name="status"
                class="select select-bordered w-full mb-3">

                <option value="available">Available</option>
                <option value="occupied">Occupied</option>

            </select>

            <div class="modal-action">

                <button class="btn btn-primary">
                    Update
                </button>

                <button type="button"
                    class="btn"
                    onclick="edit_modal.close()">
                    Batal
                </button>

            </div>

        </form>

    </dialog>


    {{-- DELETE MODAL --}}
    <dialog id="delete_modal" class="modal">

        <form method="POST"
            id="deleteForm"
            class="modal-box">

            @csrf
            @method('DELETE')

            <h3 class="font-bold text-lg mb-4">
                Hapus Meja
            </h3>

            <p>Yakin ingin menghapus meja ini?</p>

            <div class="modal-action">

                <button class="btn btn-error">
                    Hapus
                </button>

                <button type="button"
                    class="btn"
                    onclick="delete_modal.close()">
                    Batal
                </button>

            </div>

        </form>

    </dialog>


    <script>
        function openEditModal(btn) {

            const id = btn.dataset.id;
            const table_number = btn.dataset.table_number;
            const status = btn.dataset.status;

            document.getElementById('edit_table_number').value = table_number;
            document.getElementById('edit_status').value = status;

            document.getElementById('editForm').action = `/admin/tables/${id}`;

            edit_modal.showModal();

        }

        function openDeleteModal(btn) {

            const id = btn.dataset.id;

            document.getElementById('deleteForm').action = `/admin/tables/${id}`;

            delete_modal.showModal();

        }
    </script>

</x-layouts.admin>