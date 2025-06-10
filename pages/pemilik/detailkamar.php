<?php
session_start();

// Debugging (remove after testing)
// echo "<pre>Session: "; print_r($_SESSION); echo "</pre>";

// Check if user is logged in at all
if (!isset($_SESSION['role'])) {
    header("Location: ../../pages/login.php?error=not_logged_in");
    exit;
}

// Normalize role to lowercase for consistent comparison
$role = strtolower($_SESSION['role']);

// Define allowed roles for admin pages
$allowedRoles = ['owner'];

// Check if user has required role
if (!in_array($role, $allowedRoles)) {
    header("Location: ../../pages/login.php?error=access_denied");
    exit;
}

// Optional: Verify the user still exists in database
require_once '../../php/connect.php';
$stmt = $connect->prepare("SELECT idAdmin FROM admin WHERE username = ? AND peran = ?");
$stmt->bind_param("ss", $_SESSION['username'], $role);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    style="height: 180px; background-image: url('../../assets/img/backgroundHelp.png'); background-size: cover; background-position: center;">
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
            <!-- Kamar Anda -->
            <div class="container-fluid pt-4">
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card shadow-sm border-0 rounded-4 position-relative">
                            <img src="../../assets/img/Kamar1.png" class="card-img-top rounded-top-4" alt="Kamar 1">
                            <div class="card-body pb-5"> <!-- Added pb-5 to make space for buttons -->
                                <h5 class="card-title fw-bold">Kamar No. 1.15</h5>
                                <p class="card-text">Harga: Rp600,000 / bulan</p>
                                <p class="text-muted">Kamar Kos dengan perabotan lengkap. Sudah termasuk air dan listrik (diluar alat listrik tambahan). Terletak di lantai 1 yang memudahkan akses dan mobilitas.</p>
                                <ul>
                                    <li>Luas: 3m x 3m</li>
                                    <li>Perabotan: Meja, kursi, kasur, lemari</li>
                                    <li>Kamar mandi: dalam</li>
                                </ul>
                                <p class="fw-bold">Harga: Rp600,000/bulan</p>

                                <!-- Action Buttons Container -->
                                <div class="position-absolute bottom-0 end-0 m-3">
                                    <div class="btn-group" role="group">
                                        <!-- Edit Button -->
                                        <button class="btn btn-sm btn-outline-primary rounded-start-4"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editKamarModal"
                                            data-kamar-id="1.15">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>

                                        <!-- Delete Button -->
                                        <button class="btn btn-sm btn-outline-danger rounded-end-4"
                                            data-bs-toggle="modal"
                                            data-bs-target="#hapusKamarModal"
                                            data-kamar-id="1.15">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Edit Kamar Modal -->
                        <div class="modal fade" id="editKamarModal" tabindex="-1" aria-labelledby="editKamarModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editKamarModalLabel">Edit Kamar <span id="editKamarTitle"></span></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="formEditKamar">
                                            <input type="hidden" id="editKamarId">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="editNamaKamar" class="form-label">Nomor Kamar</label>
                                                        <input type="text" class="form-control" id="editNamaKamar" value="1.15">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="editHargaKamar" class="form-label">Harga Kamar</label>
                                                        <input type="number" class="form-control" id="editHargaKamar" value="600000">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="editLuasKamar" class="form-label">Luas Kamar</label>
                                                        <input type="text" class="form-control" id="editLuasKamar" value="3m x 3m">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="editFotoKamar" class="form-label">Foto Kamar</label>
                                                        <input class="form-control" type="file" id="editFotoKamar" accept="image/*">
                                                        <small class="text-muted">Kosongkan jika tidak ingin mengubah foto</small>
                                                    </div>
                                                    <div class="current-image">
                                                        <img src="../../assets/img/Kamar1.png" class="img-thumbnail" width="150" alt="Current Image">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="editDeskripsiKamar" class="form-label">Deskripsi Kamar</label>
                                                <textarea class="form-control" id="editDeskripsiKamar" rows="3">Kamar Kos dengan perabotan lengkap. Sudah termasuk air dan listrik (diluar alat listrik tambahan). Terletak di lantai 1 yang memudahkan akses dan mobilitas.</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="editPerabotanKamar" class="form-label">Perabotan</label>
                                                <textarea class="form-control" id="editPerabotanKamar" rows="2">Meja, kursi, kasur, lemari</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Kamar Mandi</label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="editKamarMandi" id="editKamarMandiDalam" value="dalam" checked>
                                                    <label class="form-check-label" for="editKamarMandiDalam">Dalam</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="editKamarMandi" id="editKamarMandiLuar" value="luar">
                                                    <label class="form-check-label" for="editKamarMandiLuar">Luar</label>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="button" class="btn btn-primary" id="simpanEditKamar">Simpan Perubahan</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold mt-4">Foto Lainnya</h6>
                        <div class="d-flex gap-3">
                            <img src="../../assets/img/Kamar1.png" class="img-thumbnail rounded-3" style="width: 100px; height: 80px; object-fit: cover;" alt="Kamar 1">
                            <img src="../../assets/img/Kamar2.png" class="img-thumbnail rounded-3" style="width: 100px; height: 80px; object-fit: cover;" alt="Kamar 2">
                            <img src="../../assets/img/Kamar3.png" class="img-thumbnail rounded-3" style="width: 100px; height: 80px; object-fit: cover;" alt="Kamar 3">
                        </div>
                    </div>

                    <!-- Pembayaran -->
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 rounded-4 p-4">
                            <!-- Resident Information -->
                            <div class="text-center mb-4">
                                <img src="profile-picture.jpg" class="rounded-circle mb-2" width="100" height="100" alt="Profile Picture">
                                <h5 class="fw-bold mb-1">Nama Penghuni</h5>
                                <p class="text-muted mb-1">email@example.com</p>
                                <p class="text-muted">+62 812-3456-7890</p>
                            </div>

                            <!-- Electricity Appliances List (default state) -->
                            <div id="appliancesList">
                                <h6 class="fw-bold mb-3">Tambahan Alat Listrik</h6>
                                <ul class="list-group mb-3">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Rice cooker
                                        <span>Rp30,000</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Panci Elektrik
                                        <span>Rp30,000</span>
                                    </li>
                                </ul>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button id="addButton" class="btn btn-primary me-md-2" style="background-color: #4FD1C5; border: none;">Tambah</button>
                                    <button id="editButton" class="btn btn-outline-secondary">Ubah</button>
                                </div>
                            </div>

                            <!-- Add Form (hidden by default) -->
                            <div id="addForm" class="mb-3" style="display: none;">
                                <h6 class="fw-bold mb-3">Tambah Alat Listrik</h6>
                                <div class="mb-3">
                                    <label for="applianceName" class="form-label">Nama Alat</label>
                                    <input type="text" class="form-control" id="applianceName">
                                </div>
                                <div class="mb-3">
                                    <label for="applianceCost" class="form-label">Biaya</label>
                                    <input type="number" class="form-control" id="applianceCost">
                                </div>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button id="saveButton" class="btn btn-primary me-md-2" style="background-color: #4FD1C5; border: none;">Simpan</button>
                                    <button id="cancelAddButton" class="btn btn-outline-secondary">Batal</button>
                                </div>
                            </div>

                            <!-- Edit Form (hidden by default) -->
                            <div id="editForm" class="mb-3" style="display: none;">
                                <h6 class="fw-bold mb-3">Ubah Alat Listrik</h6>
                                <div class="mb-3">
                                    <label for="editApplianceName" class="form-label">Nama Alat</label>
                                    <input type="text" class="form-control" id="editApplianceName" value="Rice cooker">
                                </div>
                                <div class="mb-3">
                                    <label for="editApplianceCost" class="form-label">Biaya</label>
                                    <input type="number" class="form-control" id="editApplianceCost" value="30000">
                                </div>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button id="updateButton" class="btn btn-primary me-md-2" style="background-color: #4FD1C5; border: none;">Update</button>
                                    <button id="cancelEditButton" class="btn btn-outline-secondary">Batal</button>
                                    <button id="deleteButton" class="btn btn-outline-danger">Hapus</button>
                                </div>
                            </div>

                            <!-- Payment Summary -->
                            <hr>
                            <div class="mt-3">
                                <h5 class="fw-bold">Total Tagihan</h5>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Subtotal (Kamar)</span>
                                    <span>Rp600,000</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Tambahan Alat Listrik</span>
                                    <span>Rp60,000</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Total</span>
                                    <span>Rp660,000</span>
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
    <script>
        // Edit button handler
        document.querySelectorAll('[data-bs-target="#editKamarModal"]').forEach(btn => {
            btn.addEventListener('click', function() {
                const kamarId = this.getAttribute('data-kamar-id');
                document.getElementById('editKamarId').value = kamarId;
                document.getElementById('editKamarTitle').textContent = kamarId;

                // In a real app, you would fetch the room data here via AJAX
                // and populate the form fields with the current values
            });
        });

        // Delete button handler
        document.querySelectorAll('[data-bs-target="#hapusKamarModal"]').forEach(btn => {
            btn.addEventListener('click', function() {
                const kamarId = this.getAttribute('data-kamar-id');
                if (confirm(`Apakah Anda yakin ingin menghapus Kamar ${kamarId}?`)) {
                    // AJAX call to delete the room would go here
                    console.log(`Deleting room ${kamarId}`);
                    // Then remove the card from DOM or refresh the page
                    this.closest('.card').remove();
                }
            });
        });

        // Save edit handler
        document.getElementById('simpanEditKamar').addEventListener('click', function() {
            // Collect all form data
            const formData = {
                id: document.getElementById('editKamarId').value,
                nama: document.getElementById('editNamaKamar').value,
                harga: document.getElementById('editHargaKamar').value,
                luas: document.getElementById('editLuasKamar').value,
                deskripsi: document.getElementById('editDeskripsiKamar').value,
                perabotan: document.getElementById('editPerabotanKamar').value,
                kamar_mandi: document.querySelector('input[name="editKamarMandi"]:checked').value,
                foto: document.getElementById('editFotoKamar').files[0]
            };

            // Here you would typically make an AJAX call to update the room
            console.log('Updating room:', formData);

            // Close the modal
            bootstrap.Modal.getInstance(document.getElementById('editKamarModal')).hide();
            alert('Perubahan berhasil disimpan!');
        });
        // Simple JavaScript to handle the form toggling
        document.getElementById('addButton').addEventListener('click', function() {
            document.getElementById('appliancesList').style.display = 'none';
            document.getElementById('addForm').style.display = 'block';
            document.getElementById('editForm').style.display = 'none';
        });

        document.getElementById('editButton').addEventListener('click', function() {
            document.getElementById('appliancesList').style.display = 'none';
            document.getElementById('addForm').style.display = 'none';
            document.getElementById('editForm').style.display = 'block';
        });

        document.getElementById('cancelAddButton').addEventListener('click', function() {
            document.getElementById('appliancesList').style.display = 'block';
            document.getElementById('addForm').style.display = 'none';
        });

        document.getElementById('cancelEditButton').addEventListener('click', function() {
            document.getElementById('appliancesList').style.display = 'block';
            document.getElementById('editForm').style.display = 'none';
        });

        // You would add more JavaScript here to handle the actual saving/updating of data
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>