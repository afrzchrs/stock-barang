<?php
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'project';
session_start();
$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
    die('FAILED' . mysqli_connect_error());
}

//tambah barang baru
if (isset($_POST['addnewbarang'])) {
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];
    $harga = $_POST['harga'];
    $total_harga =$stock * $harga;

    //tambah gambar 
    $allowed_extension = array('png','jpg');
    $nama = $_FILES['file']['name']; //nama gambar
    $dot = explode('.',$nama);
    $ekstensi = strtolower(end($dot)); //ekstensinya
    $ukuran = $_FILES['file']['size']; //sizenya
    $file_tmp = $_FILES['file']['tmp_name']; //lokasinya

    //penamaan file -> enkripsi
    $image = md5(uniqid($nama,true) . time()).'.'.$ekstensi;
    
    $cek = mysqli_query($conn,"select * from barang where namabarang='$namabarang'");
    $hitung = mysqli_num_rows($cek);

    if($hitung<1){
        //jika belum ada 
        if($ukuran==0){
            //jika tidak ingin upload
            $add = mysqli_query($conn, "INSERT INTO barang (namabarang, deskripsi, stock, harga_barang, total_harga) values('$namabarang', '$deskripsi','$stock','$harga','$total_harga')");
            if ($add) {
                header('location:index.php');
            } else {
                echo 'FAIL';
                header('location:index.php');
            }
        }else{
            //jika ingin
            //proses upload gambar
            if(in_array($ekstensi, $allowed_extension) === true){
                if($ukuran < 15000000){
                    move_uploaded_file($file_tmp,"image/".$image);

                    $add = mysqli_query($conn, "INSERT INTO barang (namabarang, deskripsi, stock, total_harga, image) values('$namabarang', '$deskripsi','$stock', '$total_harga', '$image')");
                    if ($add) {
                        header('location:index.php');
                    } else {
                        echo 'FAIL';
                        header('location:index.php');
                    }
                }else{
                    //jika filenya lebih dari 15 mb
                    echo '
                    <script>
                        alert("Ukuran file terlalu besar");
                        window.location.href="index.php";
                    </script>';
                }
            }else{
                //jika file bukan png/jpg
                echo '
                <script>
                    alert("File harus berupa png atau jpg");
                    window.location.href="index.php";
                </script>';
            }
        }
    }else{
            // jika sudah ada
            echo '
            <script>
                alert("Nama barang telah terdaftar");
                window.location.href="index.php";
            </script>';
        }
}
//tambah barang masuk
if (isset($_POST['barangmasuk'])) {
    $barang = $_POST['barang'];
    $supplier = $_POST['Supplier'];
    $qty = $_POST['qty'];
    $harga= $_POST['harga'];
    $total_harga_masuk= $qty * $harga;

    $stockcheck = mysqli_query($conn, "SELECT * FROM barang where idbarang='$barang'");
    $takestock = mysqli_fetch_array($stockcheck);

    $stocknow = $takestock["stock"];
    $harganow = $takestock["total_harga"];

    $finalstockupdate = $stocknow + $qty;
    $finalhargaupdate = $harganow + $total_harga_masuk;

    $addin = mysqli_query($conn, "INSERT INTO barang_masuk (id_supplier, idbarang, qty,harga_barang_masuk, total_harga_masuk) values('$supplier','$barang','$qty','$harga','$total_harga_masuk')");
    $stockupdate = mysqli_query($conn, "UPDATE barang SET stock='$finalstockupdate', total_harga='$finalhargaupdate' WHERE idbarang='$barang'");

    if ($addin && $stockupdate) {
        header("location:masuk.php");
    } else {
        echo 'fail';
        header('location:masuk.php');
    }
}

//tambah barang keluar
if (isset($_POST['barangkeluar'])) {
    $barang = $_POST['barang'];
    $qty = $_POST['qty'];
    $harga = $_POST['harga'];
    $pelanggan = $_POST['Pelanggan'];
    $total_harga_keluar = $qty * $harga;

    $stockcheck = mysqli_query($conn, "SELECT * FROM barang where idbarang='$barang'");
    $takestock = mysqli_fetch_array($stockcheck);
    $stocknow = $takestock["stock"];
    $harganow = $takestock["total_harga"];

    $finalstockupdate = $stocknow - $qty;
    $finalhargaupdate = $harganow - $total_harga_keluar;

    $addin = mysqli_query($conn, "INSERT INTO barang_keluar (id_pelanggan, idbarang, qty, harga_barang_keluar, total_harga_keluar) values('$pelanggan','$barang','$qty', '$harga','$total_harga_keluar')");
    $stockupdate = mysqli_query($conn, "UPDATE barang set stock='$finalstockupdate', total_harga='$finalhargaupdate' where idbarang='$barang'");

    if ($addin && $stockupdate) {
        header("location:keluar.php");
    } else {
        echo 'fail';
        header('location:keluar.php');
    }
}


//Update info barang
if (isset($_POST['updatebarang'])) {
    $idb = $_POST['idb'];
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];
    $harga = $_POST['harga'];
    $total_harga= $stock * $harga;
    
    //update gambar 
    $allowed_extension = array('png','jpg');
    $nama = $_FILES['file']['name']; //nama gambar
    $dot = explode('.',$nama);
    $ekstensi = strtolower(end($dot)); //ekstensinya
    $ukuran = $_FILES['file']['size']; //sizenya
    $file_tmp = $_FILES['file']['tmp_name']; //lokasinya

    //penamaan file -> enkripsi
    $image = md5(uniqid($nama,true) . time()).'.'.$ekstensi;

    if($ukuran==0){
        //jika tidak ingin upload
        $update = mysqli_query($conn, "UPDATE barang set namabarang='$namabarang', deskripsi='$deskripsi', stock='$stock', harga_barang='$harga', total_harga='$total_harga' where idbarang='$idb'");
        if ($update) {
            header("location:index.php");
        } else {
            echo 'fail';
            header('location:index.php');
        }
    }else{
        //jika ingin
        move_uploaded_file($file_tmp,"image/".$image);
        $update = mysqli_query($conn, "UPDATE barang set namabarang='$namabarang', deskripsi='$deskripsi', stock='$stock', image='$image' where idbarang='$idb'");
        if ($update) {
            header("location:index.php");
        } else {
            echo 'fail';
            header('location:index.php');
        }
    }
}

// Delete info barang
if (isset($_POST['deletebarang'])) {
    $idb = $_POST['delete_idb']; // Fix the variable name here

    $gambar = mysqli_query($conn,"select * from barang where idbarang='$idb'");
    $get = mysqli_fetch_array($gambar);
    $img = 'image/'.$get['image'];
    unlink($img);

    $hapus = mysqli_query($conn, "DELETE FROM barang WHERE idbarang='$idb'");
    if ($hapus) {
        header("location:index.php");
    } else {
        echo 'fail';
        header('location:index.php');
    }
}
// Update info barang Masuk
if (isset($_POST['updatebarangmasuk'])) {
    $idb = $_POST['idb'];
    $barang = $_POST['barang'];
    $idm = $_POST['idm'];
    $qty = $_POST['qty'];
    $harga_barang = $_POST['harga'];
    $supplier = $_POST['Supplier'];
    $total_harga = $qty * $harga_barang;

    $cekstock = mysqli_query($conn, "SELECT * from barang where idbarang='$idb'");
    $data = mysqli_fetch_array($cekstock);
    $stockskrg = $data['stock'];
    $hargastock = $data['total_harga'];

    $qtyskrg = mysqli_query($conn,"SELECT * FROM barang_masuk WHERE idmasuk='$idm'");
    $data = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $data["qty"];
    $hargaskrg = $data["total_harga_masuk"];

    if ($qty > $qtyskrg && $total_harga > $hargaskrg) {
        $selisih_harga = $total_harga - $hargaskrg;
        $hargafinal = $selisih_harga + $hargastock;
    
        $selisih = $qty - $qtyskrg;
        $kurangi = $stockskrg + $selisih;
    
        $kurangistocknya = mysqli_query($conn, "UPDATE barang SET stock='$kurangi', total_harga='$hargafinal' WHERE idbarang='$idb'");
        $updatenya = mysqli_query($conn, "UPDATE barang_masuk SET qty='$qty', harga_barang_masuk='$harga_barang',total_harga_masuk='$total_harga', id_supplier='$supplier', idbarang='$barang' WHERE idmasuk='$idm'");
        if ($kurangistocknya && $updatenya) {
            header("location: masuk.php");
        } else {
            echo "FAIL";
            header("location: masuk.php");
        }
    } else {
        $selisih_harga = $hargaskrg - $total_harga;
        $hargafinal = $hargastock - $selisih_harga;
    
        $selisih = $qtyskrg - $qty;
        $kurangi = $stockskrg - $selisih;
        $kurangistock = mysqli_query($conn, "UPDATE barang SET stock='$kurangi', total_harga='$hargafinal' WHERE idbarang='$idb'");
        $updatenya = mysqli_query($conn, "UPDATE barang_masuk SET qty='$qty', harga_barang_masuk='$harga_barang', total_harga_masuk='$total_harga', id_supplier='$supplier', idbarang='$barang' WHERE idmasuk='$idm'");
        if ($kurangistock && $updatenya) {
            header("location: masuk.php");
        } else {
            echo "fail";
            header("location: masuk.php");
        }
    }
}


// Delete info barang Masuk
if (isset($_POST['deletebarangmasuk'])) {
    $idb = $_POST['idb'];
    $qty = $_POST['qty'];
    $idm = $_POST['idm'];
    $harga = $_POST['harga'];
    $total_harga = $qty * $harga;

    $getdatastock = mysqli_query($conn, "SELECT * FROM barang WHERE idbarang='$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stock = $data["stock"];
    $harganow = $data["total_harga"];

    $selisih = $stock - $qty;
    $selisih_harga = $harganow - $total_harga;

    $update = mysqli_query($conn, "UPDATE barang SET stock='$selisih', total_harga ='$selisih_harga' WHERE idbarang='$idb'");
    $hapusdata = mysqli_query($conn, "DELETE FROM barang_masuk WHERE idmasuk='$idm'");
    
    if ($hapusdata && $update) {
        header("location: masuk.php");
    } else {
        echo "gagal";
        header("location:masuk.php");
    }
}


//Update Data barang keluar
if (isset($_POST['updatebarangkeluar'])) {
    $idb = $_POST['idb'];
    $idk = $_POST['idk'];
    $qty = $_POST['qty'];
    $barang = $_POST['barang'];
    $pelanggan = $_POST['Pelanggan'];
    $harga_barang = $_POST['harga'];
    $total_harga = $qty * $harga_barang;

    $cekstock = mysqli_query($conn, "SELECT * from barang where idbarang='$idb'");
    $data = mysqli_fetch_array($cekstock);
    $stockskrg = $data['stock'];
    $hargastock = $data['total_harga'];

    $qtyskrg = mysqli_query($conn,"SELECT * FROM barang_keluar WHERE idkeluar='$idk'");
    $data = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $data["qty"];
    $hargaskrg = $data["total_harga_keluar"];

    if ($qty > $qtyskrg && $total_harga > $hargaskrg) {
        $selisih_harga = $total_harga - $hargaskrg;
        $hargafinal = $hargastock - $selisih_harga;
    
        $selisih = $qty - $qtyskrg;
        $kurangi = $stockskrg - $selisih;
    
        $kurangistocknya = mysqli_query($conn, "UPDATE barang SET stock='$kurangi', total_harga='$hargafinal' WHERE idbarang='$idb'");
        $updatenya = mysqli_query($conn, "UPDATE barang_keluar SET qty='$qty', harga_barang_keluar='$harga_barang',total_harga_keluar='$total_harga', id_pelanggan='$pelanggan', idbarang='$barang' WHERE idkeluar='$idk'");
        if ($kurangistocknya && $updatenya) {
            header("location: keluar.php");
        } else {
            echo "FAIL";
            header("location: keluar.php");
        }
    } else {
        $selisih_harga = $hargaskrg - $total_harga;
        $hargafinal = $hargastock +  $selisih_harga;
    
        $selisih = $qtyskrg - $qty;
        $kurangi = $stockskrg + $selisih;
        $kurangistock = mysqli_query($conn, "UPDATE barang SET stock='$kurangi', total_harga='$hargafinal' WHERE idbarang='$idb'");
        $updatenya = mysqli_query($conn, "UPDATE barang_keluar SET qty='$qty', harga_barang_keluar='$harga_barang', total_harga_keluar='$total_harga', id_pelanggan='$pelanggan', idbarang='$barang' WHERE idkeluar='$idk'");
        if ($kurangistock && $updatenya) {
            header("location: keluar.php");
        } else {
            echo "fail";
            header("location: keluar.php");
        }
    }
}


// Delete info barang keluar
if (isset($_POST['deletebarangkeluar'])) {
    $idb = $_POST['idb'];
    $qty = $_POST['qty'];
    $idk = $_POST['idk'];
    $harga = $_POST['harga'];
    $total_harga_keluar = $qty * $harga;

    $getdatastock = mysqli_query($conn, "SELECT * FROM barang WHERE idbarang='$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stock = $data["stock"];
    $hargastock = $data["total_harga"];

    $update = $stock + $qty;
    $updateharga = $hargastock + $total_harga_keluar;

    $update = mysqli_query($conn, "UPDATE barang SET stock='$update', total_harga='$updateharga' WHERE idbarang='$idb'");
    $hapusdata = mysqli_query($conn, "DELETE FROM barang_keluar WHERE idkeluar='$idk'");

    if ($hapusdata && $update) {
        header("location:keluar.php");
    } else {
        echo "gagal";
        header("location:keluar.php");
    }
}

?>



<?php
// Menambah data admin 
if(isset($_POST['addadmin'])){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $queryinsert = mysqli_query($conn,"insert into users (email,password,username) values ('$email','$password','$username')");

    if($queryinsert){
        header('location:admin.php');
    }else{
        header('location:admin.php');
    }
}
?>  



<?php
// Mengupdate data admin 
    if(isset($_POST['updateadmin'])){
        $usernamebaru = $_POST['usernamebaru'];
        $emailbaru = $_POST['emailadmin'];
        $passwordbaru = $_POST['passwordbaru'];
        $iduser = $_POST['iduser'];

        $queryupdate = mysqli_query($conn,"update users set email='$emailbaru', password='$passwordbaru', username='$usernamebaru' where id='$iduser'");

        if($queryupdate){
            header('location:admin.php');
        }else{
            header('location:admin.php');
        }
    }
?>


<?php
// Menghapus data admin 
    if(isset($_POST['deleteadmin'])){
        $iduser = $_POST['delete_iduser'];

        $querydelete = mysqli_query($conn,"delete from users where id ='$iduser'");

        if($querydelete){
            header('location:admin.php');
        }else{
            header('location:admin.php');
        }
    }
?>

<?php
// Menambah data supplier
if(isset($_POST['addsupplier'])){
    $supplier = $_POST['supplier'];
    $no_telepon = $_POST['no_tel'];
    $alamat = $_POST['alamat'];

    $queryinsert = mysqli_query($conn,"insert into supplier (nama_supplier, no_telpon, Alamat) values ('$supplier','$no_telepon', '$alamat')");

    if($queryinsert){
        header('location:supplier.php');
    }else{
        header('location:supplier.php');
    }
}
?>  



<?php
// Mengupdate data supplier
    if(isset($_POST['updatesup'])){
        $sup = $_POST['supplier'];
        $no_tel = $_POST['no_tel'];
        $alamat = $_POST['alamat'];
        $idsup = $_POST['idsup'];

        $queryupdate = mysqli_query($conn,"update supplier set nama_supplier='$sup', no_telpon='$no_tel', Alamat='$alamat' where id_supplier='$idsup'");

        if($queryupdate){
            header('location:supplier.php');
        }else{
            header('location:supplier.php');
        }
    }
?>


<?php
// Menghapus data supplier 
    if(isset($_POST['deletesup'])){
        $idsup = $_POST['delete_idsup'];

        $querydelete = mysqli_query($conn,"delete from supplier where id_supplier ='$idsup'");

        if($querydelete){
            header('location:supplier.php');
        }else{
            header('location:supplier.php');
        }
    }
?>

<?php
// Menambah data pelanggan
if(isset($_POST['addpelanggan'])){
    $pelanggan = $_POST['pelanggan'];
    $no_telepon = $_POST['no_tel'];
    $alamat = $_POST['alamat'];

    $queryinsert = mysqli_query($conn,"insert into pelanggan (nama_pelanggan, no_telepon, Alamat) values ('$pelanggan','$no_telepon', '$alamat')");

    if($queryinsert){
        header('location:pelanggan.php');
    }else{
        header('location:pelanggan.php');
    }
}
?>  



<?php
// Mengupdate data pelanggan
    if(isset($_POST['updatepel'])){
        $pel = $_POST['pelanggan'];
        $no_tel = $_POST['no_tel'];
        $alamat = $_POST['alamat'];
        $idpel = $_POST['idpel'];

        $queryupdate = mysqli_query($conn,"update pelanggan set nama_pelanggan='$pel', no_telepon='$no_tel', Alamat='$alamat' where id_pelanggan='$idpel'");

        if($queryupdate){
            header('location:pelanggan.php');
        }else{
            header('location:pelanggan.php');
        }
    }
?>


<?php
// Menghapus data pelanggan 
    if(isset($_POST['deletepel'])){
        $idpel = $_POST['delete_idpel'];

        $querydelete = mysqli_query($conn,"delete from pelanggan where id_pelanggan ='$idpel'");

        if($querydelete){
            header('location:pelanggan.php');
        }else{
            header('location:pelanggan.php');
        }
    }
?>
<?php

//Meminjam Barang
if(isset($_POST['rental'])){
    $idbarang = $_POST['barang'];
    $qty = $_POST['qty'];
    $penerima = $_POST['penerima'];

    //ambil stock barang
    $stok_saat_ini = mysqli_query($conn,"select * from stock where idbarang='$idbarang'");
    $stok_nya = mysqli_fetch_array($stok_saat_ini);
    $stok = $stok_nya['stock'];

    //kurangi stoknya
    $new_stok = $stok-$qty;

    //query insert
    $insertpinjam=mysqli_query($conn,"Insert into peminjaman (idbarang,qty,peminjam) values ('$idbarang','$qty','$penerima')");

    //mengurangi stock
    $kurangistock = mysqli_query($conn,"update stock set stock='$new_stok' where idbarang='$idbarang'");

    if($insertpinjam&&$kurangistock){
        //jika berhasil
        echo '
        <script>
            alert("Berhasil");
            window.location.href="peminjaman.php";
        </script>';
    }else{
        //jika gaga;
        echo '
        <script>
            alert("gagal");
            window.location.href="peminjaman.php";
        </script>';
    }
}

//menyelesaikan pinjaman
if(isset($_POST['barangkembali'])){
    $idpinjam = $_POST['idp'];
    $idbarang = $_POST['idb'];

    //query eksekusi
    $update_status = mysqli_query($conn,"update peminjaman set status='Kembali' where idpeminjam='$idpinjam'");

     //ambil stock barang
     $stok_saat_ini = mysqli_query($conn,"select * from stock where idbarang='$idbarang'");
     $stok_nya = mysqli_fetch_array($stok_saat_ini);
     $stok = $stok_nya['stock'];

     //ambil qty
     $qty_saat_ini = mysqli_query($conn,"select * from peminjaman where idpeminjam='$idpinjam'");
     $qty_nya = mysqli_fetch_array($qty_saat_ini);
     $qty = $qty_nya['qty'];
 
     //kurangi stoknya
     $new_stok = $stok+$qty;
 
     //mengurangi stock
     $kembalikan_stock = mysqli_query($conn,"update stock set stock='$new_stok' where idbarang='$idbarang'");


    if($update_status&&$kembalikan_stock){
        //jika berhasil
        echo '
        <script>
            alert("Berhasil");
            window.location.href="peminjaman.php";
        </script>';
    }else{
        //jika gaga;
        echo '
        <script>
            alert("gagal");
            window.location.href="peminjaman.php";
        </script>';
    }
}
?>

<?php
// Delete info barang
if (isset($_POST['deletedatarental'])) {
    $idb = $_POST['del_idb']; // Fix the variable name here

    $gambar = mysqli_query($conn,"select * from peminjaman where idbarang='$idb'");
    $get = mysqli_fetch_array($gambar);
    $img = 'image/'.$get['image'];
    unlink($img);

    $hapus = mysqli_query($conn, "DELETE FROM peminjaman WHERE idbarang='$idb'");
    if ($hapus) {
        header("location:index.php");
    } else {
        echo 'fail';
        header('location:index.php');
    }
}
?>