<?php
$pageTitle = 'Angular XSS Vulnerability';
$contentFile = "content/angular_xss-content.php";

// Include AngularJS in the head section
$extraHeadContent = '
<!-- This will be included in the head section -->
';

// Include the base template which will render the page
include 'includes/base_template.php';
?>