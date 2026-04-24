<h2>Pilih Pembayaran</h2>

<form method="POST" action="">
@csrf

<input type="hidden" name="cart" id="cart">

<button name="method" value="cash">Bayar Cash</button>
<button name="method" value="qris">QRIS</button>

</form>

<script>
document.getElementById('cart').value = localStorage.getItem('cart')
</script>