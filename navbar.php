<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<nav class="navbar">

    <div class="navbar-top">

        <div class="navi-left">
		
            <a href="index.php">
                <img src="images/LogotextBB.png" class="textlogo" alt="Website Text Logo">
            </a>
			
        </div>

        <div class="navi-right">
		
            <a href="index.php" class="nav-icon">
                <i class="fa-solid fa-house"></i>
            </a>
			
            <a href="#" class="nav-icon cart-icon" onclick="openCart(event)">
                <i class="fas fa-shopping-cart"></i>
            </a>
			
			<div class="hamburger" onclick="toggleSidebar()">
				☰
			</div>
			
        </div>

    </div>
	<div class="navbar-bottom">
	
		<div id="searchBar" class="search-bar">

			<form action="search_results.php" method="GET" class="search-form">

				<input 
					type="text"
					id="searchInput"
					name="q"
					placeholder="Search ZanziFinds"
					value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"
					required
				>

				<button type="submit">
					Search
				</button>
				
			</form>	

		</div>
			
	</div>

	<div id="sidebar" class="sidebar">

		<span class="close-sidebar" onclick="toggleSidebar()">&times;</span>

		<a href="account_dashboard.php">
			<i class="fas fa-user"></i> Account Dashboard
		</a>

		<a href="#" onclick="openSellerDashboardModal()">
			<i class="fas fa-store"></i> Seller Dashboard
		</a>
		
		<a href="admin.php">
			<i class="fas fa-user-shield"></i> Admin Dashboard
		</a>
		
		<div class="sidebar-bottom">
			<a href="login.php">
				<i class="fas fa-sign-in-alt"></i> Log-In
			</a>

			<a href="logout.php">
				<i class="fas fa-sign-out-alt"></i> Log-Out
			</a>
		</div>

	</div>

<div id="overlay" class="overlay" onclick="toggleSidebar()"></div>
	
</nav>

<div id="searchResults" class="search-results"></div>


<script>

function toggleSidebar(){
    document.getElementById("sidebar").classList.toggle("active");
    document.getElementById("overlay").classList.toggle("active");
}

</script>

<script>
window.addEventListener("scroll", function(){
    const navbar = document.querySelector(".navbar");

    if(window.scrollY > 10){
        navbar.classList.add("scrolled");
    } else {
        navbar.classList.remove("scrolled");
    }
});
</script>
