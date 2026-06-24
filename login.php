<?php
include 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];

            header("Location: index.php");
            exit;

        } else {
            $error = "Incorrect password";
        }

    } else {
        $error = "Account not found";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>
<link rel="stylesheet" href="LogRegStyle.css">

<div class="box">
	<img src="images/MainLogoText.png" class="logo" alt="Website Logo">
    <h1> Login </h1>

    <?php if(isset($_GET['registered'])): ?>
        <p align = center style="color:green;"><b>Registration successful. Please login.</b></p>
    <?php endif; ?>

    <?php if($error): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
		<input type="password" id="password" name="password" placeholder="Password" required>

		<a href="forgot_password.php" class="forgot-link">
			Forgot Password?
		</a>

		<button type="submit" class="button">Login</button>
		
		<a href="register.php" class="register-btn">New to Zanzi Finds? Register Now</a>
    </form>
</div>
<img src="images/LogotextBB.png" class="site-name" alt="Website Logo">
</body>
</html>