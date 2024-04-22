<?php

$koneksi = mysqli_connect("localhost", "root", "", "rest");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT * FROM project WHERE id='$id'";
        $query = mysqli_query($koneksi, $sql);

        if ($query) {
            $data = mysqli_fetch_assoc($query);
            echo json_encode($data);
        } else {
            $data = [
                'status' => 'error',
                'message' => 'Failed to retrieve data',
            ];
            echo json_encode($data);
        }
    } else {
        $sql = "SELECT * FROM project";
        $query = mysqli_query($koneksi, $sql);
        $array_data = array();
        while ($data = mysqli_fetch_assoc($query)) {
            $array_data[] = $data;
        }
        echo json_encode($array_data);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = mysqli_real_escape_string($koneksi, $_POST['title']);
    $author = mysqli_real_escape_string($koneksi, $_POST['author']);
    $date = mysqli_real_escape_string($koneksi, $_POST['date']);

    $sql = "INSERT INTO project (title, author, date) VALUES ('$title', '$author', '$date')";
    $query = mysqli_query($koneksi, $sql);

    if ($query) {
        $data = [
            'status' => 'success',
        ];
        echo json_encode($data);
    } else {
        $data = [
            'status' => 'error',
        ];
        echo json_encode($data);
    }
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "DELETE FROM project WHERE id='$id'";
        $query = mysqli_query($koneksi, $sql);

        if ($query) {
            $data = [
                'status' => 'success',
            ];
            echo json_encode($data);
        } else {
            $data = [
                'status' => 'error',
            ];
            echo json_encode($data);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID not provided']);
    }
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (isset($_GET['id'], $_GET['title'], $_GET['author'], $_GET['date'])) {
        $id = $_GET['id'];
        $title = mysqli_real_escape_string($koneksi, $_GET['title']);
        $author = mysqli_real_escape_string($koneksi, $_GET['author']);
        $date = mysqli_real_escape_string($koneksi, $_GET['date']);

        $sql = "UPDATE project SET title='$title', author='$author', date='$date' WHERE id = '$id'";
        $query = mysqli_query($koneksi, $sql);

        if ($query) {
            $data = [
                'status' => 'success',
            ];
            echo json_encode($data);
        } else {
            $data = [
                'status' => 'error',
            ];
            echo json_encode($data);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Incomplete data for update']);
    }
    exit;
}
?>
