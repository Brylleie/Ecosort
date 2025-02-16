<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ECOSORT</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <header class="bg-dark text-white py-3">
        <div class="container">
            <h1>ECOSORT</h1>
        </div>
    </header>

    <section id="register" class="container mt-5">
        <h2>Register</h2>
        <?php
        include 'db_connect.php';
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Database connection details
            $servername = "localhost";
            $db_username = "root"; // Renamed to avoid conflict
            $db_password = ""; // Renamed to avoid conflict
            $dbname = "ecosort";

            try {
                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $db_username, $db_password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Get form data
                $username = $_POST["username"];
                $password = $_POST["password"];
                $user_type = $_POST["user_type"]; //LGU, Landlord, Resident
                $email = $_POST["email"];

                // Validate inputs (important!)
                if (empty($username) || empty($password) || empty($user_type) || empty($email)) {
                    $error = "All fields are required.";
                } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "Invalid email format";
                } else {
                    // Hash the password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Prepare and execute the SQL query
                    $stmt = $conn->prepare("INSERT INTO users (username, password, user_type, email) VALUES (:username, :password, :user_type, :email)");
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':password', $hashed_password); // STORE HASHED PASSWORD
                    $stmt->bindParam(':user_type', $user_type);
                    $stmt->bindParam(':email', $email);

                    if ($stmt->execute()) {
                        $success = "Registration successful! You can now <a href='login.php'>login</a>.";
                    } else {
                        $error = "Registration failed. Please try again.";
                    }
                }
            } catch(PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }

            $conn = null; // Close the database connection
        }
        ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="post" action="register.php">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="user_type">User Type:</label>
                <select id="user_type" name="user_type" class="form-control" required>
                    <option value="lgu">LGU Agent</option>
                    <option value="landlord">Landlord</option>
                    <option value="resident">Resident</option>
                </select>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </section>

    <footer class="bg-dark text-white py-3">
        <div class="container text-center">
            <p>&copy; 2025 ECOSORT Capstone Project</p>
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
