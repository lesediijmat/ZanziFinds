<?php
include 'config.php';

if(!isset($_GET['id'])){
    echo "Seller not found";
    exit;
}

$seller_id = $_GET['id'];

$stmt = $conn->prepare("
    SELECT s.business_name, s.description, u.fullname
    FROM sellers s
    JOIN users u ON s.user_id = u.id
    WHERE s.id = ?
");

$stmt->bind_param("i", $seller_id);
$stmt->execute();
$seller = $stmt->get_result()->fetch_assoc();

if(!$seller){
    echo "Seller not found";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $seller['business_name']; ?></title>
    <link rel="stylesheet" href="style.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="seller-details">
    <h1 style="text-align: center; font-size: 48px; font-weight: 800"><?php echo $seller['business_name']; ?></h1>
    <p style="text-align: center; font-size: 20px"><?php echo $seller['description']; ?></p>
    <p style="text-align: center; font-size: 20px""><strong>Owner:</strong> <?php echo $seller['fullname']; ?></p>
</div>

<div class="product-grid">

<?php
$stmt = $conn->prepare("
    SELECT l.*
    FROM listings l
    JOIN sellers s ON l.user_id = s.user_id
    WHERE s.id = ?
");

$stmt->bind_param("i", $seller_id);
$stmt->execute();
$products = $stmt->get_result();

if($products->num_rows == 0){
    echo "<p style='text-align:center;'>No products yet.</p>";
}

while($row = $products->fetch_assoc()){
?>
<div class="product-card">

    <a href="#" class="card-link"
       onclick="openProductModal(<?php echo $row['id']; ?>)">

        <img src="images/<?php echo $row['image']; ?>">

        <h3><?php echo $row['title']; ?></h3>

        <p>R<?php echo $row['price']; ?></p>

    </a>

    <button
        type="button"
        class="add-btn"
        onclick="addToCart(<?php echo $row['id']; ?>)">
        Add to Cart
    </button>

</div>
<?php
}
?>

</div>
<div id="cartModal" class="modal">

	<div class="modal-content">

		<span class="close-btn" onclick="closeCart()">&times;</span>

		<iframe id="cartFrame" src="cart.php" class="modal-frame"></iframe>

	</div>

</div>

<div id="productModal" class="modal">

    <div class="modal-content">

        <span class="close-btn" onclick="closeProductModal()">&times;</span>

        <iframe id="productFrame" class="modal-frame"></iframe>

    </div>

</div>

<div id="sellerDashboardModal" class="modal">
    <div class="modal-content">

        <span class="close-btn" onclick="closeSellerDashboardModal()">&times;</span>

        <iframe src="seller_dashboard.php" class="modal-frame"></iframe>

    </div>
</div>

<script>

function addToCart(id){

    const formData = new FormData();
    formData.append("product_id", id);

    fetch('add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        console.log("SERVER RESPONSE:", data);

        if (data.status === "success") {
            showPopup(data.message);

			const frame = document.getElementById("cartFrame");
			frame.src = "";
			setTimeout(() => {
				frame.src = "cart.php?refresh=" + Date.now();
			}, 50);
        } else {
            showPopup(data.message || "Error adding to cart");
        }

    })
    .catch(err => {
        console.log("FETCH ERROR:", err);
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
	popup.style.boxShadow = "0 4px 10px rgba(0,0,0,0.2)";

    document.body.appendChild(popup);

    setTimeout(() => popup.remove(), 2000);
}

function openCart(event){
    event.preventDefault();
    document.getElementById("cartModal").style.display = "flex";
}

function closeCart(){
    document.getElementById("cartModal").style.display = "none";
}



function openProductModal(id){
    document.getElementById("productFrame").src = "product.php?id=" + id;
    document.getElementById("productModal").style.display = "flex";
}

function closeProductModal(){
    document.getElementById("productModal").style.display = "none";
    document.getElementById("productFrame").src = "";
}

function openSellerDashboardModal(){
    document.getElementById("sellerDashboardModal").style.display = "flex";
}

function closeSellerDashboardModal(){
    document.getElementById("sellerDashboardModal").style.display = "none";
}

function openModal(id){
    document.getElementById(id).style.display = "flex";
}

function closeModal(id){
    document.getElementById(id).style.display = "none";
}

window.addEventListener("click", function(e){

    const modals = [
        "cartModal",
        "productModal",
		"sellerModal",
    ];

    modals.forEach(function(id){

        const modal = document.getElementById(id);

        if(modal && e.target === modal){
            modal.style.display = "none";

            
            if(id === "productModal"){
                document.getElementById("productFrame").src = "";
            }
        }

    });

});
</script>

</body>
</html>