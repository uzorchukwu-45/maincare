<?php 
// Prevent double loading of config
require_once 'config.php'; 

// Only start session if one isn't already active

    session_start(); 

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>DoseCare</title>

    <style>
        /* 1. Make Navbar Background White */
        .navbar-custom {
            background-color: #ffffffdc !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); /* Adds a subtle shadow */
            padding: 15px 0;
        }

        /* 2. Style Links as Buttons */
        .navbar-nav .nav-item {
            margin-left: 10px; /* Space between buttons */
        }

        .navbar-nav .nav-link {
            background-color: #565e8b; /* Your Theme Blue */
            color: white !important;
            padding: 8px 25px !important;
            border-radius: 25px; /* Rounded button edges */
            font-weight: 600;
            transition: all 0.3s ease;
            text-align: center;
        }

        /* Hover Effect */
        .navbar-nav .nav-link:hover {
            background-color: #003049; /* Darker blue on hover */
            transform: translateY(-2px); /* Slight lift effect */
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        /* Active Link Style */
        .navbar-nav .nav-link.active {
            background-color: #003049;
            border: 2px solid #565e8b;
        }
    </style>
  </head>
  <body>

<nav class="navbar navbar-expand-lg navbar-custom">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">
        <img src="./images/doselogo.png" alt="logo" width="100">
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" style="border-color: #565e8b;">
      <span class="navbar-toggler-icon" style="background-image: url('data:image/svg+xml;charset=utf8,%3Csvg viewBox=\'0 0 30 30\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath stroke=\'%23565e8b\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-miterlimit=\'10\' d=\'M4 7h22M4 15h22M4 23h22\'/%3E%3C/svg%3E');"></span>
    </button>
    
    <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
      <ul class="navbar-nav">
            
        <li class="nav-item">
          <a class="nav-link active" href="index.php">Home</a>
        </li>

         <?php if (!isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
              <a class="nav-link" href="register.php">Register</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="login.php">Login</a>
            </li>
        <?php else: ?>
            <li class="nav-item">
              <a class="nav-link" href="logout.php" style="background-color: #dc3545;">Logout</a>
            </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>