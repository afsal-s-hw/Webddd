<?php
session_start();
include "config.php";

if(!isset($_SESSION['user'])) exit('No session');

$user_email = $_SESSION['user'];
$stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
$stmt->bind_param("s",$user_email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$other_id = intval($_GET['user_id'] ?? 0);
if(!$other_id) exit('No user');

$stmt = $conn->prepare("SELECT * FROM messages WHERE 
    (sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?) ORDER BY created_at ASC");
$stmt->bind_param("iiii",$user['id'],$other_id,$other_id,$user['id']);
$stmt->execute();
$result = $stmt->get_result();

while($m = $result->fetch_assoc()){
    $class = $m['sender_id']==$user['id'] ? 'sent' : 'received';
    echo '<p class="'.$class.'">'.htmlspecialchars($m['message']).'</p>';
}
?>