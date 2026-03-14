<?php
// ----------------------
// messages_page.php
// ----------------------
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "config.php";

if(session_status() !== PHP_SESSION_ACTIVE){
    session_start();
}

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user'];

// Get logged-in user
$stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
$stmt->bind_param("s",$user_email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

function getInitial($name){ return strtoupper(substr($name,0,1)); }

// ----------------------
// Handle AJAX requests
// ----------------------
if(isset($_POST['action'])){
    // Send message
    if($_POST['action']=='send_message'){
        $receiver_id = intval($_POST['receiver_id']);
        $message = trim($_POST['message']);
        if($message != ''){
            $stmt = $conn->prepare("INSERT INTO messages(sender_id, receiver_id, message) VALUES(?,?,?)");
            $stmt->bind_param("iis",$user['id'],$receiver_id,$message);
            $stmt->execute();
        }
        exit();
    }

    // Accept or Reject blood request
    if($_POST['action']=='accept_request' || $_POST['action']=='reject_request'){
        $request_id = intval($_POST['request_id']);
        $status = $_POST['action']=='accept_request' ? 'accepted' : 'rejected';
        $stmt = $conn->prepare("UPDATE blood_requests SET status=? WHERE id=?");
        $stmt->bind_param("si",$status,$request_id);
        $stmt->execute();
        exit($status);
    }
}

// ----------------------
// Fetch recent chats safely
// ----------------------
$chats = [];
$sql = "
SELECT u.id AS user_id, u.fullname,
       (SELECT m.message FROM messages m 
        WHERE (m.sender_id=? AND m.receiver_id=u.id) OR (m.sender_id=u.id AND m.receiver_id=?)
        ORDER BY m.created_at DESC LIMIT 1) AS last_message
FROM users u
WHERE u.id != ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii",$user['id'],$user['id'],$user['id']);
$stmt->execute();
$result = $stmt->get_result();
while($row = $result->fetch_assoc()){
    $chats[] = $row;
}

// ----------------------
// Fetch pending blood requests for donors
// ----------------------
$requests = [];
if($user['donor']=='yes'){
    $sql = "SELECT br.*, u.fullname, u.email
            FROM blood_requests br
            JOIN users u ON u.id = br.user_id
            WHERE br.status='pending'";
    $result = $conn->query($sql);
    while($r = $result->fetch_assoc()){
        $requests[] = $r;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Floating Messenger</title>
<style>
/* Floating button */
.floating-btn{
    position:fixed; bottom:25px; right:25px;
    width:60px; height:60px; border-radius:50%;
    background:#e53935; color:white; font-size:24px;
    display:flex; justify-content:center; align-items:center;
    cursor:pointer; z-index:1100; box-shadow:0 6px 18px rgba(0,0,0,0.3);
}

/* Messenger box */
.messenger-box{
    position:fixed; bottom:100px; right:25px; width:320px; max-height:500px;
    background:white; border-radius:12px; box-shadow:0 5px 15px rgba(0,0,0,0.3);
    overflow:hidden; display:none; flex-direction:column; z-index:1100;
}
.messenger-box h4{ margin:10px; font-size:14px; color:#e53935; }
.chat-list{ max-height:150px; overflow-y:auto; border-bottom:1px solid #eee; }
.chat-item{ display:flex; align-items:center; padding:8px; cursor:pointer; border-bottom:1px solid #eee; }
.chat-item .initial{ width:40px; height:40px; border-radius:50%; background:#e53935; color:white; display:flex; align-items:center; justify-content:center; margin-right:10px; font-weight:bold; }
.chat-info .name{ font-weight:500; font-size:14px; }
.chat-info .last-msg{ font-size:12px; color:#666; }

.chat-window{ display:flex; flex-direction:column; border-top:1px solid #eee; padding:8px; }
.messages{ flex:1; max-height:180px; overflow-y:auto; margin-bottom:8px; }
.messages p{ margin:2px 0; padding:4px 8px; border-radius:6px; }
.messages .sent{ background:#e53935; color:white; align-self:flex-end; }
.messages .received{ background:#f0f0f0; color:#333; align-self:flex-start; }
#chatInput{ padding:6px; border:1px solid #ccc; border-radius:6px; flex:1; }
#sendBtn{ padding:6px 10px; background:#e53935; color:white; border:none; border-radius:6px; cursor:pointer; }
.button-group button{ margin-right:5px; padding:4px 8px; border:none; border-radius:5px; cursor:pointer; }
.button-group .accept{ background:green; color:white; }
.button-group .reject{ background:red; color:white; }
</style>
</head>
<body>

<!-- Floating Button -->
<div id="messengerBtn" class="floating-btn">💬</div>

<!-- Messenger Box -->
<div id="messengerBox" class="messenger-box">
    <h4>Pending Requests</h4>
    <div class="request-list">
        <?php foreach($requests as $r): ?>
        <div class="chat-item" data-requestid="<?php echo $r['id']; ?>" data-userid="<?php echo $r['user_id']; ?>">
            <div class="chat-info">
                <div class="name"><?php echo htmlspecialchars($r['fullname']); ?> requested <?php echo $r['blood_group']; ?> blood</div>
                <div class="button-group">
                    <button class="accept">Accept</button>
                    <button class="reject">Reject</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <h4>Recent Chats</h4>
    <div class="chat-list">
        <?php foreach($chats as $c): ?>
        <div class="chat-item" data-userid="<?php echo $c['user_id']; ?>">
            <div class="initial"><?php echo getInitial($c['fullname']); ?></div>
            <div class="chat-info">
                <div class="name"><?php echo htmlspecialchars($c['fullname']); ?></div>
                <div class="last-msg"><?php echo htmlspecialchars(substr($c['last_message'] ?? '', 0, 30)); ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="chat-window" id="chatWindow" style="display:none;">
        <div class="messages" id="messages"></div>
        <input type="text" id="chatInput" placeholder="Type a message...">
        <button id="sendBtn">Send</button>
    </div>
</div>

<script>
const messengerBtn = document.getElementById('messengerBtn');
const messengerBox = document.getElementById('messengerBox');
const chatWindow = document.getElementById('chatWindow');
const messagesDiv = document.getElementById('messages');
const chatInput = document.getElementById('chatInput');

messengerBtn.addEventListener('click', ()=>{
    messengerBox.style.display = messengerBox.style.display==="flex" ? "none" : "flex";
});

// Accept / Reject buttons
document.querySelectorAll('.button-group .accept, .button-group .reject').forEach(btn=>{
    btn.addEventListener('click', (e)=>{
        const requestId = e.target.closest('.chat-item').dataset.requestid;
        const action = e.target.classList.contains('accept') ? 'accept_request' : 'reject_request';
        fetch('', {
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'action='+action+'&request_id='+requestId
        }).then(res=>res.text()).then(data=>{
            e.target.closest('.chat-item').remove();
        });
        e.stopPropagation();
    });
});

// Open recent chat
document.querySelectorAll('.chat-list .chat-item').forEach(item=>{
    item.addEventListener('click', ()=>{
        chatWindow.style.display = "flex";
        const userId = item.dataset.userid;
        fetch('fetch_messages.php?user_id='+userId)
        .then(res=>res.text())
        .then(data=>messagesDiv.innerHTML=data);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
        item.classList.add('active');
    });
});

// Send message
document.getElementById('sendBtn').addEventListener('click', ()=>{
    const msg = chatInput.value;
    const userId = document.querySelector('.chat-list .chat-item.active')?.dataset.userid;
    if(!msg.trim() || !userId) return;
    fetch('', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'action=send_message&receiver_id='+userId+'&message='+encodeURIComponent(msg)
    }).then(res=>{
        messagesDiv.innerHTML += '<p class="sent">'+msg+'</p>';
        chatInput.value='';
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    });
});
</script>
</body>
</html>