<?php
$servername = "localhost"; // Replace with your database server
$db_username = "root"; // Replace with your database username
$db_password = ""; // Replace with your database password
$dbname = "ecosort"; // Replace with your database name

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $db_username, $db_password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set the default fetch mode to associative array
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
   //echo "Connected successfully";  // For Testing only
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die(); // Terminate script execution on connection failure
}
?>
