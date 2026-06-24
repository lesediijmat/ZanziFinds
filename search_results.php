<?php include 'config.php'; ?>

<?php
$query = $_GET['q'] ?? '';

$stmt = $conn->prepare("
    SELECT listings.*, users.fullname 
    FROM listings
    LEFT JOIN users ON users.id = listings.user_id
    WHERE listings.title LIKE ?
    OR users.fullname LIKE ?
");

$search = "%".$query."%";
$stmt->bind_param("ss", $search, $search);
$stmt->execute();

$listings = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>Search Results</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="product-grid">

<?php if($listings->num_rows > 0): ?>

    <?php while($row = $listings->fetch_assoc()): ?>

        <div class="product-card">

            <a href="product.php?id=<?php echo $row['id']; ?>">
                <img src="images/<?php echo $row['image']; ?>">
                <h3><?php echo $row['title']; ?></h3>
                <p>R<?php echo $row['price']; ?></p>
            </a>

            <form method="POST" action="add_to_cart.php">
                <input type="hidden" name="title" value="<?php echo $row['title']; ?>">
                <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                <button type="submit" class="add-btn">Add to Cart</button>
            </form>

        </div>

    <?php endwhile; ?>

<?php else: ?>

    <p style="padding:20px;">No results found.</p>

<?php endif; ?>

</div>

</body>
</html>