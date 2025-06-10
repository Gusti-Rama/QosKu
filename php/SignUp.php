<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validasi sederhana
    if (empty($username) || empty($email) || empty($password)) {
        echo "<script>alert('Semua field wajib diisi!'); window.history.back();</script>";
        exit;
    }

    // Cek apakah email sudah terdaftar
    $stmt = $connect->prepare("SELECT * FROM pelanggan WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $cek = $stmt->get_result();
    if ($cek->num_rows > 0) {
        echo "<script>alert('Email sudah terdaftar!'); window.history.back();</script>";
        exit;
    }

    // Hash password untuk keamanan
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Data default
    $namaLengkap = "Nama Lengkap";
    $nomorHp = "-";
    $alamat = "-";

    // Insert ke database
    $stmt = $connect->prepare("INSERT INTO pelanggan (username, password, namaLengkap, nomorHp, email, alamat) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $password_hash, $namaLengkap, $nomorHp, $email, $alamat);

    if ($stmt->execute()) {
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location.href = 'signin.php';</script>";
    } else {
        echo "<script>alert('Registrasi gagal!'); window.history.back();</script>";
    }
}
?>
