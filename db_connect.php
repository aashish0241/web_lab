<?php
// Function to get database connection
function getDbConnection() {
    // Database configuration
    $servername = "localhost";
    $username = "root"; // MySQL default username (might need to be adjusted)
    $password = ""; // MySQL default password (might need to be adjusted)
    $dbname = "portfolio_db";

    // Create connection
    $conn = new mysqli($servername, $username, $password);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if database exists, if not create it
    $db_check_query = "CREATE DATABASE IF NOT EXISTS $dbname";
    if (!$conn->query($db_check_query)) {
        die("Error creating database: " . $conn->error);
    }

    // Select database
    $conn->select_db($dbname);

    // Check if the 'messages' table exists, if not create it
    $create_table_query = "CREATE TABLE IF NOT EXISTS messages (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!$conn->query($create_table_query)) {
        die("Error creating table: " . $conn->error);
    }
    
    return $conn;
}

// Initialize the connection by default to ensure the table exists
$db_conn = getDbConnection();
?>
