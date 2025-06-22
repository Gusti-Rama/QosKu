<?php
session_start();
require "../../php/connect.php";
if (!isset($_SESSION['role'])) {
  header("Location: ../../pages/login.php?error=not_logged_in");
  exit;
}

$peran = strtolower($_SESSION['role']);

$diperbolehkan = ['owner'];

// cek peran usernya
if (!in_array($peran, $diperbolehkan)) {
  header("Location: ../../pages/login.php?error=Akses_Ditolak");
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
    <?php include '../../layout/pemilikNavbar.php'; ?>

    <div class="flex-grow-1">
      <?php include '../../layout/pemilikHeader.php'; ?>

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
            <button class="btn btn-outline-secondary rounded-4"><i class="bi bi-funnel-fill"></i> Filter</button>
            <button class="btn btn-outline-secondary rounded-4"><i class="bi bi-sort-down"></i> Urutkan</button>
          </div>
          <div>
            <button class="btn btn-primary rounded-4 me-2" data-bs-toggle="modal" data-bs-target="#tambahKamarModal">
              <i class="bi bi-plus-circle"></i> Tambah Kamar
            </button>

            <button class="btn btn-danger rounded-4" data-bs-toggle="modal" data-bs-target="#hapusKamarModal">
              <i class="bi bi-trash"></i> Hapus Kamar
            </button>
          </div>
        </div>
      </div>

      <!-- Tambah Kamar Modal -->
      <div class="modal fade" id="tambahKamarModal" tabindex="-1" aria-labelledby="tambahKamarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="tambahKamarModalLabel">Tambah Kamar Baru</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" action="../../php/prosesowner.php">
              <div class="modal-body">
                <div class="mb-3">
                  <label for="nomorKamar" class="form-label">Nomor Kamar</label>
                  <input type="number" class="form-control" id="nomorKamar" name="nomorKamar" required min="1">
                </div>
                <div class="mb-3">
                  <label for="tipeKamar" class="form-label">Tipe Kamar</label>
                  <input type="text" class="form-control" id="tipeKamar" name="tipeKamar" required>
                </div>
                <div class="mb-3">
                  <label for="harga" class="form-label">Harga Kamar</label>
                  <input type="number" class="form-control" id="harga" name="harga" required>
                </div>
                <div class="mb-3">
                  <label for="deskripsi" class="form-label">Deskripsi Kamar</label>
                  <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                </div>
                <div class="mb-3">
                  <label for="fotoKamar" class="form-label">Foto Kamar</label>
                  <input class="form-control" type="file" id="fotoKamar" name="fotoKamar" accept="image/*">
                </div>
                <input type="hidden" name="add_room" value="1">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Hapus Kamar Modal -->
      <div class="modal fade" id="hapusKamarModal" tabindex="-1" aria-labelledby="hapusKamarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="hapusKamarModalLabel">Hapus Kamar</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="../../php/prosesowner.php">
              <div class="modal-body">
                <div class="mb-3">
                  <label for="idKamar" class="form-label">Pilih Kamar yang akan dihapus</label>
                  <select class="form-select" id="idKamar" name="idKamar" required>
                    <option value="" selected disabled>Pilih kamar...</option>
                    <?php foreach ($kamar as $room): ?>
                      <option value="<?= $room['idKamar'] ?>">Kamar No. <?= htmlspecialchars($room['nomorKamar']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="alert alert-warning">
                  <i class="bi bi-exclamation-triangle"></i> Perhatian: Aksi ini tidak dapat dibatalkan!
                </div>
                <input type="hidden" name="delete_room" value="1">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Hapus</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="row g-4 px-2">
        <?php foreach ($kamar as $room): ?>
          <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
              <img src="<?= !empty($room['gambar']) ? '../../assets/img/' . $room['gambar'] : '../../assets/img/backgroundKamar.png' ?>"
                class="card-img-top"
                alt="Kamar No. <?= htmlspecialchars($room['nomorKamar']) ?>"
                style="height: 200px; object-fit: cover;">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                  <h5 class="card-title fw-bold">Kamar No. <?= htmlspecialchars($room['nomorKamar']) ?></h5>
                  <span class="badge bg-<?= $room['statusKetersediaan'] === 'Tersedia' ? 'success' : 'danger' ?>">
                    <?= htmlspecialchars($room['statusKetersediaan']) ?>
                  </span>
                </div>
                <p class="card-text">
                  <span class="d-block">Tipe: <?= htmlspecialchars($room['tipeKamar']) ?></span>
                  <span class="d-block">Harga: Rp <?= number_format($room['harga'], 0, ',', '.') ?></span>
                </p>
                <p class="card-text small text-muted"><?= nl2br(htmlspecialchars($room['deskripsi'])) ?></p>
              </div>
              <div class="card-footer bg-white border-0">
                <div class="d-grid">
                  <a href="detailkamar.php?idKamar=<?= $room['idKamar'] ?>" class="btn btn-outline-primary rounded-3">
                    Lihat Detail & Fasilitas
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
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