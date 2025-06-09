<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QosKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" href="../assets/img/QosKuIMG.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">
    <div class="d-flex min-vh-100 ms-4 me-4">
        <nav class="bg-transparent p-3 me-4 d-flex flex-column flex-shrink-0" style="width: 250px;">
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
                    style="height: 180px; background-image: url('../assets/img/backgroundHelp.png'); background-size: cover; background-position: center;">
                    <div class="text-white position-absolute bottom-0 w-100 start-0 px-3 pb-3 text-white">
                        <p class="fw-bold fs-6 mb-0">Butuh Bantuan?</p>
                        <p class="fs-6 mt-0 mb-1">Hubungi Kami</p>
                        <button class="btn btn-sm btn-light w-100 rounded-3 fw-bold">Kontak</button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main content -->
        <div class="flex-grow-1">
            <div class="d-flex justify-content-between mt-4 px-4 pt-3 bg-transparent">
                <div>
                    <p class="mb-0 fs-6 text-secondary">Pages <b>/ Kamar Anda</b></p>
                    <p class="fs-5 fw-bold">Kamar Anda</p>
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
            <div class="container-fluid pt-4">
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card shadow-sm border-0 rounded-4">
                            <img src="./img/Kamar1.png" class="card-img-top rounded-top-4" alt="Kamar 1">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">Kamar No. 1.15</h5>
                                <p class="card-text">Harga: Rp600,000 / bulan</p>
                                <p class="text-muted">Kamar Kos dengan perabotan lengkap. Sudah termasuk air dan listrik (diluar alat listrik tambahan). Terletak di lantai 1 yang memudahkan akses dan mobilitas.</p>
                                <ul>
                                    <li>Luas: 3m x 3m</li>
                                    <li>Perabotan: Meja, kursi, kasur, lemari</li>
                                    <li>Kamar mandi: dalam</li>
                                </ul>
                                <p class="fw-bold">Harga: Rp600,000/bulan</p>
                            </div>
                        </div>
                        <h6 class="fw-bold mt-4">Foto Lainnya</h6>
                        <div class="d-flex gap-3">
                            <img src="./img/Kamar1.png" class="img-thumbnail rounded-3" style="width: 100px; height: 80px; object-fit: cover;" alt="Kamar 1">
                            <img src="./img/Kamar2.png" class="img-thumbnail rounded-3" style="width: 100px; height: 80px; object-fit: cover;" alt="Kamar 2">
                            <img src="./img/Kamar3.png" class="img-thumbnail rounded-3" style="width: 100px; height: 80px; object-fit: cover;" alt="Kamar 3">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 rounded-4 p-4 h-100 d-flex flex-column">
                            <h6 class="fw-bold">Bayar Tagihan</h6>
                            <p class="mb-1">Jenis</p>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis" checked>
                                <label class="form-check-label">Bulanan</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis">
                                <label class="form-check-label">Mingguan</label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="jenis">
                                <label class="form-check-label">Harian</label>
                            </div>

                            <p class="mb-1">Metode Pembayaran</p>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metode" checked>
                                <label class="form-check-label">Uang Tunai</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metode">
                                <label class="form-check-label">Transfer Bank</label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="metode">
                                <label class="form-check-label">QRIS</label>
                            </div>
                            <hr>
                            <p>Subtotal <span class="float-end">Rp600,000</span></p>
                            <p>Promo <span class="float-end">-</span></p>
                            <hr>
                            <h5 class="fw-bold">Total <span class="float-end">Rp600,000</span></h5>
                            <button class="btn btn-primary w-100 mt-3" style="background-color: #4FD1C5; border: none;">Bayar</button>
                            <p class="mt-auto small text-muted">*Pembayaran diatas hanya untuk bulan, minggu, atau hari pertama. Tagihan anda berikutnya dapat dilakukan di menu Kamar Anda. Anda dapat mengubah jenis kamar pada pembayaran selanjutnya.</p>
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