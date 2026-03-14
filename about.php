<?php
// about.php
include "config.php"; // Include if you need dynamic content
?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About Us - Donate Life Connect</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}

body{
background:#f7f7f7;
color:#333;
}

/* NAVBAR */
.nav{
display:flex;
align-items:center;
gap:15px;
padding:18px 40px;
background:white;
box-shadow:0 2px 10px rgba(0,0,0,0.05);
position:relative;
}

.menu-btn{
font-size:24px;
border:none;
background:none;
cursor:pointer;
flex-shrink:0;
}

.nav .logo{
flex-grow:1;
text-align:center;
font-weight:600;
font-size:20px;
color:#e53935;
}

/* HERO */
.hero{
padding:70px 40px;
background:linear-gradient(90deg,#ffffff,#f5f6f8);
}

.hero h1{
font-size:44px;
line-height:1.2;
font-weight:700;
color:#e53935;
}

.hero p{
margin-top:15px;
color:#666;
max-width:600px;
}

/* ABOUT SECTION */
.section{
padding:50px 40px;
}

.section h2{
font-size:32px;
margin-bottom:20px;
color:#e53935;
}

.section p{
font-size:16px;
color:#555;
line-height:1.6;
margin-bottom:25px;
}

/* FOOTER */
.footer{
background:white;
margin-top:60px;
border-top:1px solid #eee;
}

.footer-container{
max-width:1200px;
margin:auto;
padding:50px 40px;
display:grid;
grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
gap:30px;
}

.footer-logo{
color:#e53935;
margin-bottom:10px;
}

.footer-text{
font-size:14px;
color:#666;
line-height:1.6;
}

.footer-col h4{
margin-bottom:12px;
}

.footer-col a{
display:block;
text-decoration:none;
color:#666;
font-size:14px;
margin-bottom:8px;
}

.footer-col a:hover{
color:#e53935;
}

.footer-col p{
font-size:14px;
color:#666;
margin-bottom:6px;
}

.footer-bottom{
border-top:1px solid #eee;
text-align:center;
padding:15px;
font-size:13px;
color:#777;
}

/* SIDEBAR CONTAINER */
#sidebarContainer{
position:fixed;
top:0;
left:0;
z-index:1000;
}

@media(max-width:768px){
.hero{
padding:50px 20px;
}
.hero h1{
font-size:32px;
}
.section{
padding:40px 20px;
}
.nav{
padding:15px 20px;
}
.nav .logo{
font-size:18px;
}
.menu-btn{
font-size:22px;
}
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div id="sidebarContainer"></div>

<!-- NAVBAR -->
<div class="nav">
    <button id="menuBtn" class="menu-btn">☰</button>
    <div class="logo">🩸 Donate Life Connect</div>
</div>

<!-- HERO -->
<section class="hero">
<h1>About Donate Life Connect</h1>
<p>LifeFlow is a platform dedicated to connecting blood donors with those in need. Our mission is to save lives by creating a reliable network of donors across multiple cities, ensuring that help is always close at hand.</p>
</section>

<!-- ABOUT US SECTION -->
<section class="section">
<h2>Our Mission</h2>
<p>We strive to make blood donation accessible, safe, and efficient. By connecting verified donors with recipients, we reduce the time it takes to find compatible blood and save precious lives.</p>

<h2>What We Do</h2>
<p>Donate Life Connect helps individuals, hospitals, and organizations find blood donors nearby. We provide real-time donor availability, city-wise listings, and blood group-specific search functionality.</p>

<h2>Our Values</h2>
<p>We believe in compassion, responsibility, and community service. Every donation counts, and together, we can make a difference in the lives of countless people.</p>
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

    // Menu button opens sidebar
    document.getElementById("menuBtn")?.addEventListener("click", ()=>{
        sidebar.classList.add("show");

        // Auto-hide after 3 seconds
        setTimeout(() => {
            sidebar.classList.remove("show");
        }, 2000); // 3000ms = 3 seconds
    });
});
    </script>

</body>
</html>