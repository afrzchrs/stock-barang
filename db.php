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

    //tambah gambar 
    $allowed_extension = array('png','jpg');
    $nama = $_FILES['file']['name']; //nama gambar
    $dot = explode('.',$nama);
    $ekstensi = strtolower(end($dot)); //ekstensinya
    $ukuran = $_FILES['file']['size']; //sizenya
    $file_tmp = $_FILES['file']['tmp_name']; //lokasinya

    //penamaan file -> enkripsi
    $image = md5(uniqid($nama,true) . time()).'.'.$ekstensi;
    
    $cek = mysqli_query($conn,"select * from stock where namabarang='$namabarang'");
    $hitung = mysqli_num_rows($cek);

    if($hitung<1){
        //jika belum ada 
        if($ukuran==0){
            //jika tidak ingin upload
            $add = mysqli_query($conn, "INSERT INTO stock (namabarang, deskripsi, stock) values('$namabarang', '$deskripsi','$stock')");
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

                    $add = mysqli_query($conn, "INSERT INTO stock (namabarang, deskripsi, stock, image) values('$namabarang', '$deskripsi','$stock','$image')");
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
    $keterangan = $_POST['keterangan'];
    $qty = $_POST['qty'];

    $stockcheck = mysqli_query($conn, "SELECT * FROM stock where idbarang='$barang'");
    $takestock = mysqli_fetch_array($stockcheck);

    $stocknow = $takestock["stock"];
    $finalstockupdate = $stocknow + $qty;

    $addin = mysqli_query($conn, "INSERT INTO masuk (idbarang, keterangan, qty) values('$barang','$keterangan','$qty')");
    $stockupdate = mysqli_query($conn, "UPDATE stock set stock='$finalstockupdate' where idbarang='$barang'");

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
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $stockcheck = mysqli_query($conn, "SELECT * FROM stock where idbarang='$barang'");
    $takestock = mysqli_fetch_array($stockcheck);

    $stocknow = $takestock["stock"];
    $finalstockupdate = $stocknow - $qty;

    $addin = mysqli_query($conn, "INSERT INTO keluar (idbarang, penerima, qty) values('$barang','$penerima','$qty')");
    $stockupdate = mysqli_query($conn, "UPDATE stock set stock='$finalstockupdate' where idbarang='$barang'");

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
        $update = mysqli_query($conn, "UPDATE stock set namabarang='$namabarang', deskripsi='$deskripsi', stock='$stock' where idbarang='$idb'");
        if ($update) {
            header("location:index.php");
        } else {
            echo 'fail';
            header('location:index.php');
        }
    }else{
        //jika ingin
        move_uploaded_file($file_tmp,"image/".$image);
        $update = mysqli_query($conn, "UPDATE stock set namabarang='$namabarang', deskripsi='$deskripsi', stock='$stock', image='$image' where idbarang='$idb'");
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

    $gambar = mysqli_query($conn,"select * from stock where idbarang='$idb'");
    $get = mysqli_fetch_array($gambar);
    $img = 'image/'.$get['image'];
    unlink($img);

    $hapus = mysqli_query($conn, "DELETE FROM stock WHERE idbarang='$idb'");
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
    $idm = $_POST['idm'];
    $namabarang = $_POST['namabarang'];
    $keterangan = $_POST['keterangan'];
    $qty = $_POST['qty'];

    $cekstock = mysqli_query($conn, "SELECT * from stock where idbarang='$idb'");
    $stocknya = mysqli_fetch_array($cekstock);
    $stockskrg = $stocknya['stock'];

    $qtyskrg = mysqli_query($conn,"SELECT * FROM masuk WHERE idmasuk='$idm'");
    $qtynya = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $qtynya["qty"];

    if($qty>$qtyskrg){
        $selisih = $qty - $qtyskrg;
        $kurangi = $stockskrg + $selisih;
        $kurangistocknya = mysqli_query($conn,"UPDATE stock set stock='$kurangi' where idbarang='$idb' ");
        $updatenya = mysqli_query($conn,"UPDATE masuk set qty='$qty', keterangan='$keterangan' where idmasuk='$idm'");
        if($kurangistocknya&&$updatenya){
            header("location:masuk.php");
        }else{
            echo "FAIL";
            header("location:masuk.php");
        }
    }else{
        $selisih = $qtyskrg - $qty;
        $kurangi = $stockskrg - $selisih;
        $kurangistock = mysqli_query($conn,"UPDATE stock set stock='$kurangi' where idbarang='$idb'");
        $updatenya = mysqli_query($conn,"UPDATE masuk set qty='$qty', keterangan='$keterangan' where idmasuk='$idm'");
        if($kurangistock&&$updatenya){
            header("location:masuk.php");
        }else{
            echo "fail";
            header("location:masuk.php");
        }

    }
}


// Delete info barang Masuk
if (isset($_POST['deletebarangmasuk'])) {
    $idb = $_POST['idb'];
    $qty = $_POST['qty'];
    $idm = $_POST['idm']; 

    $getdatastock = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stock = $data["stock"];

    $selisih = $stock - $qty;

    $update = mysqli_query($conn, "UPDATE stock SET stock='$selisih' WHERE idbarang='$idb'");
    $hapusdata = mysqli_query($conn, "DELETE FROM masuk WHERE idmasuk='$idm'");
    
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
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstock = mysqli_query($conn, "SELECT * from stock where idbarang='$idb'");
    $stocknya = mysqli_fetch_array($cekstock);
    $stockskrg = $stocknya['stock'];

    $qtyskrg = mysqli_query($conn,"SELECT * FROM keluar WHERE idkeluar='$idk'");
    $qtynya = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $qtynya["qty"];

    if($qty>$qtyskrg){
        $selisih = $qty - $qtyskrg;
        $kurangi = $stockskrg - $selisih;
        $kurangistocknya = mysqli_query($conn,"UPDATE stock set stock='$kurangi' where idbarang='$idb' ");
        $updatenya = mysqli_query($conn,"UPDATE keluar set qty='$qty', penerima='$penerima' where idkeluar='$idk'");
        if($kurangistocknya&&$updatenya){
            header("location:keluar.php");
        }else{
            echo "FAIL";
            header("location:keluar.php");
        }
    }else{
        $selisih = $qtyskrg - $qty;
        $kurangi = $stockskrg + $selisih;
        $kurangistock = mysqli_query($conn,"UPDATE stock set stock='$kurangi' where idbarang='$idb'");
        $updatenya = mysqli_query($conn,"UPDATE keluar set qty='$qty', penerima='$penerima' where idkeluar='$idk'");
        if($kurangistock&&$updatenya){
            header("location:keluar.php");
        }else{
            echo "fail";
            header("location:keluar.php");
        }

    }
}
// Delete info barang keluar
if (isset($_POST['deletebarangkeluar'])) {
    $idb = $_POST['idb'];
    $qty = $_POST['qty'];
    $idk = $_POST['idk'];

    $getdatastock = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stock = $data["stock"];

    $update = $stock + $qty;

    $update = mysqli_query($conn, "UPDATE stock SET stock='$update' WHERE idbarang='$idb'");
    $hapusdata = mysqli_query($conn, "DELETE FROM keluar WHERE idkeluar='$idk'");

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
    $email = $_POST['email'];
    $password = $_POST['password'];

    $queryinsert = mysqli_query($conn,"insert into account (email,password) values ('$email','$password')");

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
        $emailbaru = $_POST['emailadmin'];
        $passwordbaru = $_POST['passwordbaru'];
        $iduser = $_POST['iduser'];

        $queryupdate = mysqli_query($conn,"update account set email='$emailbaru', password='$passwordbaru' where id='$iduser'");

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

        $querydelete = mysqli_query($conn,"delete from account where id ='$iduser'");

        if($querydelete){
            header('location:admin.php');
        }else{
            header('location:admin.php');
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

?>