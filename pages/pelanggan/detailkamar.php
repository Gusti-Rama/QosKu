<?php
session_start();
require "../../php/connect.php";

// Check if user is logged in
if (!isset($_SESSION['idPelanggan'])) {
    header("Location: ../../auth/login.php");
    exit;
}

// Validate room ID
if (!isset($_GET['idKamar'])) {
    die("ID Kamar tidak ditemukan.");
}

$idKamar = intval($_GET['idKamar']);
$idPelanggan = $_SESSION['idPelanggan'];

// Get room details
$query = "SELECT * FROM kamar_kos WHERE idKamar = $idKamar";
$result = $connect->query($query);

if (!$result || $result->num_rows === 0) {
    die("Data kamar tidak ditemukan.");
}
$rooms = $result->fetch_assoc();

// Verify user has active booking for this room
$bookingQuery = "SELECT * FROM pemesanan 
                WHERE idKamar = $idKamar 
                AND (idPelanggan = $idPelanggan OR idPelanggan_aktif = $idPelanggan)
                AND statusPemesanan = 'Terkonfirmasi'
                LIMIT 1";
$bookingResult = $connect->query($bookingQuery);

if (!$bookingResult || $bookingResult->num_rows === 0) {
    die("Anda tidak memiliki akses ke kamar ini.");
}

$currentMonth = date('Y-m');
$additionalCostsQuery = "SELECT jenisBiaya, jumlahBiaya 
                        FROM biaya_tambahan 
                        WHERE idPelanggan = ? 
                        AND Periode = ? 
                        AND statusPembayaran = 'belum_lunas'";
$stmt = $connect->prepare($additionalCostsQuery);
$stmt->bind_param("is", $_SESSION['idPelanggan'], $currentMonth);
$stmt->execute();
$additionalCostsResult = $stmt->get_result();

$totalAdditional = 0;

// Get additional charges
$currentMonth = date('Y-m');
$costsQuery = "SELECT SUM(jumlahBiaya) AS total 
               FROM biaya_tambahan 
               WHERE idPelanggan = $idPelanggan 
               AND Periode = '$currentMonth' 
               AND statusPembayaran = 'belum_lunas'";
$costsResult = $connect->query($costsQuery);
$additionalCosts = $costsResult->fetch_assoc()['total'] ?? 0;
$totalAmount = $rooms['harga'] + $additionalCosts;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QosKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="icon" href="../../assets/img/QosKuIMG.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <div class="d-flex min-vh-100 ms-4 me-4">
        <?php include '../../layout/pelangganNavbar.php'; ?>

        <div class="flex-grow-1">
            <?php include '../../layout/pelangganHeader.php'; ?>

            <div class="container-fluid pt-4 pb-3">

                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card shadow-sm border-0 rounded-4">
                            <img src="../../assets/img/<?= htmlspecialchars($rooms['gambar']) ?>" class="card-img-top rounded-top-4" alt="Kamar <?= htmlspecialchars($kamar['nomorKamar']) ?>">

                            <div class="card-body">
                                <h5 class="card-title fw-bold">Kamar No. <?= htmlspecialchars($rooms['nomorKamar']) ?></h5>
                                <p class="card-text">
                                    <strong>Tipe:</strong> <?= htmlspecialchars($rooms['tipeKamar']) ?><br>
                                    <strong>Harga:</strong> Rp <?= number_format($rooms['harga'], 0, ',', '.') ?><br>
                                    <strong>Status:</strong> <?= htmlspecialchars($rooms['statusKetersediaan']) ?><br>
                                    <strong>Deskripsi:</strong><br> <?= nl2br(htmlspecialchars($rooms['deskripsi'])) ?>
                                </p>
                            </div>

                        </div>

                        <h6 class="fw-bold mt-4">Foto Lainnya</h6>
                        <div class="d-flex gap-3">
                            <img src="../../assets/img/Kamar1.png" class="img-thumbnail rounded-3" style="width: 100px; height: 80px; object-fit: cover;" alt="Kamar 1">
                            <img src="../../assets/img/Kamar2.png" class="img-thumbnail rounded-3" style="width: 100px; height: 80px; object-fit: cover;" alt="Kamar 2">
                            <img src="../../assets/img/Kamar3.png" class="img-thumbnail rounded-3" style="width: 100px; height: 80px; object-fit: cover;" alt="Kamar 3">
                        </div>
                    </div>

                    <!-- Pembayaran -->
                    <div class="col-md-4">
                        <form action="../../php/proses_pembayaran.php" method="POST">
                            <input type="hidden" name="idKamar" value="<?= $idKamar ?>">
                            <div class="card shadow-sm border-0 rounded-4 p-4">
                                <h6 class="fw-bold">Bayar Tagihan</h6>

                                <div class="mb-3">
                                    <label class="form-label">Jenis Pembayaran</label>
                                    <select class="form-select" name="jenis_pembayaran" required>
                                        <option value="bulanan">Bulanan</option>
                                        <option value="mingguan">Mingguan</option>
                                        <option value="harian">Harian</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Metode Pembayaran</label>
                                    <select class="form-select" name="metode_pembayaran" required>
                                        <option value="cash">Uang Tunai</option>
                                        <option value="transfer">Transfer Bank</option>
                                    </select>
                                </div>

                                <p class="fw-bold">Biaya Tambahan</p>
                                <ul class="ps-3">
                                    <?php if ($additionalCostsResult && $additionalCostsResult->num_rows > 0): ?>
                                        <?php while ($cost = $additionalCostsResult->fetch_assoc()): ?>
                                            <?php $totalAdditional += $cost['jumlahBiaya']; ?>
                                            <li>
                                                <?= htmlspecialchars($cost['jenisBiaya']) ?>
                                                <span class="float-end">Rp<?= number_format($cost['jumlahBiaya'], 0, ',', '.') ?></span>
                                            </li>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <li>Tidak ada biaya tambahan</li>
                                    <?php endif; ?>
                                </ul>

                                <hr>
                                <p>Harga Kamar <span class="float-end">Rp<?= number_format($rooms['harga'], 0, ',', '.') ?></span></p>
                                <?php if ($totalAdditional > 0): ?>
                                    <p>Biaya Tambahan <span class="float-end">Rp<?= number_format($totalAdditional, 0, ',', '.') ?></span></p>
                                <?php endif; ?>
                                <hr>
                                <h5 class="fw-bold">Total <span class="float-end">Rp<?= number_format($rooms['harga'] + $totalAdditional, 0, ',', '.') ?></span></h5>

                                <button type="submit" class="btn btn-primary w-100 mt-3" style="background-color: #4FD1C5; border: none;">
                                    Bayar
                                </button>
                            </div>
                        </form>
                    </div>
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