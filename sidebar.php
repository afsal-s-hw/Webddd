<?php
session_start();
include "config.php";

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Get current user info
$user_email = $_SESSION['user'];
$user = null;

$sql = "SELECT * FROM users WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
}

// Function to get initials
function getInitials($name){
    $words = explode(" ", $name);
    $initials = "";
    foreach($words as $w){
        $initials .= strtoupper($w[0]);
    }
    return $initials;
}
?>
<!-- Sidebar HTML -->
<div class="sidebar">
    <div class="sidebar-header">
        <?php if (!empty($user['photo'])): ?>
            <img src="<?php echo $user['photo']; ?>" alt="Avatar" class="avatar">
        <?php else: ?>
            <div class="avatar"><?php echo getInitials($user['fullname']); ?></div>
        <?php endif; ?>
        <div class="user-name"><?php echo htmlspecialchars($user['fullname']); ?></div>
    </div>
    <ul class="sidebar-menu">
        <li><a href="home.php">🏠 Home</a></li>
        <li><a href="blood_requests.php">🩸 Blood Requests</a></li>
        <li><a href="donors.php">🧑‍⚕️ Donors</a></li>
        <li><a href="messages_page.php">💬 Messages</a></li>
        <li><a href="logout.php">🚪 Logout</a></li>
    </ul>
</div>

<style>
body { margin:0; font-family:'Poppins',sans-serif; }
.sidebar {
    width:220px; background:#fff; height:100vh; position:fixed;
    box-shadow:2px 0 10px rgba(0,0,0,0.05); padding-top:20px;
}
.sidebar-header { text-align:center; margin-bottom:20px; }
.avatar {
    width:60px; height:60px; border-radius:50%; background:#e53935;
    color:white; display:flex; align-items:center; justify-content:center;
    font-weight:bold; font-size:20px; margin:auto;
}
.user-name { margin-top:10px; font-weight:500; font-size:16px; color:#333; }
.sidebar-menu { list-style:none; padding:0; }
.sidebar-menu li { margin:15px 0; }
.sidebar-menu li a {
    text-decoration:none; color:#333; padding:10px 20px; display:block;
    border-radius:6px; transition:0.2s;
}
.sidebar-menu li a:hover { background:#f3f3f3; }
</style>