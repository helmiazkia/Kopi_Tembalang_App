@php use Illuminate\Support\Str; @endphp

<x-layouts.admin title="Menu Option">
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
            <h1 class="text-3xl font-semibold">
                Manajemen Menu Option
            </h1>

            <button class="btn btn-primary ml-auto"
                onclick="add_modal.showModal()">
                Tambah Menu Option
            </button>
        </div>


        <div class="overflow-x-auto rounded-box bg-white p-5 shadow mt-5">

            <table class="table">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Menu</th>
                        <th>Nama Option</th>
                        <th>Tipe</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($menu_options as $index => $menu_option)

                    <tr>

                        <td>{{ $index+1 }}</td>

                        <td>{{ $menu_option->menu->name }}</td>

                        <td>{{ $menu_option->name }}</td>

                        <td>
                            <span class="badge badge-info">
                                {{ $menu_option->type }}
                            </span>
                        </td>

                        <td class="flex gap-2">

                            <button
                                class="btn btn-sm btn-primary"
                                onclick="openEditModal(this)"

                                data-id="{{ $menu_option->id }}"
                                data-menu="{{ $menu_option->menu_id }}"
                                data-name="{{ $menu_option->name }}"
                                data-type="{{ $menu_option->type }}">
                                Edit
                            </button>

                            <button
                                class="btn btn-sm btn-error"
                                onclick="openDeleteModal(this)"
                                data-id="{{ $menu_option->id }}">
                                Hapus
                            </button>

                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="5" class="text-center">
                            Tidak ada menu option tersedia
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
            action="{{ route('admin.menu_options.store') }}"
            class="modal-box">

            @csrf

            <h3 class="font-bold text-lg mb-4">
                Tambah Menu Option
            </h3>

            <input
                type="text"
                name="name"
                placeholder="Nama Option"
                class="input input-bordered w-full mb-3"
                required>

            <select
                name="menu_id"
                class="select select-bordered w-full mb-3">

                @foreach ($menus as $menu)

                <option value="{{ $menu->id }}">
                    {{ $menu->name }}
                </option>

                @endforeach

            </select>

            <select
                name="type"
                class="select select-bordered w-full mb-3">

                <option value="select">
                    Select (pilih satu)
                </option>

                <option value="checkbox">
                    Checkbox (pilih banyak)
                </option>

            </select>

            <div class="modal-action">

                <button class="btn btn-primary">
                    Simpan
                </button>

                <button
                    type="button"
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

            <h3 class="font-bold text-lg mb-4">
                Edit Menu Option
            </h3>

            <input
                type="text"
                id="edit_name"
                name="name"
                class="input input-bordered w-full mb-3">

            <select
                id="edit_menu"
                name="menu_id"
                class="select select-bordered w-full mb-3">

                @foreach ($menus as $menu)

                <option value="{{ $menu->id }}">
                    {{ $menu->name }}
                </option>

                @endforeach

            </select>

            <select
                id="edit_type"
                name="type"
                class="select select-bordered w-full mb-3">

                <option value="select">Select</option>
                <option value="checkbox">Checkbox</option>

            </select>

            <div class="modal-action">

                <button class="btn btn-primary">
                    Update
                </button>

                <button
                    type="button"
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
                Hapus Menu Option
            </h3>

            <p>Yakin ingin menghapus option ini?</p>

            <div class="modal-action">

                <button class="btn btn-error">
                    Hapus
                </button>

                <button
                    type="button"
                    class="btn"
                    onclick="delete_modal.close()">
                    Batal
                </button>

            </div>

        </form>

    </dialog>



    {{-- SCRIPT --}}
    <script>
        function openEditModal(btn) {

            const id = btn.dataset.id
            const name = btn.dataset.name
            const type = btn.dataset.type
            const menu = btn.dataset.menu

            document.getElementById('edit_name').value = name
            document.getElementById('edit_type').value = type
            document.getElementById('edit_menu').value = menu

            document.getElementById('editForm').action =
                `/admin/menu-options/${id}`

            edit_modal.showModal()

        }

        function openDeleteModal(btn) {

            const id = btn.dataset.id

            document.getElementById('deleteForm').action =
                `/admin/menu-options/${id}`

            delete_modal.showModal()

        }
    </script>

</x-layouts.admin>