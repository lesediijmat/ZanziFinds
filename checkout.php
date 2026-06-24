<?php include 'config.php'; ?>

<?php
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

if(empty($_SESSION['cart'])){
    header("Location: cart.php");
    exit;
}

$subtotal = 0;

foreach($_SESSION['cart'] as $item){
    $qty = isset($item['qty']) ? $item['qty'] : 1;
    $subtotal += $item['price'] * $qty;
}

$deliveryFee = 20;
$total = $subtotal + $deliveryFee;

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT fullname, email, address, city FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$hasSavedAddress = !empty(trim($user['address'])) && !empty(trim($user['city']));
?>

<!DOCTYPE html>
<html>
<head>
<title>Checkout</title>

<style>

body{
    font-family: Arial;
    margin:0;
}

.checkout-container{
    width:90%;
    max-width:650px;
    margin:40px auto;
    padding:25px;
    border-radius:12px;
    box-shadow:0 3px 10px rgba(0,0,0,0.1);
}

h1{
    text-align:center;
    margin-bottom:25px;
}

.customer-box,
.order-summary,
.delivery-box{
    background:#f5f5f5;
    padding:15px;
    border-radius:10px;
    margin-bottom:20px;
}

.customer-box p{
    margin:8px 0;
}

.item{
    display:flex;
    justify-content:space-between;
    margin:10px 0;
}

.total{
    font-size:20px;
    font-weight:bold;
    margin-top:15px;
}

input,
textarea{
    width:100%;
    padding:12px;
    margin-top:12px;
    border:1px solid #ccc;
    border-radius:8px;
    font-size:15px;
    box-sizing:border-box;
}

button{
    width:100%;
    padding:15px;
    background:blue;
    color:white;
    border:none;
    border-radius:8px;
    font-size:16px;
    margin-top:20px;
    cursor:pointer;
}

button:hover{
    background:#0033cc;
}

</style>
</head>

<body>

<div class="checkout-container">

<h1>Checkout</h1>

<div class="customer-box">

<h3>Customer Details</h3>

<p><strong>Name:</strong> <?php echo htmlspecialchars($user['fullname']); ?></p>

<p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>

</div>

<div class="order-summary">

<h3>Order Summary</h3>

<?php foreach($_SESSION['cart'] as $item): ?>

<div class="item">
    <span><?php echo $item['title']; ?></span>
    <?php
    $qty = isset($item['qty']) ? $item['qty'] : 1;
    $lineTotal = $item['price'] * $qty;
    ?>
    <span>R<?php echo $lineTotal; ?></span>
</div>

<?php endforeach; ?>

<div class="total">
    Total: R<?php echo $total; ?>
</div>

</div>

<form action="process_checkout.php" method="POST">

    <input type="hidden" name="total" value="<?php echo $total; ?>">

    <div class="delivery-box">

        <h3>Delivery Address</h3>

        <?php if($hasSavedAddress): ?>

        <label>
            <input type="radio" name="address_option" value="saved" checked>
            Use saved address
        </label>

        <div style="margin:10px 0 15px 25px;">
            <?php echo htmlspecialchars($user['address'] . ", " . $user['city']); ?>
        </div>

        <?php endif; ?>

        <label>
            <input type="radio" name="address_option" value="new" <?php echo !$hasSavedAddress ? 'checked' : ''; ?>>
            Use new address
        </label>

        <textarea
            name="new_address"
            placeholder="Enter new delivery address"
            style="display:none;"
        ></textarea>

    </div>

    <button type="submit">Proceed To Payment</button>

</form>

</div>

<script>

const radios = document.querySelectorAll('input[name="address_option"]');
const newAddress = document.querySelector('textarea[name="new_address"]');

function updateAddress() {

    const selected = document.querySelector('input[name="address_option"]:checked');

    if(selected.value === "saved") {

        newAddress.style.display = "none";
        newAddress.required = false;

    } else {

        newAddress.style.display = "block";
        newAddress.required = true;
    }
}

radios.forEach(r => r.addEventListener('change', updateAddress));

updateAddress();

</script>

</body>
</html>