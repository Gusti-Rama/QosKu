<?php
session_start();
require "../../php/connect.php";

if (!isset($_SESSION['username'])) {
    header("Location: ../../pages/login.php?pesan=not_logged_in");
    exit;
}

// Verify pelanggan exists and get their ID
$username = $_SESSION['username'];
$stmt = $connect->prepare("SELECT idPelanggan FROM pelanggan WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // If no pelanggan found, redirect to login
    session_destroy();
    header("Location: ../../auth/login.php");
    exit;
}

$pelanggan = $result->fetch_assoc();
$_SESSION['idPelanggan'] = $pelanggan['idPelanggan'];

if (!isset($_GET['idKamar'])) {
    die("ID Kamar tidak ditemukan.");
}

$idKamar = intval($_GET['idKamar']);

// Fetch room data
$roomQuery = "SELECT * FROM kamar_kos WHERE idKamar = $idKamar";
$roomResult = $connect->query($roomQuery);

if (!$roomResult || $roomResult->num_rows === 0) {
    die("Data kamar tidak ditemukan.");
}

$room = $roomResult->fetch_assoc();

// Calculate base prices (you can adjust these factors as needed)
$dailyPrice = ceil($room['harga'] / 30); // ~daily rate
$weeklyPrice = ceil($room['harga'] / 4); // ~weekly rate
$monthlyPrice = $room['harga']; // monthly rate
// Fetch facilities
$facilitiesQuery = "SELECT * FROM fasilitas WHERE idKamar = $idKamar";
$facilitiesResult = $connect->query($facilitiesQuery);
$facilities = $facilitiesResult->fetch_assoc();

// Fetch additional images
$imagesQuery = "SELECT * FROM kamar_images WHERE idKamar = $idKamar";
$imagesResult = $connect->query($imagesQuery);
$additionalImages = [];
while ($row = $imagesResult->fetch_assoc()) {
    $additionalImages[] = $row;
}

// Handle payment form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_payment'])) {
    $paymentMethod = $_POST['payment_method'];
    $paymentType = $_POST['payment_type'];
    $duration = intval($_POST['duration']);

    // Calculate total price based on type and duration
    switch ($paymentType) {
        case 'daily':
            $pricePerPeriod = $dailyPrice;
            $jenisSewa = 'harian';
            break;
        case 'weekly':
            $pricePerPeriod = $weeklyPrice;
            $jenisSewa = 'mingguan';
            break;
        case 'monthly':
            $pricePerPeriod = $monthlyPrice;
            $jenisSewa = 'bulanan';
            break;
    }

    $totalAmount = $pricePerPeriod * $duration;

    // Insert into pemesanan using prepared statement
    $orderQuery = "INSERT INTO pemesanan (
        tanggalPemesanan, 
        lamaSewa, 
        jenis_sewa, 
        harga_per_periode,
        totalHarga, 
        statusPemesanan, 
        idPelanggan, 
        idKamar
    ) VALUES (
        NOW(), 
        ?, 
        ?, 
        ?,
        ?, 
        'Tertunda', 
        ?, 
        ?
    )";

    $stmt = $connect->prepare($orderQuery);
    if (!$stmt) {
        die("Prepare failed: " . $connect->error);
    }

    $stmt->bind_param(
        "isdiis",
        $duration,
        $jenisSewa,
        $pricePerPeriod,
        $totalAmount,
        $pelanggan['idPelanggan'],
        $idKamar
    );

    if (!$stmt->execute()) {
        die("Error executing order: " . $stmt->error);
    }

    $orderId = $connect->insert_id;

    // Handle payment
    $paymentProof = null;
    if ($paymentMethod === 'transfer' && isset($_FILES['payment_proof'])) {
        $uploadDir = '../../assets/payment_proofs/';
        $fileName = uniqid() . '_' . basename($_FILES['payment_proof']['name']);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $targetFile)) {
            $paymentProof = $fileName;
        }
    }

    // Insert into pembayaran
    $paymentQuery = "INSERT INTO pembayaran (
        tanggalPembayaran, 
        metodePembayaran, 
        jumlahPembayaran, 
        statusPembayaran, 
        idPemesanan, 
        bukti_transfer
    ) VALUES (
        NOW(), 
        '$paymentMethod', 
        $totalAmount, 
        'Menunggu Konfirmasi', 
        $orderId, 
        " . ($paymentProof ? "'$paymentProof'" : "NULL") . "
    )";

    $connect->query($paymentQuery);

    header("Location: dashboard.php?payment_success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kamar - QosKu</title>
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

            <div class="container-fluid pt-4">
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card shadow-sm border-0 rounded-4">
                            <img src="../../assets/img/<?= htmlspecialchars($room['gambar']) ?>" class="card-img-top rounded-top-4" alt="Kamar <?= htmlspecialchars($room['nomorKamar']) ?>">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">Kamar No. <?= htmlspecialchars($room['nomorKamar']) ?></h5>
                                <p class="card-text">
                                    <strong>Tipe:</strong> <?= htmlspecialchars($room['tipeKamar']) ?><br>
                                    <strong>Harga:</strong> Rp <?= number_format($room['harga'], 0, ',', '.') ?>/bulan<br>
                                    <strong>Status:</strong> <?= htmlspecialchars($room['statusKetersediaan']) ?><br>
                                    <strong>Deskripsi:</strong><br> <?= nl2br(htmlspecialchars($room['deskripsi'])) ?>
                                </p>

                                <?php if ($facilities): ?>
                                    <h6 class="fw-bold mt-4">Fasilitas Kamar</h6>
                                    <ul>
                                        <li><strong>Luas Kamar:</strong> <?= htmlspecialchars($facilities['luasKamar']) ?></li>
                                        <li><strong>Perabotan:</strong> <?= htmlspecialchars($facilities['perabotan']) ?></li>
                                        <li><strong>Kamar Mandi:</strong> <?= htmlspecialchars($facilities['kamarMandi']) ?></li>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Additional Images -->
                        <?php if ($additionalImages): ?>
                            <h6 class="fw-bold mt-4">Foto Lainnya</h6>
                            <div class="d-flex gap-3 flex-wrap">
                                <?php foreach ($additionalImages as $image): ?>
                                    <img src="../../assets/img/<?= htmlspecialchars($image['image_path']) ?>"
                                        class="img-thumbnail rounded-3 cursor-pointer"
                                        style="width: 100px; height: 80px; object-fit: cover;"
                                        alt="Kamar <?= htmlspecialchars($kamar['nomorKamar']) ?>"
                                        onclick="showImagePreview('../../assets/img/<?= htmlspecialchars($image['image_path']) ?>')">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 rounded-4 p-4 h-100 d-flex flex-column">
                            <h6 class="fw-bold">Bayar Tagihan</h6>
                            <form id="paymentForm" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label">Jenis Pembayaran</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_type" id="monthly" value="monthly" checked>
                                        <label class="form-check-label" for="monthly">Bulanan (Rp <?= number_format($monthlyPrice, 0, ',', '.') ?>/bulan)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_type" id="weekly" value="weekly">
                                        <label class="form-check-label" for="weekly">Mingguan (Rp <?= number_format($weeklyPrice, 0, ',', '.') ?>/minggu)</label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="payment_type" id="daily" value="daily">
                                        <label class="form-check-label" for="daily">Harian (Rp <?= number_format($dailyPrice, 0, ',', '.') ?>/hari)</label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="duration" class="form-label">Durasi</label>
                                    <input type="number" class="form-control" id="duration" name="duration" min="1" value="1">
                                    <div class="form-text">Masukkan jumlah bulan/minggu/hari</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Metode Pembayaran</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash" checked>
                                        <label class="form-check-label" for="cash">Uang Tunai</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="transfer" value="transfer">
                                        <label class="form-check-label" for="transfer">Transfer Bank</label>
                                    </div>
                                </div>

                                <div id="transferProofContainer" class="mt-3 d-none">
                                    <label for="payment_proof" class="form-label">Upload Bukti Transfer</label>
                                    <input type="file" class="form-control" id="payment_proof" name="payment_proof" accept="image/*">
                                </div>

                                <hr>
                                <p>Harga per periode: <span id="pricePerPeriod" class="float-end">Rp <?= number_format($monthlyPrice, 0, ',', '.') ?></span></p>
                                <p>Durasi: <span id="durationDisplay" class="float-end">1 bulan</span></p>
                                <hr>
                                <h5 class="fw-bold">Total <span id="totalAmount" class="float-end">Rp <?= number_format($monthlyPrice, 0, ',', '.') ?></span></h5>

                                <button type="submit" name="submit_payment" class="btn btn-primary w-100 mt-3" style="background-color: #4FD1C5; border: none;">Bayar</button>
                                <p class="mt-auto small text-muted">*Pembayaran diatas hanya untuk bulan, minggu, atau hari pertama. Tagihan anda berikutnya dapat dilakukan di menu Kamar Anda.</p>
                                <p class="mt-auto small text-muted">*Pembayaran akan diverifikasi dalam 1x24 jam.</p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Success Modal -->
    <div class="modal fade" id="paymentSuccessModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <i class="bi bi-check-circle-fill text-success fs-1"></i>
                    <h5 class="mt-3">Pembayaran Berhasil!</h5>
                    <p>Pembayaran Anda sedang diproses. Kami akan mengirimkan konfirmasi via email.</p>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid rounded-3" alt="Preview">
                </div>
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <div class="footer text-center mt-5 pt-5">
        &copy; 2025, Made with ❤️ for QosKu
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Price calculation and display
        const prices = {
            monthly: <?= $monthlyPrice ?>,
            weekly: <?= $weeklyPrice ?>,
            daily: <?= $dailyPrice ?>
        };

        const periodLabels = {
            monthly: 'bulan',
            weekly: 'minggu',
            daily: 'hari'
        };

        function calculateTotal() {
            const paymentType = document.querySelector('input[name="payment_type"]:checked').value;
            const duration = parseInt(document.getElementById('duration').value) || 1;
            const pricePerPeriod = prices[paymentType];
            const total = pricePerPeriod * duration;

            document.getElementById('pricePerPeriod').textContent = 'Rp ' + pricePerPeriod.toLocaleString('id-ID');
            document.getElementById('durationDisplay').textContent = duration + ' ' + periodLabels[paymentType];
            document.getElementById('totalAmount').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        // Event listeners
        document.querySelectorAll('input[name="payment_type"]').forEach(radio => {
            radio.addEventListener('change', calculateTotal);
        });

        document.getElementById('duration').addEventListener('input', calculateTotal);

        // Transfer proof handling (same as before)
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const transferProofContainer = document.getElementById('transferProofContainer');
                transferProofContainer.classList.toggle('d-none', this.value !== 'transfer');
            });
        });

        // Initialize calculation
        calculateTotal();
        // Image preview function
        function showImagePreview(imageSrc) {
            const modal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
            document.getElementById('modalImage').src = imageSrc;
            modal.show();
        }
    </script>
</body>

</html>