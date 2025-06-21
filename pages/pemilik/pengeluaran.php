<?php
session_start();
require "../../php/connect.php";

// Check if user is logged in as owner
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'owner') {
    echo "<script>
        alert('Silakan login sebagai owner terlebih dahulu.');
        window.location.href='../../auth/login.php';
    </script>";
    exit;
}

// Process form submission for adding new expense
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $jenisPengeluaran = $_POST['jenisPengeluaran'];
    $namaPengeluaran = trim($_POST['namaPengeluaran']);
    $jumlah = (int) $_POST['jumlah'];
    $tanggal = $_POST['tanggal'];
    $keterangan = trim($_POST['keterangan'] ?? '');

    // Validate input
    if (empty($namaPengeluaran)) {
        $_SESSION['error'] = "Nama pengeluaran harus diisi";
    } elseif ($jumlah <= 0) {
        $_SESSION['error'] = "Jumlah pengeluaran harus lebih dari 0";
    } else {
        $stmt = $connect->prepare("
            INSERT INTO pengeluaran (jenisPengeluaran, namaPengeluaran, jumlah, tanggal, keterangan) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssiss", $jenisPengeluaran, $namaPengeluaran, $jumlah, $tanggal, $keterangan);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Data pengeluaran berhasil ditambahkan";
        } else {
            $_SESSION['error'] = "Gagal menambahkan data pengeluaran: " . $stmt->error;
        }
    }
    header("Location: pengeluaran.php");
    exit;
}

// Process delete request
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $connect->prepare("DELETE FROM pengeluaran WHERE idPengeluaran = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Data pengeluaran berhasil dihapus";
    } else {
        $_SESSION['error'] = "Gagal menghapus data pengeluaran";
    }
    header("Location: pengeluaran.php");
    exit;
}

// Process edit form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id = (int)$_POST['id'];
    $jenisPengeluaran = $_POST['jenisPengeluaran'];
    $namaPengeluaran = trim($_POST['namaPengeluaran']);
    $jumlah = (int) $_POST['jumlah'];
    $tanggal = $_POST['tanggal'];
    $keterangan = trim($_POST['keterangan'] ?? '');

    $stmt = $connect->prepare("
        UPDATE pengeluaran 
        SET jenisPengeluaran = ?, namaPengeluaran = ?, jumlah = ?, tanggal = ?, keterangan = ?
        WHERE idPengeluaran = ?
    ");
    $stmt->bind_param("ssissi", $jenisPengeluaran, $namaPengeluaran, $jumlah, $tanggal, $keterangan, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Data pengeluaran berhasil diperbarui";
    } else {
        $_SESSION['error'] = "Gagal memperbarui data pengeluaran";
    }
    header("Location: pengeluaran.php");
    exit;
}

// Fetch existing expenses
$expensesQuery = "SELECT * FROM pengeluaran ORDER BY tanggal DESC";
$expensesResult = $connect->query($expensesQuery);
$expenses = $expensesResult->fetch_all(MYSQLI_ASSOC);

// Calculate totals by category
$totalsQuery = "SELECT 
                jenisPengeluaran,
                SUM(jumlah) as total
                FROM pengeluaran
                GROUP BY jenisPengeluaran";
$totalsResult = $connect->query($totalsQuery);
$categoryTotals = [];
$grandTotal = 0;

while ($row = $totalsResult->fetch_assoc()) {
    $categoryTotals[$row['jenisPengeluaran']] = $row['total'];
    $grandTotal += $row['total'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengeluaran - QosKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="icon" href="../../assets/img/QosKuIMG.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">
    <div class="d-flex min-vh-100 ms-4 me-4">
        <?php include '../../layout/pemilikNavbar.php'; ?>

        <div class="flex-grow-1">
            <?php include '../../layout/pemilikHeader.php'; ?>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Pengeluaran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" id="editForm">
                            <input type="hidden" name="edit" value="1">
                            <input type="hidden" name="id" id="editId">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="editJenis" class="form-label">Jenis Pengeluaran</label>
                                    <select class="form-select" id="editJenis" name="jenisPengeluaran" required>
                                        <option value="Listrik">Listrik</option>
                                        <option value="Air">Air</option>
                                        <option value="Wifi">Wifi</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="editNama" class="form-label">Nama Pengeluaran</label>
                                    <input type="text" class="form-control" id="editNama" name="namaPengeluaran" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editJumlah" class="form-label">Jumlah</label>
                                    <input type="number" class="form-control" id="editJumlah" name="jumlah" min="1" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editTanggal" class="form-label">Tanggal</label>
                                    <input type="date" class="form-control" id="editTanggal" name="tanggal" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editKeterangan" class="form-label">Keterangan (Optional)</label>
                                    <textarea class="form-control" id="editKeterangan" name="keterangan" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Apakah Anda yakin ingin menghapus data pengeluaran ini?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <a href="#" id="confirmDelete" class="btn btn-danger">Hapus</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid pt-4 pb-3">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible"><?= $_SESSION['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible"><?= $_SESSION['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">Tambah Pengeluaran</h5>
                                <form method="POST">
                                    <input type="hidden" name="add" value="1">
                                    <div class="mb-3">
                                        <label for="jenisPengeluaran" class="form-label">Jenis Pengeluaran</label>
                                        <select class="form-select" id="jenisPengeluaran" name="jenisPengeluaran" required>
                                            <option value="Listrik">Listrik</option>
                                            <option value="Air">Air</option>
                                            <option value="Wifi">Wifi</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="namaPengeluaran" class="form-label">Nama Pengeluaran</label>
                                        <input type="text" class="form-control" id="namaPengeluaran" name="namaPengeluaran" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="jumlah" class="form-label">Jumlah</label>
                                        <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="tanggal" class="form-label">Tanggal</label>
                                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= date('Y-m-d') ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="keterangan" class="form-label">Keterangan (Optional)</label>
                                        <textarea class="form-control" id="keterangan" name="keterangan" rows="2"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Simpan</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-4">Riwayat Pengeluaran</h5>
                                <div class="row mb-4">
                                    <?php foreach (['Listrik', 'Air', 'Wifi', 'Lainnya'] as $category): ?>
                                        <div class="col-md-3 mb-3">
                                            <div class="card bg-light border-0 rounded-3 h-100">
                                                <div class="card-body text-center">
                                                    <h6 class="text-secondary">Total <?= $category ?></h6>
                                                    <h5 class="fw-bold">Rp<?= number_format($categoryTotals[$category] ?? 0, 0, ',', '.') ?></h5>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <div class="col-md-12">
                                        <div class="card bg-primary text-white border-0 rounded-3">
                                            <div class="card-body text-center">
                                                <h6 class="mb-0">Total Semua Pengeluaran</h6>
                                                <h4 class="fw-bold mb-0">Rp<?= number_format($grandTotal, 0, ',', '.') ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Expenses Table -->
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Jenis</th>
                                                <th>Nama</th>
                                                <th>Jumlah</th>
                                                <th>Keterangan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($expenses as $expense): ?>
                                                <tr>
                                                    <td><?= date('d M Y', strtotime($expense['tanggal'])) ?></td>
                                                    <td><?= htmlspecialchars($expense['jenisPengeluaran']) ?></td>
                                                    <td><?= htmlspecialchars($expense['namaPengeluaran']) ?></td>
                                                    <td>Rp<?= number_format($expense['jumlah'], 0, ',', '.') ?></td>
                                                    <td><?= !empty($expense['keterangan']) ? htmlspecialchars($expense['keterangan']) : '-' ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary edit-btn"
                                                            data-id="<?= $expense['idPengeluaran'] ?>"
                                                            data-jenis="<?= htmlspecialchars($expense['jenisPengeluaran']) ?>"
                                                            data-nama="<?= htmlspecialchars($expense['namaPengeluaran']) ?>"
                                                            data-jumlah="<?= $expense['jumlah'] ?>"
                                                            data-tanggal="<?= $expense['tanggal'] ?>"
                                                            data-keterangan="<?= htmlspecialchars($expense['keterangan'] ?? '') ?>">
                                                            Edit
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger delete-btn"
                                                            data-id="<?= $expense['idPengeluaran'] ?>">
                                                            Hapus
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
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
    <script>
        // Dynamic form
        document.getElementById('jenisPengeluaran').addEventListener('change', function() {
            const nameField = document.getElementById('namaPengeluaran');
            if (this.value !== 'Lainnya') {
                nameField.value = this.value;
            } else {
                nameField.value = '';
            }
        });

        // Trigger change event on page load
        document.getElementById('jenisPengeluaran').dispatchEvent(new Event('change'));

        // Edit button functionality
        const editButtons = document.querySelectorAll('.edit-btn');
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('editId').value = this.dataset.id;
                document.getElementById('editJenis').value = this.dataset.jenis;
                document.getElementById('editNama').value = this.dataset.nama;
                document.getElementById('editJumlah').value = this.dataset.jumlah;
                document.getElementById('editTanggal').value = this.dataset.tanggal;
                document.getElementById('editKeterangan').value = this.dataset.keterangan;

                // Trigger change for jenis select
                document.getElementById('editJenis').dispatchEvent(new Event('change'));

                editModal.show();
            });
        });

        // Delete button functionality
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const confirmDelete = document.getElementById('confirmDelete');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                confirmDelete.href = `pengeluaran.php?delete=${this.dataset.id}`;
                deleteModal.show();
            });
        });

        // Auto-fill namaPengeluaran when jenis is changed in edit modal
        document.getElementById('editJenis').addEventListener('change', function() {
            const nameField = document.getElementById('editNama');
            if (this.value !== 'Lainnya') {
                nameField.value = this.value;
            } else {
                nameField.value = '';
            }
        });
    </script>
</body>

</html>