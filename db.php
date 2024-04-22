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

    $add = mysqli_query($conn, "INSERT INTO stock (namabarang, deskripsi, stock) values('$namabarang', '$deskripsi','$stock')");
    if ($add) {
        header('location:index.php');
    } else {
        echo 'FAIL';
        header('location:index.php');
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

    $update = mysqli_query($conn, "UPDATE stock set namabarang='$namabarang', deskripsi='$deskripsi', stock='$stock' where idbarang='$idb'");
    if ($update) {
        header("location:index.php");
    } else {
        echo 'fail';
        header('location:index.php');
    }
}

// Delete info barang
if (isset($_POST['deletebarang'])) {
    $idb = $_POST['delete_idb']; // Fix the variable name here

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

//Menambah Admin baru

<?php
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

//edit data admin

<?php
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

//hapus data admin
<?php
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
