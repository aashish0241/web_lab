<?php
// Start session for login management
session_start();

// Include database connection
require_once 'db_connect.php';

// Admin credentials
$admin_username = "admin";
$admin_password = "admin123"; // In a real application, this should be hashed and stored in the database

// Check login status
$is_logged_in = false;
$login_error = '';

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        $is_logged_in = true;
    } else {
        $login_error = "Invalid username or password";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: admin.php");
    exit;
}

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $is_logged_in = true;
}

// Get messages from database if logged in
$messages = [];
if ($is_logged_in) {
    $conn = getDbConnection();
    $query = "SELECT * FROM messages ORDER BY submitted_at DESC";
    $result = $conn->query($query);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Portfolio</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 60px;
        }
        .login-container {
            max-width: 400px;
            margin: 60px auto;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .table-container {
            margin-top: 20px;
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.html">Portfolio</a>
            <?php if ($is_logged_in): ?>
            <div class="ml-auto">
                <a href="admin.php?logout=1" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        <?php if (!$is_logged_in): ?>
            <!-- Login Form -->
            <div class="login-container">
                <h2 class="text-center mb-4">Admin Login</h2>
                
                <?php if ($login_error): ?>
                    <div class="alert alert-danger"><?php echo $login_error; ?></div>
                <?php endif; ?>
                
                <form method="post" action="admin.php">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                </form>
                
                <div class="mt-3 text-center">
                    <a href="index.html">Back to Portfolio</a>
                </div>
            </div>
        <?php else: ?>
            <!-- Admin Dashboard -->
            <div class="admin-header">
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h2>
                <a href="index.html" class="btn btn-primary">View Portfolio</a>
            </div>
            
            <div class="table-container">
                <h3>Contact Form Submissions</h3>
                
                <?php if (empty($messages)): ?>
                    <div class="alert alert-info">No messages have been submitted yet.</div>
                <?php else: ?>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Message</th>
                                <th>Submitted At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages as $message): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($message['id']); ?></td>
                                    <td><?php echo htmlspecialchars($message['name']); ?></td>
                                    <td><?php echo htmlspecialchars($message['email']); ?></td>
                                    <td><?php echo htmlspecialchars($message['message']); ?></td>
                                    <td><?php echo htmlspecialchars($message['submitted_at']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>