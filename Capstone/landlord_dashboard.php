<?php
    session_start();

    // Check if the user is a landlord and is logged in
    if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "landlord") {
        header("Location: login.php?error=Unauthorized"); // Redirect with an error message
        exit();
    }

    // Get the landlord's user ID from the session
    $landlord_id = $_SESSION["user_id"];

    // Database connection details
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbname = "ecosort";

    $conn = null; // Initialize connection variable

    try {
        // Create PDO connection
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage()); // Fatal error - stop execution
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Landlord Dashboard - ECOSORT</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <header class="bg-light py-3">
            <div class="container">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <a class="navbar-brand" href="#">ECOSORT - Landlord Dashboard</a>
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
            <h2>Welcome, Landlord!</h2>
            <p>Monitor tenant compliance with waste segregation and promote responsible waste disposal practices.</p>

            <?php
            try {
                // Total Violations Query
                $stmt = $conn->prepare("SELECT COUNT(*) AS total_violations FROM violations v
                                         JOIN properties p ON v.property_id = p.property_id
                                         WHERE p.landlord_id = :landlord_id");
                $stmt->bindParam(':landlord_id', $landlord_id, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result && isset($result['total_violations'])) {
                    echo "<p>Total Waste Violations for Your Properties: " . htmlspecialchars($result['total_violations']) . "</p>";
                } else {
                    echo "<p>No violations found for your properties.</p>";
                }

                // Violations Table Query
                echo "<h3>Violation Details:</h3>";
                $stmt_violations = $conn->prepare("SELECT v.violation_id, v.violation_date, v.description, v.image_path, p.address
                                                    FROM violations v
                                                    JOIN properties p ON v.property_id = p.property_id
                                                    WHERE p.landlord_id = :landlord_id
                                                    ORDER BY v.violation_date DESC LIMIT 10");
                $stmt_violations->bindParam(':landlord_id', $landlord_id, PDO::PARAM_INT);
                $stmt_violations->execute();
                $violations = $stmt_violations->fetchAll(PDO::FETCH_ASSOC);

                if ($violations) {
                    echo "<table class='table table-bordered'>";
                    echo "<thead class='thead-light'><tr><th>Violation ID</th><th>Date</th><th>Property</th><th>Description</th><th>Image</th></tr></thead>";
                    echo "<tbody>";
                    foreach ($violations as $violation) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($violation['violation_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($violation['violation_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($violation['address']) . "</td>";
                        echo "<td>" . htmlspecialchars($violation['description']) . "</td>";
                        echo "<td><img src='" . htmlspecialchars($violation['image_path']) . "' alt='Violation Image' width='100'></td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                } else {
                    echo "<p>No violations found for your properties.</p>";
                }

            } catch (PDOException $e) {
                echo "<div class='alert alert-danger'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            } finally {
                // Closing the database connection
                if ($conn !== null) {
                    $conn = null;
                }
            }
            ?>

            <!-- Tenant Management Tools -->
            <h3>Tenant Management</h3>
            <p>Manage tenants and send reminders to improve waste disposal practices.</p>
            <!-- Add forms and functionality for tenant management here -->

            <!-- Customizable Reminders -->
            <h3>Customizable Reminders</h3>
            <p>Customize reminders for tenants regarding waste disposal schedules.</p>
            <!-- Add forms and functionality for reminder customization here -->

            <!-- Tenant Data Privacy Statement -->
            <h3>Tenant Data Privacy</h3>
            <p>We are committed to protecting your tenants' data. All data is handled in accordance with our privacy policy. (Link to full privacy policy)</p>
        </section>

        <footer class="bg-light text-center py-3">
            <p>&copy; 2025 ECOSORT Capstone Project</p>
        </footer>
         <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>
    <?php if ($conn) $conn = null; ?>
