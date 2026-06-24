<?php 
include 'config.php'; 
?>

<?php
$deliveryFee = 20;
$subtotal = 0;

if(isset($_GET['remove'])){
    $index = $_GET['remove'];
    unset($_SESSION['cart'][$index]);

    header("Location: cart.php");
    exit;
}

if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}

foreach($_SESSION['cart'] as $item){

    if(is_array($item) && isset($item['price'], $item['qty'])){
        $subtotal += $item['price'] * $item['qty'];
    }
}

$total = $subtotal + $deliveryFee;
?>

<!DOCTYPE html>
<html>
<head>
<title>Cart</title>

<style>
body{
    font-family: Arial;
    margin: 0;
}

.container{
    width: 90%;
    max-width: 800px;
    margin: 30px auto;
}

.logo{
    width: 180px;
    display: block;
    margin: 0 auto;
}

h1{
    text-align: center;
}

.cart-item{
    background: white;
    padding: 15px;
    border-radius: 10px;
    margin: 10px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.cart-left{
    display: flex;
    flex-direction: column;
}

.price{
    font-weight: bold;
}

.qty-controls {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 6px 10px;
    border-radius: 10px;
    width: fit-content;
}

.qty-controls span {
    min-width: 24px;
    text-align: center;
    font-weight: 600;
    font-size: 15px;
    color: #111;
	line-height: 1;   
}

.qty-controls button {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 8px;
    background: #eee;
    color: #111;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1; 
    padding: 0; 
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    transition: all 0.2s ease;
}

.qty-controls button:hover {
    background: #4D32E3;
    color: white;
}
	
.remove-btn{
    background: #e53935;
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 13px;
	font-weight: bold;
	width: auto;
    display: inline-block;
}

.remove-btn:hover{
    background: #c62828;
}

.summary{
    background: white;
    padding: 15px;
    border-radius: 10px;
    margin-top: 20px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

button{
    width: 100%;
    padding: 15px;
    background: blue;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    margin-top: 10px;
}

button:hover{
    background: #0033cc;
}
</style>
</head>

<body>

<div class="container">

<img src="images/MainLogoTextB.png" class="logo">

<h1>Cart</h1>

<?php if(empty($_SESSION['cart'])): ?>

    <p style="text-align:center;">Your cart is empty</p>

<?php else: ?>

    <?php foreach($_SESSION['cart'] as $product_id => $item): ?>

        <div class="cart-item" data-item="<?php echo $product_id; ?>">

            <div class="cart-left">
				<strong><?php echo $item['title']; ?></strong>
				<span class="price">R<?php echo $item['price'] * $item['qty']; ?></span>
            </div>
				<div class="qty-controls">
					<button onclick="changeQty(<?php echo $product_id; ?>, -1)">−</button>

					<span id="qty-<?php echo $product_id; ?>">
						<?php echo $item['qty']; ?>
					</span>

					<button onclick="changeQty(<?php echo $product_id; ?>, 1)">+</button>
				</div>
			<button class="remove-btn" onclick="removeItem(<?php echo $product_id; ?>, this)">
				Remove
			</button>
			
        </div>

    <?php endforeach; ?>

    <div class="summary" id="summaryBox">
		<p>Subtotal: R<span id="subtotal"><?php echo $subtotal; ?></span></p>
		<p>Delivery: R<span id="delivery">20</span></p>
		<h3>Total: R<span id="total"><?php echo $subtotal + $deliveryFee; ?></span></h3>

        <form method="POST" action="checkout.php">
            <input type="hidden" name="total" value="<?php echo $total; ?>">
            <button type="submit">Checkout</button>
        </form>
    </div>

<?php endif; ?>

</div>

<script>
function removeItem(id, button){

    fetch('remove_from_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'index=' + id
    })
    .then(res => res.json())
    .then(data => {

        if(data.status === "success"){

            const item = document.querySelector(`[data-item="${id}"]`);
            if(item) item.remove();

            document.getElementById("subtotal").innerText = data.subtotal;
            document.getElementById("total").innerText = data.total;

            if(data.empty || data.subtotal == 0){

                document.querySelector(".summary")?.remove();

                document.querySelector(".container").innerHTML += `
                    <p style="text-align:center;" id="emptyMsg">
                        Your cart is empty
                    </p>
                `;
            }
        }
    });
}

function changeQty(id, change){

    fetch('update_qty.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + id + '&change=' + change
    })
    .then(res => res.json())
    .then(data => {

        if(data.status === "success"){

            document.getElementById("qty-" + id).innerText = data.qty;

            document.getElementById("subtotal").innerText = data.subtotal;
            document.getElementById("total").innerText = data.total;
            document.getElementById("delivery").innerText = data.delivery;
        }
    });
}
</script>
</body>
</html>
