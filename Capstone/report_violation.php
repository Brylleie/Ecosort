<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "resident") {
    header("Location: login.php");
    exit();
}

$resident_id = $_SESSION["user_id"];

// Database connection details
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "ecosort";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get form data
    $description = $_POST["description"];
    $is_public = isset($_POST["is_public"]) ? true : false;  // If the checkbox is checked, it's public

    // File upload handling
    $target_dir = "uploads/"; // Directory to store uploaded files (must be writable by the web server)
    $image_paths = []; // Array to hold paths of uploaded files
    $upload_messages = []; // Array to hold upload messages
    $upload_errors = []; // Array to hold upload errors

    // Loop through each uploaded file
    foreach ($_FILES["image"]["name"] as $key => $image_name) {
        $image_tmp_name = $_FILES["image"]["tmp_name"][$key];
        $imageFileType = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $unique_name = uniqid() . '.' . $imageFileType; // Add uniqid for unique filenames
        $target_file = $target_dir . $unique_name;

        $uploadOk = 1;

        // Check if image file is a actual image or fake image
        $check = getimagesize($image_tmp_name);
        if ($check === false) {
            $uploadOk = 0;
            $upload_errors[] = "File $image_name is not an image.";
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            $uploadOk = 0;
            $upload_errors[] = "Sorry, file $image_name already exists.";
        }

        // Check file size
        if ($_FILES["image"]["size"][$key] > 5000000) {
            $uploadOk = 0;
            $upload_errors[] = "Sorry, your file $image_name is too large (max 5MB).";
        }

        // Allow certain file formats
        $allowed_types = ["jpg", "jpeg", "png", "gif", "mp4", "webm"];
        if (!in_array($imageFileType, $allowed_types)) {
            $uploadOk = 0;
            $upload_errors[] = "Sorry, only JPG, JPEG, PNG, GIF, MP4 & WEBM files are allowed for file $image_name.";
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $image_paths[] = null; // Ensure image_path is null
            $upload_messages[] = "Sorry, your file $image_name was not uploaded. " . implode(" ", $upload_errors);
        } else {
            if (move_uploaded_file($image_tmp_name, $target_file)) {
                $image_paths[] = $target_file;
                $upload_messages[] = "The file " . htmlspecialchars(basename($image_name)) . " has been uploaded.";
            } else {
                $image_paths[] = null; // Ensure image_path is null
                $upload_messages[] = "Sorry, there was an error uploading your file $image_name.";
            }
        }
    }

    // Prepare and execute the SQL query
    foreach ($image_paths as $image_path) {
        $stmt = $conn->prepare("INSERT INTO violations (reporter_id, description, image_path, is_public) VALUES (:reporter_id, :description, :image_path, :is_public)");
        $stmt->bindParam(':reporter_id', $resident_id, PDO::PARAM_INT);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':image_path', $image_path);
        $stmt->bindParam(':is_public', $is_public, PDO::PARAM_BOOL);  // Bind the is_public value

        if (!$stmt->execute()) {
            $error_message = "Report submission failed for one or more files. Please try again.";
            header("Location: resident_dashboard.php?error=" . urlencode($error_message));
            exit();
        }
    }

    $success_message = "Report submitted successfully! " . implode(" ", $upload_messages);
    header("Location: resident_dashboard.php?success=" . urlencode($success_message)); // Redirect back to dashboard
    exit();

} catch(PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
    header("Location: resident_dashboard.php?error=" . urlencode($error_message));
    exit();
} finally {
    $conn = null;
}
?>