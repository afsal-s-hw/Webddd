<?php
// faq.php
session_start();
include "config.php";

if(empty($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user'];
$user = null;

$sql = "SELECT * FROM users WHERE email='$user_email'";
$result = $conn->query($sql);
if($result->num_rows > 0){
    $user = $result->fetch_assoc();
}

// Function to get initials
function getInitials($name){
    $words = explode(" ", $name);
    $initials = "";
    foreach($words as $w){
        if(!empty($w)) $initials .= strtoupper($w[0]);
    }
    return $initials;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FAQ - Donate Life Connect</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{background:#f7f7f7;color:#333;line-height:1.6;}
a{text-decoration:none;color:#e53935;transition:0.3s;}
a:hover{color:#c62828;}

/* NAVBAR */
.nav{display:flex;align-items:center;gap:15px;padding:18px 40px;background:white;box-shadow:0 2px 10px rgba(0,0,0,0.05);position:relative;}
.logo{flex-grow:1;text-align:center;font-weight:600;font-size:20px;color:#e53935;}
.profile-btn{width:50px;height:50px;border-radius:50%;overflow:hidden;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:18px;cursor:pointer;border:2px solid #e53935;flex-shrink:0;margin-right:10px;transition:0.3s;}
.profile-btn:hover{transform:scale(1.1);}
.profile-btn img{width:100%;height:100%;object-fit:cover;}

/* HERO */
.hero{padding:70px 40px;background:linear-gradient(90deg,#ffffff,#f5f6f8);}
.hero h1{font-size:44px;line-height:1.2;font-weight:700;color:#e53935;}
.hero p{margin-top:15px;color:#666;max-width:600px;text-align:center;margin-left:auto;margin-right:auto;}

/* FAQ Section */
.section{padding:50px 40px;}
.section h2{font-size:32px;margin-bottom:20px;color:#e53935;text-align:center;}
.faq-item{background:white;border-radius:12px;padding:20px;border:1px solid #eee;margin-bottom:20px;}
.faq-question{font-weight:600;cursor:pointer;color:#e53935;display:flex;justify-content:space-between;align-items:center;}
.faq-answer{margin-top:10px;font-size:16px;color:#555;line-height:1.6;display:none;}

/* FOOTER */
.footer{background:white;margin-top:60px;border-top:1px solid #eee;}
.footer-container{max-width:1200px;margin:auto;padding:50px 40px;display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:30px;}
.footer-logo{color:#e53935;margin-bottom:10px;font-size:22px;font-weight:700;}
.footer-text{font-size:14px;color:#666;line-height:1.6;}
.footer-col h4{margin-bottom:12px;}
.footer-col a{display:block;text-decoration:none;color:#666;font-size:14px;margin-bottom:8px;}
.footer-col a:hover{color:#e53935;}
.footer-col p{font-size:14px;color:#666;margin-bottom:6px;}
.footer-bottom{border-top:1px solid #eee;text-align:center;padding:15px;font-size:13px;color:#777;}

/* SIDEBAR CONTAINER */
#sidebarContainer{position:fixed;top:0;left:0;z-index:1000;}

/* Responsive */
@media(max-width:768px){
.hero{padding:50px 20px;}
.hero h1{font-size:32px;}
.section{padding:40px 20px;}
.nav{padding:15px 20px;}
.logo{font-size:18px;}
.profile-btn{width:45px;height:45px;font-size:16px;}
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div id="sidebarContainer"></div>

<!-- NAVBAR -->
<div class="nav">
    <div id="profileBtn" class="profile-btn">
        <?php if(!empty($user['photo']) && file_exists("uploads/".$user['photo'])): ?>
            <img src="uploads/<?php echo $user['photo']; ?>" alt="Profile">
        <?php else: ?>
            <?php echo getInitials($user['fullname']); ?>
        <?php endif; ?>
    </div>
    <div class="logo">🩸 Donate Life Connect</div>
</div>

<!-- HERO -->
<section class="hero">
<h1>Frequently Asked Questions</h1>
<p>Find answers to the most common questions about blood donation and LifeFlow.</p>
</section>

<!-- FAQ SECTION -->
<section class="section">
<h2>FAQ</h2>

<div class="faq-item">
<div class="faq-question">What is Donate Life Connect? <span>+</span></div>
<div class="faq-answer">Donate Life Connect is a platform connecting verified blood donors with recipients in need across multiple cities.</div>
</div>

<div class="faq-item">
<div class="faq-question">How can I become a donor? <span>+</span></div>
<div class="faq-answer">You can register on our platform, provide your blood group, and verify your details to appear in our donor list.</div>
</div>

<div class="faq-item">
<div class="faq-question">Is my personal information safe? <span>+</span></div>
<div class="faq-answer">Yes. Your data is protected and only shared with recipients or hospitals when necessary.</div>
</div>

<div class="faq-item">
<div class="faq-question">How do I search for donors? <span>+</span></div>
<div class="faq-answer">Use our search feature by city or blood group to find donors nearby quickly.</div>
</div>

<div class="faq-item">
<div class="faq-question">Do I need an account to request blood? <span>+</span></div>
<div class="faq-answer">No, you can view donor information without an account, but registering helps you contact donors directly.</div>
</div>

</section>

<!-- FOOTER -->
<footer class="footer">
<div class="footer-container">
<div class="footer-col">
<h3 class="footer-logo">🩸 Donate Life Connect</h3>
<p class="footer-text">
Helping people connect with blood donors quickly and safely. Every drop counts and together we can save lives.
</p>
</div>
<div class="footer-col">
<h4>Navigation</h4>
<a href="index.php">Home</a>
<a href="search.php">Search Donors</a>
<a href="about.php">About Us</a>
<a href="faq.php">FAQ</a>
<a href="contact.php">Contact</a>
</div>
<div class="footer-col">
<h4>Account</h4>
<a href="login.php">Login</a>
<a href="register.php">Register</a>
</div>
<div class="footer-col">
<h4>Contact</h4>
<p>📍 India</p>
<p>📧 support@lifeflow.com</p>
<p>📞 +91 9876543210</p>
</div>
</div>
<div class="footer-bottom">
<p>© 2026 Donate Life Connect. All rights reserved.</p>
</div>
</footer>

<script>
/* Load Sidebar */
fetch("home_sidebar.php")
.then(res => res.text())
.then(data => {
    document.getElementById("sidebarContainer").innerHTML = data;
    const sidebar = document.getElementById("sidebar");
    const profileBtn = document.getElementById("profileBtn");
    if(profileBtn && sidebar){
        profileBtn.addEventListener("click", ()=>{
            sidebar.classList.toggle("show");
        });
    }
});

/* FAQ Toggle */
document.querySelectorAll(".faq-question").forEach(q=>{
q.addEventListener("click", ()=>{
    const answer = q.nextElementSibling;
    const symbol = q.querySelector("span");
    if(answer.style.display === "block"){
        answer.style.display = "none";
        symbol.textContent = "+";
    } else {
        answer.style.display = "block";
        symbol.textContent = "−";
    }
});
});
</script>

</body>
</html>