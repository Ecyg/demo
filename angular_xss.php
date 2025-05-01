<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Site Template'; ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/script.js" defer></script>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="hamburger" id="menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="logo">Uncommon Vulnerablity Playground</div>
        <div class="user-menu">
            <div class="user-avatar">WHS</div>
        </div>
    </header>

    <!-- Sidebar Navigation -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            Main Navigation
            <div class="close-sidebar" id="close-sidebar">×</div>
        </div>
        <ul class="menu-items">
            <li class="menu-item">
                <a href="template.php">Dashboard</a>
            </li>
            <li class="menu-item has-submenu">
                <a href="#" class="submenu-toggle">Vulnerablities</a>
                <ul class="submenu">
                    <li class="submenu-item"><a href="mass_assignment.php">Mass Assignment</a></li>
                    <li class="submenu-item"><a href="hpp.php">XSS via HTTP Parameter Pollution</a></li>
                    <li class="submenu-item"><a href="type_juggling.php">Type Juggling</a></li>
                    <li class="submenu-item"><a href="angular_xss.php">Angular XSS</a></li>
                </ul>
            </li>
            <li class="menu-item has-submenu">
                <a href="#" class="submenu-toggle">Bug Reports</a>
                <ul class="submenu">
                    <li class="submenu-item"><a href="services-service1.php">TBA</a></li>
                    <li class="submenu-item"><a href="services-service2.php">TBA</a></li>
                </ul>
            </li>
            <li class="menu-item">
                <a href="about.php">About Us</a>
            </li>
            <li class="menu-item">
                <a href="contact.php">Contact</a>
            </li>
            <li class="menu-item">
                <a href="settings.php">Settings</a>
            </li>
        </ul>
    </nav>

    <!-- Overlay for closing sidebar when clicking outside -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Main Content Area -->
    <main class="main-content">
        <?php 
        $contentFile = "content/angular_xss-content.php"; // Default content file
        // This is where page-specific content will be included
        if (isset($contentFile) && file_exists($contentFile)) {
            include $contentFile;
        } else {
            echo '<p>No content found.</p>';
        }
        ?>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Your Company. All rights reserved.</p>
    </footer>
</body>
</html>