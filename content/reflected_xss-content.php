<?php
// Simple Vulnerable Reflected XSS Page
// Deliberately vulnerable for educational purposes

// Get user input without any sanitization
$name = isset($_GET['name']) ? $_GET['name'] : '';
$comment = isset($_POST['comment']) ? $_POST['comment'] : '';

// Store message if form was submitted
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($comment)) {
    $message = "<div class='message-box success'>Thanks for your comment!</div>";
}
?>

<section class="content-section">
    <h1>Welcome to Our Guestbook</h1>
    
    <!-- Vulnerable greeting - directly outputs user input without sanitization -->
    <?php if(!empty($name)): ?>
        <div class="greeting">
            Hello, <?php echo $name; ?>! <!-- XSS VULNERABILITY: No escaping of user input -->
        </div>
    <?php endif; ?>
    
    <!-- Display message after form submission -->
    <?php echo $message; ?>
    
    <div class="guestbook-form">
        <h2>Leave a Comment</h2>
        <form method="POST" action="?name=<?php echo $name; ?>">
            <div class="form-group">
                <label for="comment">Your Comment:</label>
                <textarea name="comment" id="comment" rows="4" placeholder="Share your thoughts..."><?php 
                    // XSS VULNERABILITY: No escaping of user input when populating form
                    echo $comment; 
                ?></textarea>
            </div>
            
            <!-- Storing previous comments in hidden field - also vulnerable -->
            <input type="hidden" name="previous_comments" value="<?php echo isset($_POST['previous_comments']) ? $_POST['previous_comments'] : ''; ?>">
            
            <button type="submit" class="btn">Submit Comment</button>
        </form>
    </div>
    
    <div class="search-form">
        <h2>Search Previous Comments</h2>
        <form method="GET">
            <div class="form-group">
                <label for="search">Search Term:</label>
                <input type="text" name="search" id="search" value="<?php 
                    // XSS VULNERABILITY: No escaping when populating search field
                    echo isset($_GET['search']) ? $_GET['search'] : ''; 
                ?>">
            </div>
            <button type="submit" class="btn">Search</button>
        </form>
        
        <?php if(isset($_GET['search']) && !empty($_GET['search'])): ?>
            <div class="search-results">
                <h3>Search Results for: <?php 
                    // XSS VULNERABILITY: No escaping for search term
                    echo $_GET['search']; 
                ?></h3>
                <p>No results found. Try another search term.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="recent-comments">
        <h2>User Preferences</h2>
        <div class="preferences">
            <!-- Vulnerable cookie-based theme setting -->
            <script>
                // XSS VULNERABILITY: Reads cookies and displays without sanitization
                function getCookie(name) {
                    const value = `; ${document.cookie}`;
                    const parts = value.split(`; ${name}=`);
                    if (parts.length === 2) return parts.pop().split(';').shift();
                }
                
                // Display theme preference from cookie - vulnerable to XSS
                document.write("<p>Your theme preference: " + (getCookie('theme') || 'Default') + "</p>");
            </script>
            
            <form method="GET" action="">
                <input type="text" name="theme" placeholder="Set your theme">
                <button type="submit" onclick="document.cookie='theme='+encodeURIComponent(document.getElementsByName('theme')[0].value);">Save Theme</button>
            </form>
        </div>
    </div>
</section>

<!-- Using external CSS file -->
<link rel="stylesheet" href="css/styles.css">

<!-- Additional custom styles for the XSS page -->
<style>
    .greeting {
        background-color: #f0f8ff;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        font-size: 1.2em;
    }
    
    .guestbook-form, .search-form, .recent-comments {
        margin: 20px 0;
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
    }
    
    .message-box {
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 4px;
    }
    
    .message-box.success {
        background-color: #dff0d8;
        color: #3c763d;
    }
    
    .preferences {
        background-color: #f1f1f1;
        padding: 15px;
        border-radius: 4px;
    }
</style>