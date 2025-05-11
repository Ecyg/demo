<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Vulnerability Playground'; ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/navigation.js" defer></script>
    <script src="js/script.js" defer></script>
    <?php if (isset($extraHeadContent)) echo $extraHeadContent; ?>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="hamburger" id="menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="logo">Uncommon Vulnerability Playground</div>
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
        <!-- Menu items dynamically inserted by JavaScript -->
        <ul class="menu-items"></ul>
    </nav>

    <!-- Overlay for closing sidebar when clicking outside -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Main Content Area -->
    <main class="main-content">
        <?php 
        // Content file is defined by the page that includes this template
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