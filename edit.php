<?php include 'config.php'; ?>

<?php
$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM listings WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if(isset($_POST['update'])){

    $title = $_POST['title'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("
        UPDATE listings 
        SET title=?, price=?, category=?, description=?
        WHERE id=?
    ");

    $stmt->bind_param(
        "sdssi",
        $title,
        $price,
        $category,
        $description,
        $id
    );

    $stmt->execute();

    echo "
    <script>
        window.parent.closeEditModal();
        window.parent.location.reload();
    </script>
    ";
    exit;
}
?>
<style>
*{
    box-sizing:border-box;
}

body{
    margin:0;
    padding:20px;
    font-family:Arial, sans-serif;
}

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
}

form{
    display:flex;
    flex-direction:column;
	gap:10px;
}

input,
textarea,
select{
    width:100%;
    padding:14px;
    border:2px solid #c7dff0;
    border-radius:10px;
    font-size:15px;
    transition:0.3s;
}

input:focus,
textarea:focus{
    outline:none;
    border-color:#0077cc;
    box-shadow:0 0 8px rgba(0,119,204,0.2);
}

textarea{
    min-height:80px;
    resize:none;
}

button{
    background:#0077cc;
    color:white;
    border:none;
    padding:14px;
    border-radius:10px;
    font-size:16px;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    background:#005fa3;
    transform:translateY(-2px);
}

</style>
<!DOCTYPE html>
<html>
<body>
<h1>Edit Listing</h1>

	<div class="info">
	
		<form method="POST">
			<input type="text" name="title" value="<?php echo $data['title']; ?>">
			<input type="number" name="price" value="<?php echo $data['price']; ?>">

			<select name="category" required>
				<option value="Home Cooked"
					<?php if($data['category'] == 'Home Cooked') echo 'selected'; ?>>
					Home Cooked
				</option>
				
				<option value="Electronics"
					<?php if($data['category'] == 'Electronics') echo 'selected'; ?>>
					Electronics
				</option>
				
				<option value="Eat Now"
					<?php if($data['category'] == 'Eat Now') echo 'selected'; ?>>
					Eat Now
				</option>
				
				<option value="Sugar Delights"
					<?php if($data['category'] == 'Sugar Delights') echo 'selected'; ?>>
					Sugar Delights
				</option>
				
				<option value="Bakery Stop"
					<?php if($data['category'] == 'Bakery Stop') echo 'selected'; ?>>
					Bakery Stop
				</option>
				
				<option value="Crafts"
					<?php if($data['category'] == 'Crafts') echo 'selected'; ?>>
					Crafts
				</option>
				
			</select>

			<textarea name="description"><?php echo $data['description']; ?></textarea>

			<button name="update">Update</button>
		</form>
		
	</div>

</body>
</html>