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
    header("Location: ../pages/pelanggan/detailkamar.php?idKamar=" . $_POST['idKamar']);
    exit;
}

// Get form data
$idKamar = intval($_POST['idKamar']);
$idPelanggan = $_SESSION['idPelanggan'];
$jenisPembayaran = $_POST['jenis_pembayaran'] ?? 'bulanan';
$metodePembayaran = $_POST['metode_pembayaran'] ?? 'cash';

// Get extension data if this is an extension payment
$jenisPerpanjangan = $_POST['jenis_perpanjangan'] ?? null;
$durasiPerpanjangan = intval($_POST['durasi_perpanjangan'] ?? 1);

// Get room price
$roomQuery = "SELECT harga FROM kamar_kos WHERE idKamar = ?";
$stmt = $connect->prepare($roomQuery);
$stmt->bind_param("i", $idKamar);
$stmt->execute();
$roomResult = $stmt->get_result();

if ($roomResult->num_rows === 0) {
    $_SESSION['error'] = "Kamar tidak ditemukan";
    header("Location: ../pages/pelanggan/detailkamar.php?idKamar=" . $idKamar);
    exit;
}
$room = $roomResult->fetch_assoc();
$hargaKamar = $room['harga'];

// Calculate payment amount based on type
$currentMonth = date('Y-m'); // Define this for both cases

if ($jenisPembayaran === 'perpanjangan') {
    // Calculate extension price
    switch ($jenisPerpanjangan) {
        case 'mingguan':
            $hargaPerPeriode = ceil($hargaKamar / 4); // Approximate weekly price
            break;
        case 'harian':
            $hargaPerPeriode = ceil($hargaKamar / 30); // Approximate daily price
            break;
        default: // bulanan
            $hargaPerPeriode = $hargaKamar;
            break;
    }
    $totalHarga = $hargaPerPeriode * $durasiPerpanjangan;
} else {
    // Regular payment - calculate additional costs
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
}

// Start transaction
$connect->begin_transaction();

try {
    if ($jenisPembayaran === 'perpanjangan') {
        // Get current active booking to determine the new start date
        $currentBookingQuery = "SELECT * FROM pemesanan 
                              WHERE idKamar = ? 
                              AND (idPelanggan = ? OR idPelanggan_aktif = ?)
                              AND statusPemesanan = 'Terkonfirmasi'
                              AND is_active = 1
                              LIMIT 1";
        $stmt = $connect->prepare($currentBookingQuery);
        $stmt->bind_param("iii", $idKamar, $idPelanggan, $idPelanggan);
        $stmt->execute();
        $currentBookingResult = $stmt->get_result();
        
        if ($currentBookingResult->num_rows === 0) {
            throw new Exception("Tidak ada pemesanan aktif untuk diperpanjang");
        }
        
        $currentBooking = $currentBookingResult->fetch_assoc();
        $startDate = $currentBooking['tanggal_selesai']; // Start from current end date
        
        // Calculate end date based on extension type and duration
        switch ($jenisPerpanjangan) {
            case 'bulanan':
                $endDate = date('Y-m-d', strtotime($startDate . " + $durasiPerpanjangan months"));
                break;
            case 'mingguan':
                $endDate = date('Y-m-d', strtotime($startDate . " + $durasiPerpanjangan weeks"));
                break;
            case 'harian':
                $endDate = date('Y-m-d', strtotime($startDate . " + $durasiPerpanjangan days"));
                break;
        }
        
        // Create new pemesanan record for extension
        $extensionQuery = "INSERT INTO pemesanan (
            tanggalPemesanan,
            lamaSewa,
            jenis_sewa,
            harga_per_periode,
            totalHarga,
            statusPemesanan,
            idPelanggan,
            idPelanggan_aktif,
            idKamar,
            tanggal_mulai,
            tanggal_selesai,
            is_active,
            jenisPemesanan
        ) VALUES (
            CURDATE(),
            ?,
            ?,
            ?,
            ?,
            'Tertunda',
            ?,
            ?,
            ?,
            ?,
            ?,
            0,
            'perpanjang'
        )";
        
        $stmt = $connect->prepare($extensionQuery);
        $stmt->bind_param("isiiiiiss", 
            $durasiPerpanjangan,
            $jenisPerpanjangan,
            $hargaPerPeriode,
            $totalHarga,
            $idPelanggan,
            $idPelanggan,
            $idKamar,
            $startDate,
            $endDate
        );
        $stmt->execute();
        $extensionId = $connect->insert_id;
        
        // Create payment record for the extension
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
            'Menunggu Konfirmasi', 
            ?
        )";
        
        $stmt = $connect->prepare($insertPayment);
        $stmt->bind_param("sii", $metodePembayaran, $totalHarga, $extensionId);
        $stmt->execute();
        
        $_SESSION['success'] = "Permintaan perpanjangan sewa berhasil diajukan! Menunggu konfirmasi dari pemilik.";
    } else {
        // Regular payment processing
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
                             AND is_active = 1
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
        
        $_SESSION['success'] = "Pembayaran berhasil diproses!";
    }

    // Commit transaction
    $connect->commit();
    
    header("Location: ../pages/pelanggan/detailkamar.php?idKamar=" . $idKamar . "&payment_success=1");
    exit;

} catch (Exception $e) {
    // Rollback transaction on error
    $connect->rollback();
    $_SESSION['error'] = "Gagal memproses pembayaran: " . $e->getMessage();
    header("Location: ../pages/pelanggan/detailkamar.php?idKamar=" . $idKamar);
    exit;
}