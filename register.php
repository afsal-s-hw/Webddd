<?php
include "config.php";

$msg="";

if(isset($_POST['register'])){

$fullname=$_POST['fullname'];
$email=$_POST['email'];
$password=$_POST['password'];
$confirm=$_POST['confirm'];

if($password!=$confirm){
$msg="Passwords do not match";
}else{

$hash=password_hash($password,PASSWORD_DEFAULT);

$check=$conn->query("SELECT * FROM users WHERE email='$email'");

if($check->num_rows>0){
$msg="Email already registered";
}else{

$sql="INSERT INTO users(fullname,email,password)
VALUES('$fullname','$email','$hash')";

if($conn->query($sql)){
$msg="Account created successfully";
}else{
$msg="Error creating account";
}

}

}

}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Register - LifeFlow</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}

body{
background:#f3f4f6;
}

/* HEADER */

.nav{
display:flex;
align-items:center;
padding:18px 40px;
background:white;
box-shadow:0 2px 10px rgba(0,0,0,0.05);
}

.logo{
display:flex;
align-items:center;
gap:10px;
font-weight:600;
font-size:20px;
}

.logo span{
color:#e53935;
}

/* MAIN */

.main{
display:flex;
justify-content:center;
align-items:center;
padding:80px 20px;
}

/* REGISTER CARD */

.container{
text-align:center;
}

.icon{
width:55px;
height:55px;
background:#fdecec;
border-radius:50%;
display:flex;
align-items:center;
justify-content:center;
margin:auto;
color:#e53935;
font-size:22px;
margin-bottom:10px;
}

h2{
font-weight:600;
}

.subtitle{
color:#777;
font-size:14px;
margin-bottom:25px;
}

.card{
background:white;
padding:30px;
border-radius:12px;
width:340px;
box-shadow:0 3px 15px rgba(0,0,0,0.05);
text-align:left;
}

label{
font-size:13px;
color:#444;
}

.input{
width:100%;
padding:10px;
border:1px solid #ddd;
border-radius:8px;
margin-top:6px;
margin-bottom:15px;
font-size:14px;
}

.btn{
width:100%;
background:#e53935;
color:white;
border:none;
padding:12px;
border-radius:8px;
font-weight:500;
cursor:pointer;
}

.btn:hover{
background:#d32f2f;
}

.msg{
margin-bottom:15px;
font-size:14px;
text-align:center;
color:green;
}

.login{
text-align:center;
margin-top:15px;
font-size:13px;
}

.login a{
color:#e53935;
text-decoration:none;
}

/* HELP FLOAT BUTTON */

.help-button{
position:fixed;
bottom:25px;
right:25px;
width:60px;
height:60px;
background:#e53935;
color:white;
border-radius:50%;
display:flex;
align-items:center;
justify-content:center;
font-size:24px;
cursor:pointer;
box-shadow:0 8px 20px rgba(0,0,0,0.2);
z-index:999;
}

/* HELP BOX */

.help-box{
position:fixed;
bottom:85px;
right:20px;
width:180px;
background:white;
border-radius:8px;
box-shadow:0 5px 15px rgba(0,0,0,0.15);
overflow:hidden;
display:none;
z-index:999;
animation:slideUp .3s ease;
}

.help-header{
background:#e53935;
color:white;
padding:6px 10px;
font-size:12px;
display:flex;
justify-content:space-between;
align-items:center;
}

.help-content{
padding:8px;
font-size:12px;
color:#555;
line-height:1.4;
    }

.help-header button{
background:none;
border:none;
color:white;
font-size:16px;
cursor:pointer;
}



@keyframes slideUp{
from{
transform:translateY(20px);
opacity:0;
}
to{
transform:translateY(0);
opacity:1;
}
}

</style>
</head>

<body>

<!-- HEADER -->

<div class="nav">
<div class="logo">
<span>🩸</span> LifeFlow
</div>
</div>

<!-- MAIN -->

<div class="main">

<div class="container">

<div class="icon">🩸</div>

<h2>Create Account</h2>

<div class="subtitle">Join the LifeFlow community</div>

<div class="card">

<?php if($msg!=""){ ?>
<div class="msg"><?php echo $msg; ?></div>
<?php } ?>

<form method="POST">

<label>Full Name *</label>
<input class="input" type="text" name="fullname" placeholder="Your full name" required>

<label>Email *</label>
<input class="input" type="email" name="email" placeholder="you@email.com" required>

<label>Password *</label>
<input class="input" type="password" name="password" placeholder="••••••••" required>

<label>Confirm Password *</label>
<input class="input" type="password" name="confirm" placeholder="••••••••" required>

<button class="btn" name="register">Create Account</button>

</form>

<div class="login">
Already have an account? <a href="login.php">Sign In</a>
</div>

</div>

</div>

</div>

<!-- HELP BUTTON -->

<div class="help-button" onclick="window.location.href='contact_help.php'">
💬
</div>

<!-- HELP POPUP -->

<div id="helpBox" class="help-box">

<div class="help-header">
<span>LifeFlow Support</span>
<button onclick="closeHelp()">✖</button>
</div>

<div class="help-content">

<p><b>Hello there 👋</b></p>

<p>
Need help registering or finding blood donors?
</p>


<p>
Click the chat button to contact our support team.
</p>

</div>

</div>

<script>

let helpBox=document.getElementById("helpBox");

function openHelp(){
helpBox.style.display="block";

setTimeout(function(){
helpBox.style.display="none";
},5000); // close after 5 seconds
}

function closeHelp(){
helpBox.style.display="none";
}

/* open every 10 seconds */

setInterval(function(){
openHelp();
},10000);

    </script>

</body>
</html>