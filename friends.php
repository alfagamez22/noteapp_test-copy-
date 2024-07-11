<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$friends = [];
$potential_friends = [];
$friend_requests = [];
$search_performed = false;

// This Handles adding a friend
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_friend' && isset($_POST['friend_id'])) {
        $friend_id = intval($_POST['friend_id']);
        
        // This will check if a friend request already exists
        $sql_check_request = "SELECT * FROM friend_requests WHERE sender_id = ? AND receiver_id = ?";
        if ($stmt_check = $conn->prepare($sql_check_request)) {
            $stmt_check->bind_param("ii", $user_id, $friend_id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check->num_rows == 0) {
                // If no existing request, create a new friend request
                $sql_insert_request = "INSERT INTO friend_requests (sender_id, receiver_id, status) VALUES (?, ?, 'pending')";
                if ($stmt_insert = $conn->prepare($sql_insert_request)) {
                    $stmt_insert->bind_param("ii", $user_id, $friend_id);
                    $stmt_insert->execute();
                    $stmt_insert->close();
                }
            }
            
            $stmt_check->close();
        }
        
        header('Location: friends.php');
        exit;
    }
}

// This Handles removing a friend
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'remove_friend' && isset($_POST['friend_id'])) {
        $friend_id = intval($_POST['friend_id']);
        $sql_remove_friend = "DELETE FROM friends WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)";
        if ($stmt_remove = $conn->prepare($sql_remove_friend)) {
            $stmt_remove->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);
            $stmt_remove->execute();
            $stmt_remove->close();
        }
        header('Location: friends.php');
        exit;
    }
}

// Fetch friends list
$sql_friends = "SELECT u.user_id AS id, u.name AS username 
                FROM friends f 
                JOIN users u ON f.friend_id = u.user_id 
                WHERE f.user_id = ?";
$stmt_friends = $conn->prepare($sql_friends);
$stmt_friends->bind_param("i", $user_id);
$stmt_friends->execute();
$result_friends = $stmt_friends->get_result();

while ($row_friends = $result_friends->fetch_assoc()) {
    $friends[] = $row_friends;
}

$stmt_friends->close();

// Handle searching for potential friends
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'search_friend' && isset($_POST['friend_name'])) {
    $friend_name = $_POST['friend_name'];
    $sql_search = "SELECT user_id, name FROM users WHERE user_id != ? AND user_id NOT IN (SELECT friend_id FROM friends WHERE user_id = ?) AND name LIKE ?";
    if ($stmt_search = $conn->prepare($sql_search)) {
        $search = "%$friend_name%";
        $stmt_search->bind_param("iis", $user_id, $user_id, $search);
        $stmt_search->execute();
        $result_search = $stmt_search->get_result();
        $potential_friends = $result_search->fetch_all(MYSQLI_ASSOC);
        $stmt_search->close();
    }
    $search_performed = true;
}

// Fetch pending friend requests
$sql_requests = "SELECT r.request_id, u.user_id AS sender_id, u.name AS sender_name 
                FROM friend_requests r
                JOIN users u ON r.sender_id = u.user_id
                WHERE r.receiver_id = ? AND r.status = 'pending'";
$stmt_requests = $conn->prepare($sql_requests);
$stmt_requests->bind_param("i", $user_id);
$stmt_requests->execute();
$result_requests = $stmt_requests->get_result();

while ($row_requests = $result_requests->fetch_assoc()) {
    $friend_requests[] = $row_requests;
}

$stmt_requests->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friends</title>
    <link rel="stylesheet" href="css/friends.css">
</head>
<body>
    <div class="container">
        <h1>Manage Friends</h1>

        <section>
            <h2>Friends List</h2>
            <ul>
                <?php foreach ($friends as $friend): ?>
                    <li>
                        <?php echo htmlspecialchars($friend['username']); ?>
                        <form method="POST" action="friends.php" style="display:inline;">
                            <input type="hidden" name="action" value="remove_friend">
                            <input type="hidden" name="friend_id" value="<?php echo $friend['id']; ?>">
                            <button type="submit" class="remove-btn">Remove</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section>
            <h2>Add Friend</h2>
            <form method="POST" action="friends.php" class="search-form">
                <div class="form-group">
                    <label for="friend_name">Friend's Name:</label>
                    <input type="text" id="friend_name" name="friend_name" required>
                    <button type="submit" name="action" value="search_friend" class="search-btn">Search</button>
                </div>
            </form>
            <?php if ($search_performed): ?>
                <form method="GET" action="friends.php" class="clear-form">
                    <button type="submit" class="clear-btn">Clear Filters</button>
                </form>
            <?php endif; ?>
            <?php if (!empty($potential_friends)): ?>
                <h3>Select a Friend to Add:</h3>
                <ul>
                    <?php foreach ($potential_friends as $friend): ?>
                        <li>
                            <?php echo htmlspecialchars($friend['name']); ?>
                            <form method="POST" action="friends.php" style="display:inline;">
                                <input type="hidden" name="action" value="add_friend">
                                <input type="hidden" name="friend_id" value="<?php echo $friend['user_id']; ?>">
                                <button type="submit" class="add-btn">Add Friend</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php elseif ($search_performed): ?>
                <p>No matching users found.</p>
            <?php endif; ?>
        </section>

        <section>
            <h2>Friend Requests</h2>
            <?php if (!empty($friend_requests)): ?>
                <ul>
                    <?php foreach ($friend_requests as $request): ?>
                        <li>
                            <?php echo htmlspecialchars($request['sender_name']); ?>
                            <form method="POST" action="process_request.php" style="display:inline;">
                                <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                <button type="submit" name="action" value="accept_request" class="accept-btn">Accept</button>
                                <button type="submit" name="action" value="reject_request" class="reject-btn">Reject</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No pending friend requests.</p>
            <?php endif; ?>
        </section>

        <a href="menu.php" class="back-to-menu">Back to Menu</a>
    </div>
</body>
</html>
