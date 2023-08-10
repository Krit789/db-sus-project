<?php
$servername = "49.228.131.109";
$username = "susproject";
$password = "tIuc]@zwVzqQp)Cs";
$database = "susproject";
$port = 3357;

// Create connection
$conn = new mysqli($servername, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>
