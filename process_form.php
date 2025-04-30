<?php
// Include database connection
require_once 'db_connect.php';

// Initialize response array (for AJAX requests)
$response = [
    'success' => false,
    'message' => ''
];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get database connection
    $conn = getDbConnection();
    
    // Get form data and sanitize
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    // Validate form fields
    $errors = [];
    
    // Validate name
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    // Validate email
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Validate message
    if (empty($message)) {
        $errors[] = "Message is required";
    } elseif (strlen($message) < 10) {
        $errors[] = "Message must be at least 10 characters long";
    }
    
    // If validation passes, save to database
    if (empty($errors)) {
        // Use MySQL prepared statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);
        
        $result = $stmt->execute();
        
        if ($result) {
            // Set success message
            $response['success'] = true;
            $response['message'] = "Thank you! Your message has been sent successfully.";
            
            // For HTML response, redirect with success parameter
            if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
                header("Location: index.html?status=success#contact");
                exit;
            }
        } else {
            // Set error message
            $response['message'] = "Error: " . $conn->error;
            
            // For HTML response
            if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
                header("Location: index.html?status=error#contact");
                exit;
            }
        }
    } else {
        // Join error messages
        $response['message'] = implode("<br>", $errors);
        
        // For HTML response
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            header("Location: index.html?status=validation_error#contact");
            exit;
        }
    }
    
    // Close the MySQL connection
    $conn->close();
    
    // Return JSON response for AJAX requests
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
} else {
    // If not a POST request, redirect to homepage
    header("Location: index.html");
    exit;
}
?>
