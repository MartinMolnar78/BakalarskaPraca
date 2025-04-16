<?php
$servername = "sql5.webzdarma.cz";  
$username = "martan78euwe3193";         
$password = "";             
$dbname = "martan78euwe3193";   

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
