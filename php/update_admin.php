<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../pages/login.php");
    exit();
}

include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idAdmin = $_POST['idAdmin'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $namaAdmin = $_POST['namaAdmin'];
    $peran = $_POST['peran'];

    // Check if username already exists for other users
    $check_query = "SELECT idAdmin FROM admin WHERE username = ? AND idAdmin != ?";
    $check_stmt = $connect->prepare($check_query);
    $check_stmt->bind_param("si", $username, $idAdmin);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        header("Location: ../pages/pemilik/listakun.php?error=username_exists");
        exit();
    }

    // Update admin
    if (!empty($password)) {
        // Update with new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE admin SET username = ?, password = ?, namaAdmin = ?, peran = ? WHERE idAdmin = ?";
        $stmt = $connect->prepare($query);
        $stmt->bind_param("ssssi", $username, $hashed_password, $namaAdmin, $peran, $idAdmin);
    } else {
        // Update without changing password
        $query = "UPDATE admin SET username = ?, namaAdmin = ?, peran = ? WHERE idAdmin = ?";
        $stmt = $connect->prepare($query);
        $stmt->bind_param("sssi", $username, $namaAdmin, $peran, $idAdmin);
    }

    if ($stmt->execute()) {
        header("Location: ../pages/pemilik/listakun.php?success=admin_updated");
    } else {
        header("Location: ../pages/pemilik/listakun.php?error=update_failed");
    }
} else {
    header("Location: ../pages/pemilik/listakun.php");
}
?> 