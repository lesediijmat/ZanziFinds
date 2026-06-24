<?php include 'config.php'; ?>

<?php

if(!isset($_SESSION['user_id']) || empty($_SESSION['cart'])){
    header("Location: cart.php");
    exit;
}

if(!isset($_POST['address_option'])){
    die("Invalid checkout request.");
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT fullname, email, address, city FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$hasSavedAddress = !empty(trim($user['address'])) && !empty(trim($user['city']));

$addressOption = $_POST['address_option'];

$deliveryAddress = "";

if($addressOption === 'saved'){

    if(!$hasSavedAddress){
        die("Invalid saved address selection.");
    }

    $deliveryAddress = $user['address'] . ", " . $user['city'];

} else {

    $deliveryAddress = trim($_POST['new_address'] ?? '');

    if(empty($deliveryAddress)){
        die("Please enter delivery address.");
    }
}

$total = 0;

foreach($_SESSION['cart'] as $item){
    $total += $item['price'];
}

$stmt = $conn->prepare("
    INSERT INTO orders (user_id, total, status, deliveryAddress)
    VALUES (?, ?, 'Pending', ?)
");

$stmt->bind_param("ids", $user_id, $total, $deliveryAddress);
$stmt->execute();

$orderId = $conn->insert_id;

$_SESSION['checkout'] = [
    'order_id' => $orderId,
    'total' => $total
];

header("Location: payfast_redirect.php");
exit;

?>