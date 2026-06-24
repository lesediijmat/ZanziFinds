<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$allowed_roles = [
    'super_admin',
    'user_admin',
    'listing_admin'
];

if (!$user || !in_array($user['role'], $allowed_roles)) {
    header("Location: index.php");
    exit;
}

$isSuperAdmin   = ($user['role'] === 'super_admin');
$isUserAdmin    = ($user['role'] === 'user_admin');
$isListingAdmin = ($user['role'] === 'listing_admin');
?>

<?php
$listingsCount = $conn->query("SELECT COUNT(*) as total FROM listings")->fetch_assoc()['total'];

$usersCount = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];

$ordersCount = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];

$revenue = $conn->query("SELECT SUM(total) as total FROM orders")->fetch_assoc()['total'];
if(!$revenue) $revenue = 0;

if (isset($_GET['delete'])) {

    if (!$isListingAdmin && !$isSuperAdmin) {
        die("Unauthorized");
    }

    $id = (int)$_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM listings WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: admin.php");
    exit;
}

$listings = $conn->query("
    SELECT listings.*, sellers.business_name 
    FROM listings 
    LEFT JOIN sellers ON sellers.id = listings.seller_id
");

if(isset($_GET['fetch_edit'])){

    $id = $_GET['fetch_edit'];

    $result = $conn->query("SELECT * FROM listings WHERE id=$id");
    $data = $result->fetch_assoc();

    echo json_encode($data);
    exit;
}

if (isset($_POST['update'])) {

    if (!$isListingAdmin && !$isSuperAdmin) {
        die("Unauthorized: You cannot update listings.");
    }

    $id = (int)$_POST['edit_id'];
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

    header("Location: admin.php");
    exit;
}
if (isset($_GET['delete_user'])) {

    if (!$isUserAdmin && !$isSuperAdmin) {
        die("Unauthorized");
    }

    $id = (int)$_GET['delete_user'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: admin.php#users");
    exit;
}

$categoryRevenue = [];

$result = $conn->query("
    SELECT category,
           COUNT(*) as total_listings,
           SUM(price) as revenue
    FROM listings
    GROUP BY category
");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categoryRevenue[] = $row;
    }
}
?>

<style>
    
html {
    scroll-behavior:smooth;
}
    
body{
    justify-content:center;
    align-items:center;
    background:#32C8E3;
}

.admin-nav{
    width:90%;
    margin:20px auto;
    display:flex;
    justify-content:flex-start;
}

.back-site-btn{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:12px 18px;
    background:white;
    color:#333;
    text-decoration:none;
    border-radius:12px;
    font-weight:600;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
    transition:0.3s;
}

.back-site-btn:hover{
    transform:translateY(-2px);
}

h1 {
	margin:30px;
	text-align:center;
	font-family:'Open Sans', sans-serif;
	font-weight:700;
}

.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(150px,1fr));
    gap:15px;
    margin:20px;
}

.stat-box{
    background:white;
    text-decoration:none;
    color:inherit;
    padding:20px;
    border-radius:12px;
    text-align:center;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
    transition:0.3s;
}

.stat-box:hover{
    transform: translateY(-3px);
}

.stat-box h3{
    margin:0;
    font-size:28px;
    color:#0077cc;
}

.stat-box p{
    margin:5px 0 0;
    color:black;
    font-size:16px;
}

.stat-box i{
    font-size:30px;
    margin-bottom:10px;
    display:block;
    color:#0077cc;
}

.stat-box:nth-child(1) i { 
	color:dark blue;
}

.stat-box:nth-child(2) i { 
	color:red;
}

.stat-box:nth-child(3) i { 
	color:orange; 
}

.stat-box:nth-child(4) i { 
	color:green; 
}
.table-wrapper{
    width:100%;
    overflow-x:auto;
    -webkit-overflow-scrolling:touch;
}

table{
    width:80%;
    margin:40px auto;
    border-collapse: collapse;
    background: rgba(50, 200, 227, 0.15);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid black;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

td{
    text-align:center;
    vertical-align:middle;
}

tr:nth-child(even){
    background: rgba(255,255,255,0.4);
}

.action-buttons{
    display:flex;
    flex-direction: column;
    gap:8px;
    align-items:center;
}

.edit-btn, .delete-btn{
    padding:8px 12px;
	width:50px;
    text-align:center;
    border-radius:6px;
    border:1px solid black;
    color:white;
    font-size:13px;
    font-weight:bold;
}

.edit-btn{
    background:blue;
    text-decoration:none;	
}

.delete-btn{
    background:red;
    text-decoration:none;	
}

.modal{
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;

    /* soft white overlay instead of dark */
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
    width:300px;

    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);

    box-shadow:0 5px 20px rgba(0,0,0,0.25);
}

.edit-modal-content{
    padding:0;
    border-radius:18px;
    width:65%;
    max-width:800px;

    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);

    box-shadow:0 10px 35px rgba(0,0,0,0.25);
    position:relative;
    overflow:hidden;
}

.modal-actions{
    margin-top:20px;
    display:flex;
    justify-content:space-between;
}

.cancel-btn{
    padding:10px;
    border:none;
    background:gray;
	color:black;
    border-radius:6px;
    cursor:pointer;
}

.confirm-delete-btn{
    padding:10px;
    background:red;
    color:black;
    border-radius:6px;
    text-decoration:none;
}

.edit-popup{
    width:auto;
	min-width:60%;
    height:auto;
    padding:0;
    overflow:hidden;
    border-radius:18px;
    background:#e1f2fc;
    position:relative;
}

.close-edit-btn{
    position:absolute;
    top:15px;
    right:15px;
    border:none;
    border-radius:50%;
    background:none;
    color:black;
    cursor:pointer;
    font-size:25px;
    z-index:1000;
    transition:0.3s;
}

</style>

<!DOCTYPE html>
<html>
<head>
<title> Admin Panel</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>

<h1>ADMINSTRATION PANEL</h1>

<div class="admin-nav">
    <a href="index.php" class="back-site-btn">
        <i class="fas fa-arrow-left"></i>
        Back to Website
    </a>
</div>

<div class="stats">

    <a href="#listings" class="stat-box">
        <i class="fas fa-box"></i>
        <h3><?php echo $listingsCount; ?></h3>
        <p>Listings</p>
    </a>

    <a href="#users" class="stat-box">
        <i class="fas fa-users"></i>
        <h3><?php echo $usersCount; ?></h3>
        <p>Users</p>
    </a>

    <a href="#orders" class="stat-box">
        <i class="fas fa-shopping-cart"></i>
        <h3><?php echo $ordersCount; ?></h3>
        <p>Orders</p>
    </a>

    <a href="#revenue" class="stat-box">
        <i class="fas fa-coins"></i>
        <h3>R <?php echo number_format($revenue,2); ?></h3>
        <p>Revenue</p>
    </a>

</div>
    
<div id="listings">
    <h2 style="text-align:center; margin-top:40px;">Listings</h2>
    
    <div class="table-wrapper">
    <table border="1" cellpadding="10">
        <tr>
            <th>Image</th>
            <th>Title</th>
            <th>Price</th>
            <th>Category</th>
            <th>Seller</th>
            <th>Manage</th>
        </tr>

        <?php while($row = $listings->fetch_assoc()): ?>
        <tr>
            <td><img src="images/<?php echo $row['image']; ?>" width="80" ></td>
            <td><?php echo $row['title']; ?></td>
            <td>R<?php echo $row['price']; ?></td>
            <td><?php echo $row['category']; ?></td>
            <td><?php echo $row['business_name']; ?></td>

            <td>
                <div class="action-buttons">
                    <a href="#"
                       class="edit-btn"
                       onclick="openEditModal(<?php echo $row['id']; ?>)">
                       <i class="fas fa-edit"></i> Edit
                    </a>

                    <a href="#" 
                        class="delete-btn" 
                        onclick="openModal(<?= $row['id'] ?>, 'listing')">
                        <i class="fas fa-trash"></i> Delete
                    </a>

                </div>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    </div>
</div>
    
<div id="users">
    <h2 style="text-align:center; margin-top:60px;">Users</h2>

    <div class="table-wrapper">
        <table border="1" cellpadding="10">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Manage</th>
            </tr>

            <?php
            $users = $conn->query("SELECT id, fullname, email, role FROM users");

            while($u = $users->fetch_assoc()):
            ?>

            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= $u['fullname'] ?></td>
                <td><?= $u['email'] ?></td>
                <td><?= $u['role'] ?></td>

                <td>
                    <?php if($isUserAdmin || $isSuperAdmin): ?>
                        <a href="admin.php?delete_user=<?= $u['id'] ?>"
                           class="delete-btn"
                           onclick="openModal(<?= $u['id'] ?>, 'user')">
                           <i class="fas fa-trash"></i> Delete
                        </a>
                    <?php else: ?>
                        <span style="color:gray;">No Access</span>
                    <?php endif; ?>
                </td>
            </tr>

            <?php endwhile; ?>
        </table>
    </div>
</div>    
    
<div style="width:80%; margin:40px auto;">
    <h2 style="text-align:center; margin-top:60px;">Revenue per Category</h2>
    <canvas id="categoryChart"></canvas>
</div>   
    
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <h3>Confirm Delete</h3>
        <p>Are you sure you want to delete this listing?</p>

        <div class="modal-actions">
            <button onclick="closeModal()" class="cancel-btn">Cancel</button>
            <a id="confirmDeleteBtn" href="#" class="confirm-delete-btn">Delete</a>
        </div>
    </div>
</div>

<div id="editModal" class="modal">
    <div class="modal-content edit-modal-content">

        <div style="text-align:right; padding:10px;">
            <button onclick="closeEditModal()" class="close-edit-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <iframe id="editFrame"
                src=""
                width="100%"
                height="500"
                style="border:none; border-radius:12px;">
        </iframe>

    </div>
</div>
    

    
<script>
function openModal(id, type){
    document.getElementById('deleteModal').style.display = 'flex';

    let url = "";

    if(type === "listing"){
        url = "admin.php?delete=" + id;
    }

    if(type === "user"){
        url = "admin.php?delete_user=" + id;
    }

    document.getElementById('confirmDeleteBtn').href = url;
}

function closeModal(){
    document.getElementById('deleteModal').style.display = 'none';
}

window.onclick = function(e){

    let deleteModal = document.getElementById('deleteModal');
    let editModal = document.getElementById('editModal');

    if(e.target === deleteModal){
        closeModal();
    }

    if(e.target === editModal){
        closeEditModal();
    }
}

function openEditModal(id){
    document.getElementById('editModal').style.display = 'flex';

    document.getElementById('editFrame').src =
        "edit.php?id=" + id;
}

function closeEditModal(){
    document.getElementById('editModal').style.display = 'none';

    document.getElementById('editFrame').src = "";
}
</script>

<script>
const labels = <?= json_encode(array_column($categoryRevenue, 'category')); ?>;
const revenue = <?= json_encode(array_column($categoryRevenue, 'revenue')); ?>;

new Chart(document.getElementById('categoryChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Revenue per Category (R)',
            data: revenue,
            backgroundColor: 'rgba(255, 0, 0, 0.7)',
            borderColor: 'rgba(255, 0, 0, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
</body>
</html>
