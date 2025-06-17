<?php
session_start();
require_once "../../php/connect.php";

if (!isset($_SESSION['username']) || !isset($_SESSION['idPelanggan'])) {
    header("Location: ../../pages/login.php");
    exit;
}

// Fetch pelanggan data
$stmt = $connect->prepare("SELECT * FROM pelanggan WHERE idPelanggan = ?");
$stmt->bind_param("i", $_SESSION['idPelanggan']);
$stmt->execute();
$result = $stmt->get_result();
$pelanggan = $result->fetch_assoc();

// Fetch transaction history
$stmt = $connect->prepare("
    SELECT p.tanggalPemesanan, p.totalHarga 
    FROM pemesanan p 
    WHERE p.idPelanggan = ? 
    ORDER BY p.tanggalPemesanan DESC 
    LIMIT 3
");
$stmt->bind_param("i", $_SESSION['idPelanggan']);
$stmt->execute();
$transactions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
    <div class="d-flex min-vh-100 ms-4 me-4">
        <?php include '../../layout/pelangganNavbar.php'; ?>

        <div class="flex-grow-1">
            <div class="position-relative rounded-4 text-white"
                style="background-image:url('../../assets/img/backgroundProfil.png'); height:200px;background-size:cover; background-position:center;">

                <?php include '../../layout/pelangganHeader.php'; ?>

                <div
                    class="position-absolute"
                    style="bottom: 0; left: 0; right: 0; transform: translateY(50%); padding: 0 1rem;">
                    <div class="bg-white bg-opacity-75 rounded-4 shadow-sm d-flex align-items-center p-3 mx-auto border border-white border-2" style="max-width: 1150px;">
                        <?php
                        $profilePicture = !empty($pelanggan['profilePicture']) 
                            ? "../../assets/img/" . $pelanggan['profilePicture']
                            : "../../assets/img/";
                        ?>
                        <img
                            src="<?= $profilePicture?>"
                            alt="Profile Pic"
                            class="rounded-circle me-3"
                            style="width:60px; height:60px; object-fit:cover;" />
                        <div>
                            <p class="mb-0 fw-bold text-dark"><?= htmlspecialchars($pelanggan['namaLengkap']) ?></p>
                            <p class="mb-0 text-dark small"><?= htmlspecialchars($pelanggan['email']) ?></p>
                        </div>
                    </div>
                </div>

            </div>

            <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show m-4" role="alert">
                <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show m-4" role="alert">
                <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); endif; ?>

            <div class="pt-5 mt-4 container-fluid">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card shadow-sm rounded-4 p-4 h-100 border-0">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0">Informasi Pribadi</h6>
                                <button class="btn btn-sm btn-dark rounded-3" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                    <i class="bi bi-pencil-fill text-white"></i> Edit
                                </button>
                            </div>
                            <hr class="mt-0">
                            <p class="mb-1"><strong>Nama Lengkap:</strong> <?= htmlspecialchars($pelanggan['namaLengkap']) ?></p>
                            <p class="mb-1"><strong>Telepon:</strong> <?= htmlspecialchars($pelanggan['nomorHp']) ?></p>
                            <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($pelanggan['email']) ?></p>
                            <p class="mb-1"><strong>Alamat:</strong> <?= htmlspecialchars($pelanggan['alamat']) ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm rounded-4 p-4 h-100 border-0">
                            <h6 class="fw-bold mb-3">Pengaturan Platform</h6>
                            <hr class="mt-0">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="emailtagihankamar" checked>
                                <label class="form-check-label" for="emailtagihankamar">Email saya untuk tagihan kamar</label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="emailtagihantambahan">
                                <label class="form-check-label" for="emailtagihantambahan">Email saya untuk tagihan tambahan</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="emailinvoice">
                                <label class="form-check-label" for="emailinvoice">Email saya invoice pembayaran</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="langgananberita">
                                <label class="form-check-label" for="langgananberita">Berlangganan ke berita</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="infokamarkosong">
                                <label class="form-check-label" for="infokamarkosong">Informasi kamar kosong</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="infopromo">
                                <label class="form-check-label" for="infopromo">Informasi diskon dan promo</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-4 mt-0">
                    <div class="col-md-12">
                        <div class="card shadow-sm rounded-4 p-4 h-100 border-0">
                            <h6 class="fw-bold mb-3">Riwayat Transaksi</h6>
                            <hr class="mt-0">
                            <ul class="list-unstyled small mb-0">
                                <?php foreach ($transactions as $transaction): ?>
                                <li><?= date('d M Y', strtotime($transaction['tanggalPemesanan'])) ?> — Rp<?= number_format($transaction['totalHarga'], 0, ',', '.') ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <a class="btn btn-light w-100 text-dark fw-bold mt-3 border-0" href="riwayattransaksi.php" style="background-color: #4FD1C5;">Lihat Semua</a>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- /.flex-grow-1 -->
    </div> <!-- /.d-flex.min-vh-100 -->

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../php/update_profile.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Foto Profil</label>
                            <input type="file" class="form-control" name="profilePicture" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="namaLengkap" value="<?= htmlspecialchars($pelanggan['namaLengkap']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="tel" class="form-control" name="nomorHp" value="<?= htmlspecialchars($pelanggan['nomorHp']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($pelanggan['email']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control" name="alamat" rows="3" required><?= htmlspecialchars($pelanggan['alamat']) ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" style="background-color: #4FD1C5; border: none;">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer text-center mt-5 pt-5">
        &copy; 2025, Made with ❤️ for QosKu
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>