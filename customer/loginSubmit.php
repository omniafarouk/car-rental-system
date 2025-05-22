<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $userPassword = "";
    $dbname = "car_rental_system";

    // Create connection 
    $conn = new mysqli($servername, $username, $userPassword, $dbname);
    if ($conn->connect_error) {
        die(json_encode(['ok' => false, 'message' => "Connection failed: " . $conn->connect_error]));
    }

    // Check connection
    if ($conn->connect_error) {
        die(json_encode(['ok' => false, 'messages' => "Connection failed: " . $conn->connect_error]));
    }

    // Collect form data
    $password = $_POST['password'];
    $email = $_POST['email'];
    $ok = false;
    $messages = "";
    
    $stmt = $conn->prepare("SELECT password, fname, lname FROM customer WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $stored_hashed_password = $row['password'];
        
        // Verify the input password against the stored hash
        if (password_verify($password, $stored_hashed_password)) {
            $ok = true;
            $messages = $row['fname'] . " " . $row['lname'];
        } else {
            $ok = false;
            $messages = "Invalid Password.";
        }
    }else{
        $ok = false;
        $messages = "Invalid email.";
    }
    $stmt->close();
    $conn->close();

    // outputs a jason string about the attempted login
    header('Content-Type: application/json');
    echo json_encode(
        array(
            'ok' => $ok,
            'messages' => $messages
    ));
}
?>