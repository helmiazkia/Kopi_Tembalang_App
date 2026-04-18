

    <div class="container mx-auto p-10">

        <h1 class="text-2xl font-bold mb-5">
            Buat Order
        </h1>

        <form method="POST" action="/order">
            @csrf

            <label>Customer</label>

            <input type="text"
                name="customer_name"
                class="input input-bordered w-full mb-3"
                required>

            <label>Meja</label>

            <select name="table_id" class="select select-bordered w-full mb-4">

                @foreach($tables as $table)
                <option value="{{ $table->id }}">
                    Meja {{ $table->table_number }}
                </option>
                @endforeach

            </select>

            <hr class="mb-4">

            <div id="items">

                <div class="border p-4 mb-3 item-block">

                    <label>Menu</label>

                    <select name="items[0][menu_id]"
                        class="select select-bordered w-full mb-3 menu-select">

                        @foreach($menus as $menu)
                        <option value="{{ $menu->id }}">
                            {{ $menu->name }}
                        </option>
                        @endforeach

                    </select>

                    <label>Qty</label>

                    <input type="number"
                        name="items[0][qty]"
                        value="1"
                        min="1"
                        class="input input-bordered w-full mb-3">

                    <label>Options</label>

                    <div class="options-area">

                        @foreach($optionItems as $option)

                        <div class="option-group"
                            data-menu="{{ $option->menuOption->menu_id }}">

                            <input type="checkbox"
                                name="items[0][options][]"
                                value="{{ $option->id }}">

                            {{ $option->menuOption->name }} :
                            {{ $option->name }}

                            @if($option->price > 0)
                            (+{{ number_format($option->price) }})
                            @endif

                        </div>

                        @endforeach

                    </div>

                </div>

            </div>

            <button type="button"
                class="btn btn-secondary mb-3"
                onclick="addItem()">

                Add Item

            </button>

            <br>

            <button class="btn btn-primary">
                Buat Order
            </button>

        </form>

    </div>


    <script>
        let index = 1;

        /* FILTER OPTION BERDASARKAN MENU */
        function filterOptions(container) {

            const menuSelect = container.querySelector('.menu-select');
            const menuId = menuSelect.value;

            const options = container.querySelectorAll('.option-group');

            options.forEach(option => {

                if (option.dataset.menu == menuId) {

                    option.style.display = 'block';

                } else {

                    option.style.display = 'none';

                    option.querySelector('input').checked = false;

                }

            });

        }

        /* EVENT MENU CHANGE */
        document.addEventListener('change', function(e) {

            if (e.target.classList.contains('menu-select')) {

                const container = e.target.closest('.item-block');

                filterOptions(container);

            }

        });


        /* ADD ITEM */
        function addItem() {

            const html = `

<div class="border p-4 mb-3 item-block">

<label>Menu</label>

<select name="items[${index}][menu_id]"
class="select select-bordered w-full mb-3 menu-select">

@foreach($menus as $menu)
<option value="{{ $menu->id }}">{{ $menu->name }}</option>
@endforeach

</select>

<label>Qty</label>

<input type="number"
name="items[${index}][qty]"
value="1"
min="1"
class="input input-bordered w-full mb-3">

<label>Options</label>

<div class="options-area">

@foreach($optionItems as $option)

<div class="option-group"
data-menu="{{ $option->menuOption->menu_id }}">

<input type="checkbox"
name="items[${index}][options][]"
value="{{ $option->id }}">

{{ $option->menuOption->name }} :
{{ $option->name }}

</div>

@endforeach

</div>

</div>

`;

            document.getElementById('items').insertAdjacentHTML('beforeend', html);

            index++;

        }

        /* INITIAL FILTER */
        document.querySelectorAll('.item-block').forEach(block => {
            filterOptions(block);
        });
    </script>

