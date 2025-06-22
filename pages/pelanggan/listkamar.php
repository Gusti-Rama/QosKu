<?php
session_start();
require "../../php/connect.php";
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pelanggan') {
  echo "<script>
        alert('Silakan login sebagai pelanggan terlebih dahulu.');
        window.location.href = '../../auth/login.php'; // sesuaikan path
    </script>";
  exit;
}
// Pagination
$perPage = 6;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

// Count total rooms
$countQuery = "SELECT COUNT(*) AS total FROM kamar_kos";
$countResult = $connect->query($countQuery);
$totalRooms = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRooms / $perPage);

// Fetch rooms for the current page
$query = "SELECT * FROM kamar_kos ORDER BY nomorKamar LIMIT $perPage OFFSET $offset";
$hasil = $connect->query($query);
$kamar = [];
if ($hasil) {
  $kamar = $hasil->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QosKu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
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
        <div class="d-flex justify-content-between align-items-center mb-3 px-2">
          <div>
            <button class="btn btn-outline-secondary rounded-4"><i class="bi bi-funnel-fill"></i> Filter</button>
            <button class="btn btn-outline-secondary rounded-4"><i class="bi bi-sort-down"></i> Urutkan</button>
          </div>
        </div>

        <div class="row g-4 px-2">
          <?php foreach ($kamar as $room): ?>
            <div class="col-md-4 mb-4">
              <?php if ($room['statusKetersediaan'] === 'Ditempati'): ?>
                <!-- Occupied Room - No clickable link -->
                <div class="card shadow-sm border-0 rounded-4 h-100 card-disabled">
                  <div class="card-img-container">
                    <img src="<?= !empty($room['gambar']) ? '../../assets/img/' . $room['gambar'] : '../../assets/img/backgroundKamar.png' ?>"
                      class="card-img-top"
                      alt="Kamar No. <?= htmlspecialchars($room['nomorKamar']) ?>"
                      style="height: 200px; object-fit: cover;">
                    <div class="img-overlay"></div>
                  </div>
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                      <h5 class="card-title fw-bold">Kamar No. <?= htmlspecialchars($room['nomorKamar']) ?></h5>
                      <span class="badge bg-danger">
                        <?= htmlspecialchars($room['statusKetersediaan']) ?>
                      </span>
                    </div>
                    <p class="card-text">
                      <span class="d-block">Tipe: <?= htmlspecialchars($room['tipeKamar']) ?></span>
                      <span class="d-block">Harga: Rp <?= number_format($room['harga'], 0, ',', '.') ?></span>
                    </p>
                    <p class="card-text small text-muted"><?= nl2br(htmlspecialchars($room['deskripsi'])) ?></p>
                  </div>
                </div>
              <?php else: ?>
                <!-- Available Room - Clickable link -->
                <a class="card shadow-sm border-0 rounded-4 h-100 text-decoration-none card-hover"
                  href="pesankamar.php?idKamar=<?= urlencode($room['idKamar']) ?>">
                  <div class="card-img-container">
                    <img src="<?= !empty($room['gambar']) ? '../../assets/img/' . $room['gambar'] : '../../assets/img/backgroundKamar.png' ?>"
                      class="card-img-top"
                      alt="Kamar No. <?= htmlspecialchars($room['nomorKamar']) ?>"
                      style="height: 200px; object-fit: cover;">
                    <div class="img-overlay"></div>
                  </div>
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                      <h5 class="card-title fw-bold">Kamar No. <?= htmlspecialchars($room['nomorKamar']) ?></h5>
                      <span class="badge bg-<?= $room['statusKetersediaan'] === 'Tersedia' ? 'success' : 'warning' ?>">
                        <?= htmlspecialchars($room['statusKetersediaan']) ?>
                      </span>
                    </div>
                    <p class="card-text">
                      <span class="d-block">Tipe: <?= htmlspecialchars($room['tipeKamar']) ?></span>
                      <span class="d-block">Harga: Rp <?= number_format($room['harga'], 0, ',', '.') ?></span>
                    </p>
                    <p class="card-text small text-muted"><?= nl2br(htmlspecialchars($room['deskripsi'])) ?></p>
                  </div>
                </a>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
        <?php if ($totalPages > 1): ?>
          <nav class="mt-4">
            <ul class="pagination justify-content-center">
              <?php if ($page > 1): ?>
                <li class="page-item">
                  <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                </li>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                  <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>

              <?php if ($page < $totalPages): ?>
                <li class="page-item">
                  <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                </li>
              <?php endif; ?>
            </ul>
          </nav>
        <?php endif; ?>


        <?php if (empty($kamar)): ?>
          <div class="col-12 text-center py-5">
            <h5>Belum ada kamar yang tersedia</h5>
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