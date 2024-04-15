<?php
require_once '../database/connection.php';

$query = "SELECT * FROM products";
$result = mysqli_query($link, $query);

$data = array();
if ($result) {
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    } else {
        echo "No products found.";
    }
    mysqli_free_result($result); // Free result set
} else {
    echo "Error executing query: " . mysqli_error($link);
}

mysqli_close($link); 

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Table</title>

</head>

<body>
    <div class="container">
        <h1 class="text-center">Product Table</h1>

        <div class="container" style="margin-bottom: 10px;">
            <div class="row">

                <div class="col-md-2">
                    <button id="openExportModalBtn" class="btn btn-primary btn-block">Export Data</button>
                </div>

                <div class="col-md-2">
                    <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#importModal">Import Data</button>
                </div>

            </div>
        </div>

        <table id="productTable" class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th class="text-center">Product ID</th>
                    <th class="text-center">Product Name</th>
                    <th class="text-center">Description</th>
                    <th class="text-center">Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row) : ?>
                    <tr>
                        <td class="text-center"><?= $row['product_id'] ?></td>
                        <td><?= $row['product_name'] ?></td>
                        <td><?= $row['description'] ?></td>
                        <td class="text-right"><?= $row['price'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php
    include("cdn_links.html");
    ?>

    <!-- FileSaver.js -->
    <script src="../vendor/FileSaver/src/FileSaver.js"></script>

    <script>
        var productData = <?php echo json_encode($data); ?>;
    </script>
    <script src="../javascript/custom.js"></script>


    <?php
    include("modal.html");
    ?>

</body>

</html>