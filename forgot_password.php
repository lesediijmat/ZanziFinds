<?php
include 'config.php';

$message = '';
$link = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1){

        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $update = $conn->prepare("
            UPDATE users
            SET reset_token=?, token_expiry=?
            WHERE email=?
        ");
        $update->bind_param("sss", $token, $expiry, $email);
        $update->execute();

        $link = "http://localhost/ZanziFinds/reset_password.php?token=".$token;

        $message = "Password reset link generated:";
    }
    else{
        $message = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Forgot Password</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="LogRegStyle.css">
</head>
<body>

<div class="box">

    <h1>Forgot Password</h1>

    <?php if($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <?php if($link): ?>
        <p>
            <a href="<?php echo $link; ?>">
                Reset Password
            </a>
        </p>
    <?php endif; ?>

    <form method="POST">

        <input
            type="email"
            name="email"
            placeholder="Enter Email"
            required
        >

        <button type="submit" class="button">
            Send Reset Link
        </button>

    </form>

</div>

</body>
</html>