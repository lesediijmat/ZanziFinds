<?php include 'config.php'; ?>

<?php
$id = intval($_GET['id']);

$stmt = $conn->prepare("
SELECT listings.*, users.fullname, sellers.business_name
FROM listings
LEFT JOIN users ON users.id = listings.user_id
LEFT JOIN sellers ON sellers.id = listings.seller_id
WHERE listings.id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$query = $stmt->get_result();
$product = $query->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $product['title']; ?></title>

<style>
body {
    font-family: Poppins;
    margin: 10px;
	display: flex;
	color:white;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.container {
	width:300;
    max-width: 500px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding: 40px;
}

.image img {
    width: 300px;
    border-radius: 12px;
}

.details {
    max-width: 100%;
	font-size: 24px;
}

.price {
    font-size: 30px;
	font-weight:700;
    color: blue;
    margin: 10px 0;
    -webkit-text-stroke: 1px black;
}

.seller {
	font-size: 24px;
    color: white;
    margin:10px 0;
}
.add-btn{
    background:#0077cc;
    color:white;
	font-weight:700;
    padding:10px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    margin-top:10px;
    width:100%;
    font-size:16px;
    transition:0.3s ease;
}
.add-btn:hover{
    background:#005fa3;
    transform: translateY(-2px);
    box-shadow:0 6px 12px rgba(0,0,0,0.2);
}
.add-btn:active{
    transform: scale(0.85);
}
</style>
</head>

<body>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="container">

    <div class="image">
		<img src="images/<?php echo $product['image']; ?>">
    </div>

    <div class="details">
        <h1><?php echo $product['title']; ?></h1>

        <div class="price">R<?php echo $product['price']; ?></div>

        <div class="seller">
            Sold by: <?php echo $product['business_name'] ?? 'Unknown Seller'; ?>
        </div>

        <p><?php echo $product['description']; ?></p>

        <button type="button"
            class="add-btn"
            onclick="addToCart(<?php echo $product['id']; ?>)">
            Add To Cart
        </button>
    </div>

</div>
<script>

function addToCart(id){

    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'product_id=' + id
    })
    .then(res => res.json())
    .then(data => {

        console.log(data);

        if(data.status === "success"){

            showPopup(data.message || "Added to cart");
            
            if (typeof refreshCartFromSession === "function") {
                refreshCartFromSession();
            }

        } else {
            showPopup(data.message || "Error adding to cart");
        }

    })
    .catch(err => {
        console.log(err);
        showPopup("Something went wrong");
    });
}
    
function showPopup(message){

    let popup = document.createElement("div");

    popup.innerText = message;

    popup.style.position = "fixed";
    popup.style.bottom = "20px";
    popup.style.left = "50%";
    popup.style.transform = "translateX(-50%)";
    popup.style.background = "green";
    popup.style.color = "white";
    popup.style.padding = "12px 18px";
    popup.style.borderRadius = "12px";
    popup.style.zIndex = "99999";

    document.body.appendChild(popup);

    setTimeout(() => popup.remove(), 2000);
}
</script>
    
</body>
</html>
