<h2>Data Pemesan</h2>

<form method="POST" action="/checkout">
@csrf

<input type="hidden" name="table_id" value="{{ $table->id }}">

<p>Meja: {{ $table->table_number }}</p>

<input name="customer_name" placeholder="Nama" required>
<input name="phone" placeholder="No HP" required>

<button>Lanjut ke Pembayaran</button>
</form>