<?php
require 'db.php';
require 'cek.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Barang Masuk</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    <style>
        .zoomable{
            width: 100px;
        }
        .zoomable:hover{
            transform: scale(2.5);
            transition: 0.3s ease;
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand" href="index.html">REZKY ELECTRIC</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
        <!-- Navbar-->
        <ul class="navbar-nav ml-auto ml-md-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading"><strong>Kelola Data Barang</strong></div>
                        <a class="nav-link" href="index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Stock Barang
                        </a>
                        <a class="nav-link" href="masuk.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Stock Masuk
                        </a>
                        <a class="nav-link" href="keluar.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Stock Keluar
                        </a>
                        <div class="sb-sidenav-menu-heading"><strong>Kelola Data lainnnya</strong></div>
                        <a class="nav-link" href="supplier.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Kelola Supplier
                        </a>
                        <a class="nav-link" href="pelanggan.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Kelola Pelanggan
                        </a>
                        <div class="sb-sidenav-menu-heading"><strong>Kelola Data Users</strong></div>
                        <a class="nav-link" href="admin.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Kelola Admin
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    <?php
                    echo $_SESSION['username']
                    ?>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <h1 class="mt-4">Stock Barang Masuk</h1>
                    <div class="card mb-4">
                        <div class="card-header">
                            <!-- Button to Open the Modal -->
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                                Tambah Barang Masuk
                            </button>
                            
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Gambar</th>
                                            <th>Nama Barang</th>
                                            <th>Supplier</th>
                                            <th>Jumlah</th>
                                            <th>Harga_barang(jt)</th>
                                            <th>Total_harga(jt)</th>
                                            <th>aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $takeall = mysqli_query($conn, 'SELECT *
                                        FROM barang s
                                        JOIN barang_masuk m ON s.idbarang = m.idbarang
                                        JOIN supplier a ON a.id_supplier = m.id_supplier;
                                        ');
                                        $i = 1; // Move the initialization outside the loop
                                        while ($takerow = mysqli_fetch_array($takeall)) {
                                            $idb = $takerow['idbarang'];
                                            $idm = $takerow['idmasuk'];
                                            $tanggal = $takerow['tanggal'];
                                            $idbarang = $takerow['namabarang'];
                                            $Supplier = $takerow['nama_supplier'];
                                            $ids = $takerow['id_supplier'];
                                            $qty = $takerow['qty'];
                                            $harga_barang = $takerow['harga_barang_masuk'];
                                            $total_harga= $takerow['total_harga_masuk'];
                                            
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
                                                <td><?= $tanggal; ?></td>
                                                <td><?= $img; ?></td>
                                                <td><?= $idbarang; ?></td>
                                                <td><?= $Supplier; ?></td>
                                                <td><?= $qty; ?></td>
                                                <td><?= $harga_barang; ?></td>
                                                <td><?= $total_harga; ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#edit<?= $idb ?>">
                                                        Edit
                                                    </button>
                                                    <input type="hidden" name="hapusidbarang" value="<?= $idb; ?>">
                                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete<?= $idb ?>">
                                                        Delete
                                                    </button>
                                                </td>
                                            </tr>
                                            <!-- The Edit Modal -->
                                            <div class="modal fade" id="edit<?= $idb; ?>">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">

                                                        <!-- Modal Header -->
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Edit Data Barang</h4>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>

                                                        <!-- Modal body -->
                                                        <form method="post" enctype="multipart/form-data">
                                                            <div class="modal-body">
                                                                <select name="barang" class="form-control">
                                                                    <?php
                                                                    $all = mysqli_query($conn, "SELECT * from barang, supplier");
                                                                    while ($fetch = mysqli_fetch_array($all)) {
                                                                        $namabarang = $fetch['namabarang'];
                                                                        $idbarang = $fetch['idbarang'];
                                                                    ?>
                                                                        <option value="<?= $idbarang; ?>"><?= $namabarang; ?></option>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </select><br>
                                                                <select name="Supplier" class="form-control">
                                                                    <?php
                                                                    $all = mysqli_query($conn, "SELECT * from supplier");
                                                                    while ($fetch = mysqli_fetch_array($all)) {
                                                                        $nama_supplier = $fetch['nama_supplier'];
                                                                        $id_supplier = $fetch['id_supplier'];
                                                                    ?>
                                                                        <option value="<?= $id_supplier; ?>"><?= $nama_supplier; ?></option>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </select><br>
                                                                <input type="number" name="qty" value='<?= $qty; ?>' class="form-control" required><br>
                                                                <input type="number" name="harga" value='<?= $harga_barang; ?>' class="form-control" required><br>
                                                                <input type="file" name="file" class="form-control"><br>
                                                                <input type="hidden" name="idb" value='<?= $idb; ?>' class="form-control" required>
                                                                <input type="hidden" name="idm" value='<?= $idm; ?>' class="form-control" required>
                                                                <button type="submit" class="btn btn-primary" name="updatebarangmasuk">Submit</button>
                                                            </div>
                                                        </form>

                                                    </div>
                                                </div>
                                            </div>
                                            <!-- The Delete Modal -->
                                            <div class="modal fade" id="delete<?= $idb; ?>">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">

                                                        <!-- Modal Header -->
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Hapus Data Barang?</h4>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>

                                                        <!-- Modal body -->
                                                        <form method="post">
                                                            <div class="modal-body">
                                                                Apakah Anda yakin akan menghapus <?= $idbarang; ?> dari tabel?<br><br>
                                                                <input type="hidden" name="idb" value='<?= $idb; ?>' class="form-control" required>
                                                                <input type="hidden" name="idm" value='<?= $idm; ?>' class="form-control" required>
                                                                <input type="hidden" name="supplier" value='<?= $Supplier; ?>' class="form-control" required>
                                                                <input type="hidden" name="qty" value='<?= $qty; ?>' class="form-control" required>
                                                                <input type="hidden" name="harga" value='<?= $harga_barang; ?>' class="form-control" required>
                                                                <button type="submit" class="btn btn-danger" name="deletebarangmasuk">Hapus</button>
                                                            </div>
                                                        </form>

                                                    </div>
                                                </div>
                                            </div>

                                        <?php
                                        };
                                        ?>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Your Website 2020</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/datatables-demo.js"></script>
</body>
<!-- The Modal -->
<div class="modal fade" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Tambah Barang Masuk</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <form method="post">
                <div class="modal-body">

                    <select name="barang" class="form-control">
                        <?php
                        $all = mysqli_query($conn, "SELECT * from barang, supplier");
                        while ($fetch = mysqli_fetch_array($all)) {
                            $namabarang = $fetch['namabarang'];
                            $idbarang = $fetch['idbarang'];
                        ?>
                            <option value="<?= $idbarang; ?>"><?= $namabarang; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <br>
                    <input type="number" name="qty" placeholder="Quantity" class="form-control" required><br>
                    <select name="Supplier" class="form-control">
                        <?php
                        $all = mysqli_query($conn, "SELECT * from supplier");
                        while ($fetch = mysqli_fetch_array($all)) {
                            $nama_supplier = $fetch['nama_supplier'];
                            $id_supplier = $fetch['id_supplier'];
                        ?>
                            <option value="<?= $id_supplier; ?>"><?= $nama_supplier; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <br>
                    <input type="number" name="harga" placeholder="harga_barang" class="form-control" required><br>
                    <input type="file" name="file" class="form-control"><br>
                    <button type="submit" class="btn btn-primary" name="barangmasuk">Submit</button>
                </div>
            </form>

        </div>
    </div>
</div>

</html>