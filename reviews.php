<?php
session_start();
include "config.php";

// Redirect if not logged in
if(empty($_SESSION['user'])){
    header("Location: index.php");
    exit();
}

// Fetch logged-in user info
$user_email = $_SESSION['user'];
$user = null;
$stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
$stmt->bind_param("s",$user_email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Function to get initials
function getInitials($name){
    $words = explode(" ", $name);
    $initials = "";
    foreach($words as $w){
        $initials .= strtoupper($w[0]);
    }
    return $initials;
}

// Sample motivational quotes (you can replace with DB fetch later)
$motivations = [
    ["quote"=>"The best way to find yourself is to lose yourself in the service of others.", "author"=>"Mahatma Gandhi"],
    ["quote"=>"Act as if what you do makes a difference. It does.", "author"=>"William James"],
    ["quote"=>"No one has ever become poor by giving.", "author"=>"Anne Frank"],
    ["quote"=>"Blood donation is a gift of life. Be a hero today!", "author"=>"Donate Life"],
    ["quote"=>"Small acts, when multiplied by millions of people, can transform the world.", "author"=>"Howard Zinn"]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Donate Life Connect</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{background:#f7f7f7;color:#333;}
.nav{display:flex;align-items:center;gap:15px;padding:18px 40px;background:white;box-shadow:0 2px 10px rgba(0,0,0,0.05);}
.logo{display:flex;align-items:center;gap:10px;font-weight:600;font-size:20px;flex-grow:1;}
.logo span{color:#e53935;}
.profile-btn{width:50px;height:50px;border-radius:50%;overflow:hidden;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:18px;cursor:pointer;border:2px solid #e53935;flex-shrink:0;margin-right:10px;}
.profile-btn img{width:100%;height:100%;object-fit:cover;}
.hero{padding:70px 40px;background:linear-gradient(90deg,#ffffff,#f5f6f8);}
.badge{background:#ffeaea;color:#e53935;padding:6px 14px;border-radius:20px;display:inline-block;font-size:14px;margin-bottom:15px;}
.hero h1{font-size:44px;line-height:1.2;font-weight:700;}
.hero h1 span{color:#e53935;}
.hero p{margin-top:15px;color:#666;max-width:520px;}
.buttons{margin-top:25px;display:flex;gap:15px;}
.btn{padding:12px 20px;border-radius:8px;border:none;font-weight:500;cursor:pointer;}
.btn-primary{background:#e53935;color:white;}
.btn-secondary{background:white;border:1px solid #ddd;}
.stats{display:flex;justify-content:space-around;background:#eef3f7;padding:35px;margin-top:40px;border-radius:12px;}
.stat{text-align:center;}
.stat-icon{width:45px;height:45px;background:#ffeaea;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:auto;color:#e53935;font-size:20px;margin-bottom:10px;}
.stat h3{font-size:20px;}
.stat p{font-size:13px;color:#777;}
.section{padding:50px 40px;}
.section-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px;}
.section-header h2{font-size:26px;}
.section-header a{color:#e53935;text-decoration:none;font-size:14px;}
.donor-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:20px;}
.card{background:white;border-radius:12px;padding:20px;border:1px solid #eee;display:flex;flex-direction:column;align-items:center;text-align:center;}
.blood{display:inline-block;background:#e53935;color:white;padding:6px 12px;border-radius:8px;font-weight:600;font-size:14px;margin-bottom:8px;}
.available{font-size:12px;color:#28a745;margin-bottom:8px;}
.profile-img{width:60px;height:60px;border-radius:50%;background:#e53935;color:white;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:18px;margin-bottom:10px;overflow:hidden;}
.profile-img img{width:100%;height:100%;object-fit:cover;}
.name{font-weight:600;margin-bottom:3px;}
.location{font-size:13px;color:#777;margin-bottom:3px;}
.distance{font-size:12px;color:#999;margin-bottom:8px;}
.contact{width:100%;padding:10px;border-radius:8px;border:1px solid #ddd;background:white;cursor:pointer;}
.contact:hover{background:#f7f7f7;}
.footer{background:white;margin-top:60px;border-top:1px solid #eee;}
.footer-container{max-width:1200px;margin:auto;padding:50px 40px;display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:30px;}
.footer-logo{color:#e53935;margin-bottom:10px;}
.footer-text{font-size:14px;color:#666;line-height:1.6;}
.footer-col h4{margin-bottom:12px;}
.footer-col a{display:block;text-decoration:none;color:#666;font-size:14px;margin-bottom:8px;}
.footer-col a:hover{color:#e53935;}
.footer-col p{font-size:14px;color:#666;margin-bottom:6px;}
.footer-bottom{border-top:1px solid #eee;text-align:center;padding:15px;font-size:13px;color:#777;}
#sidebarContainer{position:fixed;top:0;left:0;z-index:1000;}
@media(max-width:768px){.hero{padding:50px 20px;}.hero h1{font-size:32px;}.stats{flex-direction:column;gap:20px;}.section{padding:40px 20px;}.nav{padding:15px 20px;}}
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{background:#f7f7f7;color:#333;}
.nav{display:flex;align-items:center;gap:15px;padding:18px 40px;background:white;box-shadow:0 2px 10px rgba(0,0,0,0.05);}
.logo{display:flex;align-items:center;gap:10px;font-weight:600;font-size:20px;flex-grow:1;}
.logo span{color:#e53935;}
.profile-btn{width:50px;height:50px;border-radius:50%;overflow:hidden;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:18px;cursor:pointer;border:2px solid #e53935;flex-shrink:0;margin-right:10px;}
.profile-btn img{width:100%;height:100%;object-fit:cover;}
.hero{padding:70px 40px;background:linear-gradient(90deg,#ffffff,#f5f6f8);}
.badge{background:#ffeaea;color:#e53935;padding:6px 14px;border-radius:20px;display:inline-block;font-size:14px;margin-bottom:15px;}
.hero h1{font-size:44px;line-height:1.2;font-weight:700;}
.hero h1 span{color:#e53935;}
.hero p{margin-top:15px;color:#666;max-width:520px;}
.section{padding:50px 40px;}
.section-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px;}
.section-header h2{font-size:26px;}
.section-header a{color:#e53935;text-decoration:none;font-size:14px;}
.card-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:20px;}
.card{background:white;border-radius:12px;padding:20px;border:1px solid #eee;display:flex;flex-direction:column;align-items:center;text-align:center;}
.quote-text{font-size:16px;color:#555;font-style:italic;margin-bottom:12px;}
.quote-author{font-weight:600;color:#e53935;font-size:14px;}

/* Sidebar */
#sidebar{position:fixed;top:0;left:-260px;width:260px;height:100%;background:white;box-shadow:2px 0 10px rgba(0,0,0,0.08);transition:0.3s;padding:25px;z-index:1000;}
#sidebar.show{left:0;}
#sidebar .logo{margin-bottom:20px;}
#sidebar a{display:block;padding:12px 15px;margin-top:8px;border-radius:10px;text-decoration:none;color:#333;}
#sidebar a:hover{background:#f4f4f4;}

/* Footer */
.footer{background:white;margin-top:60px;border-top:1px solid #eee;}
.footer-container{max-width:1200px;margin:auto;padding:50px 40px;display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:30px;}
.footer-logo{color:#e53935;margin-bottom:10px;}
.footer-text{font-size:14px;color:#666;line-height:1.6;}
.footer-col h4{margin-bottom:12px;}
.footer-col a{display:block;text-decoration:none;color:#666;font-size:14px;margin-bottom:8px;}
.footer-col a:hover{color:#e53935;}
.footer-col p{font-size:14px;color:#666;margin-bottom:6px;}
.footer-bottom{border-top:1px solid #eee;text-align:center;padding:15px;font-size:13px;color:#777;}
</style>
</head>
<body>

<div id="sidebarContainer"></div>

<!-- NAVBAR -->
<div class="nav">
    <div id="profileBtn" class="profile-btn">
        <?php if(!empty($user['photo']) && file_exists("uploads/".$user['photo'])): ?>
            <img src="uploads/<?php echo $user['photo']; ?>" alt="Profile">
        <?php else: ?>
            <?php echo strtoupper(substr($user['fullname'],0,1)); ?>
        <?php endif; ?>
    </div>
    <div class="logo"><span>🩸</span> Donate Life Connect</div>
</div>

<!-- Hero -->
<section class="hero">
    <div class="badge">❤ Motivation</div>
    <h1>Stay Inspired, <span>Save Lives</span></h1>
    <p>Read these motivational quotes to keep your spirit high and inspire others to donate blood.</p>
</section>

<!-- Motivation Cards -->
<section class="section">
<div class="section-header"><h2>Motivational Quotes</h2></div>
<div class="card-grid">
<?php foreach($motivations as $m): ?>
    <div class="card">
        <div class="quote-text">"<?= htmlspecialchars($m['quote']); ?>"</div>
        <div class="quote-author">- <?= htmlspecialchars($m['author']); ?></div>
    </div>
<?php endforeach; ?>
</div>
</section>

<footer class="footer">
<div class="footer-container">
<div class="footer-col">
<h3 class="footer-logo">🩸 Donate Life Connect</h3>
<p class="footer-text">Helping people connect with blood donors quickly and safely. Every drop counts and together we can save lives.</p>
</div>
<div class="footer-col"><h4>Navigation</h4><a href="#">Home</a><a href="#">Search Donors</a><a href="#">Become a Donor</a><a href="#">Blood Banks</a></div>
<div class="footer-col"><h4>Account</h4><a href="profile.php">Profile</a></div>
<div class="footer-col"><h4>Contact</h4><p>📍 India</p><p>📧 support@lifeflow.com</p><p>📞 +91 9876543210</p></div>
</div>
<div class="footer-bottom"><p>© 2026 LifeFlow. All rights reserved.</p></div>
</footer>

<script>
fetch("home_sidebar.php")
.then(res => res.text())
.then(data => {
    document.getElementById("sidebarContainer").innerHTML = data;
    const sidebar = document.getElementById("sidebar");
    const profileBtn = document.getElementById("profileBtn");

    if(profileBtn && sidebar){
        profileBtn.addEventListener("click", () => {
            sidebar.classList.add("show");

            setTimeout(() => { sidebar.classList.remove("show"); }, 3000);
        });
    }
});

document.querySelectorAll(".contact").forEach(btn=>{
    btn.addEventListener("click",()=>{ alert("Contact feature can be connected with phone or email."); });
});
</script>

</body>
</html>