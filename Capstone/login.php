<?php
    session_start();

    // Check if the user is already logged in
    if (isset($_SESSION["user_id"])) {
        // Redirect to the appropriate dashboard based on user_type
        if ($_SESSION["user_type"] == "lgu") {
            header("Location: lgu_dashboard.php");
            exit();
        } elseif ($_SESSION["user_type"] == "landlord") {
            header("Location: landlord_dashboard.php");
            exit();
        } elseif ($_SESSION["user_type"] == "resident") {
            header("Location: resident_dashboard.php");
            exit();
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Database connection details
        $servername = "localhost";
        $db_username = "root";
        $db_password = "";
        $dbname = "ecosort";

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $db_username, $db_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Get form data
            $username = $_POST["username"];
            $password = $_POST["password"];

            // Prepare and execute the SQL query
            $stmt = $conn->prepare("SELECT user_id, user_type, password FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                // Verify the password using password_verify()
                if (password_verify($password, $result['password'])) {
                    // Password is correct!
                    $_SESSION["user_id"] = $result['user_id'];
                    $_SESSION["user_type"] = $result['user_type'];

                    // Redirect to the appropriate dashboard based on user_type
                    if ($_SESSION["user_type"] == "lgu") {
                        header("Location: lgu_dashboard.php");
                        exit();
                    } elseif ($_SESSION["user_type"] == "landlord") {
                        header("Location: landlord_dashboard.php");
                        exit();
                    } elseif ($_SESSION["user_type"] == "resident") {
                        header("Location: resident_dashboard.php");
                        exit();
                    }
                } else {
                    $error = "Invalid username or password.";
                }
            } else {
                $error = "Invalid username or password.";
            }
        } catch(PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }

        $conn = null; // Close the database connection
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - ECOSORT</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <header class="bg-light py-3">
            <div class="container">
                <h1>ECOSORT - Login</h1>
            </div>
        </header>

        <section id="login" class="container mt-5">
            <h2>Login</h2>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="post" action="login.php">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Login</button>
                <p>Don't have an account? <a href="register.php">Register here</a>.</p>
            </form>
        </section>

        <footer class="bg-light text-center py-3">
            <p>&copy; 2025 ECOSORT Capstone Project</p>
        </footer>
     <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>
