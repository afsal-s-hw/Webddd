<?php
include "config.php";

$msg="";

if(isset($_POST['register'])){

$fullname=$_POST['fullname'];
$email=$_POST['email'];
$phone=$_POST['phone'];
$bloodgroup=$_POST['bloodgroup'];
$city=$_POST['city'];
$age=$_POST['age'];
$gender=$_POST['gender'];
$weight=$_POST['weight'];
$lastdonation=$_POST['lastdonation'];

$sql="INSERT INTO donors(fullname,email,phone,bloodgroup,city,age,gender,weight,last_donation)
VALUES('$fullname','$email','$phone','$bloodgroup','$city','$age','$gender','$weight','$lastdonation')";
// After adding donor info in donors table
$conn->query("UPDATE users SET donor='yes' WHERE email='$email'");
if($conn->query($sql)){
$msg="Donor registered successfully";
}else{
$msg="Error registering donor";
}

}
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Blood Donor Register - LifeFlow</title>

    
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

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

.nav{
display:flex;
align-items:center;
padding:18px 40px;
background:white;
box-shadow:0 2px 10px rgba(0,0,0,0.05);
}

.logo{
font-size:20px;
font-weight:600;
}

.logo span{
color:#e53935;
}

.main{
display:flex;
justify-content:center;
align-items:center;
padding:60px 20px;
}

.card{
background:white;
padding:30px;
border-radius:12px;
width:360px;
box-shadow:0 3px 15px rgba(0,0,0,0.05);
}

.card h2{
text-align:center;
margin-bottom:5px;
}

.subtitle{
text-align:center;
font-size:14px;
color:#777;
margin-bottom:20px;
}

.msg{
text-align:center;
margin-bottom:10px;
color:green;
}

.input{
width:100%;
padding:10px;
border:1px solid #ddd;
border-radius:8px;
margin-bottom:15px;
}

select{
width:100%;
padding:10px;
border:1px solid #ddd;
border-radius:8px;
margin-bottom:15px;
}

.btn{
width:100%;
background:#e53935;
color:white;
border:none;
padding:12px;
border-radius:8px;
cursor:pointer;
font-weight:500;
}

.btn:hover{
background:#c62828;
}

/* BLOOD POPUP */

.blood-popup{
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.4);
display:none;
align-items:center;
justify-content:center;
z-index:1000;
}

.blood-box{
background:white;
padding:25px;
border-radius:12px;
text-align:center;
width:260px;
animation:pop .3s ease;
}

.blood-grid{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:10px;
margin-top:15px;
}

.blood-grid button{
padding:10px;
border:none;
background:#e53935;
color:white;
border-radius:8px;
cursor:pointer;
font-weight:500;
}

.close-btn{
margin-top:15px;
background:#ccc;
border:none;
padding:8px 15px;
border-radius:6px;
cursor:pointer;
}

@keyframes pop{
from{transform:scale(.8);opacity:0;}
to{transform:scale(1);opacity:1;}
}

/* HELP BUTTON */

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
cursor:pointer;
}

@keyframes slideUp{
from{transform:translateY(20px);opacity:0;}
to{transform:translateY(0);opacity:1;}
}
    /* GENDER SELECTION */

.gender-title{
font-size:14px;
margin-bottom:8px;
color:#555;
}

.gender-grid{
display:flex;
gap:10px;
margin-bottom:15px;
}

.gender-grid button{
flex:1;
padding:10px;
border:1px solid #ddd;
background:white;
border-radius:8px;
cursor:pointer;
font-size:14px;
transition:0.2s;
}

.gender-grid button:hover{
border-color:#e53935;
}

.gender-grid button.active{
background:#e53935;
color:white;
border-color:#e53935;
}

/* MODERN DATE INPUT */


</style>

</head>

<body>

<div class="nav">
<div class="logo"><span>🩸</span> LifeFlow</div>
</div>

<div class="main">

<div class="card">

<h2>Blood Donor Register</h2>
<div class="subtitle">Become a LifeFlow blood donor</div>

<?php if($msg!=""){ ?>
<div class="msg"><?php echo $msg; ?></div>
<?php } ?>

<form method="POST">

<input class="input" type="text" name="fullname" placeholder="Full Name" required>

<input class="input" type="email" name="email" placeholder="Email (optional)">

<input class="input" type="text" name="phone" placeholder="Phone Number" required>

<input class="input" type="text" id="bloodgroup" name="bloodgroup" placeholder="Select Blood Group" readonly onclick="openBloodPopup()" required>

<input type="hidden" name="gender" id="genderInput" required>

<div class="gender-title">Select Gender</div>

<div class="gender-grid">

<button type="button" onclick="setGender('Male',this)">👨 Male</button>

<button type="button" onclick="setGender('Female',this)">👩 Female</button>

<button type="button" onclick="setGender('Other',this)">⚧ Other</button>

    </div>

<input class="input" type="text" name="city" placeholder="City / Location" required>

<input class="input" type="number" name="age" placeholder="Age (18-60)" required>

<input class="input" type="number" name="weight" placeholder="Weight (kg)" required>

<div class="date-title">Last Donation Date</div>

<input class="input modern-date" type="date" name="lastdonation">

<button class="btn" name="register">Register as Donor</button>

</form>

</div>

</div>

<!-- BLOOD POPUP -->

<div id="bloodPopup" class="blood-popup">

<div class="blood-box">

<h3>Select Blood Group</h3>

<div class="blood-grid">

<button onclick="setBlood('A+')">A+</button>
<button onclick="setBlood('A-')">A-</button>
<button onclick="setBlood('B+')">B+</button>
<button onclick="setBlood('B-')">B-</button>
<button onclick="setBlood('O+')">O+</button>
<button onclick="setBlood('O-')">O-</button>
<button onclick="setBlood('AB+')">AB+</button>
<button onclick="setBlood('AB-')">AB-</button>

</div>

<button class="close-btn" onclick="closeBloodPopup()">Close</button>

</div>

</div>

<!-- HELP BUTTON -->

<div class="help-button" onclick="window.location.href='contact_help.php'">
💬
</div>

<!-- HELP BOX -->

<div id="helpBox" class="help-box">

<div class="help-header">
<span>LifeFlow Support</span>
<button onclick="closeHelp()">✖</button>
</div>

<div class="help-content">

<p><b>Hello 👋</b></p>
<p>Want to register as a blood donor?</p>
<p>Click the chat button for help.</p>

</div>

</div>

<script>

function openBloodPopup(){
document.getElementById("bloodPopup").style.display="flex";
}

function closeBloodPopup(){
document.getElementById("bloodPopup").style.display="none";
}

function setBlood(group){
document.getElementById("bloodgroup").value=group;
closeBloodPopup();
}

let helpBox=document.getElementById("helpBox");

function openHelp(){
helpBox.style.display="block";

setTimeout(function(){
helpBox.style.display="none";
},5000);
}

function closeHelp(){
helpBox.style.display="none";
}

setInterval(function(){
openHelp();
},10000);

    function setGender(gender,btn){

document.getElementById("genderInput").value=gender;

let buttons=document.querySelectorAll(".gender-grid button");

buttons.forEach(b=>{
b.classList.remove("active");
});

btn.classList.add("active");

    }
   
</script>
    
</body>
</html>