<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
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
    
    // Handle Profile Picture Upload
    $target_dir = "profile_pics/"; // Create this directory and make it writable
    $profile_picture = ""; // Initialize
    if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
        $image_name = basename($_FILES["profile_picture"]["name"]);
        $image_name_parts = explode('.', $image_name);
        $image_ext = strtolower(end($image_name_parts));
        $unique_name = uniqid() . '.' . $image_ext;
        $target_file = $target_dir . $unique_name;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        // Check if image file is an actual image or fake image
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if ($check === false) {
            header("Location: profile.php?error=File is not an image.");
            exit();
        }
        
        // Check file size
        if ($_FILES["profile_picture"]["size"] > 2000000) {
            header("Location: profile.php?error=Sorry, your file is too large (max 2MB).");
            exit();
        }
        
        // Allow certain file formats
        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowed_types)) {
            header("Location: profile.php?error=Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
            exit();
        }
        
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $profile_picture = $target_file;
        } else {
            header("Location: profile.php?error=Sorry, there was an error uploading your file.");
            exit();
        }
    }
    
    // Get Description
    $description = $_POST["description"];
    
    // Update User Data
    $sql = "UPDATE users SET description = :description, profile_picture = :profile_picture WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':profile_picture', $profile_picture);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        header("Location: profile.php?success=Profile updated successfully!");
        exit();
    } else {
        header("Location: profile.php?error=Profile update failed. Please try again.");
        exit();
    }
    
} catch (PDOException $e) {
    header("Location: profile.php?error=Database error: " . urlencode($e->getMessage()));
    exit();
} finally {
    if (isset($conn)) {
        $conn = null;
    }
}
