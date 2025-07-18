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

// Verify the user still exists in database
$stmt = $connect->prepare("SELECT idAdmin FROM admin WHERE username = ? AND peran = ?");
$stmt->bind_param("ss", $_SESSION['username'], $peran);
$stmt->execute();
if (!$stmt->get_result()->num_rows) {
  session_destroy();
  header("Location: ../../pages/login.php?error=invalid_session");
  exit;
}

// Add these at the top of your PHP code (after the session checks)
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'asc';

// Modify your room query to include filtering and sorting
$whereClause = "";
if ($filter === 'ditempati') {
  $whereClause = "WHERE statusKetersediaan = 'Tidak Tersedia'";
} elseif ($filter === 'tersedia') {
  $whereClause = "WHERE statusKetersediaan = 'Tersedia'";
}

$orderClause = "ORDER BY nomorKamar " . ($sort === 'desc' ? 'DESC' : 'ASC');

// Update your room query
$query = "SELECT * FROM kamar_kos $whereClause $orderClause LIMIT $perPage OFFSET $offset";
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
    <?php include '../../layout/adminNavbar.php'; ?>

    <div class="flex-grow-1">
      <?php include '../../layout/adminHeader.php'; ?>

      <!-- Display error and success messages -->
      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert">
          <?= $_SESSION['error'] ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

      <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
          <?= $_SESSION['success'] ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
      <?php endif; ?>

      <div class="container-fluid pt-4 pb-3">
        <div class="d-flex justify-content-between align-items-center mb-3 px-2">
          <div>
            <!-- Filter Dropdown -->
            <div class="btn-group">
              <button class="btn btn-outline-secondary rounded-4 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-funnel-fill"></i> Filter
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item <?= $filter === 'all' ? 'active' : '' ?>"
                    href="?filter=all&sort=<?= $sort ?>">Semua Kamar</a></li>
                <li><a class="dropdown-item <?= $filter === 'tersedia' ? 'active' : '' ?>"
                    href="?filter=tersedia&sort=<?= $sort ?>">Tersedia</a></li>
              </ul>
            </div>

            <!-- Sort Dropdown -->
            <div class="btn-group ms-2">
              <button class="btn btn-outline-secondary rounded-4 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-sort-down"></i> Urutkan
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item <?= $sort === 'asc' ? 'active' : '' ?>"
                    href="?filter=<?= $filter ?>&sort=asc">No. Kamar (A-Z)</a></li>
                <li><a class="dropdown-item <?= $sort === 'desc' ? 'active' : '' ?>"
                    href="?filter=<?= $filter ?>&sort=desc">No. Kamar (Z-A)</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 px-2">
        <?php foreach ($kamar as $room): ?>
          <div class="col-md-4 mb-4">
            <a class="card shadow-sm border-0 rounded-4 h-100 text-decoration-none card-hover"
              href="detailkamar.php?idKamar=<?= urlencode($room['idKamar']) ?>">
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
          </div>
        <?php endforeach; ?>
        <?php if ($totalPages > 1): ?>
          <nav class="mt-4">
            <ul class="pagination justify-content-center">
              <?php if ($page > 1): ?>
                <li class="page-item">
                  <a class="page-link" href="?page=<?= $page - 1 ?>&filter=<?= $filter ?>&sort=<?= $sort ?>">Previous</a>
                </li>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                  <a class="page-link" href="?page=<?= $i ?>&filter=<?= $filter ?>&sort=<?= $sort ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>

              <?php if ($page < $totalPages): ?>
                <li class="page-item">
                  <a class="page-link" href="?page=<?= $page + 1 ?>&filter=<?= $filter ?>&sort=<?= $sort ?>">Next</a>
                </li>
              <?php endif; ?>
            </ul>
          </nav>
        <?php endif; ?>

        <?php if (empty($kamar)): ?>
          <div class="col-12 text-center py-5">
            <h5>Belum ada kamar yang tersedia</h5>
            <p>Tambahkan kamar baru menggunakan tombol di atas</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  </div>

  <div class="footer text-center mt-5 pt-5">
    &copy; 2025, Made with ❤️ for QosKu
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Basic form validation
      const addRoomForm = document.querySelector('form[action="../../php/prosesowner.php"]');
      if (addRoomForm) {
        addRoomForm.addEventListener('submit', function(e) {
          // Check if all required fields are filled
          const nomorKamar = this.querySelector('#nomorKamar').value;
          const tipeKamar = this.querySelector('#tipeKamar').value;
          const harga = this.querySelector('#harga').value;

          if (!nomorKamar || !tipeKamar || !harga) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return false;
          }
        });
      }
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>