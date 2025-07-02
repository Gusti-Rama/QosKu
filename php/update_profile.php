<?php
session_start();
require_once "connect.php";

if (!isset($_SESSION['username']) || !isset($_SESSION['idPelanggan'])) {
    header("Location: ../pages/login.php?pesan=not_logged_in");
    exit;
}

// Get current profile data
$stmt = $connect->prepare("SELECT profilePicture FROM pelanggan WHERE idPelanggan = ?");
$stmt->bind_param("i", $_SESSION['idPelanggan']);
$stmt->execute();
$result = $stmt->get_result();
$currentData = $result->fetch_assoc();

// Handle file upload only if a new file was provided
$profilePicture = $currentData['profilePicture']; // Keep current picture by default

if (!empty($_FILES['profilePicture']['name'])) {
    $targetDir = "../assets/img/";
    $fileName = basename($_FILES['profilePicture']['name']);
    $targetFile = $targetDir . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Check if image file is actual image
    $check = getimagesize($_FILES['profilePicture']['tmp_name']);
    if ($check === false) {
        $_SESSION['error'] = "File bukan gambar.";
        header("Location: ../pages/pelanggan/profil.php");
        exit;
    }
    
    // Check file size (max 2MB)
    if ($_FILES['profilePicture']['size'] > 3000000) {
        $_SESSION['error'] = "Ukuran file terlalu besar (maks 3MB).";
        header("Location: ../pages/pelanggan/profil.php");
        exit;
    }
    
    // Allow certain file formats
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowedTypes)) {
        $_SESSION['error'] = "Hanya format JPG, JPEG, PNG & GIF yang diperbolehkan.";
        header("Location: ../pages/pelanggan/profil.php");
        exit;
    }
    
    // Generate unique filename to prevent overwriting
    $newFileName = "profile_" . $_SESSION['idPelanggan'] . "_" . time() . "." . $imageFileType;
    $targetFile = $targetDir . $newFileName;
    
    // Try to upload file
    if (move_uploaded_file($_FILES['profilePicture']['tmp_name'], $targetFile)) {
        $profilePicture = $newFileName;
        
        // Delete old profile picture if it exists and isn't the default
        if (!empty($currentData['profilePicture']) && $currentData['profilePicture'] !== 'profilepic.png') {
            $oldFile = $targetDir . $currentData['profilePicture'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
    } else {
        $_SESSION['error'] = "Gagal mengunggah gambar.";
        header("Location: ../pages/pelanggan/profil.php");
        exit;
    }
}

// Update other profile information
$stmt = $connect->prepare("
    UPDATE pelanggan 
    SET namaLengkap = ?, nomorHp = ?, email = ?, alamat = ?, profilePicture = ?
    WHERE idPelanggan = ?
");
$stmt->bind_param(
    "sssssi",
    $_POST['namaLengkap'],
    $_POST['nomorHp'],
    $_POST['email'],
    $_POST['alamat'],
    $profilePicture,
    $_SESSION['idPelanggan']
);

if ($stmt->execute()) {
    $_SESSION['success'] = "Profil berhasil diperbarui";
} else {
    $_SESSION['error'] = "Gagal memperbarui profil: " . $stmt->error;
}

header("Location: ../pages/pelanggan/profil.php");
exit;
?>