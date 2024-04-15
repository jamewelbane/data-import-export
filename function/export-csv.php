<?php
require_once '../database/connection.php';


$query = "SELECT * FROM products";
$result = mysqli_query($link, $query);

// Initialize CSV data
$csvData = "Product ID,Product Name,Description,Price\r\n";

// Loop through the results and append to CSV data
while ($row = mysqli_fetch_assoc($result)) {
    $csvData .= "{$row['product_id']},{$row['product_name']},{$row['description']},{$row['price']}\r\n";
}

// Set headers for CSV download
header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="products.csv"');

// Output CSV data
echo $csvData;
?>
