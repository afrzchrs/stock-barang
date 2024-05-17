<?php
require 'db.php';
require 'cek.php';
?>
<html>
<head>
  <title>Laporan Persediaan Barang</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="style.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
  <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>
  <style>
        table {
            width: 100%;
        }
        th, td {
            text-align: center;
            vertical-align: middle;
        }
        th:nth-child(1), td:nth-child(1) {
            width: 5%;
        }
        th:nth-child(2), td:nth-child(2) {
            width: 15%;
        }
        th:nth-child(3), td:nth-child(3) {
            width: 20%;
        }
        th:nth-child(4), td:nth-child(4) {
            width: 20%;
        }
        th:nth-child(5), td:nth-child(5) {
            width: 10%;
        }
        th:nth-child(6), td:nth-child(6) {
            width: 15%;
        }
        th:nth-child(7), td:nth-child(7) {
            width: 15%;
        }
        .zoomable {
            width: 100px;
            height: auto;
        }
    </style>
</head>

<body>
<div class="container">
			<h2>Laporan Persediaan Stock Bahan</h2>
			<h4>(Inventory)</h4>
				<div class="data-tables datatable-dark">
					
					<!-- Masukkan table nya disini, dimulai dari tag TABLE -->
                    <table class="table table-bordered" id="mauexport" width="100%" cellspacing="0">
                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Gambar</th>
                                            <th>Nama Barang</th>
                                            <th>Deskripsi</th>
                                            <th>Stock</th>
                                            <th>Harga_barang(jt)</th>
                                            <th>Total_harga(jt)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $takeall = mysqli_query($conn, 'SELECT * FROM barang');
                                        $i = 1; // Move the initialization outside the loop
                                        while ($takerow = mysqli_fetch_array($takeall)) {
                                            $namabarang = $takerow['namabarang'];
                                            $total_harga = $takerow['total_harga'];
                                            $harga_barang = $takerow['harga_barang'];
                                            $deskripsi = $takerow['deskripsi'];
                                            $stock = $takerow['stock'];
                                            $idb   = $takerow['idbarang'];

                                            // cek ada gambbar atau tidak
                                            $gambar = $takerow['image'];
                                            if($gambar==null){
                                                //jika tidak ada 
                                                $img = 'No Photo';
                                            }else{
                                                //jika ada
                                                $img = '<img src="image/'.$gambar.'" class="zoomable">';
                                            }

                                        ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td><?= $img; ?></td>
                                                <td><?= $namabarang; ?></td>
                                                <td><?= $deskripsi; ?></td>
                                                <td><?= $stock; ?></td>
                                                <td><?= $harga_barang; ?></td>
                                                <td><?= $total_harga; ?></td>     
                                                <?php
                                        };
                                        ?>
                                    </tbody>
                                </table>
					
				</div>
</div>
	
<script>
$(document).ready(function() {
    function convertImageToBase64(url, callback) {
        var xhr = new XMLHttpRequest();
        xhr.onload = function() {
            var reader = new FileReader();
            reader.onloadend = function() {
                callback(reader.result);
            }
            reader.readAsDataURL(xhr.response);
        };
        xhr.open('GET', url);
        xhr.responseType = 'blob';
        xhr.send();
    }

    function prepareImageExport(images, callback) {
        var promises = [];

        images.forEach(function(img, index) {
            var src = img.src;
            promises.push(new Promise(function(resolve) {
                convertImageToBase64(src, function(base64Img) {
                    resolve({ index: index, base64Img: base64Img });
                });
            }));
        });

        Promise.all(promises).then(callback);
    }

    var table = $('#mauexport').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdfHtml5',
                autoBom: false, // Prevent automatic download
                customize: function(doc) {
                    var imgElements = document.querySelectorAll('.zoomable');
                    var addImagesToPdf = function(results) {
                        results.forEach(function(result) {
                            doc.content[1].table.body[result.index + 1][1] = {
                                image: result.base64Img,
                                width: 50
                            };
                        });
                        pdfMake.createPdf(doc).download();
                    };
                    prepareImageExport(imgElements, addImagesToPdf);
                    return false; // Prevent the automatic download, as we will trigger it manually
                }
            },
            'copy', 'csv', 'excel'
        ]
    });
});
</script>




<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>

</body>
</html>

