<?php
// Database connection
$servername = "localhost";
$username = "root";
$userPassword = "";
$dbname = "car_rental_system";

$conn = new mysqli($servername, $username, $userPassword, $dbname);

header('Content-Type: application/json');

if ($conn->connect_error) {
    echo json_encode(['ok' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

try {
    // Retrieve search parameters from GET request
    $sdate = isset($_GET['sdate']) ? mysqli_real_escape_string($conn, $_GET['sdate']) : '';
    $edate = isset($_GET['edate']) ? mysqli_real_escape_string($conn, $_GET['edate']) : '';
    $rdate = isset($_GET['rdate']) ? mysqli_real_escape_string($conn, $_GET['rdate']) : '';
    $carId = isset($_GET['carId']) ? mysqli_real_escape_string($conn, $_GET['carId']) : '';
    $email = isset($_GET['email']) ? mysqli_real_escape_string($conn, $_GET['email']) : '';

    if (empty($email)) {
        throw new Exception("Email parameter is required.");
    }

    error_log(print_r($_GET, true)); // Debug the incoming GET parameters

    // Base SQL query
    $sql = "SELECT r.* FROM reservation as r 
            JOIN customer as c ON r.customer_id = c.customer_id 
            WHERE c.email LIKE '%$email%'";

    // Add conditions dynamically
    $conditions = [];
    if (!empty($sdate)) $conditions[] = "r.start_date = '$sdate'";
    if (!empty($edate)) $conditions[] = "r.end_date = '$edate'";
    if (!empty($carId)) $conditions[] = "r.car_id = '$carId'";
    if (!empty($rdate)) $conditions[] = "r.reservation_date LIKE '%$rdate%'";

    if (count($conditions) > 0) {
        $sql .= " AND " . implode(" AND ", $conditions);
    }

    error_log("Final Query: $sql"); // Log the final query

    // Execute the query
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        throw new Exception("Database Query Failed: " . mysqli_error($conn));
    }
    $reservations = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $reservations[] = $row;
        error_log(print_r($row, true));
    }

    // Return results in JSON format
    echo json_encode([
        'ok' => true,
        'reservations' => $reservations
    ]);

} catch (Exception $e) {
    // Return an error response in JSON format
    echo json_encode([
        'ok' => false,
        'message' => 'Error fetching reservations: ' . $e->getMessage()
    ]);
} finally {
    $conn->close(); // Ensure the connection is closed
}
?>
