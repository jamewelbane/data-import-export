<?php

if (isset($_FILES['importFile'])) {
    $file = $_FILES['importFile'];

    // Check for error
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo '<script>alert("Error uploading file. Please try again.");';
        echo 'window.location.href = "../product/product-list.php";</script>';
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
            $hasDuplicates = false; // Flag to track if duplicates were encountered
            foreach ($csvData as $row) {
                $product_id = $row[0];
                $product_name = $row[1];
                $description = $row[2];
                $price = $row[3];

                // Check if product ID already exists
                $check_sql = "SELECT COUNT(*) as count FROM products WHERE product_id = ?";
                $check_stmt = $link->prepare($check_sql);
                $check_stmt->bind_param("i", $product_id);
                $check_stmt->execute();
                $result = $check_stmt->get_result();
                $count = $result->fetch_assoc()['count'];
                $check_stmt->close();

                if ($count > 0) {
                    // Product ID already exists, skip it and set flag to true
                    $hasDuplicates = true;
                    continue;
                }

                // Proceed with inserting the new product
                $sql = "INSERT INTO products (product_id, product_name, description, price) VALUES (?, ?, ?, ?)";
                $stmt = $link->prepare($sql);
                $stmt->bind_param("isss", $product_id, $product_name, $description, $price);
                $stmt->execute();
            }

            // Display appropriate alert message based on flag
            if ($hasDuplicates) {
                echo '<script>alert("CSV data inserted successfully. Duplicate product IDs were skipped.");';
            } else {
                echo '<script>alert("CSV data inserted successfully.");';
            }
            echo 'window.location.href = "../product/product-list.php";</script>';
        } else {
            echo "Error moving uploaded file.";
        }
    } elseif ($fileType === 'xlsx' || $fileType === 'xls') {
        // Handle Excel file
        // Check if ZipArchive class exists
        if (!class_exists('ZipArchive')) {
            echo "Zip extension is not enabled or properly installed.";
            exit; // Stop execution if Zip extension is not available
        }

        require '../vendor/autoload.php';
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file['tmp_name']);
        $spreadsheet = $reader->load($file['tmp_name']);
        $data = $spreadsheet->getActiveSheet()->toArray();

        array_shift($data);

        require '../database/connection.php';
        $hasDuplicates = false; // Flag to track if duplicates were encountered
        foreach ($data as $row) {
            $product_id = $row[0];
            $product_name = $row[1];
            $description = $row[2];
            $price = $row[3];

            // Check if product ID already exists
            $check_sql = "SELECT COUNT(*) as count FROM products WHERE product_id = ?";
            $check_stmt = $link->prepare($check_sql);
            $check_stmt->bind_param("i", $product_id);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            $count = $result->fetch_assoc()['count'];
            $check_stmt->close();

            if ($count > 0) {
                // Product ID already exists, skip it and set flag to true
                $hasDuplicates = true;
                continue;
            }

            // Proceed with inserting the new product
            $sql = "INSERT INTO products (product_id, product_name, description, price) VALUES (?, ?, ?, ?)";
            $stmt = $link->prepare($sql);
            $stmt->bind_param("isss", $product_id, $product_name, $description, $price);
            $stmt->execute();
        }

        // Display appropriate alert message based on flag
        if ($hasDuplicates) {
            echo '<script>alert("Excel data inserted successfully. Duplicate product IDs were skipped.");';
        } else {
            echo '<script>alert("Excel data inserted successfully.");';
        }
        echo 'window.location.href = "../product/product-list.php";</script>';
    } else {
        echo "Unsupported file format. Only CSV and Excel files are allowed.";
        exit();
    }
} else {
    echo "No file uploaded.";
}
