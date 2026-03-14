<?php
session_start();
include "config.php";

if(!isset($_SESSION['user'])){
header("Location: login.php");
exit();
}

/* logged user */

$email=$_SESSION['user'];

$stmt=$conn->prepare("SELECT id,fullname FROM users WHERE email=?");
$stmt->bind_param("s",$email);
$stmt->execute();
$res=$stmt->get_result();
$user=$res->fetch_assoc();

$my_id=$user['id'];

/* selected chat */

$chat_id=isset($_GET['user']) ? intval($_GET['user']) : 0;

/* send message */

if(isset($_POST['send']) && $chat_id){

$msg=trim($_POST['message']);

if($msg!=""){

$stmt=$conn->prepare("INSERT INTO messages(sender_id,receiver_id,message) VALUES(?,?,?)");
$stmt->bind_param("iis",$my_id,$chat_id,$msg);
$stmt->execute();

}

header("Location: chat.php?user=".$chat_id);
exit();

}

/* get recent user ids */

$stmt=$conn->prepare("
SELECT DISTINCT sender_id FROM messages WHERE receiver_id=?
UNION
SELECT DISTINCT receiver_id FROM messages WHERE sender_id=?
");

$stmt->bind_param("ii",$my_id,$my_id);
$stmt->execute();

$ids=$stmt->get_result();

$recent=[];

while($row=$ids->fetch_assoc()){

$id=($row['sender_id'] ?? $row['receiver_id']);

$stmt2=$conn->prepare("SELECT id,fullname FROM users WHERE id=?");
$stmt2->bind_param("i",$id);
$stmt2->execute();
$r=$stmt2->get_result()->fetch_assoc();

if($r) $recent[]=$r;

}

/* load messages */

$messages=[];

if($chat_id){

$stmt=$conn->prepare("
SELECT * FROM messages
WHERE (sender_id=? AND receiver_id=?)
OR (sender_id=? AND receiver_id=?)
ORDER BY created_at
");

$stmt->bind_param("iiii",$my_id,$chat_id,$chat_id,$my_id);
$stmt->execute();

$messages=$stmt->get_result();

}

?>

<!DOCTYPE html>
<html>
<head>

<title>Chat</title>

<style>

body{
margin:0;
font-family:Arial;
background:#f4f4f4;
}

.container{
display:flex;
height:100vh;
}

/* sidebar */

.sidebar{
width:260px;
background:white;
border-right:1px solid #eee;
}

.sidebar h3{
padding:20px;
margin:0;
border-bottom:1px solid #eee;
}

.user{
padding:15px;
cursor:pointer;
border-bottom:1px solid #f2f2f2;
}

.user:hover{
background:#f7f7f7;
}

/* chat */

.chat{
flex:1;
display:flex;
flex-direction:column;
}

.messages{
flex:1;
padding:20px;
overflow-y:auto;
}

.msg{
max-width:60%;
padding:12px;
margin-bottom:10px;
border-radius:8px;
}

.sent{
background:#e53935;
color:white;
margin-left:auto;
}

.received{
background:white;
border:1px solid #ddd;
}

/* input */

.chat-form{
display:flex;
padding:15px;
background:white;
border-top:1px solid #eee;
}

.chat-form input{
flex:1;
padding:10px;
border:1px solid #ddd;
border-radius:5px;
}

.chat-form button{
margin-left:10px;
padding:10px 16px;
background:#e53935;
color:white;
border:none;
border-radius:5px;
cursor:pointer;
}

</style>

</head>

<body>

<div class="container">

<!-- sidebar -->

<div class="sidebar">

<h3>Messages</h3>

<?php foreach($recent as $r): ?>

<div class="user" onclick="location.href='chat.php?user=<?=$r['id']?>'">

<?=htmlspecialchars($r['fullname'])?>

</div>

<?php endforeach; ?>

</div>


<!-- chat -->

<div class="chat">

<div class="messages">

<?php if($chat_id): ?>

<?php while($m=$messages->fetch_assoc()): ?>

<div class="msg <?= $m['sender_id']==$my_id ? 'sent':'received' ?>">

<?=htmlspecialchars($m['message'])?>

</div>

<?php endwhile; ?>

<?php else: ?>

<p>Select a user to start chat</p>

<?php endif; ?>

</div>

<?php if($chat_id): ?>

<form method="POST" class="chat-form">

<input type="text" name="message" placeholder="Type message..." required>

<button name="send">Send</button>

</form>

<?php endif; ?>

</div>

</div>

</body>
</html>