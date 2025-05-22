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
    $officeID=isset($_GET['officeID']) ? mysqli_real_escape_string($conn, $_GET['officeID']) : '';

    $sql = "SELECT * FROM car_system as c JOIN office as o ON c.office_id = o.office_id";

    /*
    // Build the SQL query dynamically
    $sql = "SELECT c.model, c.year, c.plate_id, c.daily_rental_price, o.location , r.customer_id,r.start_date,
            r.end_date,r.reservation_status,cust.fname,
            cust.lname,cust.email,cust.phone_number
            FROM car_system as c 
            JOIN office o ON c.office_id = o.office_id
            left JOIN reservation as r on c.plate_id = r.car_id 
            left join customer as cust on cust.customer_id = r.customer_id";

    */

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
    if (!empty($officeID)) $conditions[] = "o.office_id = '%$officeID%'";

    if (count($conditions) > 0) {
        error_log("WHERE: " . implode(" AND ", $conditions));
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
