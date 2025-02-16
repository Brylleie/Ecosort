<?php
session_start(); // Start the session (if needed)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECOSORT: Effective Waste Segregation</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
     <!-- Font Awesome CSS (or similar icon library) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="YOUR_SRI_HERE" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Style for the profile icon container */
        .profile-container {
            position: absolute; /* Absolutely position it */
            top: 30px;           /* Adjust from top as needed */
            right: 20px;         /* Adjust from right as needed */
            z-index: 1000;        /* Ensure it's on top of other elements */
        }

        /* Style for the profile icon */
        .profile-icon {
            font-size: 24px; /* Adjust size as needed */
            color: beige;       /* Adjust color as needed */
        }
    </style>
</head>
<body>
    <!-- Profile Icon Container -->
    <div class="profile-container">
        <?php if (isset($_SESSION["user_id"])): ?>
            <a href="profile.php">
                <i class="fas fa-user-circle profile-icon"></i>
            </a>
        <?php endif; ?>
    </div>

    <header class="bg-light py-3">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a class="navbar-brand" href="#">ECOSORT</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#about">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#features">Features</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#gamification">Gamification</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#getinvolved">Get Involved</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact">Contact</a>
                        </li>
                        <?php if (!isset($_SESSION["user_id"])): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="login.php">Login</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <section id="hero" class="jumbotron text-center">
        <div class="container">
            <h2 class="display-4">Empowering Communities Through Effective Waste Segregation</h2>
            <p class="lead">ECOSORT aims to provide universal access to safe, inclusive, and accessible green and public spaces by improving waste segregation.</p>
            <p class="lead">Did you know? Baguio City aims to cut expenses by 65% through improved waste segregation and management! Join us in making a difference.</p>
            <a href="#features" class="btn btn-primary btn-lg">Learn More</a>
        </div>
    </section>

    <section id="about" class="py-5">
        <div class="container">
            <h2>About ECOSORT</h2>
            <p>ECOSORT addresses the challenge of unmanaged waste due to lack of concern, knowledge, and discipline. We aim to improve waste habits, create cleaner communities, and enhance waste management.</p>
            <h3>The Problem</h3>
            <ul>
                <li>Waste isn't properly segregated by residents and tenants.</li>
                <li>LGUs struggle with monitoring and enforcement.</li>
            </ul>
            <p><strong>Meet Alex:</strong> Alex, an LGU agent, is frustrated by unsegregated waste and the difficulty in monitoring. ECOSORT can help Alex to identify violators and promote compliance.</p>
            <p><strong>For Landlords:</strong> ECOSORT can assist in monitoring tenant compliance with waste segregation rules.</p>
        </div>
    </section>

    <section id="features" class="bg-light py-5">
        <div class="container">
            <h2>Key Features</h2>
            <div class="row">
                <div class="col-md-4">
                    <h3>Educational Resources</h3>
                    <p>Easy-to-understand guides and tutorials, promoting awareness of waste segregation benefits.</p>
                </div>
                <div class="col-md-4">
                    <h3>Gamification</h3>
                    <p>Engaging platform to promote better waste management habits. Earn points, badges, and climb the leaderboard!</p>
                    </div>
                                    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>
                   
