<?php
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, business_name FROM sellers WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$seller_result = $stmt->get_result();

$seller_ids = [];
$sellers = [];

while($row = $seller_result->fetch_assoc()){
    $seller_ids[] = $row['id'];
    $sellers[] = $row;
}

if(empty($seller_ids)){
    echo "<h2 style='text-align:center;color:white;margin-top:50px;'>
        You need a seller profile first.
    </h2>";
    exit;
}

if(isset($_POST['delete_id'])){
    $id = intval($_POST['delete_id']);

    $del = $conn->prepare("DELETE FROM listings WHERE id = ?");
    $del->bind_param("i", $id);
    $del->execute();

    echo json_encode(["status"=>"success","id"=>$id]);
    exit;
}
?>

<style>

body{
    margin:0;
    background:transparent !important;
}

h1{
    text-align:center;
    margin-bottom:25px;
    color:black;
    font-size:32px;
}

h2{
    text-align:center;
    color:black;
}

.info{
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    width:90%;
    margin:10px auto; 
}

.add-listing{
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

.add-listing:hover{
    transform:translateY(-2px);
}

.modal{
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    justify-content:center;
    align-items:center;
}

.modal-content{
    padding:15px;
    border-radius:12px;
    text-align:center;
    font-size:18px;
    color: white;
    width:300px;
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow:0 5px 20px rgba(0,0,0,0.25);
}

.cancel-btn{
    padding:10px;
    font-size:18px;
    border:none;
    background:gray;
    color:black;
    border-radius:6px;
    cursor:pointer;
}

.confirm-delete-btn{
    padding:10px;
    font-size:18px;
    border:none;
    background:red;
    color:black;
    border-radius:6px;
    cursor:pointer;
    text-decoration:none;
}
</style>

<!DOCTYPE html>
<html>
<body>

<link rel="stylesheet" href="style.css">

<div class="info">

    <h1>Seller Dashboard</h1>

    <h2>Welcome, <?php echo htmlspecialchars($sellers[0]['business_name']); ?></h2>

    <hr>

    <a href="seller.php" class="add-listing">
        + Add New Listing
    </a>

    <hr>

    <h2>My Listings</h2>

    <div class="product-grid">

    <?php
    $ids = implode(",", array_map("intval", $seller_ids));

    $result = $conn->query("
        SELECT * 
        FROM listings 
        WHERE seller_id IN ($ids)
    ");

    if($result->num_rows == 0){
        echo "<p style='color:white;text-align:center;'>You have no listings yet.</p>";
    }

    while($row = $result->fetch_assoc()){
    ?>

        <div class="product-card" id="listing-<?php echo $row['id']; ?>">

            <img src="images/<?php echo $row['image']; ?>">

            <h3><?php echo $row['title']; ?></h3>

            <h4>R <?php echo $row['price']; ?></h4>

            <a href="edit.php?id=<?php echo $row['id']; ?>">Edit</a> |
            <a href="#" class="delete-btn" onclick="event.preventDefault(); openDeleteModal(<?php echo $row['id']; ?>)">
               Delete
            </a>

        </div>

    <?php } ?>

    </div>
</div>

<div id="deleteModal" class="modal">
    <div class="modal-content">
        <h3>Confirm Delete</h3>
        <p>Are you sure you want to delete this listing?</p>

        <div class="modal-actions">
            <button onclick="closeDeleteModal()" class="cancel-btn">Cancel</button>
            <button id="confirmDeleteBtn" class="confirm-delete-btn">Delete</button>
        </div>
    </div>
</div>

<script>
let selectedId = null;

function openDeleteModal(id){
    selectedId = id;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal(){
    document.getElementById('deleteModal').style.display = 'none';
    selectedId = null;
}

document.addEventListener("DOMContentLoaded", function () {

    document.getElementById('confirmDeleteBtn').addEventListener('click', function(){

        if(!selectedId) return;

        fetch('seller_dashboard.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'delete_id=' + selectedId
        })
        .then(res => res.json())
        .then(data => {

            if(data.status === "success"){

                let card = document.getElementById("listing-" + data.id);
                if(card){
                    card.remove();
                }

                closeDeleteModal();

            } else {
                alert(data.message || "Delete failed");
            }

        })
        .catch(err => {
            console.log(err);
            alert("Something went wrong");
        });

    });

});

window.onclick = function(e){
    let modal = document.getElementById('deleteModal');
    if(e.target === modal){
        closeDeleteModal();
    }
}
</script>

</body>
</html>