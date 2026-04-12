@php use Illuminate\Support\Str; @endphp
<x-layouts.admin title="Menu Option Item">
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
            <h1 class="text-3xl font-semibold">Manajemen Menu Option Item</h1>
            <button class="btn btn-primary ml-auto" onclick="add_modal.showModal()">Tambah Menu Option Item</button>
        </div>

        <div class="overflow-x-auto rounded-box bg-white p-5 shadow mt-5">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Menu Option</th>
                        <th>Harga</th>
                        <th>Aksi</th>

                    </tr>
                </thead>

                <tbody>
                    @forelse ($menu_option_items as $index => $menu_option_item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $menu_option_item->name }}</td>
                        <td>{{ $menu_option_item->option->name }}</td>
                        <td>Rp {{ number_format($menu_option_item->price) }}</td>
                        <td>
                            <button
                                class="btn btn-sm btn-primary"
                                onclick="openEditModal(this)"
                                data-id="{{ $menu_option_item->id }}"
                                data-name="{{ $menu_option_item->name }}"
                                data-menu_option="{{ $menu_option_item->menu_option_id }}"
                                data-price="{{ $menu_option_item->price }}">
                                Edit
                            </button>

                            <button
                                class="btn btn-sm btn-error"
                                onclick="openDeleteModal(this)"
                                data-id="{{ $menu_option_item->id }}">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada menu option item tersedia.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ADD MODAL --}}
    <dialog id="add_modal" class="modal">
        <form method="POST" enctype="multipart/form-data" action="{{ route('admin.menu_option_items.store') }}" class="modal-box">
            @csrf

            <h3 class="font-bold text-lg mb-4">Tambah Option Item</h3>

            <input type="text" name="name" placeholder="Nama Option Item" class="input input-bordered w-full mb-3" required>
            <select name="menu_option_id" class="select select-bordered w-full mb-3">
                @foreach ($menu_options as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            <input type="number"
                name="price"
                placeholder="Harga tambahan"
                class="input input-bordered w-full mb-3"
                required>
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

            <h3 class="font-bold text-lg mb-4">Edit Option Item</h3>

            <input type="text" id="edit_name" name="name" class="input input-bordered w-full mb-3">
            <select id="edit_menu_option" name="menu_option_id" class="select select-bordered w-full mb-3">
                @foreach ($menu_options as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            <input type="number"
                id="edit_price"
                name="price"
                class="input input-bordered w-full mb-3">
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

            <h3 class="font-bold text-lg mb-4">Hapus Option Item</h3>
            <p>Yakin ingin menghapus Option Item ini?</p>

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
            const menu_option = btn.dataset.menu_option;

            document.getElementById('edit_name').value = name;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_menu_option').value = menu_option;

            document.getElementById('editForm').action = `/admin/menu_option_items/${id}`;

            edit_modal.showModal();
        }

        function openDeleteModal(btn) {
            const id = btn.dataset.id;

            document.getElementById('deleteForm').action = `/admin/menu_option_items/${id}`;

            delete_modal.showModal();
        }
    </script>






</x-layouts.admin>