<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Chat dengan AI</h3>
            </div>
            
            <div class="card-body">
            <iframe src="<?php echo base_url('jajal')?>" width="100%" height="600px" frameborder="0"></iframe>
            </div>

            <div class="card-footer">
                <!-- <div class="input-group">
                    <input type="text" id="user-input" class="form-control rounded-0" 
                           placeholder="Ketik pertanyaan Anda..." autocomplete="off">
                    <span class="input-group-append">
                        <button type="button" class="btn btn-primary rounded-0" onclick="sendMessage()">
                            Kirim
                        </button>
                    </span>
                </div> -->
            </div>
        </div>
    </div>
</div>

<?= $this->section('css') ?>
<style>
.chat-box {
    padding: 15px;
}

.direct-chat-msg {
    margin-bottom: 1rem;
}

.direct-chat-text {
    padding: 0.5rem 1rem;
    margin: 5px 0;
    display: inline-block;
    max-width: 80%;
}

.user-message {
    text-align: right;
}

.user-message .direct-chat-text {
    background: #007bff;
    color: #fff;
}

.loading {
    text-align: center;
    padding: 10px;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
function sendMessage() {
    const userInput = $('#user-input');
    const chatBox = $('#chat-box');
    const message = userInput.val().trim();
    
    if (!message) return;

    // Add user message
    appendMessage(message, true);
    userInput.val('').focus();

    // Show loading
    const loadingDiv = $('<div class="loading">').text('ChatGPT sedang mengetik...');
    chatBox.append(loadingDiv);
    scrollToBottom();

    // Send to server
    $.ajax({
        url: '<?= base_url('chatgpt/send') ?>',
        type: 'POST',
        dataType: 'json',
        data: {
            message: message
        },
        success: function(response) {
            loadingDiv.remove();
            if (response.success) {
                appendMessage(response.message, false);
            } else {
                appendMessage('Error: ' + response.message, false);
                console.error('API Error:', response);
            }
        },
        error: function(xhr, status, error) {
            loadingDiv.remove();
            console.error('AJAX Error:', {
                status: status,
                error: error,
                response: xhr.responseText
            });
            let errorMsg = 'Error koneksi';
            try {
                const response = JSON.parse(xhr.responseText);
                errorMsg = response.message || errorMsg;
            } catch (e) {
                errorMsg = xhr.responseText || errorMsg;
            }
            appendMessage('Error: ' + errorMsg, false);
        }
    });
}

function appendMessage(message, isUser) {
    const chatBox = $('#chat-box');
    const messageDiv = $('<div class="direct-chat-msg' + (isUser ? ' user-message' : '') + '">');
    const textDiv = $('<div class="direct-chat-text rounded-0">').text(message);
    
    messageDiv.append(textDiv);
    chatBox.append(messageDiv);
    scrollToBottom();
}

function scrollToBottom() {
    const chatBox = $('#chat-box');
    chatBox.scrollTop(chatBox[0].scrollHeight);
}

// Handle Enter key
$('#user-input').keypress(function(e) {
    if (e.which == 13) {
        sendMessage();
    }
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>