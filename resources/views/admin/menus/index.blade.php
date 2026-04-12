@php use Illuminate\Support\Str; @endphp
<x-layouts.admin title="Manajemen Menu">
@include('admin.menu_management_tabs')


    {{-- ALERT --}}
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
            <h1 class="text-3xl font-semibold">Manajemen Menu</h1>
            <button class="btn btn-primary ml-auto" onclick="add_modal.showModal()">Tambah Menu</button>
        </div>

        <div class="overflow-x-auto rounded-box bg-white p-5 shadow mt-5">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Gambar</th>
                        <th>Status</th>
                        <th>Harga</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($menus as $index => $menu)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $menu->name }}</td>
                        <td>{{ $menu->category->name }}</td>
                        <td>
                            @if($menu->image)
                            <img src="{{ asset('images/menu/'.$menu->image) }}" class="w-12 h-12 rounded">
                            @endif
                        </td>
                        <td>
                            @if($menu->is_available)
                            <span class="badge badge-success">Tersedia</span>
                            @else
                            <span class="badge badge-error">Tidak</span>
                            @endif
                        </td>
                        <td>Rp {{ number_format($menu->price) }}</td>
                        <td class="max-w-xs">
                            {{ Str::limit($menu->description, 50) }}
                        </td>
                        <td>
                            <button
                                class="btn btn-sm btn-primary"
                                onclick="openEditModal(this)"
                                data-id="{{ $menu->id }}"
                                data-name="{{ $menu->name }}"
                                data-price="{{ $menu->price }}"
                                data-category="{{ $menu->category_id }}"
                                data-description="{{ $menu->description }}"
                                data-available="{{ $menu->is_available }}"
                                data-image="{{ $menu->image }}">
                                Edit
                            </button>

                            <button
                                class="btn btn-sm btn-error"
                                onclick="openDeleteModal(this)"
                                data-id="{{ $menu->id }}">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada menu tersedia.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ADD MODAL --}}
    <dialog id="add_modal" class="modal">
        <form method="POST" enctype="multipart/form-data" action="{{ route('admin.menus.store') }}" class="modal-box">
            @csrf

            <h3 class="font-bold text-lg mb-4">Tambah Menu</h3>

            <input type="text" name="name" placeholder="Nama Menu" class="input input-bordered w-full mb-3" required>

            <select name="category_id" class="select select-bordered w-full mb-3">
                @foreach ($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            <input type="number" name="price" placeholder="Harga" class="input input-bordered w-full mb-3" required>


            <textarea name="description" class="textarea textarea-bordered w-full mb-3" placeholder="Deskripsi"></textarea>
            <input type="file" name="image" class="file-input file-input-bordered w-full mb-3">

            <select name="is_available" class="select select-bordered w-full mb-3">
                <option value="1">Tersedia</option>
                <option value="0">Tidak Tersedia</option>
            </select>

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

            <h3 class="font-bold text-lg mb-4">Edit Menu</h3>

            <input type="text" id="edit_name" name="name" class="input input-bordered w-full mb-3">

            <select id="edit_category" name="category_id" class="select select-bordered w-full mb-3">
                @foreach ($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>

            <input type="number" id="edit_price" name="price" class="input input-bordered w-full mb-3">

            <textarea id="edit_description" name="description" class="textarea textarea-bordered w-full mb-3" placeholder="Deskripsi"></textarea>

            <input type="file" name="image" class="file-input file-input-bordered w-full mb-3">

            <select id="edit_available" name="is_available" class="select select-bordered w-full mb-3">
                <option value="1">Tersedia</option>
                <option value="0">Tidak Tersedia</option>
            </select>
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

            <h3 class="font-bold text-lg mb-4">Hapus Menu</h3>
            <p>Yakin ingin menghapus menu ini?</p>

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
            const price = btn.dataset.price;
            const category = btn.dataset.category;
            const description = btn.dataset.description;
            const available = btn.dataset.available;

            document.getElementById('edit_name').value = name;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_category').value = category;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_available').value = available;

            document.getElementById('editForm').action = `/admin/menus/${id}`;

            edit_modal.showModal();
        }

        function openDeleteModal(btn) {
            const id = btn.dataset.id;

            document.getElementById('deleteForm').action = `/admin/menus/${id}`;

            delete_modal.showModal();
        }
    </script>

</x-layouts.admin>