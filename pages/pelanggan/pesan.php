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
$query = "SELECT * FROM kamar_kos ORDER BY nomorKamar";
$result = $connect->query($query);
$rooms = [];
if ($result) {
  $rooms = $result->fetch_all(MYSQLI_ASSOC);
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
    <nav class="bg-transparent p-3 me-4 d-flex flex-column flex-shrink-0" style="width: 250px;">
      <a class="navbar-brand fw-bold fs-3 pt-3 border-bottom" href="#" style="color: #2D3748;">
        <img src="../../assets/img/QosKuIMG.png" class="mb-1" alt="Logo" height="80">QosKu
      </a>
      <div class="flex-grow-1 mt-3 d-flex flex-column justify-content-between h-100">
        <ul class="nav flex-column">
          <li class="nav-item mb-2">
            <div class="bg-white rounded-4 shadow-sm py-2 px-2 d-flex align-items-center">
              <a href="#" class="nav-link text-dark fw-bold d-flex align-items-center gap-2">
                <span class="d-flex justify-content-center align-items-center rounded-3"
                  style="width: 32px; height: 32px; background-color: #4FD1C5;">
                  <i class="bi bi-house-door-fill text-white"></i>
                </span>
                Kamar Anda
              </a>
            </div>
          </li>
          <li class="nav-item mb-2">
            <div class="bg-transparent rounded-4 py-2 px-2 d-flex align-items-center">
              <a href="#" class="nav-link text-secondary d-flex align-items-center gap-2">
                <span class="d-flex justify-content-center align-items-center rounded-3 bg-white"
                  style="width: 32px; height: 32px;">
                  <i class="bi bi-credit-card-fill" style="color: #4FD1C5;"></i>
                </span>
                Pesan Kamar
              </a>
            </div>
          </li>
          <li class="nav-item mb-2">
            <div class="bg-transparent rounded-4 py-2 px-2 d-flex align-items-center">
              <a href="#" class="nav-link text-secondary d-flex align-items-center gap-2">
                <span class="d-flex justify-content-center align-items-center rounded-3 bg-white"
                  style="width: 32px; height: 32px;">
                  <i class="bi bi-person-fill" style="color: #4FD1C5;"></i>
                </span>
                Profil
              </a>
            </div>
          </li>
        </ul>
        <div class="position-relative mt-auto rounded-4"
          style="height: 180px; background-image: url('../../assets/img/backgroundHelp.png'); background-size: cover; background-position: center;">
          <div class="text-white position-absolute bottom-0 w-100 start-0 px-3 pb-3 text-white">
            <p class="fw-bold fs-6 mb-0">Butuh Bantuan?</p>
            <p class="fs-6 mt-0 mb-1">Hubungi Kami</p>
            <button class="btn btn-sm btn-light w-100 rounded-3 fw-bold">Kontak</button>
          </div>
        </div>
      </div>
    </nav>

    <div class="flex-grow-1">
      <div class="d-flex justify-content-between mt-4 px-4 pt-3 bg-transparent">
        <div>
          <p class="mb-0 fs-6 text-secondary">Pages <b>/ Pesan Kamar</b></p>
          <p class="fs-5 fw-bold">Pesan Kamar</p>
        </div>
        <div class="d-flex align-items-start gap-3">
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-white rounded-4 border-end-0 rounded-end-0">
              <i class="bi bi-search"></i>
            </span>
            <input type="text" class="form-control border-start-0 rounded-4 rounded-start-0" placeholder="Pencarian">
          </div>
          <div class="d-flex align-items-center gap-1">
            <i class="bi bi-person-fill fs-5"></i>
            <span class="fs-6">Profil</span>
          </div>
          <i class="bi bi-gear-fill fs-5"></i>
          <i class="bi bi-bell-fill fs-5"></i>
        </div>
      </div>

      <div class="container-fluid pt-4 pb-3">
        <div class="d-flex justify-content-between align-items-center mb-3 px-2">
          <div>
            <button class="btn btn-outline-secondary rounded-4"><i class="bi bi-funnel-fill"></i> Filter</button>
            <button class="btn btn-outline-secondary rounded-4"><i class="bi bi-sort-down"></i> Urutkan</button>
          </div>
        </div>

        <div class="row g-4 px-2">
          <?php foreach ($rooms as $room): ?>
            <div class="col-md-4">
              <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <?php
                $imagePath = !empty($room['gambar']) ? "../../assets/img/" . $room['gambar'] : "../../assets/img/backgroundKamar.png";
                ?>
                <img src="<?= $imagePath ?>" class="card-img-top" alt="Kamar No. <?= htmlspecialchars($room['nomorKamar']) ?>">
                <div class="card-body">
                  <h5 class="card-title fw-bold">Kamar No. <?= htmlspecialchars($room['nomorKamar']) ?></h5>
                  <p class="card-text">
                    <small class="text-muted">Tipe: <?= htmlspecialchars($room['tipeKamar']) ?></small><br>
                    <small class="text-muted">Harga: Rp <?= number_format($room['harga'], 0, ',', '.') ?></small><br>
                    <small class="text-muted">Status: <?= htmlspecialchars($room['statusKetersediaan']) ?></small>
                  </p>
                  <div class="d-flex justify-content-center my-4">
                    <a href="pesankamar.php?idKamar=<?= urlencode($room['idKamar']) ?>" class="btn btn-light text-dark px-4 rounded-3 fw-bold w-100">
                      Pesan Sekarang
                    </a>
                  </div>

                </div>
              </div>
            </div>
          <?php endforeach; ?>

          <?php if (empty($rooms)): ?>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>