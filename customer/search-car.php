<?php
// Database connection
$servername = "localhost";
$username = "root";
$userPassword = "";
$dbname = "car_rental_system";

$conn = new mysqli($servername, $username, $userPassword, $dbname);

header('Content-Type: application/json');

try {
    // Retrieve search parameters from GET request
    $model = isset($_GET['model']) ? mysqli_real_escape_string($conn, $_GET['model']) : '';
    $year = isset($_GET['year']) ? intval($_GET['year']) : 0;
    $car_id = isset($_GET['car_id']) ? mysqli_real_escape_string($conn, $_GET['car_id']) : '';
    $capacity = isset($_GET['capacity']) ? intval($_GET['capacity']) : 0;
    $fuel = isset($_GET['fuel']) ? mysqli_real_escape_string($conn, $_GET['fuel']) : '';
    $mileage = isset($_GET['mileage']) ? intval($_GET['mileage']) : 0;
    $bodyType = isset($_GET['bodyType']) ? mysqli_real_escape_string($conn, $_GET['bodyType']) : '';
    $transmission = isset($_GET['transmission']) ? mysqli_real_escape_string($conn, $_GET['transmission']) : '';
    $color = isset($_GET['color']) ? mysqli_real_escape_string($conn, $_GET['color']) : '';
    $price = isset($_GET['price']) ? floatval($_GET['price']) : 0;
    $officeLocation = isset($_GET['officeLocation']) ? mysqli_real_escape_string($conn, $_GET['officeLocation']) : '';
    $startDate=isset($_GET['startDate']) ? mysqli_real_escape_string($conn, $_GET['startDate']) : '';
    $endDate=isset($_GET['endDate']) ? mysqli_real_escape_string($conn, $_GET['endDate']) : '';

    // echo "<script>console.log('start date: " . $startDate . " and end date: " . $endDate . "');</script>";
    error_log("start date: {$startDate} and end date: {$endDate}"); 

    // Build the SQL query dynamically
    $sql = "SELECT c.*, o.location, sf.additional_features 
            FROM car_system as c 
            JOIN office as o ON c.office_id = o.office_id
            left Join special_features as sf on c.plate_id = sf.plate_id";

    // Add conditions dynamically
    $conditions = [];
    if (!empty($model)) $conditions[] = "c.model LIKE '%$model%'";
    if ($year > 0) $conditions[] = "c.year = $year";
    if (!empty($car_id)) $conditions[] = "c.plate_id = '$car_id'";
    if ($capacity > 0) $conditions[] = "c.seating_capacity = $capacity";
    if (!empty($fuel)) $conditions[] = "c.fuel_type = '$fuel'";
    if ($mileage > 0) $conditions[] = "c.mileage <= $mileage";
    if (!empty($bodyType)) $conditions[] = "c.body_type = '$bodyType'";
    if (!empty($transmission)) $conditions[] = "c.transmission = '$transmission'";
    if (!empty($color)) $conditions[] = "c.color LIKE '%$color%'";
    if ($price > 0) $conditions[] = "c.daily_rental_price <= $price";
    if (!empty($officeLocation)) $conditions[] = "o.location LIKE '%$officeLocation%'";

    if (count($conditions) > 0) {
        // echo "<script>console.log('conditions: " .$conditions. "');</script>";
        error_log("conditions: " . implode(" AND ", $conditions)); 
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    // Execute the query
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        throw new Exception("Database Query Failed: " . mysqli_error($conn));
    }

    // Fetch the results
    $cars = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $cars[] = $row;
    }

    // Return results in JSON format
    echo json_encode([
        'ok' => true,
        'cars' => $cars
    ]);

} catch (Exception $e) {
    // Return an error response in JSON format
    echo json_encode([
        'ok' => false,
        'message' => 'Error fetching cars: ' . $e->getMessage()
    ]);
}
