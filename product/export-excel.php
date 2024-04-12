<?php
// Include necessary libraries
require_once '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Fetch data from the database
require_once 'fetch-products.php'; // Assuming this script fetches product data and stores it in $data array

// Create a new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set column headers
$sheet->setCellValue('A1', 'Product ID');
$sheet->setCellValue('B1', 'Product Name');
$sheet->setCellValue('C1', 'Description');
$sheet->setCellValue('D1', 'Price');

// Populate data rows
$row = 2; // Start from row 2 to leave row 1 for headers
foreach ($data as $product) {
    $sheet->setCellValue('A' . $row, $product['product_id']);
    $sheet->setCellValue('B' . $row, $product['product_name']);
    $sheet->setCellValue('C' . $row, $product['description']);
    $sheet->setCellValue('D' . $row, $product['price']);
    $row++;
}

// Set headers for Excel file download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="products.xlsx"');
header('Cache-Control: max-age=0');

// Write the Spreadsheet object to a file
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
