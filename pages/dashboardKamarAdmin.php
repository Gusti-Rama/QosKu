<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QosKu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  <link rel="stylesheet" href="./css/style.css">
  <link rel="icon" href="./img/QosKuIMG.png" type="image/png">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">
  <div class="d-flex min-vh-100 ms-4 me-4">
    <nav class="bg-transparent p-3 me-4 d-flex flex-column" style="width: 250px;">
      <a class="navbar-brand fw-bold fs-3 pt-3 border-bottom" href="#" style="color: #2D3748;">
        <img src="./img/QosKuIMG.png" class="mb-1" alt="Logo" height="80">QosKu
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
                List Kamar
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
                Laporan
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
          style="height: 180px; background-image: url('./img/backgroundHelp.png'); background-size: cover; background-position: center;">
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
          <p class="mb-0 fs-6 text-secondary">Pages <b>/ List Kamar</b></p>
          <p class="fs-5 fw-bold">List Kamar</p>
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
  <!-- Ulangi 9 kamar -->
  <!-- Gunakan col-md-6 col-lg-4 agar tampil 3 per baris di layar besar -->

  <!-- Kamar 1.1 -->
  <div class="col-md-6 col-lg-4">
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
      <img src="./img/backgroundKamar.png" class="card-img-top" alt="Kamar No. 1.1">
      <div class="card-body">
        <h5 class="card-title fw-bold">Kamar No. 1.1</h5>
        <div class="d-flex justify-content-center my-4">
          <button class="btn btn-light text-dark px-4 rounded-3 fw-bold w-100">Lihat Detail Kamar & Penghuni</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Kamar 1.2 -->
  <div class="col-md-6 col-lg-4">
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
      <img src="./img/backgroundKamar.png" class="card-img-top" alt="Kamar No. 1.2">
      <div class="card-body">
        <h5 class="card-title fw-bold">Kamar No. 1.2</h5>
        <div class="d-flex justify-content-center my-4">
          <button class="btn btn-light text-dark px-4 rounded-3 fw-bold w-100">Lihat Detail Kamar & Penghuni</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Kamar 1.3 -->
  <div class="col-md-6 col-lg-4">
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
      <img src="./img/backgroundKamar.png" class="card-img-top" alt="Kamar No. 1.3">
      <div class="card-body">
        <h5 class="card-title fw-bold">Kamar No. 1.3</h5>
        <div class="d-flex justify-content-center my-4">
          <button class="btn btn-light text-dark px-4 rounded-3 fw-bold w-100">Lihat Detail Kamar & Penghuni</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Kamar 1.4 -->
  <div class="col-md-6 col-lg-4">
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
      <img src="./img/backgroundKamar.png" class="card-img-top" alt="Kamar No. 1.4">
      <div class="card-body">
        <h5 class="card-title fw-bold">Kamar No. 1.4</h5>
        <div class="d-flex justify-content-center my-4">
          <button class="btn btn-light text-dark px-4 rounded-3 fw-bold w-100">Lihat Detail Kamar & Penghuni</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Kamar 1.5 -->
  <div class="col-md-6 col-lg-4">
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
      <img src="./img/backgroundKamar.png" class="card-img-top" alt="Kamar No. 1.5">
      <div class="card-body">
        <h5 class="card-title fw-bold">Kamar No. 1.5</h5>
        <div class="d-flex justify-content-center my-4">
          <button class="btn btn-light text-dark px-4 rounded-3 fw-bold w-100">Lihat Detail Kamar & Penghuni</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Kamar 1.6 -->
  <div class="col-md-6 col-lg-4">
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
      <img src="./img/backgroundKamar.png" class="card-img-top" alt="Kamar No. 1.6">
      <div class="card-body">
        <h5 class="card-title fw-bold">Kamar No. 1.6</h5>
        <div class="d-flex justify-content-center my-4">
          <button class="btn btn-light text-dark px-4 rounded-3 fw-bold w-100">Lihat Detail Kamar & Penghuni</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Kamar 1.7 -->
  <div class="col-md-6 col-lg-4">
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
      <img src="./img/backgroundKamar.png" class="card-img-top" alt="Kamar No. 1.7">
      <div class="card-body">
        <h5 class="card-title fw-bold">Kamar No. 1.7</h5>
        <div class="d-flex justify-content-center my-4">
          <button class="btn btn-light text-dark px-4 rounded-3 fw-bold w-100">Lihat Detail Kamar & Penghuni</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Kamar 1.8 -->
  <div class="col-md-6 col-lg-4">
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
      <img src="./img/backgroundKamar.png" class="card-img-top" alt="Kamar No. 1.8">
      <div class="card-body">
        <h5 class="card-title fw-bold">Kamar No. 1.8</h5>
        <div class="d-flex justify-content-center my-4">
          <button class="btn btn-light text-dark px-4 rounded-3 fw-bold w-100">Lihat Detail Kamar & Penghuni</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Kamar 1.9 -->
  <div class="col-md-6 col-lg-4">
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
      <img src="./img/backgroundKamar.png" class="card-img-top" alt="Kamar No. 1.9">
      <div class="card-body">
        <h5 class="card-title fw-bold">Kamar No. 1.9</h5>
        <div class="d-flex justify-content-center my-4">
          <button class="btn btn-light text-dark px-4 rounded-3 fw-bold w-100">Lihat Detail Kamar & Penghuni</button>
        </div>
      </div>
    </div>
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
