<!DOCTYPE html>
<html>
<head>
    <title>Keranjang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body { font-family:sans-serif; padding:15px; background:#f5f5f5; }

        .card {
            background:white;
            padding:10px;
            margin-bottom:10px;
            border-radius:10px;
        }

        .btn {
            background:#6d28d9;
            color:white;
            padding:10px;
            border:none;
            width:100%;
            border-radius:8px;
            margin-top:10px;
        }

        .delete {
            color:red;
            font-size:12px;
            cursor:pointer;
        }

        .total {
            font-size:18px;
            font-weight:bold;
            margin-top:10px;
        }
    </style>
</head>
<body>

<h2>🛒 Keranjang</h2>

<div id="cart"></div>

{{-- 🔥 FORM KE CHECKOUT --}}
<form method="GET" action="/checkout">
    
    <input type="hidden" name="table" value="{{ $table->id }}">

    <div class="total" id="total"></div>

    <button class="btn">Lanjut ke Checkout</button>
</form>

<script>
let cart = JSON.parse(localStorage.getItem('cart') || '[]')

function renderCart(){

    let html = ''
    let total = 0

    if(cart.length === 0){
        document.getElementById('cart').innerHTML = '<p>Keranjang kosong</p>'
        return
    }

    cart.forEach((item,i)=>{

        let optionTotal = 0

        if(item.options){
            Object.values(item.options).forEach(opt=>{
                optionTotal += opt.price
            })
        }

        let subtotal = item.price + optionTotal
        total += subtotal

        html += `
        <div class="card">
            <b>${item.name}</b><br>
            Harga: Rp ${item.price}<br>
            Tambahan: Rp ${optionTotal}<br>
            <b>Total: Rp ${subtotal}</b><br>
            Catatan: ${item.notes || '-'} <br>

            <span class="delete" onclick="removeItem(${i})">❌ Hapus</span>
        </div>
        `
    })

    document.getElementById('cart').innerHTML = html
    document.getElementById('total').innerText = 'Total: Rp ' + total.toLocaleString()
}

// 🔥 HAPUS ITEM
function removeItem(index){
    cart.splice(index,1)
    localStorage.setItem('cart', JSON.stringify(cart))
    renderCart()
}

// INIT
renderCart()
</script>

</body>
</html>