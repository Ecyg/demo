<?php
// Log headers for demonstration
$log_file = '/tmp/request_headers.log';

// Function to log headers
function log_headers() {
    global $log_file;
    $headers = getallheaders();
    $log_content = date('[Y-m-d H:i:s]') . " - Request Headers:\n";
    
    foreach ($headers as $name => $value) {
        $log_content .= "$name: $value\n";
    }
    
    $log_content .= "Raw request body: " . file_get_contents('php://input') . "\n\n";
    file_put_contents($log_file, $log_content, FILE_APPEND);
}

// Log current request headers
log_headers();

// Process the request based on Content-Length (vulnerable to Request Smuggling)
$response = "";
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Vulnerable: This code doesn't handle multiple/conflicting Content-Length or Transfer-Encoding headers
    // It relies on PHP's handling which can differ from proxies/load balancers
    
    if (isset($_POST['message'])) {
        $response = "Received message: " . htmlspecialchars($_POST['message']);
    } else {
        $response = "No message received";
    }
}

// Weak handling of chunked encoding
if (isset($_SERVER['HTTP_TRANSFER_ENCODING']) && 
    stristr($_SERVER['HTTP_TRANSFER_ENCODING'], 'chunked')) {
    // Vulnerable: This attempts to manually process chunked encoding but is incomplete
    // and would conflict with upstream servers that also process chunking
    $raw_data = file_get_contents('php://input');
    $decoded = "";
    $pos = 0;
    
    while ($pos < strlen($raw_data)) {
        $chunk_size_end = strpos($raw_data, "\r\n", $pos);
        if ($chunk_size_end === false) break;
        
        $size_str = substr($raw_data, $pos, $chunk_size_end - $pos);
        $chunk_size = hexdec(trim($size_str));
        
        if ($chunk_size <= 0) break;
        
        $pos = $chunk_size_end + 2;
        $decoded .= substr($raw_data, $pos, $chunk_size);
        $pos += $chunk_size + 2;
    }
    
    // Log our manual chunked decoding attempt
    file_put_contents($log_file, "Manual chunked decode result: $decoded\n", FILE_APPEND);
}

// Function to get most recently logged headers (for display only)
function get_recent_logs() {
    global $log_file;
    if (file_exists($log_file)) {
        $logs = file_get_contents($log_file);
        $entries = explode("\n\n", $logs);
        // Get last 3 log entries
        $recent = array_slice($entries, -3);
        return implode("\n\n", $recent);
    }
    return "No logs available";
}
?>

<section class="content-section">
    <h1>HTTP Request Smuggling Vulnerability Demo</h1>
    
    <div class="info-box">
        <p><strong>What is HTTP Request Smuggling?</strong> This vulnerability occurs when front-end and back-end servers interpret HTTP requests differently, allowing attackers to "smuggle" unauthorized requests.</p>
        <p>The vulnerability typically arises from inconsistent handling of:</p>
        <ul>
            <li>Content-Length headers</li>
            <li>Transfer-Encoding headers</li>
            <li>Multiple conflicting headers</li>
        </ul>
    </div>

    <div class="demo-box">
        <h2>Test the Vulnerability</h2>
        <p>Submit a request with the form below, then try manipulating headers with a proxy tool like Burp Suite.</p>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="message">Message:</label>
                <input type="text" id="message" name="message" placeholder="Enter a message">
            </div>
            <button type="submit" class="btn">Send Request</button>
        </form>
        
        <?php if (!empty($response)): ?>
            <div class="response-box">
                <h3>Server Response:</h3>
                <p><?php echo $response; ?></p>
            </div>
        <?php endif; ?>
        
        <div class="hint-box">
            <h3>Exploitation Hints:</h3>
            <p>Try using tools like Burp Suite to:</p>
            <ol>
                <li>Send requests with both Content-Length and Transfer-Encoding: chunked headers</li>
                <li>Send duplicate Content-Length headers with different values</li>
                <li>Use "Transfer-Encoding: chunked" but with unusual capitalization or spacing</li>
            </ol>
        </div>
    </div>
    
    <div class="recent-logs">
        <h2>Recent Request Headers</h2>
        <pre><?php echo htmlspecialchars(get_recent_logs()); ?></pre>
    </div>
</section>

<style>
.info-box, .demo-box, .response-box, .hint-box, .recent-logs {
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

.response-box {
    background-color: #e8f5e9;
    border-left: 5px solid #4CAF50;
    margin-top: 20px;
}

.hint-box {
    background-color: #fff3e0;
    border-left: 5px solid #FF9800;
    margin-top: 20px;
}

.recent-logs {
    background-color: #f5f5f5;
    border: 1px solid #ddd;
}

.recent-logs pre {
    white-space: pre-wrap;
    font-family: monospace;
    font-size: 14px;
    max-height: 300px;
    overflow-y: auto;
    padding: 10px;
    background-color: #f8f8f8;
    border: 1px solid #eee;
}

.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

input[type="text"] {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

button.btn {
    background-color: #4a6fa5;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 4px;
    cursor: pointer;
}

button.btn:hover {
    background-color: #166088;
}
</style>