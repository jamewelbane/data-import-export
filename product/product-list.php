<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Table</title>
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.11.5/datatables.min.css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>

</head>

<body>
    <div class="container">
        <h1>Product Table</h1>
        <button id="exportCsvBtn">Export to CSV</button>
        <button id="exportExcelBtn" onclick="downloadExcel()">Export to Excel</button>

        <table id="productTable" class="table">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Connect to your database and fetch data from the "products" table
                require_once '../database/connection.php'; // Include your database connection file

                $query = "SELECT * FROM products";
                $result = mysqli_query($link, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>{$row['product_id']}</td>";
                        echo "<td>{$row['product_name']}</td>";
                        echo "<td>{$row['description']}</td>";
                        echo "<td>{$row['price']}</td>";
                        // echo "<td>{$row['image_url']}</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No products found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Include jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs4/dt-1.11.5/datatables.min.js"></script>

    <!-- Include script for exporting to CSV -->
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#productTable').DataTable();

            // Export to CSV button click event
            $('#exportCsvBtn').on('click', function() {
                window.location.href = 'export-csv.php';
            });
        });
    </script>

    <script>
        function downloadExcel() {
            const table = document.getElementById("productTable");
            const data = [];

            // Get table headers
            const headers = [];
            for (let i = 0; i < table.rows[0].cells.length; i++) {
                headers.push(table.rows[0].cells[i].innerText);
            }
            data.push(headers);

            // Get table data
            for (let i = 1; i < table.rows.length; i++) {
                const rowData = [];
                for (let j = 0; j < table.rows[i].cells.length; j++) {
                    rowData.push(table.rows[i].cells[j].innerText);
                }
                data.push(rowData);
            }

            // Create a Blob object with the data
            const workbook = XLSX.utils.book_new();
            const worksheet = XLSX.utils.json_to_sheet(data);
            XLSX.utils.book_append_sheet(workbook, worksheet, "Products");
            const blob = new Blob([XLSX.write(workbook, {
                bookType: 'xlsx',
                type: 'binary'
            })], {
                type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
            });

            // Create a downloadable link and trigger download
            const link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = "products.xlsx";
            link.click();
        }
    </script>




</body>

</html>