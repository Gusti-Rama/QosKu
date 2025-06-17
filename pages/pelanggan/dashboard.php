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

// Fetch active booking with room assignment
$booking = [];
$kamar = [];
$tagihan = 0;
$hargaKamar = 0;
$biayaTambahan = 0;
$roomAssigned = false;

$bookingQuery = "SELECT p.*, k.* 
                FROM pemesanan p
                JOIN kamar_kos k ON p.idKamar = k.idKamar
                WHERE (p.idPelanggan = ? OR p.idPelanggan_aktif = ?)
                AND p.statusPemesanan = 'Terkonfirmasi'
                AND p.tanggal_mulai <= CURDATE()
                AND (p.tanggal_selesai >= CURDATE() OR p.tanggal_selesai = '1970-01-01')
                LIMIT 1";

$stmt = $connect->prepare($bookingQuery);
$stmt->bind_param("ii", $idPelanggan, $idPelanggan);
$stmt->execute();
$bookingResult = $stmt->get_result();

if ($bookingResult->num_rows > 0) {
    $booking = $bookingResult->fetch_assoc();
    $roomAssigned = true;

    $kamar = [
        'idKamar' => $booking['idKamar'],
        'nomorKamar' => $booking['nomorKamar'],
        'tipeKamar' => $booking['tipeKamar'],
        'harga' => $booking['harga'],
        'gambar' => $booking['gambar'],
        'tanggal_mulai' => $booking['tanggal_mulai'],
        'tanggal_selesai' => $booking['tanggal_selesai']
    ];

    $hargaKamar = $booking['harga'];

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

    if ($costsResult->num_rows > 0) {
        $costsData = $costsResult->fetch_assoc();
        $biayaTambahan = $costsData['total'] ?? 0;
    }

    $tagihan = $hargaKamar + $biayaTambahan;
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
                                    <p class="card-title fs-6 fw-bold text-secondary">Tagihan Anda</p>
                                    <?php if ($roomAssigned): ?>
                                        <p class="card-text fs-3 fw-bold mb-0">Rp<?= number_format($tagihan, 0, ',', '.') ?></p>
                                        <small class="text-muted">Jatuh tempo: <?= date('d M Y', strtotime($kamar['tanggal_selesai'])) ?></small>
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
                                    <p class="card-title fs-6 fw-bold text-secondary">Harga Kamar</p>
                                    <?php if ($roomAssigned): ?>
                                        <p class="card-text fs-4 fw-bold mb-0">Rp<?= number_format($hargaKamar, 0, ',', '.') ?>/bulan</p>
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
                                    <p class="card-title fs-6 fw-bold text-secondary">Biaya Tambahan</p>
                                    <?php if ($roomAssigned && $biayaTambahan > 0): ?>
                                        <p class="card-text fs-4 fw-bold mb-0">Rp<?= number_format($biayaTambahan, 0, ',', '.') ?></p>
                                    <?php elseif ($roomAssigned): ?>
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
                    <?php if ($roomAssigned): ?>
                        <div class="col-md-12">
                            <div class="card shadow-sm border-0 rounded-4 h-100">
                                <div class="card-header rounded-4 bg-white border-0">
                                    <h5 class="fw-bold pt-3">Detail Kamar</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <img src="../../assets/img/<?= htmlspecialchars($kamar['gambar']) ?>"
                                                class="img-fluid rounded-3 w-100"
                                                style="height: 300px; object-fit: cover;"
                                                alt="Kamar <?= htmlspecialchars($kamar['nomorKamar']) ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <h5 class="fw-bold">Kamar No. <?= htmlspecialchars($kamar['nomorKamar']) ?></h5>
                                            <p><strong>Tipe:</strong> <?= htmlspecialchars($kamar['tipeKamar']) ?></p>
                                            <p><strong>Periode:</strong>
                                                <?= date('d M Y', strtotime($kamar['tanggal_mulai'])) ?> -
                                                <?= date('d M Y', strtotime($kamar['tanggal_selesai'])) ?>
                                            </p>
                                            <p><strong>Status:</strong> <span class="badge bg-success">Aktif</span></p>
                                            <a href="detailkamar.php?idKamar=<?= $kamar['idKamar'] ?>"
                                                class="btn btn-primary mt-2">
                                                <i class="bi bi-eye-fill"></i> Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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