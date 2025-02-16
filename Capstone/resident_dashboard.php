<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php"); // Redirect to login if not logged in
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

    // Fetch user data including profile_picture
    $stmt = $conn->prepare("SELECT username, profile_picture, description FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION["username"] = $user["username"];  // Keep username in session too
        $_SESSION["profile_picture"] = $user["profile_picture"]; // Store profile picture in session
        $_SESSION["description"] = $user["description"];
    }

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to display reports
function displayReports($conn, $sql, $title, $user_id = null) {
    echo "<h3>" . htmlspecialchars($title) . "</h3>";
    try {
        $stmt = $conn->prepare($sql);
        // Bind the user_id parameter if it's not null
        if ($user_id !== null) {
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        }
        $stmt->execute();
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($reports) {
            foreach ($reports as $report) {
                echo "<div class='post'>";
                echo "<div class='post-header'>";
                if (isset($_SESSION["profile_picture"]) && !empty($_SESSION["profile_picture"])) {
                    echo "<img src='" . htmlspecialchars($_SESSION["profile_picture"]) . "' alt='Profile Picture' class='img-fluid'>";
                } else {
                    echo "<img src='default-profile.png' alt='Default Profile Picture' class='img-fluid'>";
                }
                echo "<strong>" . htmlspecialchars($report['username']) . "</strong> <small class='text-muted'>" . htmlspecialchars($report['violation_date']) . "</small>";
                echo "</div>";
                echo "<div class='post-content'>";
                echo "<p>" . htmlspecialchars($report['description']) . "</p>";
                if ($report['image_path']) {
                    $image_paths = explode(",", $report['image_path']);
                    foreach ($image_paths as $path) {
                        $file_ext = pathinfo($path, PATHINFO_EXTENSION);
                        if (in_array(strtolower($file_ext), ['jpg', 'jpeg', 'png', 'gif'])) {
                            echo "<img src='" . htmlspecialchars($path) . "' alt='Violation Image' class='img-fluid'>";
                        } elseif (in_array(strtolower($file_ext), ['mp4', 'webm', 'ogg'])) {
                            echo "<video class='img-fluid' controls><source src='" . htmlspecialchars($path) . "' type='video/" . htmlspecialchars($file_ext) . "'>Your browser does not support the video tag.</video>";
                        } else {
                            echo "Unsupported file format";
                        }
                    }
                }
                echo "</div>";
                echo "<div class='post-footer'>";
                echo "<span class='badge badge-secondary'>" . htmlspecialchars($report['status']) . "</span>";
                echo "<div class='comments-section'>";
                // Display existing comments
                try {
                    $stmt_comments = $conn->prepare(" SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.user_id WHERE c.violation_id = :violation_id ORDER BY c.comment_date ASC");
                    $stmt_comments->bindParam(':violation_id', $report['violation_id'], PDO::PARAM_INT);
                    $stmt_comments->execute();
                    $comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);

                    if ($comments) {
                        echo "<ul class='list-unstyled'>";
                        foreach ($comments as $comment) {
                            echo "<li><strong>" . htmlspecialchars($comment['username']) . ":</strong> " . htmlspecialchars($comment['comment_text']) . " <small class='text-muted'>(" . htmlspecialchars($comment['comment_date']) . ")</small></li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<p>No comments yet.</p>";
                    }
                } catch (PDOException $e) {
                    echo "<div class='alert alert-danger'>Error fetching comments: " . htmlspecialchars($e->getMessage()) . "</div>";
                }

                // Add comment form
                if ($user_id) {  // Only show the form if the user is logged in
                    echo "<form method='post' action='add_comment.php' class='comment-form'>";
                    echo "<input type='hidden' name='violation_id' value='" . htmlspecialchars($report['violation_id']) . "'>";
                    echo "<div class='form-group'>";
                    echo "<textarea class='form-control' name='comment_text' rows='2' placeholder='Add your comment' required></textarea>";
                    echo "</div>";
                    echo "<button type='submit' class='btn btn-sm btn-primary'>Add Comment</button>";
                    echo "</form>";
                } else {
                    echo "<p><a href='resident_login.php'>Login</a> to add a comment.</p>";
                }

                echo "</div>"; // End comments section
                echo "</div>"; // End post footer
                echo "</div>"; // End post
            }
        } else {
            echo "<p>No reports found.</p>";
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Dashboard - ECOSORT</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .post {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #fff;
        }
        .post-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .post-header img {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }
        .post-content {
            margin-bottom: 10px;
        }
        .post-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .comment-form {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <header class="bg-light py-3">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a class="navbar-brand" href="#">ECOSORT - Resident Dashboard</a>
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
        <h2>Welcome!</h2>

        <nav>
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="my-reports -tab" data-toggle="tab" href="#my-reports" role="tab" aria-controls="my-reports" aria-selected="true">My Reports</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="community-reports-tab" data-toggle="tab" href="#community-reports" role="tab" aria-controls="community-reports" aria-selected="false">Community Reports</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="report-violation-tab" data-toggle="tab" href="#report-violation" role="tab" aria-controls="report-violation" aria-selected="false">Report Violation</a>
                </li>
            </ul>
        </nav>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="my-reports" role="tabpanel" aria-labelledby="my-reports-tab">
                <?php
                $sql = "SELECT v.violation_id, v.violation_date, v.description, v.image_path, v.status, u.username FROM violations v JOIN users u ON v.reporter_id = u.user_id WHERE v.reporter_id = :user_id ORDER BY v.violation_date DESC";
                displayReports($conn, $sql, "My Reports", $user_id);
                ?>
            </div>

            <div class="tab-pane fade" id="community-reports" role="tabpanel" aria-labelledby="community-reports-tab">
                <?php
                $sql = "SELECT v.violation_id, v.violation_date, v.description, v.image_path, v.status, u.username FROM violations v JOIN users u ON v.reporter_id = u.user_id WHERE v.is_public = TRUE ORDER BY v.violation_date DESC";
                displayReports($conn, $sql, "Community Reports", null);
                ?>
            </div>

            <div class="tab-pane fade" id="report-violation" role="tabpanel" aria-labelledby="report-violation-tab">
                <h3>Report a Waste Violation</h3>
                <form method="post" action="report_violation.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="description">Description of Violation:</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="image">Upload Image/Video:</label>
                        <input type="file" class="form-control-file" id="image" name="image[]" accept="image/*,video/*" multiple>
                        <small class="form-text text-muted">Upload photos or videos as evidence of the violation.</small>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_public" name="is_public" checked>
                            <label class="form-check-label" for="is_public">Make this report public</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="report_only" name="report_only">
                            <label class="form-check-label" for="report_only">Report only to LGU/Landlord</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Report</button>
                </form>
            </div>
        </div>
    </section>

    <footer class="bg-light text-center py-3">
        <p>&copy; 2025 ECOSORT Capstone Project</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(function () {
            $('#myTab a').on('click', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });
    </script>
</body>
</html>