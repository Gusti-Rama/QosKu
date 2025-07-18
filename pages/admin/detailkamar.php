<?php
session_start();
require "../../php/connect.php";

if (!isset($_SESSION['role'])) {
    header(header: "Location: ../../pages/login.php?pesan=not_logged_in");
    exit;
}

$peran = strtolower($_SESSION['role']);

$diperbolehkan = ['admin'];

// cek peran usernya
if (!in_array($peran, $diperbolehkan)) {
    header("Location: ../../pages/login.php?pesan=Akses_Ditolak");
    exit;
}

// Validate room ID
$idKamar = isset($_GET['idKamar']) ? (int)$_GET['idKamar'] : 0;
if (!$idKamar) {
    header("Location: dashboard.php");
    exit;
}

// Fetch room details
$stmt = $connect->prepare("
    SELECT k.*, f.luasKamar, f.perabotan, f.kamarMandi 
    FROM kamar_kos k
    LEFT JOIN fasilitas f ON k.idKamar = f.idKamar
    WHERE k.idKamar = ?
");
$stmt->bind_param("i", $idKamar);
$stmt->execute();
$kamar = $stmt->get_result()->fetch_assoc();

if (!$kamar) {
    header("Location: dashboard.php");
    exit;
}

// Fetch additional images
$imageStmt = $connect->prepare("SELECT image_path FROM kamar_images WHERE idKamar = ?");
$imageStmt->bind_param("i", $idKamar);
$imageStmt->execute();
$additionalImages = $imageStmt->get_result()->fetch_all(MYSQLI_ASSOC);

$occupantStmt = $connect->prepare("
    SELECT p.* 
    FROM pemesanan pm
    JOIN pelanggan p ON (pm.idPelanggan = p.idPelanggan OR pm.idPelanggan_aktif = p.idPelanggan)
    WHERE pm.idKamar = ? 
    AND pm.statusPemesanan = 'Terkonfirmasi'
    AND (pm.tanggal_mulai <= CURDATE() AND (pm.tanggal_selesai >= CURDATE() OR pm.tanggal_selesai = '1970-01-01'))
    LIMIT 1
");
$occupantStmt->bind_param("i", $idKamar);
$occupantStmt->execute();
$occupant = $occupantStmt->get_result()->fetch_assoc();

$additionalCosts = [];
if ($occupant) {
    $costsStmt = $connect->prepare("
        SELECT * FROM biaya_tambahan 
        WHERE idPelanggan = ? AND statusPembayaran = 'belum_lunas'
    ");
    $costsStmt->bind_param("i", $occupant['idPelanggan']);
    $costsStmt->execute();
    $additionalCosts = $costsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Calculate total costs
$roomPrice = $kamar['harga'] ?? 0;
$additionalCostsTotal = 0;
foreach ($additionalCosts as $cost) {
    $additionalCostsTotal += $cost['jumlahBiaya'];
}
$totalCost = $roomPrice + $additionalCostsTotal;
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
        <?php include '../../layout/adminNavbar.php'; ?>

        <div class="flex-grow-1">
            <?php include '../../layout/adminHeader.php'; ?>

            <!-- Kamar Anda -->
            <div class="container-fluid pt-4">
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card shadow-sm border-0 rounded-4 position-relative">
                            <!-- Main Image -->
                            <img src="../../assets/img/<?= htmlspecialchars($kamar['gambar'] ?? 'room-placeholder.jpg') ?>"
                                class="card-img-top rounded-top-4"
                                alt="Kamar <?= htmlspecialchars($kamar['nomorKamar']) ?>"
                                style="height: 400px; object-fit: cover;">

                            <div class="card-body pb-5">
                                <h5 class="card-title fw-bold">Kamar No. <?= htmlspecialchars($kamar['nomorKamar']) ?></h5>
                                <p class="card-text">Harga: Rp<?= number_format($kamar['harga'], 0, ',', '.') ?> / bulan</p>
                                <p class="text-muted"><?= nl2br(htmlspecialchars($kamar['deskripsi'])) ?></p>

                                <!-- Facilities -->
                                <h6 class="fw-bold mt-4">Fasilitas Kamar</h6>
                                <ul>
                                    <?php if ($kamar['luasKamar']): ?>
                                        <li>Luas: <?= htmlspecialchars($kamar['luasKamar']) ?></li>
                                    <?php endif; ?>

                                    <?php if ($kamar['perabotan']): ?>
                                        <li>Perabotan: <?= htmlspecialchars($kamar['perabotan']) ?></li>
                                    <?php endif; ?>

                                    <?php if ($kamar['kamarMandi']): ?>
                                        <li>Kamar mandi: <?= htmlspecialchars($kamar['kamarMandi']) ?></li>
                                    <?php endif; ?>
                                </ul>
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

                    <!-- Occupant and Payment Info -->
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 rounded-4 p-4">
                            <?php if ($occupant): ?>
                                <div class="text-center mb-4">
                                    <?php if (!empty($occupant['profilePicture'])): ?>
                                        <img src="../../assets/img/<?= htmlspecialchars($occupant['profilePicture']) ?>"
                                            class="rounded-circle mb-2"
                                            style="width: 100px; height: 100px; object-fit: cover;"
                                            alt="Foto <?= htmlspecialchars($occupant['namaLengkap']) ?>">
                                    <?php else: ?>
                                        <div class="d-inline-block bg-secondary rounded-circle mb-2"
                                            style="width: 100px; height: 100px; line-height: 100px;">
                                            <span class="text-white fs-1"><?= substr($occupant['namaLengkap'], 0, 1) ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($occupant['namaLengkap']) ?></h5>
                                    <p class="text-muted mb-1"><?= htmlspecialchars($occupant['email']) ?></p>
                                    <p class="text-muted"><?= htmlspecialchars($occupant['nomorHp']) ?></p>
                                </div>
                            <?php else: ?>
                                <div class="text-center mb-4">
                                    <p class="text-muted">Belum ada penghuni saat ini</p>
                                </div>
                            <?php endif; ?>

                            <div id="appliancesList">
                                <h6 class="fw-bold mb-3">Biaya Tambahan</h6>

                                <?php if ($additionalCosts): ?>
                                    <ul class="list-group mb-3">
                                        <?php foreach ($additionalCosts as $cost): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <?= htmlspecialchars($cost['namaBiaya']) ?>
                                                (<?= htmlspecialchars($cost['jenisBiaya']) ?>)
                                                <span>Rp<?= number_format($cost['jumlahBiaya'], 0, ',', '.') ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted mb-3">Belum ada biaya tambahan</p>
                                <?php endif; ?>

                                <?php if ($occupant): ?>
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button id="addButton" class="btn btn-primary me-md-2"
                                            style="background-color: #4FD1C5; border: none;">
                                            <i class="bi bi-plus"></i> Tambah
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Add Form (hidden by default) -->
                            <div id="addForm" class="mb-3" style="display: none;">
                                <h6 class="fw-bold mb-3">Tambah Biaya Tambahan</h6>
                                <form id="addCostForm" method="POST" action="../../php/prosesadmin.php">
                                    <div class="mb-3">
                                        <label for="namabiaya" class="form-label">Nama Biaya</label>
                                        <input type="text" class="form-control" id="namabiaya" name="namabiaya" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="jumlahbiaya" class="form-label">Harga</label>
                                        <input type="number" class="form-control" id="jumlahbiaya" name="jumlahbiaya" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="jenisbiaya" class="form-label">Jenis Biaya</label>
                                        <select class="form-select" name="jenisbiaya" aria-label="Jenis Biaya">
                                            <option value="Listrik">Listrik</option>
                                            <option value="Air">Air</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="idPelanggan" value="<?= $occupant['idPelanggan'] ?? '' ?>">
                                    <input type="hidden" name="idKamar" value="<?= $idKamar ?>">
                                    <input type="hidden" name="add_cost" value="1">

                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button type="submit" class="btn btn-primary me-md-2"
                                            style="background-color: #4FD1C5; border: none;">
                                            Simpan
                                        </button>
                                        <button type="button" id="cancelAddButton" class="btn btn-outline-secondary">
                                            Batal
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Payment Summary -->
                            <hr>
                            <div class="mt-3">
                                <h5 class="fw-bold">Total Tagihan</h5>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Subtotal (Kamar)</span>
                                    <span>Rp<?= number_format($roomPrice, 0, ',', '.') ?></span>
                                </div>

                                <?php if ($additionalCosts): ?>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Tambahan Alat Listrik</span>
                                        <span>Rp<?= number_format($additionalCostsTotal, 0, ',', '.') ?></span>
                                    </div>
                                <?php endif; ?>

                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Total</span>
                                    <span>Rp<?= number_format($totalCost, 0, ',', '.') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
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

    <script>
        // Add cost form toggle
        document.getElementById('addButton')?.addEventListener('click', function() {
            document.getElementById('appliancesList').style.display = 'none';
            document.getElementById('addForm').style.display = 'block';
        });

        document.getElementById('cancelAddButton')?.addEventListener('click', function() {
            document.getElementById('appliancesList').style.display = 'block';
            document.getElementById('addForm').style.display = 'none';
        });

        // Image preview function
        function showImagePreview(imageSrc) {
            const modal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
            document.getElementById('modalImage').src = imageSrc;
            modal.show();
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>