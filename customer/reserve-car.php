<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $userPassword = "";
    $dbname = "car_rental_system";

    $conn = new mysqli($servername, $username, $userPassword, $dbname);
    header('Content-Type: application/json');

    if ($conn->connect_error) {
        echo json_encode(['ok' => false, 'message' => "Connection failed: " . $conn->connect_error]);
        exit();
    }

    // Collect reservation details
    $email = $_POST['email'];
    $password = $_POST['password'];
    $car_id = $_POST['car_id'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];

    $activeStatus = "Active";
    $rentedStatus = "Rented";

    if (empty($password) || empty($car_id) || empty($startDate) || empty($endDate) || empty($email)) {
        echo json_encode(['ok' => false, 'message' => 'Missing required fields.']);
        exit();
    }

    // Check if customer exists
    $customer_query = "SELECT customer_id, password FROM customer WHERE email = ?";
    $stmt = $conn->prepare($customer_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stored_hashed_password = $row['password'];
        $customer_id = $row['customer_id'];

        if (password_verify($password, $stored_hashed_password)) {
            // Check if reservation exists
            $check_query = "SELECT * FROM reservation WHERE customer_id = ? AND car_id = ? AND (? BETWEEN start_date AND end_date)";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param("sss", $customer_id, $car_id, $startDate);
            $stmt->execute();
            $result_check = $stmt->get_result();

            if ($result_check->num_rows > 0) {
                echo json_encode(['ok' => false, 'message' => 'You reserved this car before.']);
                exit();
            } else {
                // Fetch car's rent price
                $car_query = "SELECT daily_rental_price FROM car_system WHERE plate_id = ?";
                $stmt = $conn->prepare($car_query);
                $stmt->bind_param("s", $car_id);
                $stmt->execute();
                $car_result = $stmt->get_result();

                if ($car_result->num_rows > 0) {
                    $car = $car_result->fetch_assoc();
                    $rentPrice = $car['daily_rental_price'];

                    $startDateObj = new DateTime($startDate);
                    $endDateObj = new DateTime($endDate);

                    $interval = $startDateObj->diff($endDateObj);
                    $days = $interval->days;
                    $total_payment = $days * $rentPrice;

                    // Insert reservation
                    $insert_query = "INSERT INTO reservation (customer_id, car_id, start_date, end_date, total_payment) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($insert_query);
                    $stmt->bind_param("ssssi", $customer_id, $car_id, $startDate, $endDate, $total_payment);

                    $insert_query2 = "INSERT INTO car_status_history (plate_id, status, status_date) VALUES (?, ?, ?)";
                    $stmt2 = $conn->prepare($insert_query2);
                    $stmt2->bind_param("sss", $car_id, $rentedStatus, $startDate);

                    $insert_query3 = "INSERT INTO car_status_history (plate_id, status, status_date) VALUES (?, ?, ?)";
                    $stmt3 = $conn->prepare($insert_query3);
                    $stmt3->bind_param("sss", $car_id, $activeStatus, $endDate);

                    if ($stmt->execute() && $stmt2->execute() && $stmt3->execute()) {
                        echo json_encode(['ok' => true, 'message' => 'Car reserved successfully.']);
                        exit();
                    } else {
                        echo json_encode(['ok' => false, 'message' => 'Failed to reserve the car.']);
                        exit();
                    }
                } else {
                    echo json_encode(['ok' => false, 'message' => 'Car not found.']);
                    exit();
                }
            }
        } else {
            echo json_encode(['ok' => false, 'message' => 'Invalid password.']);
            exit();
        }
    } else {
        echo json_encode(['ok' => false, 'message' => 'Customer not found.']);
        exit();
    }

    $stmt->close();
    $stmt2->close();
    $stmt3->close();
    $conn->close();
}
?>
