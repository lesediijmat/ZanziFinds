<?php include 'config.php'; ?>

<?php

if(isset($_GET['m_payment_id'])){

    $orderId = $_GET['m_payment_id'];

    $stmt = $conn->prepare("UPDATE orders SET status='Paid' WHERE id=?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
}

unset($_SESSION['cart']);
unset($_SESSION['checkout']);

?>

<!DOCTYPE html>
<html>
<head>
<title>Payment Successful</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body{
    font-family:Arial;
    background:#32C8E3;
    margin:0;
    padding:20px;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    box-sizing:border-box;
}

.box{
    background:white;
    width:100%;
    max-width:420px;

    padding:25px 20px;
    border-radius:12px;
    text-align:center;
    box-sizing:border-box;
}

h1{
    font-size:34px;
}

p{
    font-size:28px;
}

a{
    display:inline-block;
    margin-top:20px;
    padding:12px 20px;
    background:blue;
    color:white;
    text-decoration:none;
    border-radius:8px;
	font-size:28px;
}

</style>
</head>

<body>

<div class="box">

<h1>Payment Successful</h1>
<p>
	Your order has been
	placed successfully.
</p>

<a href="index.php">Continue Shopping</a>

</div>

</body>
</html>