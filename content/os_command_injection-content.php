<?php
// home-content.php - Safe system information display
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Information</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #007cba;
            padding-bottom: 10px;
        }
        .info-section {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #007cba;
        }
        .info-label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }
        .info-value {
            font-family: 'Courier New', monospace;
            background: #e8e8e8;
            padding: 8px;
            border-radius: 4px;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>System Information Dashboard</h1>
        
        <div class="info-section">
            <div class="info-label">Complete System Information:</div>
            <div class="info-value"><?php echo htmlspecialchars(php_uname()); ?></div>
        </div>
        
        <div class="info-section">
            <div class="info-label">Operating System:</div>
            <div class="info-value"><?php echo htmlspecialchars(php_uname('s')); ?></div>
        </div>
        
        <div class="info-section">
            <div class="info-label">Host Name:</div>
            <div class="info-value"><?php echo htmlspecialchars(php_uname('n')); ?></div>
        </div>
        
        <div class="info-section">
            <div class="info-label">Release Version:</div>
            <div class="info-value"><?php echo htmlspecialchars(php_uname('r')); ?></div>
        </div>
        
        <div class="info-section">
            <div class="info-label">Version Information:</div>
            <div class="info-value"><?php echo htmlspecialchars(php_uname('v')); ?></div>
        </div>
        
        <div class="info-section">
            <div class="info-label">Machine Type:</div>
            <div class="info-value"><?php echo htmlspecialchars(php_uname('m')); ?></div>
        </div>
        
        <div class="info-section">
            <div class="info-label">PHP Version:</div>
            <div class="info-value"><?php echo htmlspecialchars(phpversion()); ?></div>
        </div>
        
        <div class="info-section">
            <div class="info-label">Server Software:</div>
            <div class="info-value"><?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Not available'); ?></div>
        </div>
        
        <div class="info-section">
            <div class="info-label">Current Working Directory:</div>
            <div class="info-value"><?php echo htmlspecialchars(getcwd()); ?></div>
        </div>
        
        <div class="info-section">
            <div class="info-label">Server Time:</div>
            <div class="info-value"><?php echo htmlspecialchars(date('Y-m-d H:i:s T')); ?></div>
        </div>

<div class="info-section">
    <div class="info-label">Check if an IP address is internal</div>
    <form method="POST">
        <input type="text" name="ip" placeholder="Enter IP address" required>
        <button type="submit">Check</button>
    </form>
    <div class="info-value">
        <?php 
        if (isset($_POST['ip'])) {
            $ip = $_POST['ip'];
           
            echo system("ping -c 1 $ip");
        }
        ?>
    </div>
</div>
</body>
</html>