<?php include 'config.php'; ?>

<?php

if(!isset($_GET['category'])){
    header("Location: index.php");
    exit;
}

$category = $_GET['category'];

$stmt = $conn->prepare("
    SELECT listings.*, users.fullname
    FROM listings
    LEFT JOIN users ON users.id = listings.user_id
    WHERE listings.category = ?
");

$stmt->bind_param("s", $category);
$stmt->execute();
$listings = $stmt->get_result();

$categoryInfo = [
    "Bakery Stop" => "Browser freshly baked goods, breads, and homemade treats from our catelogue of local bakers.",
    "Eat Now" => "Get quick meals and fast food deals for when you’re hungry on the go.",
    "Home Cooked" => "Feeling traditional or modern homemade meals? Grab them here prepared with real love by our local cooks.",
    "Sugar Delights" => "Sweet treats, desserts, cakes, and confectionery items at the reach of your grasp.",
    "Electronics" => "Explore our eletronic gadgets, devices, and tech accessories.",
    "Crafts" => "Handmade crafts, art, and antiques from our local creatives and entrepreneurs."
];

$category = $_GET['category'] ?? "Unknown Category";

?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo $category; ?> - ZanziFinds</title>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap">
</head>

<body>

<?php include 'navbar.php'; ?>

<h1 style="text-align:center; margin-top:20px; font-family:'Montserrat', sans-serif;">
    <?php echo $category; ?>
</h1>

<p style="text-align:center; font-size: 18px; font-weight: 300; max-width:500px; margin:0 auto; color:black;">
    <?php 
        echo $categoryInfo[$category] ?? "Browse products in this category.";
    ?>
</p>

<div class="product-grid">

<?php if($listings->num_rows > 0): ?>

    <?php while($row = $listings->fetch_assoc()): ?>

    <div class="product-card">

        <a href="product.php?id=<?php echo $row['id']; ?>" class="card-link">

            <img src="images/<?php echo $row['image']; ?>">

            <h3><?php echo $row['title']; ?></h3>

            <p>R<?php echo $row['price']; ?></p>

        </a>

		<button type="button"
		class="add-btn"
		onclick="addToCart('<?php echo $row['title']; ?>', '<?php echo $row['price']; ?>')">
		Add to Cart
		</button>

    </div>

    <?php endwhile; ?>

<?php else: ?>

    <div class="empty-state">
		<p>No listings found in this category.</p>
	</div>

<?php endif; ?>

</div>

<script>

function addToCart(title, price){

    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'title=' + encodeURIComponent(title) + '&price=' + encodeURIComponent(price)
    })
    .then(res => res.json())
    .then(data => {

        showPopup(data.message);

    })
    .catch(err => {
        showPopup("Something went wrong");
        console.log(err);
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
	popup.style.boxShadow = "0 4px 10px rgba(0,0,0,0.2)";

    document.body.appendChild(popup);

    setTimeout(() => popup.remove(), 2000);
}

</script>

</body>
</html>