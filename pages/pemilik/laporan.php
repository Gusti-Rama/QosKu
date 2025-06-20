<?php
session_start();
require_once "../../php/connect.php";

// Check login and role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'owner') {
    echo "<script>
        alert('Silakan login sebagai owner terlebih dahulu.');
        window.location.href='../../auth/login.php';
    </script>";
    exit;
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

function rupiah($angka) {
    return 'Rp' . number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="en">
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
                    <!-- Omset Card -->
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="card-title fs-6 fw-bold text-secondary">Omset Bulan ini</p>
                                    <p class="card-text fs-3 fw-bold mb-0"><?= rupiah($omset) ?></p>
                                </div>
                                <div class="text-white rounded-4 d-flex align-items-center justify-content-center"
                                    style="width: 60px; height: 60px; background-color: #4FD1C5;">
                                    <i class="bi bi-receipt-cutoff fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kamar Terpakai Card -->
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="card-title fs-6 fw-bold text-secondary">Kamar Terpakai</p>
                                    <p class="card-text fs-4 fw-bold mb-0"><?= $kamarTerpakai ?>/<?= $jumlahKamar ?></p>
                                    <small class="text-<?= $selisihKamar >= 0 ? 'success' : 'danger' ?>">
                                        (<?= $selisihKamar >= 0 ? '+' : '' ?><?= $selisihKamar ?>) dari bulan lalu
                                    </small>
                                </div>
                                <div class="text-white rounded-4 d-flex align-items-center justify-content-center"
                                    style="width: 60px; height: 60px; background-color: #4FD1C5;">
                                    <i class="bi bi-door-closed-fill fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pengeluaran Card -->
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

                <div class="row g-2 justify-content-center">
                    <!-- Statistik Keuangan -->
                    <div class="col-md-7 bg-white rounded-4 p-4 shadow-sm">
                        <h6 class="fw-semibold mb-3">Keuntungan enam bulan terakhir (dalam jutaan rupiah)</h6>
                        <div class="mb-4">
                            <canvas id="profitChart" height="160"></canvas>
                        </div>
                        <p class="fw-semibold mb-1">Kamar Terpakai</p>
                        <p class="text-<?= $selisihKamar >= 0 ? 'success' : 'danger' ?> small mb-3">
                            (<?= $selisihKamar >= 0 ? '+' : '' ?><?= $selisihKamar ?>) dari bulan lalu</p>

                        <div class="row text-center mb-4">
                            <div class="col"><i class="bi bi-cash-coin me-1 text-success"></i><br><span
                                    class="fw-bold"><?= rupiah($profit) ?></span><br><span
                                    class="text-muted small">Profit</span></div>
                            <div class="col"><i class="bi bi-bar-chart me-1 text-info"></i><br><span
                                    class="fw-bold"><?= rupiah($omset) ?></span><br><span
                                    class="text-muted small">Omset</span></div>
                            <div class="col"><i class="bi bi-wallet2 me-1 text-danger"></i><br><span
                                    class="fw-bold"><?= rupiah($pengeluaran) ?></span><br><span
                                    class="text-muted small">Pengeluaran</span></div>
                            <div class="col"><i class="bi bi-door-closed me-1 text-primary"></i><br><span
                                    class="fw-bold"><?= $jumlahKamar ?></span><br><span class="text-muted small">Total
                                    Kamar</span></div>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="export_laporan.php?type=monthly" class="btn btn-info text-white">Unduh Laporan Bulanan</a>
                            <a href="export_laporan.php?type=yearly" class="btn btn-info text-white">Unduh Laporan Tahunan</a>
                        </div>
                    </div>

                    <!-- Riwayat Transaksi -->
                    <div class="col-md-5 bg-white rounded-4 p-4 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-semibold">Riwayat Transaksi</h5>
                            <span class="text-<?= $profit >= 0 ? 'success' : 'danger' ?> small fw-semibold">
                                <?= $profit >= 0 ? '+' : '' ?><?= $jumlahKamar > 0 ? round(($profit / ($omset ?: 1)) * 100) : 0 ?>% keuntungan bulan ini
                            </span>
                        </div>
                        <ul class="list-unstyled">
                            <?php foreach ($transaksi as $item): ?>
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="bi <?= $item['ikon'] ?> fs-5 me-2"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold d-flex justify-content-between">
                                            <span>
                                                <?= $item['jenis'] ?>
                                                <?php if ($item['id']) echo "#{$item['id']}"; ?>
                                            </span>
                                            <span class="text-success"><?= rupiah($item['jumlah']) ?></span>
                                        </div>
                                        <div class="text-muted small"><?= $item['tanggal'] ?> • <?= $item['jam'] ?></div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="text-end">
                            <a href="riwayatPembayaran.php" class="btn btn-outline-success btn-sm">Lihat Semua</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Profit Chart
        const ctx = document.getElementById('profitChart').getContext('2d');
        const profitChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [<?= implode(',', $chartLabels) ?>],
                datasets: [{
                    label: 'Profit (juta Rp)',
                    data: [<?= implode(',', $chartData) ?>],
                    backgroundColor: [
                        'rgba(79, 209, 197, 0.7)',
                        'rgba(79, 209, 197, 0.7)',
                        'rgba(79, 209, 197, 0.7)',
                        'rgba(79, 209, 197, 0.7)',
                        'rgba(79, 209, 197, 0.7)',
                        'rgba(79, 209, 197, 0.7)'
                    ],
                    borderColor: [
                        'rgba(79, 209, 197, 1)',
                        'rgba(79, 209, 197, 1)',
                        'rgba(79, 209, 197, 1)',
                        'rgba(79, 209, 197, 1)',
                        'rgba(79, 209, 197, 1)',
                        'rgba(79, 209, 197, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value + ' jt';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>