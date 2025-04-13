<?php
// Extract raw query string manually
$queryString = $_SERVER['QUERY_STRING'];

// Manually extract all "message" parameters from the raw query
preg_match_all('/message=([^&]*)/', $queryString, $matches);

// If there are multiple "message" parameters, handle them
if (!empty($matches[1])) {
    // Sanitize only the first instance
    $sanitized_first = htmlspecialchars(urldecode($matches[1][0]), ENT_QUOTES, 'UTF-8');

    // Keep subsequent parameters **unsanitized**
    $raw_params = array_slice($matches[1], 1);
    $unsanitized_rest = implode(' ', array_map('urldecode', $raw_params));

    // Construct the final vulnerable message
    $message = $sanitized_first . ' ' . $unsanitized_rest;
} else {
    $message = "No message provided";
}

// Display the message (vulnerable to XSS via HTTP Parameter Pollution)
echo "<h2>Message:</h2>";
echo "<p>" . $message . "</p>"; // XSS can occur in the second parameter
?>

<form method="GET">
    <label>Enter a message: <input type="text" name="message"></label><br>
    <button type="submit">Submit</button>
</form>




