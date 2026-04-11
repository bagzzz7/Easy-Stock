<div class="easystock-chatbot">
    <!-- Chat Toggle Button -->
    <button class="chat-toggle-btn" onclick="toggleChat()" id="chatToggleBtn">
        <i class="fas fa-comment-dots"></i>
        <span class="toggle-text">EasyStock AI</span>
        <span class="notification-badge" id="unreadBadge" style="display: none;">1</span>
    </button>
    
    <!-- Chat Container -->
    <div class="chat-container" id="chatContainer">
        <!-- Chat Header -->
        <div class="chat-header">
            <div class="header-left">
                <div class="bot-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="bot-info">
                    <h4>EasyStock Assistant</h4>
                    <span class="status-indicator">
                        <span class="status-dot"></span>
                        Online
                    </span>
                </div>
            </div>
            <div class="header-actions">
                <button class="header-btn" onclick="minimizeChat()" title="Minimize">
                    <i class="fas fa-minus"></i>
                </button>
                <button class="header-btn" onclick="closeChat()" title="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <!-- Chat Messages Area -->
        <div class="chat-messages" id="chatMessages">
            <!-- Welcome Message -->
            <div class="message-row bot">
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        👋 Hello! I'm your EasyStock AI assistant. I can help you with:
                        <ul style="margin: 8px 0 0 20px; padding: 0;">
                            <li>Medicine information and uses</li>
                            <li>Dosage recommendations</li>
                            <li>Side effects and precautions</li>
                            <li>Stock availability</li>
                        </ul>
                    </div>
                    <span class="message-time">Just now</span>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="quick-actions">
            <button class="quick-action-btn" onclick="sendQuickQuestion('What medicines are low in stock?')">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Low Stock</span>
            </button>
            <button class="quick-action-btn" onclick="sendQuickQuestion('Show me expiring medicines')">
                <i class="fas fa-clock"></i>
                <span>Expiring Soon</span>
            </button>
            <button class="quick-action-btn" onclick="sendQuickQuestion('Today\'s sales summary')">
                <i class="fas fa-chart-line"></i>
                <span>Sales</span>
            </button>
        </div>
        
        <!-- Chat Input Area -->
        <div class="chat-input-area">
            <form id="chatForm" onsubmit="sendMessage(); return false;">
                <input type="text" 
                       id="userMessage" 
                       class="chat-input-field" 
                       placeholder="Type your question here..." 
                       autocomplete="off">
                <button type="submit" class="send-btn" id="sendButton">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
            <div class="input-footer">
                <span class="typing-indicator" id="typingIndicator" style="display: none;">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
                <span class="disclaimer-text">AI-powered • For informational purposes only</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let isChatOpen = false;
    let isMinimized = false;
    
    function toggleChat() {
        const container = document.getElementById('chatContainer');
        const toggleBtn = document.getElementById('chatToggleBtn');
        
        if (container.style.display === 'none' || container.style.display === '') {
            container.style.display = 'flex';
            toggleBtn.classList.add('active');
            isChatOpen = true;
            
            // Hide notification badge
            document.getElementById('unreadBadge').style.display = 'none';
            
            // Scroll to bottom
            scrollToBottom();
        } else {
            container.style.display = 'none';
            toggleBtn.classList.remove('active');
            isChatOpen = false;
        }
    }
    
    function minimizeChat() {
        const container = document.getElementById('chatContainer');
        const toggleBtn = document.getElementById('chatToggleBtn');
        
        if (!isMinimized) {
            container.style.display = 'none';
            toggleBtn.classList.remove('active');
            isMinimized = true;
        }
    }
    
    function closeChat() {
        const container = document.getElementById('chatContainer');
        const toggleBtn = document.getElementById('chatToggleBtn');
        
        container.style.display = 'none';
        toggleBtn.classList.remove('active');
        isChatOpen = false;
        isMinimized = false;
    }
    
    function scrollToBottom() {
        const messages = document.getElementById('chatMessages');
        messages.scrollTop = messages.scrollHeight;
    }
    
    async function sendMessage() {
        const input = document.getElementById('userMessage');
        const message = input.value.trim();
        const sendBtn = document.getElementById('sendButton');
        
        if (!message) return;
        
        // Disable input and button
        input.disabled = true;
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        
        // Add user message
        addMessage(message, 'user');
        input.value = '';
        
        // Show typing indicator
        document.getElementById('typingIndicator').style.display = 'flex';
        scrollToBottom();
        
        try {
            const response = await fetch('{{ route("chatbot.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: message })
            });
            
            const data = await response.json();
            
            // Hide typing indicator
            document.getElementById('typingIndicator').style.display = 'none';
            
            if (data.success) {
                addMessage(data.message, 'bot');
                
                // Add disclaimer if present
                if (data.disclaimer) {
                    setTimeout(() => {
                        addMessage(data.disclaimer, 'disclaimer');
                    }, 500);
                }
            } else {
                addMessage('I apologize, but I encountered an issue. Please try again in a moment.', 'bot-error');
            }
        } catch (error) {
            document.getElementById('typingIndicator').style.display = 'none';
            addMessage('Network error. Please check your connection and try again.', 'bot-error');
            console.error('Chatbot error:', error);
        } finally {
            // Re-enable input and button
            input.disabled = false;
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
            input.focus();
            scrollToBottom();
        }
    }
    
    function addMessage(text, type) {
        const messages = document.getElementById('chatMessages');
        const messageDiv = document.createElement('div');
        
        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        
        if (type === 'user') {
            messageDiv.className = 'message-row user';
            messageDiv.innerHTML = `
                <div class="message-content user">
                    <div class="message-bubble user">${escapeHtml(text)}</div>
                    <span class="message-time user">${time}</span>
                </div>
            `;
        } else if (type === 'bot' || type === 'bot-error') {
            const botType = type === 'bot-error' ? 'bot error' : 'bot';
            messageDiv.className = `message-row ${type === 'bot-error' ? 'bot-error' : 'bot'}`;
            messageDiv.innerHTML = `
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <div class="message-bubble ${type === 'bot-error' ? 'error' : ''}">${escapeHtml(text)}</div>
                    <span class="message-time">${time}</span>
                </div>
            `;
        } else if (type === 'disclaimer') {
            messageDiv.className = 'message-row disclaimer';
            messageDiv.innerHTML = `
                <div class="message-content disclaimer">
                    <div class="message-bubble disclaimer">${escapeHtml(text)}</div>
                </div>
            `;
        }
        
        messages.appendChild(messageDiv);
        scrollToBottom();
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function sendQuickQuestion(question) {
        document.getElementById('userMessage').value = question;
        sendMessage();
    }
    
    // Handle Enter key
    document.getElementById('chatForm').addEventListener('submit', function(e) {
        e.preventDefault();
        sendMessage();
    });
    
    // Show notification when new message arrives (if chat is closed)
    function showNotification() {
        if (!isChatOpen) {
            document.getElementById('unreadBadge').style.display = 'flex';
        }
    }
</script>

<style>
.easystock-chatbot {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 9999;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Toggle Button */
.chat-toggle-btn {
    background: linear-gradient(135deg, #2A5C7D 0%, #1E4560 100%);
    color: white;
    border: none;
    padding: 14px 28px;
    border-radius: 60px;
    cursor: pointer;
    box-shadow: 0 8px 25px rgba(42, 92, 125, 0.4);
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.chat-toggle-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(42, 92, 125, 0.5);
    background: linear-gradient(135deg, #1E4560 0%, #163A4F 100%);
}

.chat-toggle-btn i {
    font-size: 20px;
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #FF6B6B;
    color: white;
    font-size: 11px;
    font-weight: 600;
    min-width: 20px;
    height: 20px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
}

/* Chat Container */
.chat-container {
    position: fixed;
    bottom: 100px;
    right: 30px;
    width: 380px;
    height: 600px;
    background: white;
    border-radius: 24px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
    display: none;
    flex-direction: column;
    overflow: hidden;
    animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Chat Header */
.chat-header {
    background: linear-gradient(135deg, #2A5C7D 0%, #1E4560 100%);
    color: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.header-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.bot-avatar {
    width: 45px;
    height: 45px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.bot-info h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.status-indicator {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.9);
}

.status-dot {
    width: 8px;
    height: 8px;
    background: #4ECDC4;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.1); }
}

.header-actions {
    display: flex;
    gap: 8px;
}

.header-btn {
    background: rgba(255, 255, 255, 0.15);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    font-size: 14px;
}

.header-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.05);
}

/* Chat Messages Area */
.chat-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background: #F8FAFC;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.message-row {
    display: flex;
    gap: 12px;
    animation: fadeIn 0.3s ease;
}

.message-row.user {
    justify-content: flex-end;
}

.message-row.bot-error {
    opacity: 0.9;
}

.message-avatar {
    width: 36px;
    height: 36px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: #2A5C7D;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
    flex-shrink: 0;
    border: 2px solid #2A5C7D;
}

.message-content {
    max-width: 75%;
}

.message-content.user {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.message-bubble {
    background: white;
    padding: 12px 16px;
    border-radius: 18px;
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.03);
    font-size: 14px;
    line-height: 1.5;
    color: #1A2B3C;
    word-wrap: break-word;
    border: 1px solid rgba(0, 0, 0, 0.03);
}

.message-bubble.user {
    background: linear-gradient(135deg, #2A5C7D 0%, #1E4560 100%);
    color: white;
    border-bottom-right-radius: 4px;
    box-shadow: 0 5px 15px rgba(42, 92, 125, 0.2);
}

.message-bubble.bot {
    border-bottom-left-radius: 4px;
}

.message-bubble.error {
    background: #FEF2F2;
    color: #DC2626;
    border: 1px solid #FECACA;
}

.message-bubble.disclaimer {
    background: #FEF3C7;
    color: #92400E;
    font-size: 12px;
    text-align: center;
    border: 1px solid #FDE68A;
}

.message-time {
    font-size: 11px;
    color: #94A3B8;
    margin-top: 4px;
    display: block;
}

.message-time.user {
    text-align: right;
}

/* Quick Actions */
.quick-actions {
    display: flex;
    gap: 8px;
    padding: 15px 20px;
    background: white;
    border-top: 1px solid #E2E8F0;
}

.quick-action-btn {
    flex: 1;
    background: #F1F5F9;
    border: 1px solid #E2E8F0;
    border-radius: 40px;
    padding: 8px 4px;
    font-size: 11px;
    font-weight: 500;
    color: #1E293B;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    transition: all 0.2s;
}

.quick-action-btn:hover {
    background: #2A5C7D;
    color: white;
    border-color: #2A5C7D;
    transform: translateY(-2px);
}

.quick-action-btn i {
    font-size: 16px;
}

/* Chat Input Area */
.chat-input-area {
    padding: 20px;
    background: white;
    border-top: 1px solid #E2E8F0;
}

#chatForm {
    display: flex;
    gap: 10px;
    position: relative;
}

.chat-input-field {
    flex: 1;
    padding: 14px 18px;
    border: 2px solid #E2E8F0;
    border-radius: 40px;
    outline: none;
    font-size: 14px;
    transition: all 0.2s;
    background: #F8FAFC;
}

.chat-input-field:focus {
    border-color: #2A5C7D;
    background: white;
    box-shadow: 0 0 0 3px rgba(42, 92, 125, 0.1);
}

.chat-input-field:disabled {
    background: #F1F5F9;
    cursor: not-allowed;
}

.send-btn {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #2A5C7D 0%, #1E4560 100%);
    border: none;
    border-radius: 50%;
    color: white;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    box-shadow: 0 5px 15px rgba(42, 92, 125, 0.3);
}

.send-btn:hover:not(:disabled) {
    transform: scale(1.05);
    box-shadow: 0 8px 20px rgba(42, 92, 125, 0.4);
}

.send-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.input-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 8px;
    padding: 0 5px;
}

.typing-indicator {
    display: flex;
    gap: 4px;
    padding: 5px 10px;
    background: #F1F5F9;
    border-radius: 30px;
}

.typing-indicator span {
    width: 6px;
    height: 6px;
    background: #94A3B8;
    border-radius: 50%;
    animation: typing 1.4s infinite;
}

.typing-indicator span:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-indicator span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.6; }
    30% { transform: translateY(-4px); opacity: 1; }
}

.disclaimer-text {
    font-size: 10px;
    color: #94A3B8;
}

/* Scrollbar Styling */
.chat-messages::-webkit-scrollbar {
    width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
    background: #F1F5F9;
}

.chat-messages::-webkit-scrollbar-thumb {
    background: #CBD5E1;
    border-radius: 10px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
    background: #94A3B8;
}

/* Mobile Responsive */
@media (max-width: 480px) {
    .easystock-chatbot {
        bottom: 15px;
        right: 15px;
    }
    
    .chat-container {
        width: calc(100vw - 30px);
        height: calc(100vh - 100px);
        bottom: 85px;
        right: 15px;
    }
    
    .chat-toggle-btn {
        padding: 12px 20px;
        font-size: 14px;
    }
    
    .quick-actions {
        padding: 12px 15px;
    }
    
    .quick-action-btn {
        padding: 6px 2px;
        font-size: 10px;
    }
}
</style>
@endpush