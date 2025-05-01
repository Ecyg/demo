<?php
// Initialize Angular application with search functionality
// This is an intentionally vulnerable implementation
?>

<!-- Angular Application -->
<div ng-app>
    <h1>Product Search</h1>
    
    <div class="search-box">
        <form method="GET" action="angular_xss.php">
            <input type="text" name="q" placeholder="Search for products..." 
                   value="<?php echo isset($_GET['q']) ? $_GET['q'] : ''; ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <?php if(isset($_GET['q']) && !empty($_GET['q'])): ?>
        <div class="search-results">
            <!-- VULNERABLE: Directly placing user input inside Angular expression context without proper escaping -->
            <h2>Search Results for: {{<?php echo $_GET['q']; ?>}}</h2>
            
            <div class="result-item">
                <!-- This is also vulnerable, but in a different way -->
                <div ng-init="searchTerm='<?php echo $_GET['q']; ?>'">
                    <p>You searched for: {{searchTerm}}</p>
                </div>
                
                <!-- Mock search results -->
                <div class="no-results">
                    <p>No products found matching your search.</p>
                    <p>Try using different keywords or browse our categories.</p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="welcome-message">
            <p>Welcome to our product search. Enter a keyword to begin searching.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Angular JS library (specifically vulnerable version) -->
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.6/angular.min.js"></script>

<style>
    .search-box {
        margin: 20px 0;
    }
    
    input[type="text"] {
        padding: 10px;
        width: 300px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    
    button {
        padding: 10px 15px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    
    .search-results {
        margin-top: 20px;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 4px;
    }
    
    .result-item {
        margin-top: 15px;
    }
    
    .welcome-message {
        margin: 30px 0;
        padding: 20px;
        background-color: #e8f4fc;
        border-radius: 4px;
    }
</style>