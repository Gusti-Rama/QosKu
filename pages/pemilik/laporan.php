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

// --------- FETCH DATA FOR TRANSACTION HISTORY ----------

// Fetch all orders
$ordersQuery = "SELECT 
    p.idPemesanan,
    p.tanggalPemesanan,
    p.statusPemesanan,
    pl.namaLengkap AS namaPelanggan,
    k.nomorKamar
FROM 
    pemesanan p
JOIN 
    pelanggan pl ON p.idPelanggan = pl.idPelanggan
JOIN 
    kamar_kos k ON p.idKamar = k.idKamar
ORDER BY 
    p.tanggalPemesanan DESC
LIMIT 6";
$ordersResult = $connect->query($ordersQuery);
$allOrders = $ordersResult->fetch_all(MYSQLI_ASSOC);

// Fetch all payments
$paymentsQuery = "SELECT 
    pb.idPembayaran,
    pb.tanggalPembayaran,
    pb.metodePembayaran,
    pb.jumlahPembayaran,
    pb.statusPembayaran
FROM 
    pembayaran pb
ORDER BY 
    pb.tanggalPembayaran DESC
LIMIT 6";
$paymentsResult = $connect->query($paymentsQuery);
$allPayments = $paymentsResult->fetch_all(MYSQLI_ASSOC);

// Fetch all expenses
$expensesQuery = "SELECT 
    idPengeluaran,
    tanggal,
    namaPengeluaran,
    jenisPengeluaran,
    jumlah
FROM 
    pengeluaran
ORDER BY 
    tanggal DESC
LIMIT 6";
$expensesResult = $connect->query($expensesQuery);
$allExpenses = $expensesResult->fetch_all(MYSQLI_ASSOC);

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

                <div class="row">
                    <div class="col-md-7">
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-body p-4">
                                <h6 class="fw-semibold mb-3">Keuntungan enam bulan terakhir (dalam jutaan rupiah)</h6>
                                <div class="chart-container mb-4">
                                    <canvas id="profitChart" height="160"></canvas>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-3 mb-3">
                                        <div class="card shadow-sm border-0 rounded-4 h-100">
                                            <div class="card-body text-center p-3">
                                                <i class="bi bi-cash-coin fs-2 text-success mb-2"></i>
                                                <h6 class="fw-bold mb-1"><?= rupiah($profit) ?></h6>
                                                <small class="text-muted">Profit</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="card shadow-sm border-0 rounded-4 h-100">
                                            <div class="card-body text-center p-3">
                                                <i class="bi bi-bar-chart fs-2 text-info mb-2"></i>
                                                <h6 class="fw-bold mb-1"><?= rupiah($omset) ?></h6>
                                                <small class="text-muted">Omset</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="card shadow-sm border-0 rounded-4 h-100">
                                            <div class="card-body text-center p-3">
                                                <i class="bi bi-wallet2 fs-2 text-danger mb-2"></i>
                                                <h6 class="fw-bold mb-1"><?= rupiah($pengeluaran) ?></h6>
                                                <small class="text-muted">Pengeluaran</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="card shadow-sm border-0 rounded-4 h-100">
                                            <div class="card-body text-center p-3">
                                                <i class="bi bi-door-closed fs-2 text-primary mb-2"></i>
                                                <h6 class="fw-bold mb-1"><?= $jumlahKamar ?></h6>
                                                <small class="text-muted">Total Kamar</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-grid gap-2">
                                    <a href="export_laporan.php?type=monthly" class="btn btn-info text-white">Unduh
                                        Laporan Bulanan</a>
                                    <a href="export_laporan.php?type=yearly" class="btn btn-info text-white">Unduh
                                        Laporan Tahunan</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-semibold">Riwayat Transaksi</h5>
                                    <span class="text-<?= $profit >= 0 ? 'success' : 'danger' ?> small fw-semibold">
                                        <?= $profit >= 0 ? '+' : '' ?><?= $jumlahKamar > 0 ? round(($profit / ($omset ?: 1)) * 100) : 0 ?>%
                                        keuntungan bulan ini
                                    </span>
                                </div>
                                <ul class="list-unstyled">
                                    <?php
                                    // Combine all transactions into one array
                                    $allTransactions = [];

                                    // Add orders (pemesanan)
                                    foreach ($allOrders as $order) {
                                        $allTransactions[] = [
                                            'type' => 'order',
                                            'id' => $order['idPemesanan'],
                                            'date' => $order['tanggalPemesanan'],
                                            'description' => 'Pemesanan Kamar ' . $order['nomorKamar'],
                                            'customer' => $order['namaPelanggan'],
                                            'amount' => 0, // Orders don't have amounts in this view
                                            'status' => $order['statusPemesanan'],
                                            'icon' => 'bi-journal-text',
                                            'color' => 'primary'
                                        ];
                                    }

                                    // Add payments (pembayaran)
                                    foreach ($allPayments as $payment) {
                                        $allTransactions[] = [
                                            'type' => 'payment',
                                            'id' => $payment['idPembayaran'],
                                            'date' => $payment['tanggalPembayaran'],
                                            'description' => 'Pembayaran',
                                            'amount' => $payment['jumlahPembayaran'],
                                            'method' => $payment['metodePembayaran'],
                                            'status' => $payment['statusPembayaran'],
                                            'icon' => 'bi-cash-coin',
                                            'color' => 'success'
                                        ];
                                    }

                                    // Add expenses (pengeluaran)
                                    foreach ($allExpenses as $expense) {
                                        $allTransactions[] = [
                                            'type' => 'expense',
                                            'id' => $expense['idPengeluaran'],
                                            'date' => $expense['tanggal'],
                                            'description' => $expense['namaPengeluaran'],
                                            'amount' => -$expense['jumlah'], // Negative for expenses
                                            'category' => $expense['jenisPengeluaran'],
                                            'icon' => 'bi-wallet2',
                                            'color' => 'danger'
                                        ];
                                    }

                                    // Sort all transactions by date (newest first)
                                    usort($allTransactions, function ($a, $b) {
                                        return strtotime($b['date']) - strtotime($a['date']);
                                    });

                                    // Display only the 6 most recent transactions
                                    $recentTransactions = array_slice($allTransactions, 0, 6);

                                    foreach ($recentTransactions as $tx):
                                        $txDate = strtotime($tx['date']);
                                    ?>
                                        <li class="mb-3 d-flex align-items-start">
                                            <i class="bi <?= $tx['icon'] ?> fs-5 me-2 text-<?= $tx['color'] ?>"></i>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold d-flex justify-content-between">
                                                    <span>
                                                        <?= $tx['description'] ?>
                                                        <?php if ($tx['type'] === 'order'): ?>
                                                            (<?= $tx['customer'] ?>)
                                                        <?php endif; ?>
                                                    </span>
                                                    <?php if ($tx['type'] !== 'order'): ?>
                                                        <span class="text-<?= $tx['amount'] >= 0 ? 'success' : 'danger' ?>">
                                                            <?= rupiah(abs($tx['amount'])) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-muted small">
                                                    <?= date('d M', $txDate) ?> • <?= date('H:i', $txDate) ?>
                                                    <?php if ($tx['type'] === 'order'): ?>
                                                        • <span class="badge bg-<?=
                                                                                $tx['status'] === 'Terkonfirmasi' ? 'success' : ($tx['status'] === 'Tertunda' ? 'warning text-dark' : 'danger')
                                                                                ?>">
                                                            <?= $tx['status'] ?>
                                                        </span>
                                                    <?php elseif ($tx['type'] === 'payment'): ?>
                                                        • <?= ucfirst($tx['method']) ?>
                                                        • <span class="badge bg-<?=
                                                                                $tx['status'] === 'Lunas' ? 'success' : ($tx['status'] === 'Menunggu Konfirmasi' ? 'warning text-dark' : 'secondary')
                                                                                ?>">
                                                            <?= $tx['status'] ?>
                                                        </span>
                                                    <?php else: ?>
                                                        • <?= $tx['category'] ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <div class="text-end">
                                    <a href="riwayattransaksi.php" class="btn btn-outline-success btn-sm">Lihat Semua</a>
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
        // Profit Chart
        const ctx = document.getElementById('profitChart').getContext('2d');

        const profitChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [<?= implode(',', $chartLabels) ?>],
                datasets: [{
                    label: 'Profit (juta Rp)',
                    data: [<?= implode(',', $chartData) ?>],
                    backgroundColor: 'rgba(255, 255, 255, 1)',
                    borderColor: 'rgba(255, 255, 255, 1)',
                    borderWidth: 1,
                    borderRadius: 10,
                    borderSkipped: false,
                    barThickness: 10,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.3)',
                            drawBorder: false
                        },
                        ticks: {
                            color: 'rgba(255, 255, 255, 1)',
                            callback: function(value) {
                                return value + ' Juta';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.3)'
                        }
                    }
                },
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>