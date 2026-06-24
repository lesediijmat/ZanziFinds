<?php 
include 'config.php'; 
?>

<?php
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $passwordRaw = $_POST['password'];

    if (strlen($passwordRaw) < 6) {
        $error = "Password must be at least 6 characters long.";
    }
    elseif (!preg_match("/[A-Za-z]/", $passwordRaw)) {
        $error = "Password must contain at least one letter.";
    }
    elseif (!preg_match("/[0-9]/", $passwordRaw)) {
        $error = "Password must contain at least one number.";
    }

    if (!$error) {

        $password = password_hash($passwordRaw, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users(fullname,email,password) VALUES(?,?,?)");
        $stmt->bind_param("sss", $name, $email, $password);

        if ($stmt->execute()) {
            header("Location: login.php?registered=1");
            exit;
        } else {
            if ($conn->errno == 1062) {
                $error = "Email already exists.";
            } else {
                $error = "Something went wrong. Try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Register</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>
<link rel="stylesheet" href="LogRegStyle.css">

<div class="box">
    <img src="images/MainLogoText.png" class="logo" alt="Website Logo">
    <h1> Create Account </h1>

    <?php if($error): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="fullname" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="button">Register</button>
		
		<a href="login.php" class="login-btn">Already have an account? Login</a>
    </form>
</div>
<img src="images/LogotextBB.png" class="site-name" alt="Website Logo">

</body>
</html>