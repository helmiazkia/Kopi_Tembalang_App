@php use Illuminate\Support\Str; @endphp
<x-layouts.admin title="Manajemen Kategori">

    @if(session('success'))
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
            <h1 class="text-3xl font-semibold">Manajemen Kategori</h1>

            <button class="btn btn-primary ml-auto" onclick="add_modal.showModal()">
                Tambah Kategori
            </button>
        </div>

        <div class="overflow-x-auto rounded-box bg-white p-5 shadow mt-5">

            <table class="table">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($categories as $index => $category)

                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $category->name }}</td>

                        <td>
                            <button
                                class="btn btn-sm btn-primary"
                                onclick="openEditModal(this)"
                                data-id="{{ $category->id }}"
                                data-name="{{ $category->name }}">
                                Edit
                            </button>

                            <button
                                class="btn btn-sm btn-error"
                                onclick="openDeleteModal(this)"
                                data-id="{{ $category->id }}">
                                Hapus
                            </button>
                        </td>
                    </tr>

                    @empty

                    <tr>
                        <td colspan="3" class="text-center">
                            Tidak ada kategori tersedia.
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>
    {{-- ADD MODAL --}}
    <dialog id="add_modal" class="modal">
        <form method="POST" enctype="multipart/form-data" action="{{ route('admin.categories.store') }}" class="modal-box">
            @csrf

            <h3 class="font-bold text-lg mb-4">Tambah Kategori</h3>

            <input type="text" name="name" placeholder="Nama Kategori" class="input input-bordered w-full mb-3" required>

            <div class="modal-action">
                <button class="btn btn-primary">Simpan</button>
                <button type="button" class="btn" onclick="add_modal.close()">Batal</button>
            </div>
        </form>
    </dialog>


    {{-- EDIT MODAL --}}
    <dialog id="edit_modal" class="modal">
        <form method="POST" enctype="multipart/form-data" id="editForm" class="modal-box">
            @csrf
            @method('PUT')

            <h3 class="font-bold text-lg mb-4">Edit Kategori</h3>

            <input type="text" id="edit_name" name="name" class="input input-bordered w-full mb-3">
            <div class="modal-action">
                <button class="btn btn-primary">Update</button>
                <button type="button" class="btn" onclick="edit_modal.close()">Batal</button>
            </div>
        </form>
    </dialog>
    {{-- DELETE MODAL --}}
    <dialog id="delete_modal" class="modal">
        <form method="POST" id="deleteForm" class="modal-box">
            @csrf
            @method('DELETE')

            <h3 class="font-bold text-lg mb-4">Hapus Kategori</h3>
            <p>Yakin ingin menghapus kategori ini?</p>

            <div class="modal-action">
                <button class="btn btn-error">Hapus</button>
                <button type="button" class="btn" onclick="delete_modal.close()">Batal</button>
            </div>
        </form>
    </dialog>

    {{-- SCRIPT --}}
    <script>
        function openEditModal(btn) {
            const id = btn.dataset.id;
            const name = btn.dataset.name;

            document.getElementById('edit_name').value = name;
            document.getElementById('editForm').action = `/admin/categories/${id}`;

            edit_modal.showModal();
        }

        function openDeleteModal(btn) {
            const id = btn.dataset.id;

            document.getElementById('deleteForm').action = `/admin/categories/${id}`;

            delete_modal.showModal();
        }
    </script>
</x-layouts.admin>