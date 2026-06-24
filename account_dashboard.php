<?php include 'config.php'; ?>

<?php
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$user = $result->fetch_assoc();

if(!$user){
    session_destroy();
    header("Location: login.php");
    exit;
}

$message = "";

if(isset($_POST['update_profile'])){

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE users SET fullname=?, email=? WHERE id=?");
    $stmt->bind_param("ssi", $fullname, $email, $user_id);

    $stmt->execute();
    $message = "Profile updated!";
}

if(isset($_POST['update_address'])){

    $address = $_POST['address'];
    $city = $_POST['city'];

    $stmt = $conn->prepare("UPDATE users SET address=?, city=? WHERE id=?");
    $stmt->bind_param("ssi", $address, $city, $user_id);

    $stmt->execute();
    $message = "Address updated!";
}

if(isset($_POST['update_password']) && !empty($_POST['password'])){

    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
    $stmt->bind_param("si", $password, $user_id);

    $stmt->execute();
    $message = "Password updated!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Account Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body{
    margin:0;
    font-family:Arial;
    background:#708090;
}

.page-content{
    flex:1;
}

.dashboard{
    max-width:1000px;
    margin:40px auto;
    display:flex;
    gap:20px;
	padding:0 15px;
}

.side-menu,
.back-card,
.delete-card{
    width:200px;
    background:#cacaca;
    padding:15px;
    border-radius:10px;
    height:fit-content;
    position:sticky;
    top:20px;
}

.back-card,
.delete-card{
    margin-top:15px;
}
.side-menu a,
.back-card a,
.delete-card a{
    display:block;
    padding:10px;
    text-decoration:none;
    color:black;
    border-radius:8px;
}

.side-menu a:hover,
.back-card a:hover{
    background:grey;
}

.delete-card a:hover{
    background:#FF2E2E;
}

.content{
    flex:1;
    background:#cacaca;
    padding:25px;
    border-radius:12px;
	box-shadow:0 4px 10px rgba(0,0,0,0.08);
}

.section{
    margin-bottom:30px;
    padding-bottom:20px;
    border-bottom:1px solid #eee;
}

input{
    width:100%;
    padding:10px;
    margin:8px 0;
    border:1px solid #ccc;
    border-radius:6px;
}

button{
    padding:10px 15px;
    border:none;
    background:#0077cc;
    color:white;
    border-radius:6px;
    cursor:pointer;
}

button:hover{
    background:#005fa3;
}

.msg{
    background:#e0ffe0;
    padding:10px;
    border-radius:6px;
    margin-bottom:20px;
}

.tab-link.active{
    background:#0077cc;
    color:white;
    border-radius:8px;
}

.logo{
    height: auto;
    width: auto;
    display: block;
    margin: 0 auto;
}

.modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.6);
    justify-content: center;
    align-items: center;
}

.modal-content {
    background: white;
    padding: 25px;
    border-radius: 12px;
    width: 350px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.modal-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

.btn-yes {
    background: red;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 6px;
    cursor: pointer;
}

.btn-no {
    background: green;
    border: none;
    padding: 10px 15px;
    border-radius: 6px;
    cursor: pointer;
}

input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-top: 10px;
    border-radius: 6px;
    border: 1px solid #ddd;
}
/* Mobile phones */
@media (max-width: 768px){

    .dashboard{
        flex-direction: column;
        margin: 15px auto;
        gap: 15px;
    }

    .left-column{
        width: 100%;
    }

    .side-menu,
    .back-card,
    .delete-card{
        width: 100%;
        box-sizing: border-box;
        position: static;
    }

    .content{
        width: 100%;
        box-sizing: border-box;
        padding: 15px;
    }

    .side-menu a,
    .back-card a,
    .delete-card a{
        text-align: center;
    }

    h2{
        font-size: 22px;
    }

    input,
    button{
        font-size: 16px;
    }
    
	input{
    width:90%;
    padding:10px;
    margin:8px 0;
    border:1px solid #ccc;
    border-radius:6px;
    }

    input[type="password"] {
    width: 90%;
    padding: 10px;
    margin-top: 10px;
    border-radius: 6px;
    border: 1px solid #ddd;
	}
    
    .logo{
        width: 90%;
        max-width: 200px;
    }

    .modal-content{
        width: 90%;
        max-width: 350px;
    }
}
    
</style>
</head>

<body>

<?php if($message): ?>
    <div class="msg"><?php echo $message; ?></div>
<?php endif; ?>
<div class="page-content">
	<div class="dashboard">

		<div class="left-column">

			<div class="side-menu">

				<a href="#" class="tab-link active" onclick="openTab(event, 'profile')">
					Profile
				</a>

				<a href="#" class="tab-link" onclick="openTab(event, 'security')">
					Password
				</a>

				<a href="#" class="tab-link" onclick="openTab(event, 'address')">
					Address
				</a>

			</div>

			<div class="back-card">
				<a href="index.php" >
					Back to Website
				</a>
			</div>
			
			<div class="delete-card">
				<a href="#" onclick="openModal(); return false;">
					Delete Account
				</a>
			</div>

		</div>

		<div class="content">

			<div id="profile" class="section tab-content">
				<h2>Profile</h2>

				<form method="POST">
					<input type="text" name="fullname" value="<?php echo $user['fullname']; ?>" required>
					<input type="email" name="email" value="<?php echo $user['email']; ?>" required>

					<button type="submit" name="update_profile">Update Profile</button>
				</form>
			</div>

			<div id="security" class="section tab-content" style="display:none;">
				<h2>Change Password</h2>

				<form method="POST">
					<input type="password" name="password" placeholder="New Password" required>

					<button type="submit" name="update_password">Update Password</button>
				</form>
			</div>

			<div id="address" class="section tab-content" style="display:none;">
				<h2>Address</h2>

				<form method="POST">
					<input type="text" name="address" value="<?php echo $user['address'] ?? ''; ?>" placeholder="Street Address">
					<input type="text" name="city" value="<?php echo $user['city'] ?? ''; ?>" placeholder="City">

					<button type="submit" name="update_address">Save Address</button>
				</form>
			</div>

		</div>

	</div>
	
</div>

<div>
	<img src="images/MainLogoTextB.png" class="logo" alt="Website Text Logo">
</div>

<div id="deleteModal" class="modal">

    <div class="modal-content">

        <div id="step1">
            <h3>Are you sure?</h3>
            <p>You want to delete your ZanziFinds account?</p>

            <div class="modal-actions">
                <button onclick="closeModal()" class="btn-no">No</button>
                <button onclick="showPasswordStep()" class="btn-yes">Yes</button>
            </div>
        </div>

        <form id="step2" action="acc_deletion.php" method="POST" style="display:none;">
            <h3>Confirm Password</h3>

            <input type="password" name="password" placeholder="Enter your password" required>

            <div class="modal-actions">
                <button type="button" onclick="closeModal()" class="btn-no">Cancel</button>
                <button type="submit" class="btn-yes">Delete</button>
            </div>
        </form>

    </div>
</div>

<script>
function openTab(event, tabName){

    const tabs = document.querySelectorAll(".tab-content");
    tabs.forEach(tab => tab.style.display = "none");

    const links = document.querySelectorAll(".tab-link");
    links.forEach(link => link.classList.remove("active"));

    document.getElementById(tabName).style.display = "block";

    event.currentTarget.classList.add("active");
}
</script>

<script>
function openModal() {
    document.getElementById("deleteModal").style.display = "flex";
}

function closeModal() {
    document.getElementById("deleteModal").style.display = "none";
    document.getElementById("step1").style.display = "block";
    document.getElementById("step2").style.display = "none";
}

function showPasswordStep() {
    document.getElementById("step1").style.display = "none";
    document.getElementById("step2").style.display = "block";
}

window.onclick = function(event) {
    let modal = document.getElementById("deleteModal");
    if (event.target === modal) {
        closeModal();
    }
}
</script>

</body>
</html>