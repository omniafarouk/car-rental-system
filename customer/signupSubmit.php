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
    $email = $_POST['email'];
    $lname = $_POST['lname'];
    $fname = $_POST['fname'];
    $phone = $_POST['phone'];
    $id = $_POST['id'];
    $address = $_POST['address'];
    $licenseId = $_POST['licenseId'];
    $license_exp_date = $_POST['license_exp_date'];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die(json_encode(['ok' => false, 'messages' => 'Invalid email format']));
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $ok = false;
    $messages = "";

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM customer WHERE email = ?");
    if (!$stmt) {
        die(json_encode(['ok' => false, 'messages' => "Query preparation failed: " . $conn->error]));
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt_result = $stmt->get_result();

    if ($stmt_result->num_rows > 0) {
        $ok = false;
        $messages = "Email already exists!";

    } else {
        // Insert the new customer
        $stmt = $conn->prepare("INSERT INTO customer (customer_id, lname, fname, email, phone_number, licenseId, licenseExpiryDate, address, password)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");       
        if (!$stmt) {
            die(json_encode(['ok' => false, 'messages' => "Query preparation failed: " . $conn->error]));
        }
        // SQL insertion query is split like that for security purpose
        $stmt->bind_param("sssssssss", $id, $lname, $fname, $email, $phone, $licenseId, $license_exp_date, $address, $hashed_password);

        if ($stmt->execute()) {
            $ok = true;
            $messages = "New record created successfully";
        } else {
            $ok = false;
            $messages = "Server Error: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();

    // Output JSON response
    header('Content-Type: application/json');
    echo json_encode(['ok' => $ok, 'messages' => $messages]);
}
?>
