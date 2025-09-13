<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-gradient">PayLekker Assistant</h2>
                <a href="dashboard.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <!-- Chat Container -->
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient text-white d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="chat-avatar me-3">
                            <i class="fas fa-robot fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">PayLekker Bot</h6>
                            <small class="text-light">Online • Ready to help</small>
                        </div>
                    </div>
                    <button class="btn btn-outline-light btn-sm" id="clearChat">
                        <i class="fas fa-trash me-2"></i>Clear Chat
                    </button>
                </div>
                
                <!-- Chat Messages -->
                <div class="card-body p-0">
                    <div id="chatContainer" class="chat-container">
                        <div id="chatMessages" class="chat-messages">
                            <!-- Welcome message -->
                            <div class="message bot-message">
                                <div class="message-avatar">
                                    <i class="fas fa-robot"></i>
                                </div>
                                <div class="message-content">
                                    <div class="message-bubble">
                                        <p class="mb-2">👋 Hi there! I'm your PayLekker assistant. I can help you with:</p>
                                        <ul class="mb-2">
                                            <li>Account and transaction questions</li>
                                            <li>Money transfer assistance</li>
                                            <li>Budget and spending tips</li>
                                            <li>Security and safety information</li>
                                            <li>General PayLekker support</li>
                                        </ul>
                                        <p class="mb-0">What can I help you with today?</p>
                                    </div>
                                    <div class="message-time">Just now</div>
                                </div>
                            </div>
                        </div>
                        <div id="typingIndicator" class="typing-indicator" style="display: none;">
                            <div class="message bot-message">
                                <div class="message-avatar">
                                    <i class="fas fa-robot"></i>
                                </div>
                                <div class="message-content">
                                    <div class="message-bubble typing">
                                        <div class="typing-dots">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="card-body border-top">
                    <div class="mb-3">
                        <small class="text-muted fw-semibold">Quick Questions:</small>
                        <div class="d-flex flex-wrap gap-2 mt-2" id="quickQuestions">
                            <button class="btn btn-outline-primary btn-sm quick-question" 
                                    data-question="How do I send money?">
                                <i class="fas fa-paper-plane me-1"></i>Send Money
                            </button>
                            <button class="btn btn-outline-primary btn-sm quick-question" 
                                    data-question="How do I check my balance?">
                                <i class="fas fa-wallet me-1"></i>Check Balance
                            </button>
                            <button class="btn btn-outline-primary btn-sm quick-question" 
                                    data-question="How do I create a budget?">
                                <i class="fas fa-chart-pie me-1"></i>Create Budget
                            </button>
                            <button class="btn btn-outline-primary btn-sm quick-question" 
                                    data-question="Is PayLekker safe?">
                                <i class="fas fa-shield-alt me-1"></i>Security
                            </button>
                            <button class="btn btn-outline-primary btn-sm quick-question" 
                                    data-question="What are the fees?">
                                <i class="fas fa-coins me-1"></i>Fees
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Chat Input -->
                <div class="card-footer bg-light">
                    <form id="chatForm" class="d-flex align-items-center gap-2">
                        <?php echo csrf_token_input(); ?>
                        <div class="flex-grow-1">
                            <div class="input-group">
                                <input type="text" class="form-control" id="messageInput" 
                                       placeholder="Type your message..." maxlength="500" required>
                                <button type="button" class="btn btn-outline-secondary" id="emojiBtn" 
                                        data-bs-toggle="tooltip" title="Add emoji">
                                    <i class="fas fa-smile"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" id="sendBtn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Press Enter to send • Max 500 characters
                        </small>
                    </div>
                </div>
            </div>

            <!-- Chat History -->
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-history me-2"></i>Recent Conversations
                        <button class="btn btn-outline-secondary btn-sm float-end" id="toggleHistory">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </h6>
                </div>
                <div class="card-body collapse" id="chatHistoryCollapse">
                    <div id="chatHistory">
                        <div class="text-center text-muted">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <p>Your recent chat sessions will appear here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Emoji Picker Modal -->
<div class="modal fade" id="emojiModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Choose Emoji</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="emoji-grid">
                    <span class="emoji-item">😀</span>
                    <span class="emoji-item">😊</span>
                    <span class="emoji-item">😂</span>
                    <span class="emoji-item">🤔</span>
                    <span class="emoji-item">😍</span>
                    <span class="emoji-item">😎</span>
                    <span class="emoji-item">🤗</span>
                    <span class="emoji-item">😅</span>
                    <span class="emoji-item">🙏</span>
                    <span class="emoji-item">👍</span>
                    <span class="emoji-item">👏</span>
                    <span class="emoji-item">💰</span>
                    <span class="emoji-item">💳</span>
                    <span class="emoji-item">🏦</span>
                    <span class="emoji-item">📱</span>
                    <span class="emoji-item">✅</span>
                    <span class="emoji-item">❓</span>
                    <span class="emoji-item">💡</span>
                    <span class="emoji-item">🔒</span>
                    <span class="emoji-item">🇿🇦</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.chat-container {
    height: 500px;
    overflow-y: auto;
    background-color: #f8f9fa;
}

.chat-messages {
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.message {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    max-width: 100%;
}

.user-message {
    flex-direction: row-reverse;
}

.user-message .message-content {
    align-items: flex-end;
}

.message-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 16px;
    color: white;
}

.bot-message .message-avatar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.user-message .message-avatar {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.message-content {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    max-width: 80%;
}

.message-bubble {
    padding: 0.75rem 1rem;
    border-radius: 18px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    word-wrap: break-word;
}

.bot-message .message-bubble {
    background: white;
    border-bottom-left-radius: 4px;
}

.user-message .message-bubble {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom-right-radius: 4px;
}

.message-time {
    font-size: 0.75rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.typing-indicator .message-bubble {
    padding: 0.5rem 1rem;
}

.typing-dots {
    display: flex;
    gap: 4px;
    align-items: center;
}

.typing-dots span {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background-color: #6c757d;
    animation: typing 1.4s infinite;
}

.typing-dots span:nth-child(2) { animation-delay: 0.2s; }
.typing-dots span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-10px); }
}

.quick-question {
    transition: all 0.2s;
}

.quick-question:hover {
    transform: translateY(-1px);
}

.emoji-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 10px;
    text-align: center;
}

.emoji-item {
    font-size: 1.5rem;
    padding: 0.5rem;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.emoji-item:hover {
    background-color: #f8f9fa;
}

.chat-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const chatMessages = document.getElementById('chatMessages');
    const chatContainer = document.getElementById('chatContainer');
    const typingIndicator = document.getElementById('typingIndicator');
    const emojiModal = new bootstrap.Modal(document.getElementById('emojiModal'));
    let messageCount = 0;

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Event listeners
    chatForm.addEventListener('submit', sendMessage);
    document.getElementById('clearChat').addEventListener('click', clearChat);
    document.getElementById('emojiBtn').addEventListener('click', () => emojiModal.show());
    document.getElementById('toggleHistory').addEventListener('click', toggleHistory);

    // Quick questions
    document.querySelectorAll('.quick-question').forEach(btn => {
        btn.addEventListener('click', function() {
            const question = this.dataset.question;
            messageInput.value = question;
            sendMessage(new Event('submit'));
        });
    });

    // Emoji picker
    document.querySelectorAll('.emoji-item').forEach(emoji => {
        emoji.addEventListener('click', function() {
            messageInput.value += this.textContent;
            messageInput.focus();
            emojiModal.hide();
        });
    });

    // Enter key to send
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.dispatchEvent(new Event('submit'));
        }
    });

    function sendMessage(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;

        // Add user message to chat
        addMessage(message, 'user');
        messageInput.value = '';

        // Show typing indicator
        showTypingIndicator();

        // Send to chatbot API
        fetch('api/chatbot.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('input[name="csrf_token"]').value
            },
            body: JSON.stringify({
                message: message,
                session_id: getSessionId()
            })
        })
        .then(response => response.json())
        .then(data => {
            hideTypingIndicator();
            if (data.success) {
                addMessage(data.response, 'bot');
                
                // Update quick questions if provided
                if (data.quick_questions) {
                    updateQuickQuestions(data.quick_questions);
                }
            } else {
                addMessage('Sorry, I encountered an error. Please try again.', 'bot');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            hideTypingIndicator();
            addMessage('Sorry, I seem to be having connection issues. Please try again in a moment.', 'bot');
        });
    }

    function addMessage(text, sender) {
        messageCount++;
        const messageId = `message-${messageCount}`;
        const timestamp = new Date();
        const timeString = timestamp.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });

        const messageElement = document.createElement('div');
        messageElement.className = `message ${sender}-message`;
        messageElement.id = messageId;
        
        const avatarIcon = sender === 'user' ? 'fas fa-user' : 'fas fa-robot';
        
        messageElement.innerHTML = `
            <div class="message-avatar">
                <i class="${avatarIcon}"></i>
            </div>
            <div class="message-content">
                <div class="message-bubble">
                    ${formatMessage(text)}
                </div>
                <div class="message-time">${timeString}</div>
            </div>
        `;

        chatMessages.appendChild(messageElement);
        scrollToBottom();

        // Animate message appearance
        setTimeout(() => {
            messageElement.style.opacity = '0';
            messageElement.style.transform = 'translateY(10px)';
            messageElement.style.transition = 'all 0.3s ease';
            setTimeout(() => {
                messageElement.style.opacity = '1';
                messageElement.style.transform = 'translateY(0)';
            }, 10);
        }, 0);
    }

    function formatMessage(text) {
        // Convert URLs to links
        text = text.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" rel="noopener">$1</a>');
        
        // Convert line breaks to <br>
        text = text.replace(/\n/g, '<br>');
        
        // Convert **bold** to <strong>
        text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        
        // Convert *italic* to <em>
        text = text.replace(/\*(.*?)\*/g, '<em>$1</em>');
        
        return text;
    }

    function showTypingIndicator() {
        typingIndicator.style.display = 'block';
        scrollToBottom();
    }

    function hideTypingIndicator() {
        typingIndicator.style.display = 'none';
    }

    function scrollToBottom() {
        setTimeout(() => {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }, 100);
    }

    function clearChat() {
        if (confirm('Are you sure you want to clear the chat history?')) {
            // Keep only the welcome message
            const messages = chatMessages.querySelectorAll('.message:not(:first-child)');
            messages.forEach(message => message.remove());
            messageCount = 0;
            
            // Reset quick questions
            resetQuickQuestions();
        }
    }

    function updateQuickQuestions(questions) {
        const container = document.getElementById('quickQuestions');
        container.innerHTML = questions.map(q => `
            <button class="btn btn-outline-primary btn-sm quick-question" 
                    data-question="${escapeHtml(q.text)}">
                <i class="${q.icon} me-1"></i>${escapeHtml(q.label)}
            </button>
        `).join('');
        
        // Re-attach event listeners
        container.querySelectorAll('.quick-question').forEach(btn => {
            btn.addEventListener('click', function() {
                const question = this.dataset.question;
                messageInput.value = question;
                sendMessage(new Event('submit'));
            });
        });
    }

    function resetQuickQuestions() {
        const container = document.getElementById('quickQuestions');
        container.innerHTML = `
            <button class="btn btn-outline-primary btn-sm quick-question" 
                    data-question="How do I send money?">
                <i class="fas fa-paper-plane me-1"></i>Send Money
            </button>
            <button class="btn btn-outline-primary btn-sm quick-question" 
                    data-question="How do I check my balance?">
                <i class="fas fa-wallet me-1"></i>Check Balance
            </button>
            <button class="btn btn-outline-primary btn-sm quick-question" 
                    data-question="How do I create a budget?">
                <i class="fas fa-chart-pie me-1"></i>Create Budget
            </button>
            <button class="btn btn-outline-primary btn-sm quick-question" 
                    data-question="Is PayLekker safe?">
                <i class="fas fa-shield-alt me-1"></i>Security
            </button>
            <button class="btn btn-outline-primary btn-sm quick-question" 
                    data-question="What are the fees?">
                <i class="fas fa-coins me-1"></i>Fees
            </button>
        `;
        
        // Re-attach event listeners
        document.querySelectorAll('.quick-question').forEach(btn => {
            btn.addEventListener('click', function() {
                const question = this.dataset.question;
                messageInput.value = question;
                sendMessage(new Event('submit'));
            });
        });
    }

    function toggleHistory() {
        const collapse = new bootstrap.Collapse(document.getElementById('chatHistoryCollapse'));
        const icon = document.querySelector('#toggleHistory i');
        
        if (icon.classList.contains('fa-chevron-down')) {
            icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
            loadChatHistory();
        } else {
            icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
        }
    }

    function loadChatHistory() {
        fetch('api/chatbot.php?action=history', {
            headers: {
                'X-CSRF-Token': document.querySelector('input[name="csrf_token"]').value
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.history) {
                displayChatHistory(data.history);
            }
        })
        .catch(error => console.error('Error loading chat history:', error));
    }

    function displayChatHistory(history) {
        const container = document.getElementById('chatHistory');
        
        if (!history || history.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <p>No chat history yet</p>
                </div>
            `;
            return;
        }
        
        const historyList = history.map(session => `
            <div class="border rounded p-3 mb-2">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">${escapeHtml(session.last_message.substring(0, 50))}...</h6>
                        <small class="text-muted">${formatDate(session.created_at)}</small>
                    </div>
                    <button class="btn btn-outline-primary btn-sm" 
                            onclick="loadChatSession('${session.session_id}')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
        `).join('');
        
        container.innerHTML = historyList;
    }

    function getSessionId() {
        let sessionId = sessionStorage.getItem('chat_session_id');
        if (!sessionId) {
            sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            sessionStorage.setItem('chat_session_id', sessionId);
        }
        return sessionId;
    }

    function formatDate(dateString) {
        return new Date(dateString).toLocaleString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Global function for loading chat sessions
    window.loadChatSession = function(sessionId) {
        // This would load a specific chat session
        console.log('Loading chat session:', sessionId);
        // Implementation would depend on your requirements
    };

    // Initial scroll to bottom
    scrollToBottom();
});
</script>

<?php require_once 'includes/footer.php'; ?>