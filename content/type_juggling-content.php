<?php
// Simulated user database (INSECURE: Weak password storage)
$users = [
    "admin" => "0e123456789",  // Looks like a hash, but it's actually numeric in PHP's eyes
    "user" => "password123"
];

// Process login form submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Insecure authentication check using `==` (Type Juggling Vulnerability)
    if (isset($users[$username]) && $users[$username] == $password) {
        $message = "✅ Welcome, " . htmlspecialchars($username) . "!";
    } else {
        $message = "❌ Invalid login credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vulnerable Login</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
        form { display: inline-block; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background: #f9f9f9; }
        input { display: block; margin: 10px auto; padding: 10px; width: 80%; }
        button { padding: 10px 20px; background: #28a745; color: white; border: none; cursor: pointer; }
        .message { font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>

    <h1>Login</h1>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <div class="message"><?= $message; ?></div>

</body>
</html>

