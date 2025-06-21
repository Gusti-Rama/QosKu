<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'owner') {
    echo "<script>
        alert('Silakan login sebagai owner terlebih dahulu.');
        window.location.href='../../auth/login.php';
    </script>";
    exit;
}

include '../../php/connect.php';

// Handle delete operations
if (isset($_GET['delete_pelanggan'])) {
    $id = $_GET['delete_pelanggan'];
    $query = "DELETE FROM pelanggan WHERE idPelanggan = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: listakun.php?success=pelanggan_deleted");
    exit();
}

if (isset($_GET['delete_admin'])) {
    $id = $_GET['delete_admin'];
    $query = "DELETE FROM admin WHERE idAdmin = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: listakun.php?success=admin_deleted");
    exit();
}

// Fetch pelanggan accounts
$query_pelanggan = "SELECT * FROM pelanggan ORDER BY idPelanggan DESC";
$result_pelanggan = $connect->query($query_pelanggan);

// Fetch admin accounts
$query_admin = "SELECT * FROM admin ORDER BY idAdmin DESC";
$result_admin = $connect->query($query_admin);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QosKu - List Akun</title>
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

            <div class="container-fluid p-4">
                <!-- Success Messages -->
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php 
                        switch($_GET['success']) {
                            case 'pelanggan_added':
                                echo 'Akun pelanggan berhasil ditambahkan!';
                                break;
                            case 'admin_added':
                                echo 'Akun admin berhasil ditambahkan!';
                                break;
                            case 'pelanggan_updated':
                                echo 'Akun pelanggan berhasil diperbarui!';
                                break;
                            case 'admin_updated':
                                echo 'Akun admin berhasil diperbarui!';
                                break;
                            case 'pelanggan_deleted':
                                echo 'Akun pelanggan berhasil dihapus!';
                                break;
                            case 'admin_deleted':
                                echo 'Akun admin berhasil dihapus!';
                                break;
                        }
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Error Messages -->
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php 
                        switch($_GET['error']) {
                            case 'username_exists':
                                echo 'Username sudah digunakan! Silakan pilih username lain.';
                                break;
                            case 'insert_failed':
                                echo 'Gagal menambahkan akun! Silakan coba lagi.';
                                break;
                            case 'update_failed':
                                echo 'Gagal memperbarui akun! Silakan coba lagi.';
                                break;
                            default:
                                echo 'Terjadi kesalahan! Silakan coba lagi.';
                        }
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Pelanggan Accounts Section -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-people-fill text-primary me-2"></i>
                            Daftar Akun Pelanggan
                        </h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPelangganModal">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Pelanggan
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Nama Lengkap</th>
                                        <th>Email</th>
                                        <th>No. HP</th>
                                        <th>Alamat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result_pelanggan->num_rows > 0): ?>
                                        <?php while($row = $result_pelanggan->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $row['idPelanggan'] ?></td>
                                                <td><?= htmlspecialchars($row['username']) ?></td>
                                                <td><?= htmlspecialchars($row['namaLengkap']) ?></td>
                                                <td><?= htmlspecialchars($row['email']) ?></td>
                                                <td><?= htmlspecialchars($row['nomorHp']) ?></td>
                                                <td><?= htmlspecialchars($row['alamat']) ?></td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm me-1" 
                                                            onclick="editPelanggan(<?= $row['idPelanggan'] ?>, '<?= htmlspecialchars($row['username']) ?>', '<?= htmlspecialchars($row['namaLengkap']) ?>', '<?= htmlspecialchars($row['email']) ?>', '<?= htmlspecialchars($row['nomorHp']) ?>', '<?= htmlspecialchars($row['alamat']) ?>')">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-danger btn-sm" 
                                                            onclick="deletePelanggan(<?= $row['idPelanggan'] ?>, '<?= htmlspecialchars($row['namaLengkap']) ?>')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Tidak ada data pelanggan</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Admin Accounts Section -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-shield-fill text-success me-2"></i>
                            Daftar Akun Admin
                        </h5>
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Admin
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Nama Admin</th>
                                        <th>Peran</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result_admin->num_rows > 0): ?>
                                        <?php while($row = $result_admin->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $row['idAdmin'] ?></td>
                                                <td><?= htmlspecialchars($row['username']) ?></td>
                                                <td><?= htmlspecialchars($row['namaAdmin']) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $row['peran'] === 'owner' ? 'danger' : 'primary' ?>">
                                                        <?= ucfirst($row['peran']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm me-1" 
                                                            onclick="editAdmin(<?= $row['idAdmin'] ?>, '<?= htmlspecialchars($row['username']) ?>', '<?= htmlspecialchars($row['namaAdmin']) ?>', '<?= htmlspecialchars($row['peran']) ?>')">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <?php if ($row['peran'] !== 'owner'): ?>
                                                        <button class="btn btn-danger btn-sm" 
                                                                onclick="deleteAdmin(<?= $row['idAdmin'] ?>, '<?= htmlspecialchars($row['namaAdmin']) ?>')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Tidak ada data admin</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Pelanggan Modal -->
    <div class="modal fade" id="addPelangganModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Akun Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../php/add_pelanggan.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="namaLengkap" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Pelanggan Modal -->
    <div class="modal fade" id="editPelangganModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Akun Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../php/update_pelanggan.php" method="POST">
                    <input type="hidden" name="idPelanggan" id="editPelangganId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" id="editPelangganUsername" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                            <input type="password" class="form-control" name="password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="namaLengkap" id="editPelangganNama" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="editPelangganEmail" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. HP</label>
                            <input type="text" class="form-control" name="nomorHp" id="editPelangganHp" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control" name="alamat" id="editPelangganAlamat" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Admin Modal -->
    <div class="modal fade" id="addAdminModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Akun Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../php/add_admin.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Admin</label>
                            <input type="text" class="form-control" name="namaAdmin" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Peran</label>
                            <select class="form-select" name="peran" required>
                                <option value="admin">Admin</option>
                                <option value="owner">Owner</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Admin Modal -->
    <div class="modal fade" id="editAdminModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Akun Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../php/update_admin.php" method="POST">
                    <input type="hidden" name="idAdmin" id="editAdminId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" id="editAdminUsername" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                            <input type="password" class="form-control" name="password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Admin</label>
                            <input type="text" class="form-control" name="namaAdmin" id="editAdminNama" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Peran</label>
                            <select class="form-select" name="peran" id="editAdminPeran" required>
                                <option value="admin">Admin</option>
                                <option value="owner">Owner</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="footer text-center mt-5 pt-5">
        &copy; 2025, Made with ❤️ for QosKu
    </div>

    <script>
        function editPelanggan(id, username, nama, email, hp, alamat) {
            document.getElementById('editPelangganId').value = id;
            document.getElementById('editPelangganUsername').value = username;
            document.getElementById('editPelangganNama').value = nama;
            document.getElementById('editPelangganEmail').value = email;
            document.getElementById('editPelangganHp').value = hp;
            document.getElementById('editPelangganAlamat').value = alamat;
            
            new bootstrap.Modal(document.getElementById('editPelangganModal')).show();
        }

        function editAdmin(id, username, nama, peran) {
            document.getElementById('editAdminId').value = id;
            document.getElementById('editAdminUsername').value = username;
            document.getElementById('editAdminNama').value = nama;
            document.getElementById('editAdminPeran').value = peran;
            
            new bootstrap.Modal(document.getElementById('editAdminModal')).show();
        }

        function deletePelanggan(id, nama) {
            if (confirm(`Apakah Anda yakin ingin menghapus akun pelanggan "${nama}"?`)) {
                window.location.href = `listakun.php?delete_pelanggan=${id}`;
            }
        }

        function deleteAdmin(id, nama) {
            if (confirm(`Apakah Anda yakin ingin menghapus akun admin "${nama}"?`)) {
                window.location.href = `listakun.php?delete_admin=${id}`;
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>