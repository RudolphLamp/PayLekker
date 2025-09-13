<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Assistant - PayLekker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/dashboard-enhancements.css">
    <style>
        /* Force all icons to be black */
        i, .bi, [class*="bi-"] {
            color: #000000 !important;
        }
        
        .chat-container {
            height: 70vh;
            display: flex;
            flex-direction: column;
        }
        
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 1rem;
        }
        
        .message {
            margin-bottom: 1rem;
            display: flex;
            align-items: flex-start;
        }
        
        .message.user {
            flex-direction: row-reverse;
        }
        
        .message-content {
            max-width: 70%;
            padding: 0.75rem 1rem;
            border-radius: 18px;
            position: relative;
        }
        
        .message.user .message-content {
            background: #000000;
            color: white;
            margin-left: 1rem;
        }
        
        .message.assistant .message-content {
            background: white;
            border: 1px solid #dee2e6;
            margin-right: 1rem;
            color: #000000;
        }
        
        .message-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        
        .message.user .message-avatar {
            background: #000000;
            color: white;
        }
        
        .message.assistant .message-avatar {
            background: #f8f9fa;
            color: #000000;
            border: 1px solid #dee2e6;
        }
        
        .message-time {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
        
        .chat-input {
            border-top: 1px solid #dee2e6;
            padding-top: 1rem;
        }
        
        .suggestions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .suggestion-btn {
            background: white;
            border: 1px solid #000000;
            color: #000000;
            border-radius: 20px;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .suggestion-btn:hover {
            background: #000000;
            color: white;
        }
        
        .typing-indicator {
            display: none;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .typing-dots {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 18px;
            margin-right: 1rem;
        }
        
        .typing-dots span {
            height: 8px;
            width: 8px;
            background: #6c757d;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
            animation: typing 1.5s infinite;
        }
        
        .typing-dots span:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .typing-dots span:nth-child(3) {
            animation-delay: 0.4s;
            margin-right: 0;
        }
        
        @keyframes typing {
            0%, 60%, 100% {
                transform: translateY(0);
                opacity: 0.4;
            }
            30% {
                transform: translateY(-10px);
                opacity: 1;
            }
        }
        
        /* Match dashboard styling */
        .card-header {
            background: #000000 !important;
            color: white !important;
        }
        
        .card-header h6 {
            color: white !important;
        }
        
        .card-header i {
            color: white !important;
        }
        
        .btn-outline-light {
            border-color: white;
            color: white;
        }
        
        .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: white;
            color: white;
        }
        
        .btn-primary {
            background: #000000;
            border-color: #000000;
        }
        
        .btn-primary:hover {
            background: #343a40;
            border-color: #343a40;
        }
        
        .text-success {
            color: #495057 !important;
        }
        
        .page-header h1 {
            color: #000000;
        }
        
        .page-header .text-muted {
            color: #6c757d !important;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <h4><i class="bi bi-wallet2 me-2"></i>PayLekker</h4>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-item">
                <a href="dashboard.php" class="nav-link">
                    <i class="bi bi-house-door"></i>
                    Dashboard
                </a>
            </div>
            <div class="nav-item">
                <a href="transfer-page.php" class="nav-link">
                    <i class="bi bi-arrow-left-right"></i>
                    Transfer Money
                </a>
            </div>
            <div class="nav-item">
                <a href="history-page.php" class="nav-link">
                    <i class="bi bi-clock-history"></i>
                    Transaction History
                </a>
            </div>
            <div class="nav-item">
                <a href="budget-page.php" class="nav-link">
                    <i class="bi bi-pie-chart"></i>
                    Budget
                </a>
            </div>
            <div class="nav-item">
                <a href="add-funds-page.php" class="nav-link">
                    <i class="bi bi-plus-circle"></i>
                    Add Funds
                </a>
            </div>
            <div class="nav-item">
                <a href="chat-page.php" class="nav-link active">
                    <i class="bi bi-chat-dots"></i>
                    AI Assistant
                </a>
            </div>
            <div class="nav-item">
                <a href="profile-page.php" class="nav-link">
                    <i class="bi bi-person"></i>
                    Profile
                </a>
            </div>
            <div class="nav-item mt-auto">
                <a href="#" class="nav-link" onclick="logout()">
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                </a>
            </div>
        </nav>
    </div>

    <!-- Sidebar Overlay for Mobile -->
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <!-- Main Content -->
    <div id="mainContent" class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <button id="sidebarToggle" class="sidebar-toggle">
                <i class="bi bi-list"></i>
            </button>
            <div class="user-info">
                <span class="user-name" id="userName">Loading...</span>
                <div class="user-avatar">
                    <i class="bi bi-person-circle"></i>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="page-content">
            <div class="page-header">
                <h1><i class="bi bi-chat-dots me-3"></i>AI Financial Assistant</h1>
                <p class="text-muted">Get personalized financial advice and support 24/7</p>
            </div>

            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <!-- Chat Container -->
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="bi bi-robot fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">PayLekker AI Assistant</h6>
                                    <small class="opacity-75">Online • Ready to help</small>
                                </div>
                                <div class="ms-auto">
                                    <button class="btn btn-outline-light btn-sm" onclick="clearChat()">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body p-0">
                            <div class="chat-container">
                                <div class="chat-messages" id="chatMessages">
                                    <!-- Welcome message -->
                                    <div class="message assistant">
                                        <div class="message-avatar">
                                            <i class="bi bi-robot"></i>
                                        </div>
                                        <div>
                                            <div class="message-content">
                                                <p class="mb-0">Hello! 👋 I'm your PayLekker AI assistant. I'm here to help you with:</p>
                                                <ul class="mb-0 mt-2">
                                                    <li>Account balance and transaction inquiries</li>
                                                    <li>Budget planning and financial advice</li>
                                                    <li>Transfer assistance and payment support</li>
                                                    <li>General financial questions</li>
                                                </ul>
                                                <p class="mb-0 mt-2">How can I assist you today?</p>
                                            </div>
                                            <div class="message-time">Just now</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Typing indicator -->
                                <div class="typing-indicator message assistant" id="typingIndicator">
                                    <div class="message-avatar">
                                        <i class="bi bi-robot"></i>
                                    </div>
                                    <div class="typing-dots">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                </div>
                                
                                <!-- Quick suggestions -->
                                <div class="suggestions" id="suggestions">
                                    <button class="suggestion-btn" onclick="sendMessage('What is my current balance?')">
                                        Check Balance
                                    </button>
                                    <button class="suggestion-btn" onclick="sendMessage('Show my recent transactions')">
                                        Recent Transactions
                                    </button>
                                    <button class="suggestion-btn" onclick="sendMessage('Help me create a budget')">
                                        Budget Help
                                    </button>
                                    <button class="suggestion-btn" onclick="sendMessage('How do I send money?')">
                                        Transfer Help
                                    </button>
                                </div>
                                
                                <!-- Chat input -->
                                <div class="chat-input">
                                    <form id="chatForm">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="messageInput" 
                                                   placeholder="Type your message..." maxlength="500" required>
                                            <button class="btn btn-primary" type="submit" id="sendBtn">
                                                <i class="bi bi-send"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Assistant Info -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="bi bi-info-circle me-2"></i>AI Assistant Features</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Real-time account information
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Personalized financial advice
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Budget and spending insights
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Transaction assistance
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    24/7 availability
                                </li>
                                <li class="mb-0">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Secure and private
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6><i class="bi bi-lightbulb me-2"></i>Quick Tips</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Ask about your finances:</strong>
                                <p class="small text-muted mb-0">"What's my balance?", "Show recent spending"</p>
                            </div>
                            <div class="mb-3">
                                <strong>Get budget advice:</strong>
                                <p class="small text-muted mb-0">"Help me save money", "Budget planning tips"</p>
                            </div>
                            <div class="mb-0">
                                <strong>Transfer assistance:</strong>
                                <p class="small text-muted mb-0">"How to send money?", "Transfer limits"</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/common.js"></script>
    <script>
        let chatHistory = [];
        
        // Check authentication status
        async function checkAuth() {
            const token = sessionStorage.getItem('auth_token');
            const userData = sessionStorage.getItem('user_data');
            
            if (!token) {
                window.location.href = 'auth/login.php';
                return null;
            }
            
            if (userData) {
                try {
                    const user = JSON.parse(userData);
                    updateUserInfo(user);
                    return user;
                } catch (e) {
                    console.error('Failed to parse user data:', e);
                }
            }
            
            // Try to fetch fresh user data
            try {
                const response = await fetch(API_BASE + 'profile.php', {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        sessionStorage.setItem('user_data', JSON.stringify(result.data.user));
                        updateUserInfo(result.data.user);
                        return result.data.user;
                    }
                }
                
                throw new Error('Failed to fetch user data');
                
            } catch (error) {
                console.error('Auth check error:', error);
                sessionStorage.removeItem('auth_token');
                sessionStorage.removeItem('user_data');
                window.location.href = 'auth/login.php';
                return null;
            }
        }
        
        // Update user info in UI
        function updateUserInfo(user) {
            const userNameElement = document.getElementById('userName');
            if (userNameElement) {
                userNameElement.textContent = `${user.first_name} ${user.last_name}`;
            }
            
            console.log('User info updated on chat page:', user);
        }
        
        // Send message to AI
        async function sendMessage(message = null) {
            const messageInput = document.getElementById('messageInput');
            const messageText = message || messageInput.value.trim();
            
            if (!messageText) return;
            
            // Add user message to chat
            addMessage(messageText, 'user');
            
            // Clear input
            if (!message) {
                messageInput.value = '';
            }
            
            // Show typing indicator
            showTyping(true);
            
            // Hide suggestions
            document.getElementById('suggestions').style.display = 'none';
            
            const token = sessionStorage.getItem('auth_token');
            const userData = JSON.parse(sessionStorage.getItem('user_data') || '{}');
            
            try {
                const response = await fetch(API_BASE + 'chatbot.php', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        message: messageText,
                        user_context: {
                            first_name: userData.first_name,
                            balance: userData.account_balance,
                            user_id: userData.id
                        }
                    })
                });
                
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const responseText = await response.text();
                console.log('Response text:', responseText);
                
                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    console.error('Raw response:', responseText);
                    throw new Error('Invalid response format from server');
                }
                
                console.log('Chat API response:', result);
                
                if (result.success) {
                    // Add AI response to chat
                    addMessage(result.data.response, 'assistant');
                    
                    // Show suggestions if any
                    if (result.data.suggestions && result.data.suggestions.length > 0) {
                        setTimeout(() => {
                            showSuggestions(result.data.suggestions);
                        }, 500);
                    }
                } else {
                    throw new Error(result.error || 'Failed to get AI response');
                }
                
            } catch (error) {
                console.error('Chat error:', error);
                addMessage('Sorry, I\'m having trouble connecting right now. Please try again later. 🤖\n\nError details: ' + error.message, 'assistant');
                
                // Show default suggestions on error
                setTimeout(() => {
                    showSuggestions([
                        'Check my balance',
                        'Recent transactions',
                        'Budget advice',
                        'How to send money'
                    ]);
                }, 500);
            } finally {
                showTyping(false);
            }
        }
        
        // Add message to chat
        function addMessage(text, sender) {
            const messagesContainer = document.getElementById('chatMessages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}`;
            
            const time = new Date().toLocaleTimeString('en-ZA', {
                hour: '2-digit',
                minute: '2-digit'
            });
            
            const avatarIcon = sender === 'user' ? 'bi-person' : 'bi-robot';
            
            // Format message text (preserve line breaks)
            const formattedText = text.replace(/\n/g, '<br>');
            
            messageDiv.innerHTML = `
                <div class="message-avatar">
                    <i class="bi ${avatarIcon}"></i>
                </div>
                <div>
                    <div class="message-content">
                        <div class="mb-0">${formattedText}</div>
                    </div>
                    <div class="message-time">${time}</div>
                </div>
            `;
            
            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            
            // Store in chat history
            chatHistory.push({
                text: text,
                sender: sender,
                time: time
            });
        }
        
        // Show/hide typing indicator
        function showTyping(show) {
            const typingIndicator = document.getElementById('typingIndicator');
            const messagesContainer = document.getElementById('chatMessages');
            
            if (show) {
                typingIndicator.style.display = 'flex';
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            } else {
                typingIndicator.style.display = 'none';
            }
        }
        
        // Show suggestions
        function showSuggestions(suggestions) {
            const suggestionsContainer = document.getElementById('suggestions');
            
            if (!suggestions || suggestions.length === 0) {
                suggestionsContainer.style.display = 'none';
                return;
            }
            
            const suggestionsHtml = suggestions.map(suggestion => `
                <button class="suggestion-btn" onclick="sendMessage('${suggestion.replace(/'/g, '\\\'')}')">${suggestion}</button>
            `).join('');
            
            suggestionsContainer.innerHTML = suggestionsHtml;
            suggestionsContainer.style.display = 'flex';
        }
        
        // Clear chat
        function clearChat() {
            if (confirm('Are you sure you want to clear the chat history?')) {
                document.getElementById('chatMessages').innerHTML = `
                    <!-- Welcome message -->
                    <div class="message assistant">
                        <div class="message-avatar">
                            <i class="bi bi-robot"></i>
                        </div>
                        <div>
                            <div class="message-content">
                                <p class="mb-0">Hello! 👋 I'm your PayLekker AI assistant. How can I help you today?</p>
                            </div>
                            <div class="message-time">Just now</div>
                        </div>
                    </div>
                `;
                
                // Reset suggestions
                document.getElementById('suggestions').innerHTML = `
                    <button class="suggestion-btn" onclick="sendMessage('What is my current balance?')">
                        Check Balance
                    </button>
                    <button class="suggestion-btn" onclick="sendMessage('Show my recent transactions')">
                        Recent Transactions
                    </button>
                    <button class="suggestion-btn" onclick="sendMessage('Help me create a budget')">
                        Budget Help
                    </button>
                    <button class="suggestion-btn" onclick="sendMessage('How do I send money?')">
                        Transfer Help
                    </button>
                `;
                document.getElementById('suggestions').style.display = 'flex';
                
                chatHistory = [];
            }
        }
        
        // Handle form submission
        document.getElementById('chatForm').addEventListener('submit', function(e) {
            e.preventDefault();
            sendMessage();
        });
        
        // Handle Enter key in input
        document.getElementById('messageInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
        
        // Setup sidebar
        function setupSidebar() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('show');
            });
            
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('active');
                overlay.classList.remove('show');
            });
        }
        
        // Logout function
        async function logout() {
            if (confirm('Are you sure you want to logout?')) {
                const token = sessionStorage.getItem('auth_token');
                
                try {
                    await fetch(API_BASE + 'logout.php', {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Content-Type': 'application/json'
                        }
                    });
                } catch (error) {
                    console.error('Logout error:', error);
                }
                
                sessionStorage.removeItem('auth_token');
                sessionStorage.removeItem('user_data');
                window.location.href = 'auth/login.php';
            }
        }
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', async function() {
            const user = await checkAuth();
            if (user) {
                setupSidebar();
                
                // Send welcome message with user context
                setTimeout(() => {
                    sendMessage('Hello');
                }, 1000);
            }
        });
    </script>
</body>
</html>