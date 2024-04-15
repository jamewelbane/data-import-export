$(document).ready(function() {

    $('#productTable').DataTable();

    $('#openExportModalBtn').on('click', function() {
        $('#exportModal').modal('show');
    });

});


function downloadExcel() {
    var data = productData;
    var wb = XLSX.utils.book_new();
    wb.SheetNames.push("Products");
    var ws_data = [
        ["Product ID", "Product Name", "Description", "Price"]
    ];
    data.forEach(function(row) {
        ws_data.push([row.product_id, row.product_name, row.description, row.price]);
    });
    var ws = XLSX.utils.aoa_to_sheet(ws_data);
    wb.Sheets["Products"] = ws;
    var wbout = XLSX.write(wb, {
        bookType: 'xlsx',
        type: 'binary'
    });

    function s2ab(s) {
        var buf = new ArrayBuffer(s.length);
        var view = new Uint8Array(buf);
        for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
        return buf;
    }
    saveAs(new Blob([s2ab(wbout)], {
        type: "application/octet-stream"
    }), "products.xlsx");
}
