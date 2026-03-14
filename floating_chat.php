<?php
if(session_status() == PHP_SESSION_NONE) session_start();
include "config.php";

if(!isset($_SESSION['user'])) die("Login required");

$user_email = $_SESSION['user'];
$stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
$stmt->bind_param("s",$user_email);
$stmt->execute();
$currentUser = $stmt->get_result()->fetch_assoc();

// Minimal: fetch latest 20 messages for this user (all chats)
$stmt2 = $conn->prepare("
    SELECT m.*, u.fullname 
    FROM messages m 
    JOIN users u ON m.sender_id=u.id
    WHERE m.sender_id=? OR m.receiver_id=? 
    ORDER BY m.created_at ASC LIMIT 20
");
$stmt2->bind_param("ii", $currentUser['id'],$currentUser['id']);
$stmt2->execute();
$messages = $stmt2->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Floating Chat</title>
<style>
body { font-family:sans-serif; background:#f3f3f3; margin:0; padding:0;}
/* Floating chat button */
.chat-button {
    position:fixed; bottom:20px; right:20px;
    width:60px; height:60px;
    border-radius:50%; background:#e53935; color:white;
    font-size:28px; display:flex; align-items:center; justify-content:center;
    cursor:pointer; box-shadow:0 4px 15px rgba(0,0,0,0.3);
    z-index:999;
}

/* Chat box */
.chat-box {
    position:fixed; bottom:90px; right:20px;
    width:300px; max-height:400px;
    background:white; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.2);
    display:none; flex-direction:column; overflow:hidden; z-index:999;
}

/* Chat header */
.chat-header {
    background:#e53935; color:white; padding:8px;
    font-weight:bold; display:flex; justify-content:space-between; align-items:center;
}

/* Chat content */
.chat-content {
    flex:1; padding:10px; overflow-y:auto; background:#f9f9f9;
}

/* Message bubble */
.message { margin:5px 0; padding:6px 10px; border-radius:10px; max-width:70%; word-wrap:break-word; }
.sent { background:#e53935; color:white; margin-left:auto; }
.received { background:#ddd; color:black; margin-right:auto; }

/* Input */
.chat-input { display:flex; border-top:1px solid #ddd; }
.chat-input input { flex:1; padding:8px; border:none; outline:none; }
.chat-input button { padding:8px 12px; background:#e53935; color:white; border:none; cursor:pointer; }
.chat-input button:hover { background:#d32f2f; }

</style>
</head>
<body>

<!-- Floating button -->
<div class="chat-button" onclick="toggleChat()">💬</div>

<!-- Chat box -->
<div class="chat-box" id="chatBox">
    <div class="chat-header">
        Chat
        <span style="cursor:pointer;" onclick="toggleChat()">✖</span>
    </div>
    <div class="chat-content" id="chatContent">
        <?php while($msg=$messages->fetch_assoc()): ?>
            <div class="message <?php echo $msg['sender_id']==$currentUser['id']?'sent':'received'; ?>">
                <strong><?php echo htmlspecialchars($msg['fullname']); ?>:</strong> <?php echo htmlspecialchars($msg['message']); ?>
            </div>
        <?php endwhile; ?>
    </div>
    <form class="chat-input" id="chatForm" method="POST" action="send_message.php">
        <input type="text" name="message" placeholder="Type a message..." required>
        <input type="hidden" name="receiver_id" value="1"> <!-- Replace with actual receiver -->
        <button type="submit">Send</button>
    </form>
</div>

<script>
function toggleChat(){
    let box = document.getElementById('chatBox');
    box.style.display = box.style.display==='flex'?'none':'flex';
}

// Auto scroll chat to bottom
let chatContent = document.getElementById('chatContent');
chatContent.scrollTop = chatContent.scrollHeight;
</script>

</body>
</html>