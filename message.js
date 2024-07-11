$(document).ready(function() {
    let currentFriendId = null;

    const imageInput = document.getElementById('image-input');
    const uploadButton = document.getElementById('upload-button');

    // Create and append the indicator span
    const imageIndicator = document.createElement('span');
    imageIndicator.id = 'image-indicator';
    imageIndicator.style.display = 'none';
    uploadButton.appendChild(imageIndicator);

    // Function to update the indicator color
    function updateIndicator() {
        if (imageInput.files && imageInput.files.length > 0) {
            imageIndicator.style.display = 'inline-block';
            imageIndicator.style.backgroundColor = 'green';
        } else {
            imageIndicator.style.display = 'inline-block';
            imageIndicator.style.backgroundColor = 'red';
        }
    }

    // Initial indicator state
    updateIndicator();

    // Update indicator on file input change
    imageInput.addEventListener('change', updateIndicator);

    // Load messages when clicking on a friend
    $('.friend').click(function() {
        currentFriendId = $(this).data('id');
        $('#receiver-id').val(currentFriendId);

        // Remove highlight class from all friends and add it to the selected one
        $('.friend').removeClass('highlight');
        $(this).addClass('highlight');

        loadMessages(currentFriendId);
    });

    // Send message with validation
    $('#message-form').submit(function(e) {
        e.preventDefault();
        if (!currentFriendId) {
            alert('Please select a friend to send a message to.');
            return;
        }

        let messageContent = $('#message-input').val().trim();
        let fileInput = $('#image-input').get(0);
        let formData = new FormData(this);

        if (messageContent === '' && fileInput.files.length === 0) {
            $('#validation-message').text('Message or image cannot be empty.').show();
            return;
        }

        // Hide validation message if it was previously shown
        $('#validation-message').hide();

        $.ajax({
            url: 'messages/send_messages.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#message-input').val('');
                    $('#image-input').val('');
                    updateIndicator(); // Update the indicator after sending the message
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

    // JavaScript to trigger file input when button is clicked
    document.getElementById('upload-button').addEventListener('click', function(e) {
        e.preventDefault();
        imageInput.click();
    });

    // Optional: Display selected file name
    document.getElementById('image-input').addEventListener('change', function() {
        var fileName = this.files[0].name;
        // Example: Display file name in a separate element
        document.getElementById('file-name').textContent = fileName;
    });

    // Load messages
    function loadMessages(friendId) {
        $.ajax({
            url: 'messages/get_messages.php',
            method: 'GET',
            data: { friend_id: friendId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    let messageHtml = '';
                    response.messages.forEach(function(msg) {
                        messageHtml += `<div class="message ${msg.class}">
                            <strong>${msg.sender}:</strong> ${msg.message}
                            ${msg.image_url ? `<div><img src="uploads/messages/${msg.image_url}" alt="Image" class="message-image"></div>` : ''}
                            <span class="timestamp">${msg.timestamp}</span>
                            <span class="status">${msg.status}</span> <!-- Display status -->
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
    }, 5000); // Check every 5 seconds
});
