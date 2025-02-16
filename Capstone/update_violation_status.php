<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "lgu") {
    header("Location:login.php");
    exit();
}

$lgu_user_id = $_SESSION["user_id"];

// Database connection details
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "ecosort";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get form data
    $violation_id = $_POST["violation_id"];
    $status = $_POST["status"];

    // Prepare and execute the SQL query
    $stmt = $conn->prepare("UPDATE violations SET status = :status, lgu_agent_id = :lgu_agent_id WHERE violation_id = :violation_id");
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':lgu_agent_id', $lgu_user_id, PDO::PARAM_INT);
    $stmt->bindParam(':violation_id', $violation_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: lgu_dashboard.php?success=Violation status updated successfully!");
        exit();
    } else {
        header("Location: lgu_dashboard.php?error=Violation status update failed. Please try again.");
        exit();
    }

} catch(PDOException $e) {
    header("Location: lgu_dashboard.php?error=Database error: " . urlencode($e->getMessage()));
    exit();
} finally {
    $conn = null;
}
?>
