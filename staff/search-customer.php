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
    $Fname = isset($_GET['Fname']) ? mysqli_real_escape_string($conn, $_GET['Fname']) : '';
    $Lname = isset($_GET['Lname']) ? mysqli_real_escape_string($conn, $_GET['Lname']) : '';
    $email = isset($_GET['email']) ? mysqli_real_escape_string($conn, $_GET['email']) : '';
    $phone = isset($_GET['phone']) ? mysqli_real_escape_string($conn, $_GET['phone']) : '';
    $address = isset($_GET['address']) ? mysqli_real_escape_string($conn, $_GET['address']) : '';
    $Id = isset($_GET['Id']) ? mysqli_real_escape_string($conn, $_GET['Id']) : '';
    $license_Id=isset($_GET['license_Id']) ? mysqli_real_escape_string($conn, $_GET['license_Id']) : '';
    $license_exp_date=isset($_GET['license_exp_date']) ? mysqli_real_escape_string($conn, $_GET['license_exp_date']) : '';
    /*
        $sql = "SELECT *
            FROM customer as c 
            LEFT JOIN reservation as r ON c.customer_id = r.customer_id";
    */
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
    if (!empty($Fname)) $conditions[] = "c.fname LIKE '%$Fname%'";
    if (!empty($Lname)) $conditions[] = "c.lname LIKE '%$Lname%'";
    if (!empty($email)) $conditions[] = "c.email = '$email'";
    if (!empty($phone)) $conditions[] = "c.phone_number = '$phone'";
    if (!empty($address)) $conditions[] = "c.address LIKE '%$address%'";
    if (!empty($license_Id)) $conditions[] = "c.licenseID = '$license_Id'";
    if (!empty($Id)) $conditions[] = "c.customer_id = '$Id'";
    if (!empty($license_exp_date)) $conditions[] = "c.licenseExpiryDate = $license_exp_date";

    if (count($conditions) > 0) {
        $sql = "SELECT c.customer_id,c.fname,c.lname,c.address,c.phone_number,c.email,c.licenseId
        ,c.licenseExpiryDate,c.registration_date
        ,r.car_id,r.start_date,r.end_date,r.reservation_status,r.reservation_date,r.total_payment
        FROM customer as c 
        LEFT JOIN reservation as r ON c.customer_id = r.customer_id";
        error_log("WHERE: " . implode(" AND ", $conditions));
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    // Execute the query
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        throw new Exception("Database Query Failed: " . mysqli_error($conn));
    }

    // Fetch the results
    $customers = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $customers[] = $row;
    }

    // Return results in JSON format
    echo json_encode([
        'ok' => true,
        'customers' => $customers
    ]);

} catch (Exception $e) {
    // Return an error response in JSON format
    echo json_encode([
        'ok' => false,
        'message' => 'Error searching customers: ' . $e->getMessage()
    ]);
}
