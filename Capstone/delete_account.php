<?php
    session_start();
    if (!isset($_SESSION["user_id"])) {
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION["user_id"];

    // Database connection details (as before)
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbname = "ecosort";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //  *IMPORTANT* Add a confirmation step to prevent accidental deletion!
        // Delete the user's account
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Account deleted successfully, destroy the session and redirect to logout
            session_destroy();
            header("Location: logout.php?success=Account deleted successfully."); // Or a dedicated "account deleted" page
            exit();
        } else {
            header("Location: profile.php?error=Account deletion failed. Please try again.");
            exit();
        }

    } catch(PDOException $e) {
        header("Location: profile.php?error=Database error: " . urlencode($e->getMessage()));
        exit();
    } finally {
        if(isset($conn)) {
            $conn = null;
        }
    }
    ?>
