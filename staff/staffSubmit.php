<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $userPassword = "";
    $dbname = "car_rental_system";

    // Create connection
    $conn = new mysqli($servername, $username, $userPassword, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die(json_encode(['ok' => false, 'messages' => "Connection failed: " . $conn->connect_error]));
    }

    // Collect form data
    $password = $_POST['password'];
    $id = $_POST['id'];
    $ok = false;
    $messages = "";

    $stmt = $conn->prepare("SELECT staff_id FROM staff WHERE staff_id = ? AND password = ?");
    $stmt->bind_param("ss",$id, $password);
    $stmt->execute();
    
    $stmt_result = $stmt->get_result();
    
    if($stmt_result->num_rows > 0){
      $data = $stmt_result->fetch_assoc();
      $ok = true;
      $messages = "Welcome".$data['id'];
    
    }else{
        $ok = false;
        $messages = "Invalid Id or password.";
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