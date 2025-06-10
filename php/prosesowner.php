<?php
// Database connection
include 'connect.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new room
    if (isset($_POST['add_room'])) {
        $nomorKamar = $_POST['nomorKamar'];
        $tipeKamar = $_POST['tipeKamar'];
        $harga = $_POST['harga'];
        $deskripsi = $_POST['deskripsi'];

        // Handle file upload
        $gambar = '';
        if (isset($_FILES['fotoKamar']) && $_FILES['fotoKamar']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "../assets/img/";
            $fileName = uniqid() . '_' . basename($_FILES['fotoKamar']['name']);
            $targetFile = $targetDir . $fileName;

            if (move_uploaded_file($_FILES['fotoKamar']['tmp_name'], $targetFile)) {
                $gambar = $fileName;
            }
        }

        $stmt = $connect->prepare("INSERT INTO kamar_kos (nomorKamar, tipeKamar, harga, statusKetersediaan, deskripsi, gambar) VALUES (?, ?, ?, 'Tersedia', ?, ?)");
        $stmt->bind_param("ssdss", $nomorKamar, $tipeKamar, $harga, $deskripsi, $gambar);
        $stmt->execute();

        header(header: "location: ../pages/pemilik/dashboard.php");
        exit;
    }

    // Delete room
    if (isset($_POST['delete_room'])) {
        $idKamar = $_POST['idKamar'];

        // First get image path to delete file
        $stmt = $connect->prepare("SELECT gambar FROM kamar_kos WHERE idKamar = ?");
        $stmt->bind_param("i", $idKamar);
        $stmt->execute();
        $result = $stmt->get_result();
        $room = $result->fetch_assoc();

        if ($room['gambar']) {
            $filePath = "../../assets/img/" . $room['gambar'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Then delete from database
        $stmt = $connect->prepare("DELETE FROM kamar_kos WHERE idKamar = ?");
        $stmt->bind_param("i", $idKamar);
        $stmt->execute();

        header(header: "location: ../pages/pemilik/dashboard.php");
        exit;
    }
}
