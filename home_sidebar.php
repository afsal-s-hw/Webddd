<?php
session_start();
include "config.php";

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user'];
$stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
$stmt->bind_param("s",$user_email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

function getInitial($name){
    return strtoupper(substr($name,0,1));
}
?>

<!-- Sidebar -->
<div id="sidebar" class="sidebar">

    <!-- Profile inside sidebar -->
    <div class="sidebar-profile" id="sidebarProfile">
        <?php if(!empty($user['photo'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($user['photo']); ?>" alt="Profile">
        <?php else: ?>
            <span><?php echo getInitial($user['fullname']); ?></span>
        <?php endif; ?>
        <div class="name"><?php echo htmlspecialchars($user['fullname']); ?></div>
    </div>

    

    <div class="sidebar-content">

        <p class="section-title">Navigation</p>

        <a href="home.php">Home</a>
        <a href="about.php">About Us</a>
        <a href="faq.php">FAQ Questions</a>
        <a href="search.php">Search Donor</a>
        <a href="bloodbank.php">BloodBanks</a>
        <a href="reviews.php">Motivation</a>
        <a href="chat.php">messages</a>
        <p class="section-title">Account</p>

        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>

    </div>

</div>

<style>
/* Sidebar */
.sidebar{
    position:fixed;
    left:-260px;
    top:0;
    width:260px;
    height:100%;
    background:white;
    box-shadow:2px 0 10px rgba(0,0,0,0.08);
    transition:0.3s;
    z-index:1000;
    padding:25px;
}

.sidebar.show{ left:0; }

.logo{
    display:flex;
    align-items:center;
    gap:10px;
    font-weight:600;
    font-size:20px;
    color:#e53935;
    margin-bottom:20px;
}

.section-title{
    margin-top:20px;
    font-size:14px;
    color:#999;
}

.sidebar-content a{
    display:block;
    padding:12px 15px;
    margin-top:8px;
    border-radius:10px;
    text-decoration:none;
    color:#333;
    cursor:pointer;
}

.sidebar-content a:hover{ background:#f4f4f4; }
.sidebar-content a.active{ background:#efefef; font-weight:500; }

/* Profile button floating */
.floating-btn{
    position:fixed;
    bottom:25px;
    right:25px;
    width:60px;
    height:60px;
    border-radius:50%;
    background:#e53935;
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:bold;
    font-size:22px;
    cursor:pointer;
    box-shadow:0 6px 18px rgba(0,0,0,0.3);
    z-index:1100;
}

.floating-btn img{ width:100%; height:100%; object-fit:cover; border-radius:50%; }
.floating-btn span{ font-size:22px; }

/* Sidebar profile at top */
.sidebar-profile{
    display:flex;
    flex-direction:column;
    align-items:center;
    margin-bottom:20px;
    cursor:pointer;
}

.sidebar-profile img{
    width:60px; height:60px;
    border-radius:50%;
    margin-bottom:8px;
}

.sidebar-profile span{
    width:60px; height:60px;
    border-radius:50%;
    background:#e53935;
    display:flex;
    align-items:center;
    justify-content:center;
    color:white;
    font-weight:bold;
    font-size:22px;
    margin-bottom:8px;
}

.sidebar-profile .name{
    font-weight:500;
    font-size:16px;
    color:#333;
    text-align:center;
}
</style>

<script>
const floatingBtn = document.getElementById('floatingBtn');
const sidebar = document.getElementById('sidebar');
const sidebarProfile = document.getElementById('sidebarProfile');

// Toggle sidebar on floating button click
floatingBtn.addEventListener('click', () => {
    sidebar.classList.toggle('show');
});

// Hide sidebar when clicking profile inside sidebar
sidebarProfile.addEventListener('click', () => {
    sidebar.classList.remove('show');
});

// Close sidebar if clicked outside
document.addEventListener('click', function(e){
    if(!sidebar.contains(e.target) && !floatingBtn.contains(e.target)){
        sidebar.classList.remove('show');
    }
});
</script>