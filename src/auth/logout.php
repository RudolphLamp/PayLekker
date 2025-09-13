<?php
session_start();

// Destroy all session data
session_destroy();

// Clear session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Also handle API token logout if needed
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle AJAX logout request
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
    exit();
}

// Redirect to login page with success message
header('Location: login.php?message=logged_out');
exit();
?>
