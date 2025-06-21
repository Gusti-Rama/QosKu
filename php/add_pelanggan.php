<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../pages/login.php");
    exit();
}

include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $namaLengkap = $_POST['namaLengkap'];
    $email = $_POST['email'];
    $nomorHp = '-';
    $alamat = '-';

    // Check if username already exists
    $check_query = "SELECT idPelanggan FROM pelanggan WHERE username = ?";
    $check_stmt = $connect->prepare($check_query);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        header("Location: ../pages/pemilik/listakun.php?error=username_exists");
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new pelanggan
    $query = "INSERT INTO pelanggan (username, password, namaLengkap, email, nomorHp, alamat, profilePicture) VALUES (?, ?, ?, ?, ?, ?, '')";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("ssssss", $username, $hashed_password, $namaLengkap, $email, $nomorHp, $alamat);

    if ($stmt->execute()) {
        header("Location: ../pages/pemilik/listakun.php?success=pelanggan_added");
    } else {
        header("Location: ../pages/pemilik/listakun.php?error=insert_failed");
    }
} else {
    header("Location: ../pages/pemilik/listakun.php");
}
?> 