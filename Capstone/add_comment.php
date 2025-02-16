<?php
    session_start();
    if (!isset($_SESSION["user_id"]) || ($_SESSION["user_type"] != "resident" && $_SESSION["user_type"] != "lgu")) {
        header("Location: login.php"); // Or resident_login.php
        exit();
    }

    $user_id = $_SESSION["user_id"];

    // Database connection details
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbname = "ecosort";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $violation_id = $_POST["violation_id"];
            $comment_text = $_POST["comment_text"];

            // Prepare and execute the SQL query
            $stmt = $conn->prepare("INSERT INTO comments (violation_id, user_id, comment_text) VALUES (:violation_id, :user_id, :comment_text)");
            $stmt->bindParam(':violation_id', $violation_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':comment_text', $comment_text);

            if ($stmt->execute()) {
                header("Location: resident_dashboard.php?success=Comment added successfully!"); // Redirect back to dashboard
                exit();
            } else {
                header("Location: resident_dashboard.php?error=Failed to add comment. Please try again.");
                exit();
            }
        }

    } catch(PDOException $e) {
        header("Location: resident_dashboard.php?error=Database error: " . urlencode($e->getMessage()));
        exit();
    } finally {
        if(isset($conn)){
             $conn = null;
        }

    }
    ?>
