<?php
session_start();
include "config.php";

// Redirect if not logged in
if(empty($_SESSION['user'])){
    header("Location: index.php");
    exit();
}

$user_email = $_SESSION['user'];

// Fetch user info
$stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch donor info if donor
$donor = null;
if($user['donor'] != 'no'){
    $stmt2 = $conn->prepare("SELECT * FROM donors WHERE email=? LIMIT 1");
    $stmt2->bind_param("s", $user_email);
    $stmt2->execute();
    $donor = $stmt2->get_result()->fetch_assoc();
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $fullname = $_POST['fullname'] ?? $user['fullname'];
    $email = $_POST['email'] ?? $user['email'];

    // Password update
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

    // Photo upload
    $filename = null;
    if(isset($_FILES['photo']) && $_FILES['photo']['tmp_name']){
        $filename = time().'_'.basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], "uploads/".$filename);
    }

    // --- Update users table ---
    $fields = "fullname=?, email=?";
    $params = [$fullname, $email];
    $types = "ss";

    if($password){
        $fields .= ", password=?";
        $types .= "s";
        $params[] = $password;
    }
    if($filename){
        $fields .= ", photo=?";
        $types .= "s";
        $params[] = $filename;
    }

    $fields .= " WHERE id=?";
    $types .= "i";
    $params[] = $user['id'];

    $sql = "UPDATE users SET $fields";
    $stmt3 = $conn->prepare($sql);
    $stmt3->bind_param($types, ...$params);
    $stmt3->execute();

    // --- Update donors table if donor ---
    if($user['donor'] != 'no' && $donor){
        $bloodgroup = $_POST['bloodgroup'];
        $city = $_POST['city'];
        $phone = $_POST['phone'];
        $age = $_POST['age'];
        $weight = $_POST['weight'];
        $last_donation = $_POST['last_donation'];

        $fields2 = "fullname=?, email=?, bloodgroup=?, city=?, phone=?, age=?, weight=?, last_donation=?";
        $params2 = [$fullname, $email, $bloodgroup, $city, $phone, $age, $weight, $last_donation];
        $types2 = "sssssiis";

        if($filename){
            $fields2 .= ", photo=?";
            $types2 .= "s";
            $params2[] = $filename;
        }

        $fields2 .= " WHERE id=?";
        $types2 .= "i";
        $params2[] = $donor['id'];

        $sql2 = "UPDATE donors SET $fields2";
        $stmt4 = $conn->prepare($sql2);
        $stmt4->bind_param($types2, ...$params2);
        $stmt4->execute();
    }

    header("Location: profile.php");
    exit();
}

// Function to get initials for default profile image
function getInitials($name){
    $words = explode(" ", $name);
    $initials = "";
    foreach($words as $w){
        $initials .= strtoupper($w[0]);
    }
    return $initials;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile - Donate Life Connect</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
body{margin:0;padding:0;font-family:'Poppins',sans-serif;background:#f7f7f7;color:#333;}
a{text-decoration:none;color:#333;}
.nav{display:flex;align-items:center;gap:15px;padding:18px 40px;background:white;box-shadow:0 2px 10px rgba(0,0,0,0.05);}
.logo{display:flex;align-items:center;gap:10px;font-weight:600;font-size:20px;flex-grow:1;}
.profile-btn{width:50px;height:50px;border-radius:50%;overflow:hidden;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:18px;cursor:pointer;border:2px solid #e53935;flex-shrink:0;margin-right:10px;}
.profile-btn img{width:100%;height:100%;object-fit:cover;}
#sidebar{position:fixed;top:0;left:-260px;width:260px;height:100%;background:white;box-shadow:2px 0 10px rgba(0,0,0,0.08);transition:0.3s;padding:25px;z-index:1000;}
#sidebar.show{left:0;}
#sidebar .logo{margin-bottom:20px;}
#sidebar a{display:block;padding:12px 15px;margin-top:8px;border-radius:10px;text-decoration:none;color:#333;}
#sidebar a:hover{background:#f4f4f4;}
.section{padding:50px 40px;}
.section-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px;}
.section-header h2{font-size:26px;}
.profile-form{max-width:500px;margin:auto;display:flex;flex-direction:column;gap:15px;background:white;padding:25px;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,0.05);}
.profile-form input[type=text],
.profile-form input[type=email],
.profile-form input[type=password],
.profile-form input[type=number],
.profile-form input[type=date],
.profile-form select{padding:12px 15px;border-radius:8px;border:1px solid #ddd;font-size:14px;}
.profile-form button{padding:12px 15px;border-radius:8px;border:none;background:#e53935;color:white;font-weight:600;cursor:pointer;}
.profile-img-edit{width:120px;height:120px;border-radius:50%;background:#e53935;color:white;font-weight:600;font-size:32px;display:flex;align-items:center;justify-content:center;margin:auto;overflow:hidden;position:relative;margin-bottom:20px;}
.profile-img-edit img{width:100%;height:100%;object-fit:cover;}
.profile-img-edit input[type=file]{position:absolute;bottom:0;left:0;width:100%;opacity:0;cursor:pointer;height:100%;}
.footer{background:white;margin-top:60px;border-top:1px solid #eee;}
.footer-container{max-width:1200px;margin:auto;padding:50px 40px;display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:30px;}
.footer-logo{color:#e53935;margin-bottom:10px;font-weight:600;font-size:18px;}
.footer-text{font-size:14px;color:#666;line-height:1.6;}
.footer-col h4{margin-bottom:12px;font-weight:600;}
.footer-col a{display:block;text-decoration:none;color:#666;font-size:14px;margin-bottom:8px;}
.footer-col a:hover{color:#e53935;}
.footer-bottom{border-top:1px solid #eee;text-align:center;padding:15px;font-size:13px;color:#777;}
</style>
</head>
<body>

<div id="sidebar">
    <div class="logo"><span>🩸</span> <?= htmlspecialchars($user['fullname']) ?></div>
    <a href="home.php">Home</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<div class="nav">
    <div id="profileBtn" class="profile-btn">
        <?php if(!empty($user['photo']) && file_exists("uploads/".$user['photo'])): ?>
            <img src="uploads/<?= $user['photo'] ?>" alt="Profile">
        <?php else: ?>
            <?= getInitials($user['fullname']) ?>
        <?php endif; ?>
    </div>
    <div class="logo"><span>🩸</span> Donate Life Connect</div>
</div>

<section class="section">
    <div class="section-header">
        <h2>My Profile</h2>
    </div>
    <form method="POST" enctype="multipart/form-data" class="profile-form">
        <div class="profile-img-edit">
            <?php if(!empty($user['photo']) && file_exists("uploads/".$user['photo'])): ?>
                <img src="uploads/<?= $user['photo'] ?>" alt="Profile">
            <?php else: ?>
                <?= getInitials($user['fullname']) ?>
            <?php endif; ?>
            <input type="file" name="photo">
        </div>

        <input type="text" name="fullname" placeholder="Full Name" value="<?= htmlspecialchars($user['fullname']) ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($user['email']) ?>" required>
        <input type="password" name="password" placeholder="Password (leave blank to keep)">

        <?php if($user['donor'] != 'no' && $donor): ?>
            <select name="bloodgroup" required>
                <option value="">Select Blood Group</option>
                <?php foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg): ?>
                    <option value="<?= $bg ?>" <?= ($donor['bloodgroup']==$bg)?'selected':'' ?>><?= $bg ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="city" placeholder="City" value="<?= htmlspecialchars($donor['city']) ?>" required>
            <input type="text" name="phone" placeholder="Phone" value="<?= htmlspecialchars($donor['phone']) ?>" required>
            <input type="number" name="age" placeholder="Age" value="<?= htmlspecialchars($donor['age']) ?>" required>
            <input type="number" name="weight" placeholder="Weight" value="<?= htmlspecialchars($donor['weight']) ?>" required>
            <input type="date" name="last_donation" placeholder="Last Donation Date" value="<?= htmlspecialchars($donor['last_donation']) ?>">
        <?php endif; ?>

        <button type="submit">Update Profile</button>
    </form>
</section>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-col">
            <div class="footer-logo"><span>🩸</span> Donate Life Connect</div>
            <p class="footer-text">Connecting donors and patients to save lives.</p>
        </div>
        <div class="footer-col">
            <h4>Links</h4>
            <a href="home.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="footer-bottom">© 2026 Donate Life Connect. All rights reserved.</div>
</footer>

<script>
const profileBtn = document.getElementById("profileBtn");
const sidebar = document.getElementById("sidebar");
profileBtn.addEventListener("click",()=>{ sidebar.classList.toggle("show"); });
</script>

</body>
</html>