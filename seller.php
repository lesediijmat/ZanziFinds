<?php include 'config.php'; ?>

<?php
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT id FROM sellers WHERE user_id = ?
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$seller = $stmt->get_result()->fetch_assoc();

if(!$seller){
    die("<div style='text-align:center; margin-top:50px; font-family:Arial; color:white;'>
            <h2>You are not a seller yet</h2>
            <p>Please create a seller profile to continue.</p>
        </div>
    ");
}

$sellerId = $seller['id'];

if(isset($_POST['add_listing'])){

    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];

    $imageName = time().'_'.basename($_FILES['image']['name']);
    $target = "images/" . $imageName;

    if(move_uploaded_file($_FILES['image']['tmp_name'], $target)){

		$stmt = $conn->prepare("
			INSERT INTO listings (user_id, seller_id, title, description, price, category, image)
			VALUES (?, ?, ?, ?, ?, ?, ?)
		");

		$stmt->bind_param(
			"iissdss",
			$userId,
			$sellerId,
			$title,
			$description,
			$price,
			$category,
			$imageName
		);

        $stmt->execute();

		echo "
		<script>
			window.parent.location.reload();
		</script>
		";
		exit();

    } else {
        echo "Image upload failed";
    }
}
?>

<!DOCTYPE html>
<html>
<style>
body{
	font-family:Arial;
	background:#e1f2fc;
	margin:0;
}
.box{
	width:400px;
	margin:35px auto 5px auto;
	background:white;
	padding:30px 25px;
	border-radius:12px;
	border:2px solid #335469;
	text-align:center;
}
.logo{
	width:125px;
	display:block;
	margin:0 auto 10px auto;
}
input{
	width:90%;
	padding:10px;
	margin:0 auto;
	display:block;
	font-size:16px;
	border:1px solid #335469;
	border-radius:8px;
}
select {
	display:block;
    width: 90%;
    padding: 10px;
	margin:0 auto;
    border-radius: 8px;
    border: 2px solid #335469;
    background-color: white;
	font-size:16px;
    cursor: pointer;
}
select:hover {
    border-color: #999;
}
textarea{
	width:90%;
	padding:10px;
	font-size:16px;	
	margin:0 auto;
	display:block;
	border:1px solid #335469;
	border-radius:8px;
	resize:none;
	height:60px;
}
button{
	display:block;
	margin:0 auto 0 auto;
	padding:18px;
	width:90%;
	background:blue;
	color:white;
	font-size:18px;
	font-weight:bold;
	border-radius:12px;
	border:0;
}
button:hover {
    background: #0033cc;
    transform: scale(1.05);
    transition: 0.3s;
}
</style>

<body>
<div class="box">
	<img src="images/MainLogoText.png" class="logo" alt="Website Logo">
	<h1>Product Upload Form</h1>

	<form method="POST" enctype="multipart/form-data">
		<input type="text" name="title" placeholder="Product Name" required><br>
    
		<input type="number" name="price" placeholder="Price" required><br>
		
		<select name="category" required>
			<option value="" selected disabled hidden>Select Category</option>
			<option value="Home Cooked">Home Cooked</option>
			<option value="Electronics">Electronics</option>
			<option value="Eat Now">EatNow</option>
			<option value="Sugar Delights">Sugar Delights</option>
			<option value="Bakery Stop">Bakery Stop</option>
			<option value="Crafts">Crafts</option>
		</select><br>

		<textarea name="description" placeholder="Description" required></textarea><br>

		<input type="file" name="image" required><br>

		<button name="add_listing" class="button">Upload Product</button>
</form>

</div>

</body>
</html>