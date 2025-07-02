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
$kamar = $result->fetch_assoc();

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

// Get room details and facilities
$query = "SELECT k.*, f.luasKamar, f.perabotan, f.kamarMandi 
          FROM kamar_kos k
          LEFT JOIN fasilitas f ON k.idKamar = f.idKamar
          WHERE k.idKamar = ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("i", $idKamar);
$stmt->execute();
$result = $stmt->get_result();
$kamar = $result->fetch_assoc();

// Fetch additional images
$imagesQuery = "SELECT image_path FROM kamar_images WHERE idKamar = ?";
$imagesStmt = $connect->prepare($imagesQuery);
$imagesStmt->bind_param("i", $idKamar);
$imagesStmt->execute();
$imagesResult = $imagesStmt->get_result();
$additionalImages = $imagesResult->fetch_all(MYSQLI_ASSOC);

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
$totalAmount = $kamar['harga'] + $additionalCosts;

// Get current booking information
$bookingInfoQuery = "SELECT tanggal_mulai, tanggal_selesai, lamaSewa, jenis_sewa 
                    FROM pemesanan 
                    WHERE idKamar = ? 
                    AND (idPelanggan = ? OR idPelanggan_aktif = ?)
                    AND statusPemesanan = 'Terkonfirmasi'
                    LIMIT 1";
$bookingStmt = $connect->prepare($bookingInfoQuery);
$bookingStmt->bind_param("iii", $idKamar, $idPelanggan, $idPelanggan);
$bookingStmt->execute();
$bookingInfo = $bookingStmt->get_result()->fetch_assoc();
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
                            <img src="../../assets/img/<?= htmlspecialchars($kamar['gambar']) ?>" class="card-img-top rounded-top-4" alt="Kamar <?= htmlspecialchars($kamar['nomorKamar']) ?>">

                            <div class="card-body">
                                <h5 class="card-title fw-bold">Kamar No. <?= htmlspecialchars($kamar['nomorKamar']) ?></h5>
                                <p class="card-text">
                                    <strong>Tipe:</strong> <?= htmlspecialchars($kamar['tipeKamar']) ?><br>
                                    <strong>Harga:</strong> Rp <?= number_format($kamar['harga'], 0, ',', '.') ?><br>
                                    <strong>Status:</strong> <?= htmlspecialchars($kamar['statusKetersediaan']) ?><br>
                                    <strong>Deskripsi:</strong><br> <?= nl2br(htmlspecialchars($kamar['deskripsi'])) ?>
                                <h6 class="fw-bold mt-4">Fasilitas Kamar</h6>
                                <ul>
                                    <?php if (!empty($kamar['luasKamar'])): ?>
                                        <li>Luas: <?= htmlspecialchars($kamar['luasKamar']) ?></li>
                                    <?php endif; ?>

                                    <?php if (!empty($kamar['perabotan'])): ?>
                                        <li>Perabotan: <?= htmlspecialchars($kamar['perabotan']) ?></li>
                                    <?php endif; ?>

                                    <?php if (!empty($kamar['kamarMandi'])): ?>
                                        <li>Kamar mandi: <?= htmlspecialchars($kamar['kamarMandi']) ?></li>
                                    <?php endif; ?>
                                </ul>
                                </p>
                            </div>

                        </div>

                        <?php if (!empty($additionalImages)): ?>
                            <h6 class="fw-bold mt-4">Foto Lainnya</h6>
                            <div class="d-flex gap-3 flex-wrap">
                                <?php foreach ($additionalImages as $image): ?>
                                    <img src="../../assets/img/<?= htmlspecialchars($image['image_path']) ?>"
                                        class="img-thumbnail rounded-3"
                                        style="width: 100px; height: 80px; object-fit: cover;"
                                        alt="Kamar <?= htmlspecialchars($kamar['nomorKamar']) ?>">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pembayaran -->
                    <div class="col-md-4">
                        <!-- Current Booking Info -->
                        <?php if ($bookingInfo): ?>
                            <div class="card shadow-sm border-0 rounded-4 p-4 mb-3">
                                <h6 class="fw-bold">Informasi Sewa Saat Ini</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">Mulai Sewa</small>
                                        <p class="mb-1 fw-bold"><?= date('d M Y', strtotime($bookingInfo['tanggal_mulai'])) ?></p>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Berakhir</small>
                                        <p class="mb-1 fw-bold"><?= date('d M Y', strtotime($bookingInfo['tanggal_selesai'])) ?></p>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">Jenis Sewa</small>
                                    <p class="mb-0 fw-bold"><?= ucfirst($bookingInfo['jenis_sewa']) ?> (<?= $bookingInfo['lamaSewa'] ?> periode)</p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Extension Form -->
                        <div class="card shadow-sm border-0 rounded-4 p-4 mb-3">
                            <h6 class="fw-bold">Perpanjangan Sewa</h6>

                            <?php
                            // Calculate days remaining
                            $daysRemaining = floor((strtotime($bookingInfo['tanggal_selesai']) - time()) / (60 * 60 * 24));
                            if ($daysRemaining < 7): ?>
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    Sewa Anda akan berakhir dalam <?= $daysRemaining ?> hari.
                                </div>
                            <?php endif; ?>

                            <form action="../../php/proses_pembayaran.php" method="POST">
                                <input type="hidden" name="idKamar" value="<?= $idKamar ?>">
                                <input type="hidden" name="jenis_pembayaran" value="perpanjangan">

                                <div class="mb-3">
                                    <label class="form-label">Jenis Perpanjangan</label>
                                    <select class="form-select" name="jenis_perpanjangan" id="jenisPerpanjangan" required>
                                        <option value="bulanan">Bulanan</option>
                                        <option value="mingguan" <?= $daysRemaining < 14 ? 'selected' : '' ?>>Mingguan</option>
                                        <option value="harian" <?= $daysRemaining < 7 ? 'selected' : '' ?>>Harian</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Durasi</label>
                                    <input type="number" class="form-control" name="durasi_perpanjangan" id="durasiPerpanjangan"
                                        min="1" value="<?= $daysRemaining < 14 ? 1 : 6 ?>" required>
                                    <div class="form-text">Jumlah bulan/minggu/hari</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Metode Pembayaran</label>
                                    <select class="form-select" name="metode_pembayaran" required>
                                        <option value="cash">Uang Tunai</option>
                                        <option value="transfer">Transfer Bank</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <p class="fw-bold mb-2">Biaya Perpanjangan</p>
                                    <p>Harga per periode: <span id="hargaPerPeriode" class="float-end">Rp <?= number_format($kamar['harga'], 0, ',', '.') ?></span></p>
                                    <p>Durasi: <span id="durasiDisplay" class="float-end">1 bulan</span></p>
                                    <hr>
                                    <h6 class="fw-bold">Total <span id="totalPerpanjangan" class="float-end">Rp <?= number_format($kamar['harga'], 0, ',', '.') ?></span></h6>
                                </div>

                                <button type="submit" class="btn button-utama w-100">
                                    Ajukan Perpanjangan
                                </button>
                                <small class="text-muted mt-2 d-block">*Perpanjangan akan diverifikasi oleh pemilik dalam 1x24 jam</small>
                            </form>
                        </div>

                        <!-- Regular Payment Form -->
                        <!-- <form action="../../php/proses_pembayaran.php" method="POST">
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
                                <p>Harga Kamar <span class="float-end">Rp<?= number_format($kamar['harga'], 0, ',', '.') ?></span></p>
                                <?php if ($totalAdditional > 0): ?>
                                    <p>Biaya Tambahan <span class="float-end">Rp<?= number_format($totalAdditional, 0, ',', '.') ?></span></p>
                                <?php endif; ?>
                                <hr>
                                <h5 class="fw-bold">Total <span class="float-end">Rp<?= number_format($kamar['harga'] + $totalAdditional, 0, ',', '.') ?></span></h5>

                                <button type="submit" class="btn btn-primary w-100 mt-3" style="background-color: #4FD1C5; border: none;">
                                    Bayar
                                </button>
                            </div>
                        </form> -->
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="footer text-center mt-5 pt-5">
        &copy; 2025, Made with ❤️ for QosKu
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Extension form calculations
        const jenisPerpanjangan = document.getElementById('jenisPerpanjangan');
        const durasiPerpanjangan = document.getElementById('durasiPerpanjangan');
        const hargaPerPeriode = document.getElementById('hargaPerPeriode');
        const durasiDisplay = document.getElementById('durasiDisplay');
        const totalPerpanjangan = document.getElementById('totalPerpanjangan');

        const basePrice = <?= $kamar['harga'] ?>;

        function updateExtensionCalculation() {
            const jenis = jenisPerpanjangan.value;
            const durasi = parseInt(durasiPerpanjangan.value) || 1;

            let pricePerPeriod = basePrice;
            let periodText = '';

            // Calculate price based on period type
            switch (jenis) {
                case 'mingguan':
                    pricePerPeriod = Math.ceil(basePrice / 4); // Approximate weekly price
                    periodText = durasi + (durasi === 1 ? ' minggu' : ' minggu');
                    break;
                case 'harian':
                    pricePerPeriod = Math.ceil(basePrice / 30); // Approximate daily price
                    periodText = durasi + (durasi === 1 ? ' hari' : ' hari');
                    break;
                default: // bulanan
                    pricePerPeriod = basePrice;
                    periodText = durasi + (durasi === 1 ? ' bulan' : ' bulan');
                    break;
            }

            const total = pricePerPeriod * durasi;

            // Update display
            hargaPerPeriode.textContent = 'Rp ' + total.toLocaleString('id-ID');
            durasiDisplay.textContent = periodText;
            totalPerpanjangan.textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        // Add event listeners
        jenisPerpanjangan.addEventListener('change', updateExtensionCalculation);
        durasiPerpanjangan.addEventListener('input', updateExtensionCalculation);

        // Initialize calculation
        updateExtensionCalculation();
    </script>
</body>

</html>