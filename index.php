<?php
include 'config.php';
?>
<?php
$listings = $conn->query("
SELECT listings.*, sellers.business_name
FROM listings
LEFT JOIN sellers ON sellers.id = listings.seller_id
");

if(isset($_POST['business_name'])){

    if(!isset($_SESSION['user_id'])){
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $business_name = $_POST['business_name'];
    $description = $_POST['description'];

    $check = $conn->prepare("SELECT id FROM sellers WHERE user_id = ?");
    $check->bind_param("i", $user_id);
    $check->execute();
    $result = $check->get_result();

    if($result->num_rows == 0){

        $insert = $conn->prepare("
            INSERT INTO sellers (user_id, business_name, description)
            VALUES (?, ?, ?)
        ");

        $insert->bind_param("iss", $user_id, $business_name, $description);
        $insert->execute();

        header("Location: index.php?seller=success");
        exit();

    } else {
        header("Location: index.php?seller=exists");
        exit();
    }
}

?>


<!DOCTYPE html>
<html>
<head>
<title>ZanziFinds Marketplace</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
	
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="hero-section">
	<div class="hero-text">
		<h1> Welcome to ZanziFinds </h1> 
		<p> Find SA's hidden gems at the best prices</p>
	</div>
</div>

<div class="hero-actions">
    <a href="index2.php" class="view-all">
        View All Listings
    </a>
</div>

<div class="category-grid">
    <a href="category.php?category=Bakery Stop">
		<div class="category-card">
			<img src="images/bakerystop.png">
		</div>
	</a>
	<a href="category.php?category=Eat%20Now">
		<div class="category-card">
			<img src="images/Eatnow.png">
		</div>
	</a>
    <a href="category.php?category=Home Cooked">
		<div class="category-card">
			<img src="images/Homecooked.png">
		</div>	
	</a>
	<a href="category.php?category=Sugar Delights">
		<div class="category-card">
			<img src="images/Sugar_Delights.png">
		</div>
	</a>
	<a href="category.php?category=Electronics">
		<div class="category-card">
			<img src="images/Electronics.png">
		</div>
	</a>
	<a href="category.php?category=Crafts">
		<div class="category-card">
			<img src="images/crafts.png">
		</div>
	</a>
</div>

<h1 style="text-align: center">Latest Listings</h1>

<div class="product-grid">

<?php
$latest = $conn->query("
    SELECT * 
    FROM listings 
    ORDER BY id DESC 
    LIMIT 6
");
?>

<?php while($row = $latest->fetch_assoc()): ?>

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

<?php endwhile; ?>

</div>

<h1 class="section-title" style="text-align: center">Featured Sellers</h1>

<div class="seller-grid">

<?php
$stmt = $conn->prepare("
    SELECT s.id, s.business_name, s.description
    FROM sellers s
    ORDER BY s.id DESC
    LIMIT 6
");

$stmt->execute();
$result = $stmt->get_result();

while($row = $result->fetch_assoc()){
?>

    <div class="seller-card">

        <h3><?php echo $row['business_name']; ?></h3>

        <p>
            <?php 
            echo substr($row['description'], 0, 80); 
            ?>...
        </p>
		
		<a href="seller_details.php?id=<?php echo $row['id']; ?>" class="add-btn">
			View Seller
		</a>

    </div>

<?php } ?>

</div>
<section id="become-seller">
	<div class="become-seller-section">
		<h2>Want to start selling?</h2>
		<p>Create a seller profile and start listing your products today.</p>

		<button class="add-btn" onclick="openSellerModal()">
			Become a Seller
		</button>
	</div>
</section>

<div id="sellerDashboardModal" class="modal">
    <div class="modal-content">

        <span class="close-btn" onclick="closeSellerDashboardModal()">&times;</span>

        <iframe src="seller_dashboard.php" class="modal-frame"></iframe>

    </div>
</div>

<div id="cartModal" class="modal">

	<div class="modal-content">

		<span class="close-btn" onclick="closeCart()">&times;</span>

		<iframe id="cartFrame" src="cart.php" class="modal-frame"></iframe>

	</div>

</div>

<div id="sellerModal" class="modal">
    
    <div class="modal-content">

        <span class="close-btn" onclick="closeSellerModal()">&times;</span>

        <iframe src="seller.php" class="modal-frame"></iframe>

    </div>

</div>

<div id="productModal" class="modal">

    <div class="modal-content">

        <span class="close-btn" onclick="closeProductModal()">&times;</span>

        <iframe id="productFrame" class="modal-frame"></iframe>

    </div>

</div>    
    

<footer class="footer-bar">
	<p>
		<img src="images/LogotextBB.png" class="footerlogo" alt="Website Text Logo" height="40px">
    </p>
    <p>
		<?php if(isset($_SESSION['user_id'])){ ?>
			<a href="account_dashboard.php">Profile</a>
		<?php } else { ?>
			<a href="login.php">Profile</a>
		<?php } ?>
		<a href="#" onclick="openModal('aboutModal'); return false;">About Us</a>
        <a href="#" onclick="openModal('contactModal'); return false;">Contact Us</a>
        <a href="#" onclick="openModal('privacyModal'); return false;">Privacy Policy</a>
    </p>
	<hr width="70%"></hr>
	<p>
        © <?php echo date("Y"); ?> ZanziFinds
	</p>
</footer>

<div id="aboutModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal('aboutModal')">&times;</span>
        <h1 style="text-align: center; color: #32C8E3">About Us</h1>
        <p style="text-align: center; font-size: 20px" >
			We are a C-2-C marketplace that's SA's next game changer for 
			local communities and townships. The mission we are on is to 
			provide informal traders and entrepreneurs the platform to 
			trade securely and connect to consumers on a larger scale.
		</p>
		<p style="text-align: center; font-size: 20px" >
		    The risks we will be mitigating are unsafe transactions, 
			scams, and the lack of accessibility to potential buyers. We 
			offer sellers a platform to grow thier businesses and buyers 
			the ability to shop what they want, when they want, stress-free. 
		</p>
		<p style="text-align: center; font-size: 20px" >
		    Join us today and unlock the future of South Africa's informal
			traders and entreprenuers empowerment. Convenience is our middle
			name.
		</p>
		<p style="text-align: center; font-weight: 800">	
			© ZanziFinds. Today. Tomorrow. Forever. 
        </p>
		<img 
			src="images/MainLogoTextB.png" 
			class="logo" 
			style="display:block; margin:20px auto; max-width:180px; height:auto;"
		>
    </div>
</div>

<div id="contactModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal('contactModal')">&times;</span>
        <h1 style="text-align: center; color: #32C8E3">Contact Us</h1>

			<p style="text-align: center" >
				Email: LesediiJ.Mat@gmail.com
			</p>
			<p style="text-align: center" >
			  LinkedIn: <a href="https://linkedin.com" target="_blank" style="color: #32C8E3; text-decoration: underline;">linkedin.com/in/lesedi-matabologa-6849a4344</a>
			</p>
        	<p style="text-align: center" >
				GitHub: <a href="https://github.com" target="_blank" style="color: #32C8E3; text-decoration: underline;">github.com/lesediijmat/ZanziFinds</a>
			</p>
			<p style="text-align: center" >
				WhatsApp: 068 245 8571 (Mon–Fri, 9am–5pm)
			</p>
			<p style="text-align: center" >
				We reply within 24 hours.
			</p>
			<p style="text-align: center" >
				Reach out.!! We'd love to hear from you.
			</p>
			<p style="text-align: center; font-weight: 800" >	
				© ZanziFinds. Today. Tomorrow. Forever. 
			</p>
			<img 
				src="images/MainLogoTextB.png" 
				class="logo" 
				style="display:block; margin:20px auto; max-width:180px; height:auto;"
			>
	</div>
</div>

<div id="privacyModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal('privacyModal')">&times;</span>
        <h1 style="text-align: center; color: #32C8E3">Privacy Policy</h1>
        <p style="text-align: center; font-weight: bold">
            Information collected by ZanziFinds:
            <ul>
			<li>Fullname</li>
			<li>Email</li>
			<li>Phone Number</li>
			<li>Address</li>
			<li>Listing Photos</li>
			</ul>
		</p>
		<p style="text-align: center; font-weight: bold">
			Usage of information collected:
			<ul>
			<li>The connection of buyers and sellers</li>
			<li>Ensure secure transactions</li>
			</ul>
		</p>
		<p style="text-align: center; font-weight: bold">
			Data protection:
			<ul>
			<li>Our users personal data isn't for sale to third parties</li>
			<li>Secure authentication methods</li>
			</ul>
		</p>
		<p style="text-align: center; font-weight: 800">		
			© ZanziFinds. Today. Tomorrow. Forever. 
        </p>
		<img 
			src="images/MainLogoTextB.png" 
			class="logo" 
			style="display:block; margin:20px auto; max-width:180px; height:auto;"
		>
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

function openSellerModal(){
    document.getElementById("sellerModal").style.display = "flex";
}

function closeSellerModal(){
    document.getElementById("sellerModal").style.display = "none";
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

function openModal(id){
    document.getElementById(id).style.display = "flex";
}

function closeModal(id){
    document.getElementById(id).style.display = "none";
}

window.addEventListener("click", function(e){

    const modals = [
        "cartModal",
        "sellerModal",
        "productModal",
        "aboutModal",
        "contactModal",
        "privacyModal"
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

function openSellerDashboardModal(){
    document.getElementById("sellerDashboardModal").style.display = "flex";
}

function closeSellerDashboardModal(){
    document.getElementById("sellerDashboardModal").style.display = "none";
}

</script>

</body>
</html>
