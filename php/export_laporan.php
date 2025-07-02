<?php
session_start();
require_once "connect.php";

// Check login and role
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'owner' && $_SESSION['role'] !== 'admin')) {
    header("Location: ../pages/login.php");
    exit;
}

// Get report type (monthly or yearly)
$type = isset($_GET['type']) ? $_GET['type'] : 'monthly';

// Set filename and headers based on type
if ($type === 'monthly') {
    $filename = 'Laporan_Bulanan_QosKu_' . date('F_Y') . '.xls';
    $title = 'Laporan Bulanan';
    $period = date('F Y');
} else {
    $filename = 'Laporan_Tahunan_QosKu_' . date('Y') . '.xls';
    $title = 'Laporan Tahunan';
    $period = date('Y');
}

// Set headers for Excel download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");

// Query data based on report type
if ($type === 'monthly') {
    // Monthly report data
    $currentMonth = date('m');
    $currentYear = date('Y');

    // Income data
    $incomeQuery = "SELECT 
        pb.idPembayaran,
        pb.tanggalPembayaran,
        pl.namaLengkap AS pelanggan,
        pb.jumlahPembayaran,
        pb.metodePembayaran
    FROM pembayaran pb
    JOIN pemesanan p ON pb.idPemesanan = p.idPemesanan
    JOIN pelanggan pl ON p.idPelanggan = pl.idPelanggan
    WHERE pb.statusPembayaran = 'Lunas'
    AND MONTH(pb.tanggalPembayaran) = $currentMonth
    AND YEAR(pb.tanggalPembayaran) = $currentYear
    ORDER BY pb.tanggalPembayaran";

    // Expense data
    $expenseQuery = "SELECT 
        idPengeluaran,
        tanggal,
        namaPengeluaran,
        jenisPengeluaran,
        jumlah
    FROM pengeluaran
    WHERE MONTH(tanggal) = $currentMonth
    AND YEAR(tanggal) = $currentYear
    ORDER BY tanggal";
} else {
    // Yearly report data
    $currentYear = date('Y');

    // Income data (grouped by month)
    $incomeQuery = "SELECT 
        MONTH(pb.tanggalPembayaran) AS bulan,
        SUM(pb.jumlahPembayaran) AS total
    FROM pembayaran pb
    WHERE pb.statusPembayaran = 'Lunas'
    AND YEAR(pb.tanggalPembayaran) = $currentYear
    GROUP BY MONTH(pb.tanggalPembayaran)
    ORDER BY bulan";

    // Expense data (grouped by month)
    $expenseQuery = "SELECT 
        MONTH(tanggal) AS bulan,
        SUM(jumlah) AS total
    FROM pengeluaran
    WHERE YEAR(tanggal) = $currentYear
    GROUP BY MONTH(tanggal)
    ORDER BY bulan";
}

// Execute queries
$incomeResult = $connect->query($incomeQuery);
$expenseResult = $connect->query($expenseQuery);

// Calculate totals
$totalIncome = 0;
$totalExpense = 0;

if ($type === 'monthly') {
    // For monthly report, get detailed transactions
    $incomeData = $incomeResult->fetch_all(MYSQLI_ASSOC);
    $expenseData = $expenseResult->fetch_all(MYSQLI_ASSOC);

    foreach ($incomeData as $row) {
        $totalIncome += $row['jumlahPembayaran'];
    }

    foreach ($expenseData as $row) {
        $totalExpense += $row['jumlah'];
    }
} else {
    // For yearly report, get monthly summaries
    $incomeData = [];
    while ($row = $incomeResult->fetch_assoc()) {
        $incomeData[$row['bulan']] = $row['total'];
        $totalIncome += $row['total'];
    }

    $expenseData = [];
    while ($row = $expenseResult->fetch_assoc()) {
        $expenseData[$row['bulan']] = $row['total'];
        $totalExpense += $row['total'];
    }
}

$profit = $totalIncome - $totalExpense;

// Start output
echo "<html>";
echo "<meta charset='UTF-8'>";
echo "<table border='1'>";
echo "<tr><th colspan='4' style='text-align:center;font-size:16px;background-color:#f2f2f2;'>$title - QosKu</th></tr>";
echo "<tr><th colspan='4' style='text-align:center;'>Periode: $period</th></tr>";
echo "<tr><td colspan='4'></td></tr>";

// Summary section
echo "<tr><th colspan='4' style='text-align:center;background-color:#e6e6e6;'>Ringkasan</th></tr>";
echo "<tr>";
echo "<th style='background-color:#f2f2f2;'>Total Pemasukan</th>";
echo "<td>" . number_format($totalIncome, 0, ',', '.') . "</td>";
echo "<th style='background-color:#f2f2f2;'>Total Pengeluaran</th>";
echo "<td>" . number_format($totalExpense, 0, ',', '.') . "</td>";
echo "</tr>";
echo "<tr>";
echo "<th style='background-color:#f2f2f2;'>Keuntungan</th>";
echo "<td colspan='3'>" . number_format($profit, 0, ',', '.') . "</td>";
echo "</tr>";
echo "<tr><td colspan='4'></td></tr>";

if ($type === 'monthly') {
    // Monthly report details
    echo "<tr><th colspan='4' style='text-align:center;background-color:#e6e6e6;'>Detail Pemasukan</th></tr>";
    echo "<tr><th>Tanggal</th><th>Pelanggan</th><th>Jumlah</th><th>Metode</th></tr>";

    foreach ($incomeData as $row) {
        echo "<tr>";
        echo "<td>" . date('d/m/Y', strtotime($row['tanggalPembayaran'])) . "</td>";
        echo "<td>" . $row['pelanggan'] . "</td>";
        echo "<td>" . number_format($row['jumlahPembayaran'], 0, ',', '.') . "</td>";
        echo "<td>" . $row['metodePembayaran'] . "</td>";
        echo "</tr>";
    }

    echo "<tr><td colspan='4'></td></tr>";
    echo "<tr><th colspan='4' style='text-align:center;background-color:#e6e6e6;'>Detail Pengeluaran</th></tr>";
    echo "<tr><th>Tanggal</th><th>Keterangan</th><th>Jenis</th><th>Jumlah</th></tr>";

    foreach ($expenseData as $row) {
        echo "<tr>";
        echo "<td>" . date('d/m/Y', strtotime($row['tanggal'])) . "</td>";
        echo "<td>" . $row['namaPengeluaran'] . "</td>";
        echo "<td>" . $row['jenisPengeluaran'] . "</td>";
        echo "<td>" . number_format($row['jumlah'], 0, ',', '.') . "</td>";
        echo "</tr>";
    }
} else {
    // Yearly report details
    echo "<tr><th colspan='4' style='text-align:center;background-color:#e6e6e6;'>Pemasukan per Bulan</th></tr>";
    echo "<tr><th>Bulan</th><th>Total Pemasukan</th><th>Total Pengeluaran</th><th>Keuntungan</th></tr>";

    for ($month = 1; $month <= 12; $month++) {
        $monthName = date('F', mktime(0, 0, 0, $month, 1));
        $monthIncome = isset($incomeData[$month]) ? $incomeData[$month] : 0;
        $monthExpense = isset($expenseData[$month]) ? $expenseData[$month] : 0;
        $monthProfit = $monthIncome - $monthExpense;

        echo "<tr>";
        echo "<td>" . $monthName . "</td>";
        echo "<td>" . number_format($monthIncome, 0, ',', '.') . "</td>";
        echo "<td>" . number_format($monthExpense, 0, ',', '.') . "</td>";
        echo "<td>" . number_format($monthProfit, 0, ',', '.') . "</td>";
        echo "</tr>";
    }
}

echo "</table>";
echo "</html>";
exit;
