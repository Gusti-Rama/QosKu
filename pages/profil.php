<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QosKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" href="../assets/img/QosKuIMG.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">
    <div class="d-flex min-vh-100 ms-4 me-4">
        <nav class="bg-transparent p-3 me-4 d-flex flex-column" style="width: 250px;">
            <a class="navbar-brand fw-bold fs-3 pt-3 border-bottom" href="#" style="color: #2D3748;">
                <img src="../assets/img/QosKuIMG.png" class="mb-1" alt="Logo" height="80">QosKu
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
            <div class="position-relative rounded-4"
                style="background-image:url('./img/backgroundProfil.png'); background-size:cover; background-position:center;">
                <div class="d-flex justify-content-between align-items-start mt-4" style="min-height:250px;">
                    <div class="d-flex mt-3 px-4 bg-transparent">
                        <div>
                            <p class="mb-0 fs-6 text-white">Pages <b>/ Profil</b></p>
                            <h3 class="fs-5 text-white fw-bold">Profil</h3>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 pt-4 pe-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white rounded-4 border-end-0 rounded-end-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 rounded-4 rounded-start-0"
                                placeholder="Pencarian" />
                        </div>
                        <i class="bi bi-person-fill fs-5 text-white"></i>
                        <i class="bi bi-gear-fill fs-5 text-white"></i>
                        <i class="bi bi-bell-fill fs-5 text-white"></i>
                    </div>
                </div>

                <!-- Profile Info Bar -->
                <div
                    class="position-absolute"
                    style="bottom: 0; left: 0; right: 0; transform: translateY(50%); padding: 0 1rem;">
                    <div class="bg-white rounded-4 shadow-sm d-flex align-items-center p-3 mx-auto" style="max-width: 1150px;">
                        <img
                            src="./img/profilePic.png"
                            alt="Profile Pic"
                            class="rounded-circle me-3"
                            style="width:60px; height:60px; object-fit:cover;" />
                        <div>
                            <p class="mb-0 fw-bold">John Doe</p>
                            <p class="mb-0 text-muted small">john.doe@example.com</p>
                        </div>
                    </div>
                </div>

            </div>
            <div class="pt-5 mt-4 container-fluid">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card shadow-sm rounded-4 p-4 h-100 border-0">
                            <h6 class="fw-bold mb-3">Informasi Pribadi</h6>
                            <hr class="mt-0">
                            <p class="mb-1"><strong>Nama Lengkap:</strong> John Doe</p>
                            <p class="mb-1"><strong>Telepon:</strong> 0812-3456-7890</p>
                            <p class="mb-1"><strong>Email:</strong> asd@gmail.com</p>
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
                <div class="row g-4 mt-2">
                    <div class="col-md-6">
                        <div class="card shadow-sm rounded-4 p-4 h-100 border-0">
                            <h6 class="fw-bold mb-3">Riwayat Transaksi</h6>
                            <hr class="mt-0">
                            <ul class="list-unstyled small mb-0">
                                <li>01 Jun 2025 — Rp660.000</li>
                                <li>01 Mei 2025 — Rp600.000</li>
                                <li>01 Apr 2025 — Rp600.000</li>
                            </ul>
                            <button class="btn btn-primary w-100 text-dark mt-3 border-0" style="background-color: #4FD1C5;">Lihat Semua</button>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- /.flex-grow-1 -->
    </div> <!-- /.d-flex.min-vh-100 -->

    <!-- Footer -->
    <div class="footer text-center mt-5 pt-5">
        &copy; 2025, Made with ❤️ for QosKu
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>