<?php
require_once '../database/connection.php'; // Include your database connection file

// Fetch data from the products table
$query = "SELECT * FROM products";
$result = mysqli_query($link, $query);

// Initialize an empty array to store the products data
$products = [];

// Loop through the results and append to the products array
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

// Return the products data in JSON format
header('Content-Type: application/json');
echo json_encode($products);
?>
