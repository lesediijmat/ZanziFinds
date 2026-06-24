<?php include 'config.php'; ?>

<?php
if(!isset($_SESSION['checkout'])){
    header("Location: cart.php");
    exit;
}

$orderId = $_SESSION['checkout']['order_id'];

/* ✔ FIX: calculate total correctly */
$total = 0;

foreach($_SESSION['cart'] as $item){
    $total += $item['price'];
}

$deliveryFee = 20;
$total = $total + $deliveryFee;
?>

<!DOCTYPE html>
<html>
<head>
<title>Redirecting...</title>
</head>

<body onload="document.getElementById('payfastForm').submit();">

<form id="payfastForm"
      action="https://sandbox.payfast.co.za/eng/process"
      method="POST"
      target="_top">

    <input type="hidden" name="merchant_id" value="10000100">
    <input type="hidden" name="merchant_key" value="46f0cd694581a">

    <input type="hidden" name="return_url" value="https://zanzifinds.infinityfree.io/successful.php">
    <input type="hidden" name="cancel_url" value="https://zanzifinds.infinityfree.io/cancellation.php">

    <input type="hidden" name="m_payment_id" value="<?php echo $orderId; ?>">
    <input type="hidden" name="amount" value="<?php echo number_format($total, 2, '.', ''); ?>">
    <input type="hidden" name="item_name" value="ZanziFinds Order">

</form>

<p style="text-align:center;font-family:Arial;">
    Redirecting to payment...
</p>

</body>
</html>