<?php
session_start();
$servername = "127.0.0.1"; 
$username = "root";
$password = "";
$dbname = "car_rental_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$model = isset($_POST['model']) ? $_POST['model'] : '';
$plateId = isset($_POST['plateId']) ? $_POST['plateId'] : '';
$year = isset($_POST['year']) ? $_POST['year'] : '';
$capacity = isset($_POST['capacity']) ? $_POST['capacity'] : '';
$fuel = isset($_POST['fuel']) ? $_POST['fuel'] : '';
$mileage = isset($_POST['mileage']) ? $_POST['mileage'] : '';
$bodyType = isset($_POST['bodyType']) ? $_POST['bodyType'] : '';
$transmission = isset($_POST['transmission']) ? $_POST['transmission'] : '';
$color = isset($_POST['color']) ? $_POST['color'] : '';
$price = isset($_POST['price']) ? $_POST['price'] : '';
$officeId = isset($_POST['officeId']) ? $_POST['officeId'] : '';

$ok = false;
$messages = "";

$stmt = $conn->prepare("   ");
$stmt->bind_param("sssssssssss",$email);
$stmt->execute();
$stmt_result = $stmt->get_result();







