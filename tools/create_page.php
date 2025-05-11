<?php
/**
 * Page Creator Tool
 * 
 * A simple utility script to create new pages with the correct structure.
 * Usage: php tools/create_page.php [page-name] [page-title]
 * 
 * Example: php tools/create_page.php new_vulnerability "New Vulnerability Demo"
 */

// Check if arguments are provided
if ($argc < 3) {
    echo "Usage: php create_page.php [page-name] [page-title]\n";
    echo "Example: php create_page.php new_vulnerability \"New Vulnerability Demo\"\n";
    exit(1);
}

// Get arguments
$pageName = $argv[1];
$pageTitle = $argv[2];

// Normalize page name
$pageName = strtolower(str_replace(' ', '_', $pageName));
$contentFileName = "{$pageName}-content.php";

// Create paths
$pageFile = "../{$pageName}.php";
$contentFile = "../content/{$contentFileName}";

// Check if files already exist
if (file_exists($pageFile)) {
    echo "Error: Page file already exists: {$pageFile}\n";
    exit(1);
}

if (file_exists($contentFile)) {
    echo "Error: Content file already exists: {$contentFile}\n";
    exit(1);
}

// Create page file
$pageContent = <<<EOT
<?php
\$pageTitle = '{$pageTitle}';
\$contentFile = "content/{$contentFileName}";

// Include the base template which will render the page
include 'includes/base_template.php';
?>
EOT;

// Create content file
$contentTemplate = <<<EOT
<section class="content-section">
    <h1>{$pageTitle}</h1>
    
    <div class="info-box">
        <p><strong>Description:</strong> Add description here.</p>
    </div>

    <div class="demo-box">
        <h2>Demonstration</h2>
        <p>Replace this with your content.</p>
    </div>
</section>

<style>
.info-box, .demo-box {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
}

.info-box {
    background-color: #e3f2fd;
    border-left: 5px solid #2196F3;
}

.demo-box {
    background-color: #f9f9f9;
    border: 1px solid #ddd;
}
</style>
EOT;

// Write files
if (file_put_contents($pageFile, $pageContent)) {
    echo "Created page file: {$pageFile}\n";
} else {
    echo "Error creating page file: {$pageFile}\n";
}

if (file_put_contents($contentFile, $contentTemplate)) {
    echo "Created content file: {$contentFile}\n";
} else {
    echo "Error creating content file: {$contentFile}\n";
}

echo "\nRemember to update the navigation.js file to include your new page!\n";
echo "Add the following to the appropriate section in js/navigation.js:\n\n";

echo "For a main menu item:\n";
echo "{\n";
echo "    label: \"{$pageTitle}\",\n";
echo "    url: \"{$pageName}.php\",\n";
echo "    submenu: null\n";
echo "}\n\n";

echo "For a submenu item (under an existing parent):\n";
echo "{ label: \"{$pageTitle}\", url: \"{$pageName}.php\" }\n";

echo "\nDone!\n";
?>