<?php
session_start();
if (!isset($_SESSION['role'])) {
  header(header: "Location: ../../pages/login.php?pesan=not_logged_in");
  exit;
}

$peran = strtolower($_SESSION['role']);

$diperbolehkan = ['owner'];

// cek peran usernya
if (!in_array($peran, $diperbolehkan)) {
  header("Location: ../../pages/login.php?pesan=Akses_Ditolak");
  exit;
}

include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPelanggan = $_POST['idPelanggan'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $namaLengkap = $_POST['namaLengkap'];
    $email = $_POST['email'];
    $nomorHp = $_POST['nomorHp'];
    $alamat = $_POST['alamat'];

    // Check if username already exists for other users
    $check_query = "SELECT idPelanggan FROM pelanggan WHERE username = ? AND idPelanggan != ?";
    $check_stmt = $connect->prepare($check_query);
    $check_stmt->bind_param("si", $username, $idPelanggan);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        header("Location: ../pages/pemilik/listakun.php?error=username_exists");
        exit();
    }

    // Update pelanggan
    if (!empty($password)) {
        // Update with new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE pelanggan SET username = ?, password = ?, namaLengkap = ?, email = ?, nomorHp = ?, alamat = ? WHERE idPelanggan = ?";
        $stmt = $connect->prepare($query);
        $stmt->bind_param("ssssssi", $username, $hashed_password, $namaLengkap, $email, $nomorHp, $alamat, $idPelanggan);
    } else {
        // Update without changing password
        $query = "UPDATE pelanggan SET username = ?, namaLengkap = ?, email = ?, nomorHp = ?, alamat = ? WHERE idPelanggan = ?";
        $stmt = $connect->prepare($query);
        $stmt->bind_param("sssssi", $username, $namaLengkap, $email, $nomorHp, $alamat, $idPelanggan);
    }

    if ($stmt->execute()) {
        header("Location: ../pages/pemilik/listakun.php?success=pelanggan_updated");
    } else {
        header("Location: ../pages/pemilik/listakun.php?error=update_failed");
    }
} else {
    header("Location: ../pages/pemilik/listakun.php");
}
?> 