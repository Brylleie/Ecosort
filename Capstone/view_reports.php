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

    // Prepare and execute the SQL query to fetch reports
    $stmt = $conn->prepare("SELECT description, image_path FROM violations WHERE reporter_id = :reporter_id");
    $stmt->bindParam(':reporter_id', $resident_id, PDO::PARAM_INT);
    $stmt->execute();

    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit();
} finally {
    $conn = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Reports</title>
</head>
<body>
    <h1>Your Reports</h1>
    <?php if (count($reports) > 0): ?>
        <ul>
            <?php foreach ($reports as $report): ?>
                <li>
                    <p><?php echo htmlspecialchars($report['description']); ?></p>
                    <?php if ($report['image_path']): ?>
                        <?php $file_extension = strtolower(pathinfo($report['image_path'], PATHINFO_EXTENSION)); ?>
                        <?php if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                            <img src="<?php echo htmlspecialchars($report['image_path']); ?>" alt="Report Image" style="max-width: 300px; max-height: 300px;">
                        <?php elseif (in_array($file_extension, ['mp4', 'webm'])): ?>
                            <video width="320" height="240" controls>
                                <source src="<?php echo htmlspecialchars($report['image_path']); ?>" type="video/<?php echo $file_extension; ?>">
                                Your browser does not support the video tag.
                            </video>
                        <?php endif; ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No reports found.</p>
    <?php endif; ?>
</body>
</html>