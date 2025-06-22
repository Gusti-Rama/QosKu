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

// Get current month and year
$currentMonth = date('m');
$currentYear = date('Y');

// --------- DASHBOARD STATISTICS ----------

// Total rooms
$jumlahKamarQuery = "SELECT COUNT(*) as total FROM kamar_kos";
$jumlahKamarResult = $connect->query($jumlahKamarQuery);
$jumlahKamar = $jumlahKamarResult->fetch_assoc()['total'];

// Occupied rooms
$kamarTerpakaiQuery = "SELECT COUNT(*) as occupied FROM pemesanan 
                      WHERE statusPemesanan = 'Terkonfirmasi' 
                      AND (tanggal_mulai <= CURDATE() AND (tanggal_selesai >= CURDATE() OR tanggal_selesai = '1970-01-01'))";
$kamarTerpakaiResult = $connect->query($kamarTerpakaiQuery);
$kamarTerpakai = $kamarTerpakaiResult->fetch_assoc()['occupied'];

// Monthly income (omset)
$omsetQuery = "SELECT SUM(jumlahPembayaran) as total FROM pembayaran 
              WHERE statusPembayaran = 'Lunas' 
              AND MONTH(tanggalPembayaran) = $currentMonth 
              AND YEAR(tanggalPembayaran) = $currentYear";
$omsetResult = $connect->query($omsetQuery);
$omset = $omsetResult->fetch_assoc()['total'] ?? 0;

// Monthly expenses (pengeluaran)
$pengeluaranQuery = "SELECT SUM(jumlah) as total FROM pengeluaran 
                    WHERE MONTH(tanggal) = $currentMonth 
                    AND YEAR(tanggal) = $currentYear";
$pengeluaranResult = $connect->query($pengeluaranQuery);
$pengeluaran = $pengeluaranResult->fetch_assoc()['total'] ?? 0;

// Calculate profit
$profit = $omset - $pengeluaran;

// Room change from last month
$lastMonthKamarQuery = "SELECT COUNT(*) as last_month FROM pemesanan 
                       WHERE statusPemesanan = 'Terkonfirmasi'
                       AND MONTH(tanggalPemesanan) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                       AND YEAR(tanggalPemesanan) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
$lastMonthKamarResult = $connect->query($lastMonthKamarQuery);
$lastMonthKamar = $lastMonthKamarResult->fetch_assoc()['last_month'];
$selisihKamar = $kamarTerpakai - $lastMonthKamar;

// --------- LAST 6 MONTHS PROFIT DATA FOR CHART ----------
$profitHistoryQuery = "SELECT 
    YEAR(tanggalPembayaran) as year,
    MONTH(tanggalPembayaran) as month,
    SUM(p.jumlahPembayaran) as income,
    (SELECT IFNULL(SUM(pe.jumlah), 0) FROM pengeluaran pe 
     WHERE MONTH(pe.tanggal) = MONTH(p.tanggalPembayaran) 
     AND YEAR(pe.tanggal) = YEAR(p.tanggalPembayaran)) as expenses,
    (SUM(p.jumlahPembayaran) - (SELECT IFNULL(SUM(pe.jumlah), 0) FROM pengeluaran pe 
     WHERE MONTH(pe.tanggal) = MONTH(p.tanggalPembayaran) 
     AND YEAR(pe.tanggal) = YEAR(p.tanggalPembayaran))) as profit
FROM pembayaran p
WHERE p.statusPembayaran = 'Lunas'
AND p.tanggalPembayaran >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
GROUP BY YEAR(tanggalPembayaran), MONTH(tanggalPembayaran)
ORDER BY year, month";

$profitHistoryResult = $connect->query($profitHistoryQuery);
$chartLabels = [];
$chartData = [];

while ($row = $profitHistoryResult->fetch_assoc()) {
    $monthName = date('M', mktime(0, 0, 0, $row['month'], 1));
    $chartLabels[] = "'$monthName'";
    $chartData[] = $row['profit'] / 1000000; // Convert to millions
}

// --------- RECENT TRANSACTIONS ----------
$queryPembayaran = "
SELECT 
    pembayaran.idPembayaran,
    pembayaran.tanggalPembayaran,
    'Pembayaran Diterima' AS jenis,
    pembayaran.jumlahPembayaran
FROM pembayaran
WHERE pembayaran.statusPembayaran = 'Lunas'
ORDER BY tanggalPembayaran DESC
LIMIT 6
";
$resultPembayaran = $connect->query($queryPembayaran);
$transaksi = [];

while ($row = $resultPembayaran->fetch_assoc()) {
    $waktu = strtotime($row['tanggalPembayaran']);
    $transaksi[] = [
        'jenis' => 'Pembayaran Diterima',
        'ikon' => 'bi-cash-coin text-warning',
        'id' => $row['idPembayaran'],
        'jumlah' => $row['jumlahPembayaran'],
        'tanggal' => date('d M', $waktu),
        'jam' => date('H:i', $waktu)
    ];
}
function rupiah($angka)
{
    return 'Rp' . number_format($angka, 0, ',', '.');
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - QosKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="icon" href="../../assets/img/QosKuIMG.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-light">
    <div class="d-flex min-vh-100 ms-4 me-4">
        <?php include '../../layout/pemilikNavbar.php'; ?>

        <div class="flex-grow-1">
            <?php include '../../layout/pemilikHeader.php'; ?>

            <div class="container-fluid pt-4 pb-3">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="card-title fs-6 fw-bold text-secondary">Omset Bulan ini</p>
                                    <p class="card-text fs-4 fw-bold mb-0"><?= rupiah($omset) ?></p>
                                </div>
                                <div class="text-white rounded-4 d-flex align-items-center justify-content-center"
                                    style="width: 60px; height: 60px; background-color: #4FD1C5;">
                                    <i class="bi bi-receipt-cutoff fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="card-title fs-6 fw-bold text-secondary">Kamar Terpakai</p>
                                    <p class="card-text fs-4 fw-bold mb-0">
                                        <?= $kamarTerpakai ?>/<?= $jumlahKamar ?><small
                                            class="text-<?= $selisihKamar >= 0 ? 'success' : 'danger' ?> fs-6">
                                            (<?= $selisihKamar >= 0 ? '+' : '' ?><?= $selisihKamar ?>) dari bulan lalu
                                        </small></p>
                                </div>
                                <div class="text-white rounded-4 d-flex align-items-center justify-content-center"
                                    style="width: 60px; height: 60px; background-color: #4FD1C5;">
                                    <i class="bi bi-door-closed-fill fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="card-title fs-6 fw-bold text-secondary">Pengeluaran Bulan ini</p>
                                    <p class="card-text fs-4 fw-bold mb-0"><?= rupiah($pengeluaran) ?></p>
                                </div>
                                <div class="text-white rounded-4 d-flex align-items-center justify-content-center"
                                    style="width: 60px; height: 60px; background-color: #4FD1C5;">
                                    <i class="bi bi-plus-square-fill fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
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