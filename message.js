$(document).ready(function() {
    let currentFriendId = null;

    // Load messages when clicking on a friend
    $('.friend').click(function() {
        currentFriendId = $(this).data('id');
        $('#receiver-id').val(currentFriendId);
        loadMessages(currentFriendId);
    });

    // Send message
    $('#message-form').submit(function(e) {
        e.preventDefault();
        if (!currentFriendId) {
            alert('Please select a friend to send a message to.');
            return;
        }
        $.ajax({
            url: 'messages/send_messages.php', // Updated URL
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#message-input').val('');
                    loadMessages(currentFriendId);
                } else {
                    alert('Error sending message: ' + response.error);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                alert('Error communicating with the server: ' + textStatus);
            }
        });
    });

    // Load messages
    function loadMessages(friendId) {
        $.ajax({
            url: 'messages/get_messages.php', // Updated URL
            method: 'GET',
            data: { friend_id: friendId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    let messageHtml = '';
                    response.messages.forEach(function(msg) {
                        messageHtml += `<div class="message ${msg.class}">
                            <strong>${msg.sender}:</strong> ${msg.message}
                            <span class="timestamp">${msg.timestamp}</span>
                        </div>`;
                    });
                    $('#message-list').html(messageHtml);
                } else {
                    console.error('Error loading messages:', response.error);
                    alert('Error loading messages: ' + response.error);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                alert('Error communicating with the server: ' + textStatus);
            }
        });
    }

    // Periodically check for new messages
    setInterval(function() {
        if (currentFriendId) {
            loadMessages(currentFriendId);
        }
    }, 1000); // Check every N seconds
});