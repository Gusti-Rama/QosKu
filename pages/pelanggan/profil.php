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
                        <img
                            src="../assets/img/profilePic.png"
                            alt="Profile Pic"
                            class="rounded-circle me-3"
                            style="width:60px; height:60px; object-fit:cover;" />
                        <div>
                            <p class="mb-0 fw-bold text-dark">John Doe</p>
                            <p class="mb-0 text-dark small">john.doe@example.com</p>
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
                <div class="row g-4 mt-0">
                    <div class="col-md-12">
                        <div class="card shadow-sm rounded-4 p-4 h-100 border-0">
                            <h6 class="fw-bold mb-3">Riwayat Transaksi</h6>
                            <hr class="mt-0">
                            <ul class="list-unstyled small mb-0">
                                <li>01 Jun 2025 — Rp660.000</li>
                                <li>01 Mei 2025 — Rp600.000</li>
                                <li>01 Apr 2025 — Rp600.000</li>
                            </ul>
                            <a class="btn btn-light w-100 text-dark fw-bold mt-3 border-0" href="riwayattransaksi.php" style="background-color: #4FD1C5;">Lihat Semua</a>
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