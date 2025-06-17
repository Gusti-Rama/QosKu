<?php
session_start();
require_once 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['idPelanggan'])) {
    header("Location: ../pages/login.php");
    exit;
}

// Validate form submission
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request method";
    header("Location: detailkamar.php?idKamar=" . $_POST['idKamar']);
    exit;
}

// Get form data
$idKamar = intval($_POST['idKamar']);
$idPelanggan = $_SESSION['idPelanggan'];
$jenisPembayaran = $_POST['jenis_pembayaran'] ?? 'bulanan';
$metodePembayaran = $_POST['metode_pembayaran'] ?? 'cash';

// Get room price
$roomQuery = "SELECT harga FROM kamar_kos WHERE idKamar = ?";
$stmt = $connect->prepare($roomQuery);
$stmt->bind_param("i", $idKamar);
$stmt->execute();
$roomResult = $stmt->get_result();

if ($roomResult->num_rows === 0) {
    $_SESSION['error'] = "Kamar tidak ditemukan";
    header("Location: detailkamar.php?idKamar=" . $idKamar);
    exit;
}
$room = $roomResult->fetch_assoc();
$hargaKamar = $room['harga'];

// Calculate additional costs
$currentMonth = date('Y-m');
$costsQuery = "SELECT SUM(jumlahBiaya) AS total 
               FROM biaya_tambahan 
               WHERE idPelanggan = ? 
               AND Periode = ? 
               AND statusPembayaran = 'belum_lunas'";
$costsStmt = $connect->prepare($costsQuery);
$costsStmt->bind_param("is", $idPelanggan, $currentMonth);
$costsStmt->execute();
$costsResult = $costsStmt->get_result();
$biayaTambahan = $costsResult->fetch_assoc()['total'] ?? 0;

$totalHarga = $hargaKamar + $biayaTambahan;

// Start transaction
$connect->begin_transaction();

try {
    // 1. Insert payment record
    $insertPayment = "INSERT INTO pembayaran (
                        tanggalPembayaran, 
                        metodePembayaran, 
                        jumlahPembayaran, 
                        statusPembayaran, 
                        idPemesanan
                    ) VALUES (
                        CURDATE(), 
                        ?, 
                        ?, 
                        'Lunas', 
                        (SELECT idPemesanan FROM pemesanan 
                         WHERE idKamar = ? 
                         AND (idPelanggan = ? OR idPelanggan_aktif = ?)
                         AND statusPemesanan = 'Terkonfirmasi'
                         LIMIT 1)
                    )";
    
    $stmt = $connect->prepare($insertPayment);
    $stmt->bind_param("siiii", $metodePembayaran, $totalHarga, $idKamar, $idPelanggan, $idPelanggan);
    $stmt->execute();

    // 2. Update additional costs status
    $updateCosts = "UPDATE biaya_tambahan 
                   SET statusPembayaran = 'lunas'
                   WHERE idPelanggan = ?
                   AND Periode = ?
                   AND statusPembayaran = 'belum_lunas'";
    
    $stmt = $connect->prepare($updateCosts);
    $stmt->bind_param("is", $idPelanggan, $currentMonth);
    $stmt->execute();

    // 3. Update booking if paying for extension
    if ($jenisPembayaran === 'perpanjangan') {
        $updateBooking = "UPDATE pemesanan 
                         SET tanggal_selesai = DATE_ADD(tanggal_selesai, INTERVAL 1 MONTH)
                         WHERE idKamar = ?
                         AND (idPelanggan = ? OR idPelanggan_aktif = ?)
                         AND statusPemesanan = 'Terkonfirmasi'";
        
        $stmt = $connect->prepare($updateBooking);
        $stmt->bind_param("iii", $idKamar, $idPelanggan, $idPelanggan);
        $stmt->execute();
    }

    // Commit transaction
    $connect->commit();

    $_SESSION['success'] = "Pembayaran berhasil diproses!";
    header("Location: ../../pages/pelanggan/detailkamar.php?idKamar=" . $idKamar . "&payment_success=1");
    exit;

} catch (Exception $e) {
    // Rollback transaction on error
    $connect->rollback();
    $_SESSION['error'] = "Gagal memproses pembayaran: " . $e->getMessage();
    header("Location: ../../pages/pelanggan/detailkamar.php?idKamar=" . $idKamar);
    exit;
}
?>