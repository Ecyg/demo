<?php
class User {
    public $username;
    public $email;
    public $role = 'user'; // Default role, but can be overridden due to Mass Assignment

    public function __construct($data) {
        foreach ($data as $key => $value) {
            $this->$key = $value; // Mass assignment vulnerability
        }
    }
   
    public function getRoleMessage() {
        return $this->role === 'admin' ? "<span style='color: red;'>You are an Admin!</span>" : "You are a regular user.";
    }
} 



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User($_POST);
    echo "<h2>User Created:</h2>";
    echo "Username:" . htmlspecialchars($user->username) . "<br>";
    echo "Email: " . htmlspecialchars($user->email) . "<br>";
    echo "Role: " . htmlspecialchars($user->role) . "<br>"; 
    echo "<strong>" . $user->getRoleMessage() . "</strong>";
}
?>
<link rel="stylesheet" href="css/style.css">
<body>
<h1>Mass Assignment Vulnerability Example</h1>
<form method="POST" action="">
    <label>Username: <input type="text" name="username"></label><br>
    <label>Email: <input type="email" name="email"></label><br>
    <br>
    <button type="submit">Create User</button>
</form>
</body>