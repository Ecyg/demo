<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Define allowed pages to prevent arbitrary file inclusion
$allowed_pages = ['home', 'about', 'contact'];

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple PHP Website</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        nav { margin-bottom: 20px; }
        nav a { margin: 0 10px; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <nav>
        <a href="index.php?page=home">Home</a>
        <a href="/vulnerable_app.php">Mass Assignment</a>
        <a href="/xss_hpp.php">XSS via HTTP Parameter Pollution</a>
        <a href="/type_juggling.php">Type Juggling</a>
    </nav>
    
    <div>
        <?php include "$page.php"; ?>
    </div>
</body>
</html>