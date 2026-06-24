<?php
include 'config.php';

$error = '';
$success = '';

if(!isset($_GET['token'])){
    die("Invalid token");
}

$token = $_GET['token'];

$stmt = $conn->prepare("
    SELECT id
    FROM users
    WHERE reset_token=?
    AND token_expiry > NOW()
    LIMIT 1
");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows != 1){
    die("Token expired or invalid.");
}

$user = $result->fetch_assoc();

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $password = $_POST['password'];
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $update = $conn->prepare("
        UPDATE users
        SET password=?,
            reset_token=NULL,
            token_expiry=NULL
        WHERE id=?
    ");

    $update->bind_param("si", $hashed, $user['id']);

    if($update->execute()){
        $success = "Password updated successfully.";
    }else{
        $error = "Something went wrong.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Reset Password</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="LogRegStyle.css">
</head>
<body>

<div class="box">

    <h1>Reset Password</h1>

    <?php if($error): ?>
        <p style="color:red;">
            <?php echo $error; ?>
        </p>
    <?php endif; ?>

    <?php if($success): ?>
        <p style="color:green;">
            <?php echo $success; ?>
        </p>

        <a href="login.php">
            Back to Login
        </a>
    <?php else: ?>

    <form method="POST">

        <input
            type="password"
            name="password"
            placeholder="New Password"
            required
        >

        <button type="submit" class="button">
            Update Password
        </button>

    </form>

    <?php endif; ?>

</div>

</body>
</html>