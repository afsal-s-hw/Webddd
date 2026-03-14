<?php

$host = "sql305.infinityfree.com";
$user = "if0_41165125";
$pass = "dJWdfUS22yoj";
$db = "if0_41165125_blood";

$conn = new mysqli($host,$user,$pass,$db);

if($conn->connect_error){
die("Database connection failed");
}

?>