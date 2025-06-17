<?php
session_start();
require_once "../../php/connect.php";

// Cek login dan role owner
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'owner') {
    echo "<script>
        alert('Silakan login sebagai owner terlebih dahulu.');
        window.location.href='../../auth/login.php';
    </script>";
    exit;
}

// Ambil semua data pembayaran dari database
$query = "
SELECT 
    pembayaran.idPembayaran,
    pembayaran.tanggalPembayaran,
    pembayaran.statusPembayaran,
    pembayaran.jumlahPembayaran,
    pemesanan.jenis_sewa,
    pemesanan.idPemesanan,
    pelanggan.username AS namaPelanggan
FROM 
    pembayaran
JOIN 
    pemesanan ON pembayaran.idPemesanan = pemesanan.idPemesanan
JOIN 
    pelanggan ON pemesanan.idPelanggan = pelanggan.idPelanggan
ORDER BY 
    pembayaran.tanggalPembayaran DESC
";

$result = $connect->query($query);
$transactions = [];

while ($row = $result->fetch_assoc()) {
    $transactions[] = [
        'nama' => $row['namaPelanggan'],
        'jenis' => $row['jenis_sewa'],
        'idPembayaran' => $row['idPembayaran'],
        'idPemesanan' => $row['idPemesanan'],
        'tanggal' => date('d M Y', strtotime($row['tanggalPembayaran'])),
        'harga' => $row['jumlahPembayaran'],
        'status' => $row['statusPembayaran']
    ];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Semua Riwayat Pembayaran | QosKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="icon" href="../assets/img/QosKuIMG.png" type="image/png">
</head>

<body class="bg-light">
    <div class="d-flex min-vh-100 ms-4 me-4">
        <?php include '../../layout/pemilikNavbar.php'; ?>
        <div class="flex-grow-1">
            <div class="position-relative rounded-4"
                style="background-image:url('../../assets/img/backgroundProfil.png'); height:200px;background-size:cover; background-position:center;">
                <link rel="icon" href="../../assets/img/QosKuIMG.png" type="image/png">
                <?php include '../../layout/pemilikHeader.php'; ?>
            </div>

            <div class="mt-4 container-fluid">
                <div class="row">
                    <div class="col-12 bg-white rounded-4 p-4 shadow-sm">

                        <h4 class="mb-3">Riwayat Semua Pembayaran</h4>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <label for="rowsPerPage" class="form-label me-2">Show</label>
                                <select id="rowsPerPage" class="form-select form-select-sm d-inline-block" style="width:auto">
                                    <option value="5">5</option>
                                    <option value="10" selected>10</option>
                                    <option value="25">25</option>
                                </select>
                                <span class="ms-2">entries</span>
                            </div>
                            <nav aria-label="table pagination">
                                <ul id="paginationTop" class="pagination pagination-sm mb-0"></ul>
                            </nav>
                        </div>

                        <?php if (empty($transactions)): ?>
                            <div class="alert alert-info">Belum ada data pembayaran yang tersedia.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle rounded-4 shadow-sm bg-white">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama</th>
                                            <th>Jenis Sewa</th>
                                            <th>ID Pemesanan</th>
                                            <th>ID Pembayaran</th>
                                            <th>Tanggal</th>
                                            <th>Jumlah Pembayaran</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody"></tbody>
                                </table>
                            </div>
                            <nav aria-label="table pagination">
                                <ul id="paginationBottom" class="pagination pagination-sm"></ul>
                            </nav>
                        <?php endif; ?>

                        <a href="dashboardOwner.php" class="btn btn-secondary mt-4">Kembali ke Dashboard</a>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer text-center mt-5 pt-5">
        &copy; 2025, Made with ❤️ for QosKu
    </div>

    <script>
        const transactions = <?= json_encode($transactions) ?>;

        let currentPage = 1;
        let rowsPerPage = parseInt(document.getElementById('rowsPerPage').value);

        const tableBody = document.getElementById('tableBody');
        const paginationTop = document.getElementById('paginationTop');
        const paginationBottom = document.getElementById('paginationBottom');
        const rowsSelect = document.getElementById('rowsPerPage');

        function renderStatusBadge(status) {
            if (status === 'Lunas') return `<span class="badge bg-success">${status}</span>`;
            if (status === 'Pending') return `<span class="badge bg-warning text-dark">${status}</span>`;
            return `<span class="badge bg-secondary">${status}</span>`;
        }

        function renderTable() {
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            const pageItems = transactions.slice(start, end);

            tableBody.innerHTML = pageItems.map(tx => `
                <tr>
                    <td>${tx.nama}</td>
                    <td>${tx.jenis}</td>
                    <td>${tx.idPemesanan}</td>
                    <td>${tx.idPembayaran}</td>
                    <td>${tx.tanggal}</td>
                    <td>Rp ${tx.harga}</td>
                    <td>${renderStatusBadge(tx.status)}</td>
                </tr>
            `).join('');
        }

        function renderPagination(container) {
            const totalPages = Math.ceil(transactions.length / rowsPerPage);
            const pages = [];

            pages.push(`
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
                </li>`);

            for (let p = 1; p <= totalPages; p++) {
                pages.push(`
                    <li class="page-item ${p === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${p}">${p}</a>
                    </li>`);
            }

            pages.push(`
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
                </li>`);

            container.innerHTML = pages.join('');
        }

        function onPageClick(e) {
            e.preventDefault();
            const link = e.target.closest('a.page-link');
            if (!link) return;
            const page = parseInt(link.dataset.page);
            if (page >= 1 && page <= Math.ceil(transactions.length / rowsPerPage)) {
                currentPage = page;
                updateTableAndPagination();
            }
        }

        rowsSelect.addEventListener('change', () => {
            rowsPerPage = parseInt(rowsSelect.value);
            currentPage = 1;
            updateTableAndPagination();
        });

        paginationTop.addEventListener('click', onPageClick);
        paginationBottom.addEventListener('click', onPageClick);

        function updateTableAndPagination() {
            renderTable();
            renderPagination(paginationTop);
            renderPagination(paginationBottom);
        }

        updateTableAndPagination();
    </script>
</body>
</html>
