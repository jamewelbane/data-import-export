<?php
// Check if a file was uploaded
if (isset($_FILES['importFile'])) {
    $file = $_FILES['importFile'];

    // Check for error
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo "Error uploading file. Please try again.";
        exit();
    }

    // Check file type
    $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);
    if ($fileType === 'csv') {
        // Handle CSV file
        $uploadDir = '../uploads/';
        $uploadFile = $uploadDir . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
            $csvData = array_map('str_getcsv', file($uploadFile));
            array_shift($csvData);

            require '../database/connection.php'; 
            foreach ($csvData as $row) {
                $product_id = $row[0];
                $product_name = $row[1];
                $description = $row[2];
                $price = $row[3];
                $sql = "INSERT INTO products (product_id, product_name, description, price) VALUES (?, ?, ?, ?)";
                $stmt = $link->prepare($sql);
                $stmt->bind_param("isss", $product_id, $product_name, $description, $price);
                $stmt->execute();
            }
            echo '<script>alert("CSV data inserted successfully.");';
            echo 'window.location.href = "../product/product-list.php";</script>';
        } else {
            echo "Error moving uploaded file.";
        }
    } elseif ($fileType === 'xlsx' || $fileType === 'xls') {
       
        require '../vendor/autoload.php';
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file['tmp_name']);
        $spreadsheet = $reader->load($file['tmp_name']);
        $data = $spreadsheet->getActiveSheet()->toArray();
       
        array_shift($data);
       
        require '../database/connection.php'; 
        foreach ($data as $row) {
            $product_id = $row[0];
            $product_name = $row[1];
            $description = $row[2];
            $price = $row[3];
            $sql = "INSERT INTO products (product_id, product_name, description, price) VALUES (?, ?, ?, ?)";
            $stmt = $link->prepare($sql);
            $stmt->bind_param("isss", $product_id, $product_name, $description, $price);
            $stmt->execute();
        }
        echo '<script>alert("Excel data inserted successfully.");';
        echo 'window.location.href = "../product/product-list.php";</script>';
    } else {
        echo "Unsupported file format. Only CSV and Excel files are allowed.";
        exit();
    }
} else {
    echo "No file uploaded.";
}
?>
