<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../../auth/login.php");
    exit;
}

// Database connection
require_once '../../php/connect.php';
$idPelanggan = $_SESSION['idPelanggan'];

// Fetch all active bookings with room assignments
$bookings = [];
$totalTagihan = 0;
$totalHargaKamar = 0;
$totalBiayaTambahan = 0;
$hasActiveRooms = false;

$bookingQuery = "SELECT p.*, k.* 
                FROM pemesanan p
                JOIN kamar_kos k ON p.idKamar = k.idKamar
                WHERE (p.idPelanggan = ? OR p.idPelanggan_aktif = ?)
                AND p.statusPemesanan = 'Terkonfirmasi'
                AND p.tanggal_mulai <= CURDATE()
                AND (p.tanggal_selesai >= CURDATE() OR p.tanggal_selesai = '1970-01-01')
                ORDER BY p.tanggal_mulai DESC";

$stmt = $connect->prepare($bookingQuery);
$stmt->bind_param("ii", $idPelanggan, $idPelanggan);
$stmt->execute();
$bookingResult = $stmt->get_result();

if ($bookingResult->num_rows > 0) {
    $hasActiveRooms = true;
    
    while ($booking = $bookingResult->fetch_assoc()) {
        $currentMonth = date('Y-m');
        $biayaTambahan = 0;
        
        $costsQuery = "SELECT SUM(jumlahBiaya) AS total 
                       FROM biaya_tambahan 
                       WHERE idPelanggan = ? 
                       AND Periode = ? 
                       AND statusPembayaran = 'belum_lunas'";
        $costsStmt = $connect->prepare($costsQuery);
        $costsStmt->bind_param("is", $idPelanggan, $currentMonth);
        $costsStmt->execute();
        $costsResult = $costsStmt->get_result();

        if ($costsResult->num_rows > 0) {
            $costsData = $costsResult->fetch_assoc();
            $biayaTambahan = $costsData['total'] ?? 0;
        }

        $tagihan = $booking['harga'] + $biayaTambahan;
        
        $bookings[] = [
            'idKamar' => $booking['idKamar'],
            'nomorKamar' => $booking['nomorKamar'],
            'tipeKamar' => $booking['tipeKamar'],
            'harga' => $booking['harga'],
            'gambar' => $booking['gambar'],
            'tanggal_mulai' => $booking['tanggal_mulai'],
            'tanggal_selesai' => $booking['tanggal_selesai'],
            'biaya_tambahan' => $biayaTambahan,
            'tagihan' => $tagihan
        ];
        
        $totalHargaKamar += $booking['harga'];
        $totalBiayaTambahan += $biayaTambahan;
        $totalTagihan += $tagihan;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - QosKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="icon" href="../../assets/img/QosKuIMG.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">
    <div class="d-flex min-vh-100 ms-4 me-4">
        <?php include '../../layout/pelangganNavbar.php'; ?>

        <div class="flex-grow-1">
            <?php include '../../layout/pelangganHeader.php'; ?>

            <div class="container-fluid pt-4 pb-3">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="card-title fs-6 fw-bold text-secondary">Total Tagihan</p>
                                    <?php if ($hasActiveRooms): ?>
                                        <p class="card-text fs-3 fw-bold mb-0">Rp<?= number_format($totalTagihan, 0, ',', '.') ?></p>
                                        <small class="text-muted">Jumlah semua kamar</small>
                                    <?php else: ?>
                                        <div>
                                            <i class="bi bi-exclamation-circle me-2"></i> Tidak ada tagihan
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="text-white rounded-4 d-flex align-items-center justify-content-center"
                                    style="width: 60px; height: 60px; background-color: #4FD1C5;">
                                    <i class="bi bi-receipt-cutoff fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="card-title fs-6 fw-bold text-secondary">Total Sewa Kamar</p>
                                    <?php if ($hasActiveRooms): ?>
                                        <p class="card-text fs-4 fw-bold mb-0">Rp<?= number_format($totalHargaKamar, 0, ',', '.') ?>/bulan</p>
                                        <small class="text-muted"><?= count($bookings) ?> kamar aktif</small>
                                    <?php else: ?>
                                        <div>
                                            <i class="bi bi-exclamation-circle me-2"></i> Tidak ada kamar
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="text-white rounded-4 d-flex align-items-center justify-content-center"
                                    style="width: 60px; height: 60px; background-color: #4FD1C5;">
                                    <i class="bi bi-door-closed-fill fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="card-title fs-6 fw-bold text-secondary">Total Biaya Tambahan</p>
                                    <?php if ($hasActiveRooms && $totalBiayaTambahan > 0): ?>
                                        <p class="card-text fs-4 fw-bold mb-0">Rp<?= number_format($totalBiayaTambahan, 0, ',', '.') ?></p>
                                    <?php elseif ($hasActiveRooms): ?>
                                        <p class="card-text fs-4 fw-bold mb-0">Rp0</p>
                                    <?php else: ?>
                                        <div>
                                            <i class="bi bi-exclamation-circle me-2"></i> Tidak ada
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="text-white rounded-4 d-flex align-items-center justify-content-center"
                                    style="width: 60px; height: 60px; background-color: #4FD1C5;">
                                    <i class="bi bi-plus-square-fill fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <?php if ($hasActiveRooms): ?>
                        <div class="col-12 mb-4">
                            <h4 class="fw-bold">Kamar Aktif Anda</h4>
                            <p class="text-muted">Anda memiliki <?= count($bookings) ?> kamar aktif</p>
                        </div>
                        
                        <?php foreach ($bookings as $kamar): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm border-0 rounded-4 h-100">
                                <div class="card-header rounded-4 bg-white border-0">
                                    <h5 class="fw-bold pt-3">Kamar No. <?= htmlspecialchars($kamar['nomorKamar']) ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <img src="../../assets/img/<?= htmlspecialchars($kamar['gambar']) ?>"
                                                class="img-fluid rounded-3 w-100"
                                                style="height: 200px; object-fit: cover;"
                                                alt="Kamar <?= htmlspecialchars($kamar['nomorKamar']) ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Tipe:</strong> <?= htmlspecialchars($kamar['tipeKamar']) ?></p>
                                            <p><strong>Harga:</strong> Rp<?= number_format($kamar['harga'], 0, ',', '.') ?>/bulan</p>
                                            <p><strong>Biaya Tambahan:</strong> Rp<?= number_format($kamar['biaya_tambahan'], 0, ',', '.') ?></p>
                                            <p><strong>Total Tagihan:</strong> Rp<?= number_format($kamar['tagihan'], 0, ',', '.') ?></p>
                                            <p><strong>Periode:</strong>
                                                <?= date('d M Y', strtotime($kamar['tanggal_mulai'])) ?> -
                                                <?= date('d M Y', strtotime($kamar['tanggal_selesai'])) ?>
                                            </p>
                                            <div class="d-flex gap-2 mt-3">
                                                <a href="detailkamar.php?idKamar=<?= $kamar['idKamar'] ?>"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="bi bi-eye-fill"></i> Detail
                                                </a>
                                                <a href="pembayaran.php?idKamar=<?= $kamar['idKamar'] ?>"
                                                    class="btn btn-success btn-sm">
                                                    <i class="bi bi-credit-card-fill"></i> Bayar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                    <?php else: ?>
                        <div class="col-12">
                            <div class="card shadow-sm border-0 rounded-4 py-5 text-center">
                                <div class="mb-3">
                                    <i class="bi bi-house-exclamation-fill fs-1 text-muted"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Anda belum memiliki kamar aktif</h5>
                                <p class="text-muted mb-4">Silakan selesaikan pembayaran atau pesan kamar baru</p>
                                <div class="d-flex justify-content-center gap-3">
                                    <a href="listkamar.php" class="btn btn-primary px-4">
                                        <i class="bi bi-plus-circle me-2"></i> Pesan Kamar
                                    </a>
                                    <a href="riwayat.php" class="btn btn-outline-secondary px-4">
                                        <i class="bi bi-clock-history me-2"></i> Riwayat
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="footer text-center mt-5 pt-5">
        &copy; 2025, Made with ❤️ for QosKu
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>