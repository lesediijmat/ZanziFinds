<?php
include 'config.php';

if(!isset($_SESSION['user_id'])){
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT fullname, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $business_name = trim($_POST['business_name']);
    $description = trim($_POST['description']);

    if(!empty($business_name) && !empty($description)) {

        $stmt = $conn->prepare("INSERT INTO sellers (user_id, business_name, description) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $business_name, $description);
        $stmt->execute();

        header("Location: seller.php?success=1");
        exit;
    }
}
?>

<style>

h1{
    text-align:center;
    margin-bottom:25px;
    color:black;
    font-size:32px;
}

.info{
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 16px;
	padding: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    width:90%;
	margin:0 auto; 
}

.seller-form{
    display:flex;
    flex-direction:column;
	gap:10px;
}


.seller-form input,
.seller-form textarea,
.seller-form select{
    width:100%;
    padding:14px;
    border:2px solid #c7dff0;
    border-radius:10px;
    font-size:15px;
    transition:0.3s;
}

.seller-form input:focus,
.seller-form textarea:focus{
    outline:none;
    border-color:#0077cc;
    box-shadow:0 0 8px rgba(0,119,204,0.2);
}

.seller-form textarea{
    min-height:100px;
    resize:none;
}

.seller-form button{
    background:#4D32E3;
	color:white;
    border:none;
    padding:14px;
    border-radius:10px;
    font-size:16px;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

.seller-form button:hover{
    background:#4D32E3;
    transform:translateY(-2px);
}
</style>

<!DOCTYPE html>
<html>
<body>
<h1>Create Seller Profile</h1>

	<div class="info">
	
		<form method="POST" class="seller-form">

			<div class="seller-user-info">
				<p><strong>Full Name:</strong> <?php echo $user['fullname']; ?></p>
				<p><strong>Email:</strong> <?php echo $user['email']; ?></p>
			</div>

			<label><strong>Business Name</strong></label>
			<input type="text" name="business_name" required>

			<label><strong>Description</strong></label>
			<textarea name="description" required></textarea>

			<button type="submit">
				Create Seller Profile
			</button>

		</form>
	
	</div>

</body>
</html>