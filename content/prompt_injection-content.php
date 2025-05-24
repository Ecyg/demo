<?php
// Configuration
$OLLAMA_HOST = 'http://localhost:11434';
$DEFAULT_MODEL = 'llama3.2'; // Change this to your preferred model

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'chat') {
        $message = $_POST['message'] ?? '';
        $model = $_POST['model'] ?? $DEFAULT_MODEL;
        
        if (empty($message)) {
            echo json_encode(['error' => 'Message cannot be empty']);
            exit;
        }
        
        // Prepare the request to Ollama
        $data = [
            'model' => $model,
            'prompt' => $message,
            'stream' => false
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $OLLAMA_HOST . '/api/generate');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            echo json_encode(['error' => 'Connection error: ' . curl_error($ch)]);
        } elseif ($httpCode !== 200) {
            echo json_encode(['error' => 'Server error: HTTP ' . $httpCode]);
        } else {
            $result = json_decode($response, true);
            if (isset($result['response'])) {
                echo json_encode(['response' => $result['response']]);
            } else {
                echo json_encode(['error' => 'Invalid response from Ollama']);
            }
        }
        
        curl_close($ch);
        exit;
    }
    
    if ($_POST['action'] === 'models') {
        // Get available models
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $OLLAMA_HOST . '/api/tags');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch) || $httpCode !== 200) {
            echo json_encode(['models' => []]);
        } else {
            $result = json_decode($response, true);
            $models = [];
            if (isset($result['models'])) {
                foreach ($result['models'] as $model) {
                    $models[] = $model['name'];
                }
            }
            echo json_encode(['models' => $models]);
        }
        
        curl_close($ch);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ollama Chatbot</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .chat-container {
            width: 90%;
            max-width: 800px;
            height: 80vh;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .chat-header {
            background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }
        
        .chat-header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .model-selector {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }
        
        .model-selector select {
            padding: 8px 12px;
            border: none;
            border-radius: 20px;
            background: rgba(255,255,255,0.2);
            color: white;
            outline: none;
        }
        
        .model-selector select option {
            color: #333;
        }
        
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f8f9fa;
        }
        
        .message {
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
        }
        
        .message.user {
            justify-content: flex-end;
        }
        
        .message-content {
            max-width: 70%;
            padding: 15px 20px;
            border-radius: 20px;
            position: relative;
        }
        
        .message.user .message-content {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom-right-radius: 5px;
        }
        
        .message.bot .message-content {
            background: white;
            color: #333;
            border: 1px solid #e1e8ed;
            border-bottom-left-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .message-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin: 0 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }
        
        .message.user .message-avatar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            order: 2;
        }
        
        .message.bot .message-avatar {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .chat-input {
            padding: 20px;
            background: white;
            border-top: 1px solid #e1e8ed;
        }
        
        .input-group {
            display: flex;
            gap: 10px;
        }
        
        .input-group input {
            flex: 1;
            padding: 15px 20px;
            border: 2px solid #e1e8ed;
            border-radius: 25px;
            outline: none;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .input-group input:focus {
            border-color: #4facfe;
        }
        
        .input-group button {
            padding: 15px 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: transform 0.2s;
        }
        
        .input-group button:hover {
            transform: translateY(-2px);
        }
        
        .input-group button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
            color: #666;
        }
        
        .loading.show {
            display: block;
        }
        
        .error {
            background: #fee;
            color: #c33;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #c33;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .typing {
            animation: pulse 1.5s infinite;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <h1>🤖 Ollama Chatbot</h1>
            <p>Powered by Ollama AI</p>
            <div class="model-selector">
                <select id="modelSelect">
                    <option value="<?php echo htmlspecialchars($DEFAULT_MODEL); ?>">
                        <?php echo htmlspecialchars($DEFAULT_MODEL); ?>
                    </option>
                </select>
            </div>
        </div>
        
        <div class="chat-messages" id="chatMessages">
            <div class="message bot">
                <div class="message-avatar">🤖</div>
                <div class="message-content">
                    Hello! I'm your Ollama-powered AI assistant. How can I help you today?
                </div>
            </div>
        </div>
        
        <div class="loading" id="loading">
            <div class="typing">🤖 AI is thinking...</div>
        </div>
        
        <div class="chat-input">
            <div class="input-group">
                <input type="text" id="messageInput" placeholder="Type your message here..." 
                       onkeypress="handleKeyPress(event)">
                <button onclick="sendMessage()" id="sendButton">Send</button>
            </div>
        </div>
    </div>

    <script>
        let isWaiting = false;
        
        // Load available models on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadModels();
        });
        
        function loadModels() {
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=models'
            })
            .then(response => response.json())
            .then(data => {
                if (data.models && data.models.length > 0) {
                    const select = document.getElementById('modelSelect');
                    select.innerHTML = '';
                    data.models.forEach(model => {
                        const option = document.createElement('option');
                        option.value = model;
                        option.textContent = model;
                        if (model === '<?php echo $DEFAULT_MODEL; ?>') {
                            option.selected = true;
                        }
                        select.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading models:', error);
            });
        }
        
        function handleKeyPress(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendMessage();
            }
        }
        
        function sendMessage() {
            if (isWaiting) return;
            
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            
            if (!message) return;
            
            // Add user message to chat
            addMessage(message, 'user');
            messageInput.value = '';
            
            // Show loading
            showLoading(true);
            isWaiting = true;
            
            // Get selected model
            const selectedModel = document.getElementById('modelSelect').value;
            
            // Send to server
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=chat&message=${encodeURIComponent(message)}&model=${encodeURIComponent(selectedModel)}`
            })
            .then(response => response.json())
            .then(data => {
                showLoading(false);
                isWaiting = false;
                
                if (data.error) {
                    addMessage('Error: ' + data.error, 'bot', true);
                } else {
                    addMessage(data.response, 'bot');
                }
            })
            .catch(error => {
                showLoading(false);
                isWaiting = false;
                console.error('Error:', error);
                addMessage('Connection error. Please check if Ollama is running.', 'bot', true);
            });
        }
        
        function addMessage(content, sender, isError = false) {
            const chatMessages = document.getElementById('chatMessages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}`;
            
            const avatar = document.createElement('div');
            avatar.className = 'message-avatar';
            avatar.textContent = sender === 'user' ? '👤' : '🤖';
            
            const messageContent = document.createElement('div');
            messageContent.className = 'message-content';
            if (isError) {
                messageContent.className += ' error';
            }
            messageContent.textContent = content;
            
            messageDiv.appendChild(avatar);
            messageDiv.appendChild(messageContent);
            
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        
        function showLoading(show) {
            const loading = document.getElementById('loading');
            const sendButton = document.getElementById('sendButton');
            
            if (show) {
                loading.classList.add('show');
                sendButton.disabled = true;
            } else {
                loading.classList.remove('show');
                sendButton.disabled = false;
            }
        }
    </script>
</body>
</html>