<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['idPelanggan'])) {
    header("Location: ../pages/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPelanggan = $_SESSION['idPelanggan'];
    $namaLengkap = trim($_POST['namaLengkap']);
    $nomorHp = trim($_POST['nomorHp']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $alamat = trim($_POST['alamat']);
    
    // Handle profile picture upload
    $profilePicture = null;
    if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/img/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . basename($_FILES['profilePicture']['name']);
        $targetFile = $uploadDir . $fileName;
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES['profilePicture']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['profilePicture']['tmp_name'], $targetFile)) {
                $profilePicture = $fileName;
            }
        }
    }
    
    // Update profile in database
    $query = "UPDATE pelanggan SET 
              namaLengkap = ?, 
              nomorHp = ?, 
              email = ?, 
              alamat = ?,
              profilePicture = ?";
    
    $params = [$namaLengkap, $nomorHp, $email, $alamat, $profilePicture];
    $types = "sssss";
    
    if ($profilePicture) {
        $query .= ", profilePicture = ?";
        $params[] = $profilePicture;
        $types .= "s";
    }
    
    $query .= " WHERE idPelanggan = ?";
    $params[] = $idPelanggan;
    $types .= "i";
    
    $stmt = $connect->prepare($query);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Profil berhasil diperbarui!";
    } else {
        $_SESSION['error'] = "Gagal memperbarui profil: " . $stmt->error;
    }
    
    header("Location: ../pages/pelanggan/profil.php");
    exit;
}
?> 