<?php
include 'db_connect.php';
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "lgu") {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LGU Dashboard - ECOSORT</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="bg-light py-3">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a class="navbar-brand" href="#">ECOSORT - LGU Dashboard</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                            <a class="nav-link" href="index.php">Homepage</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <section id="dashboard" class="container mt-5">
        <h2>Welcome, LGU Agent!</h2>
        <p>Monitor and manage waste violations effectively to create a cleaner community.</p>

        <?php
        // Database connection details
        $servername = "localhost";
        $db_username = "root"; // Renamed to avoid conflict
        $db_password = ""; // Renamed to avoid conflict
        $dbname = "ecosort";

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $db_username, $db_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Total Waste Violations
            $stmt = $conn->prepare("SELECT COUNT(*) AS total_violations FROM violations");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result && isset($result['total_violations'])) {
                echo "<p>Total Reported Waste Violations: " . htmlspecialchars($result['total_violations']) . "</p>";
            } else {
                echo "<p>No violations found.</p>";
            }

            // Violations Table
            echo "<h3>Recent Violations:</h3>";
            $stmt_violations = $conn->prepare("SELECT v.violation_id, v.violation_date, v.description, v.image_path, u.username, v.status
            FROM violations v
            JOIN users u ON v.reporter_id = u.user_id
            ORDER BY v.violation_date DESC");
            $stmt_violations->execute();
            $violations = $stmt_violations->fetchAll(PDO::FETCH_ASSOC);

            if ($violations) {
                echo "<table class='table table-bordered'>";
                echo "<thead class='thead-light'><tr><th>Violation ID</th><th>Date</th><th>User</th><th>Description</th><th>Image</th></tr></thead>";
                echo "<tbody>";
                foreach ($violations as $violation) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($violation['violation_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($violation['violation_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($violation['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($violation['description']) . "</td>";
                    echo "<td><img src='" . htmlspecialchars($violation['image_path']) . "' alt='Violation Image' width='100'></td>";  // Display the image
                    echo "<td>" . htmlspecialchars($violation['status']) . "</td>";
                    echo "<td>";
                    // Add a form to allow LGU agents to review and update the status
                    echo "<form method='post' action='update_violation_status.php'>";
                    echo "<input type='hidden' name='violation_id' value='" . htmlspecialchars($violation['violation_id']) . "'>";
                    echo "<select class='form-control' name='status'>";
                    echo "<option value='submitted'" . ($violation['status'] == 'submitted' ? ' selected' : '') . ">Submitted</option>";
                    echo "<option value='reviewed'" . ($violation['status'] == 'reviewed' ? ' selected' : '') . ">Reviewed</option>";
                    echo "<option value='action_taken'" . ($violation['status'] == 'action_taken' ? ' selected' : '') . ">Action Taken</option>";
                    echo "</select><br>";
                    echo "<button type='submit' class='btn btn-sm btn-primary'>Update</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                    
                }
                echo "</tbody>";
                echo "</table>";
            } else {
                echo "<p>No violations found.</p>";
            }

        } catch(PDOException $e) {
            echo "<div class='alert alert-danger'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        } finally {
            $conn = null;
        }
        ?>

         <!-- Automatic Educational Content -->
        <h3>Automatic Educational Content</h3>
        <p>Promoting waste segregation awareness among residents.</p>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Did You Know?</h5>
                <p class="card-text">Proper waste segregation can significantly reduce landfill waste and save valuable resources!</p>
                <a href="#" class="btn btn-primary">Learn More</a>
            </div>
        </div>
    </section>

    <footer class="bg-light text-center py-3">
        <p>&copy; 2025 ECOSORT Capstone Project</p>
    </footer>
         <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>



