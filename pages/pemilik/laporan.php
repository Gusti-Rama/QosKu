<?php
session_start();
require_once "../../php/connect.php";

// Cek login dan role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'owner') {
    echo "<script>
        alert('Silakan login sebagai owner terlebih dahulu.');
        window.location.href='../../auth/login.php';
    </script>";
    exit;
}

// --------- SIMULASI DATA (ganti dengan query SQL sesuai database kamu) ----------
$jumlahKamar = 30;
$selisihKamar = 3;
$profit = 10200000;
$omset = 13000000;
$pengeluaran = 2800000;

// --------- RIWAYAT TRANSAKSI ----------
$queryPembayaran = "
SELECT 
    pembayaran.idPembayaran,
    pembayaran.tanggalPembayaran,
    'Pembayaran Diterima' AS jenis
FROM pembayaran
ORDER BY tanggalPembayaran DESC
LIMIT 4
";
$resultPembayaran = $connect->query($queryPembayaran);
$transaksi = [];
while ($row = $resultPembayaran->fetch_assoc()) {
    $waktu = strtotime($row['tanggalPembayaran']);
    $transaksi[] = [
        'jenis' => 'Pembayaran Diterima',
        'ikon' => 'bi-cash-coin text-warning',
        'id' => $row['idPembayaran'],
        'tanggal' => date('d M', $waktu),
        'jam' => date('H:i', $waktu)
    ];
}
$transaksi[] = ['jenis' => 'Tagihan Listrik','ikon' => 'bi-lightning-charge text-primary','id' => '', 'tanggal' => '21 Apr', 'jam' => '13:28'];
$transaksi[] = ['jenis' => 'Pemesanan Kamar','ikon' => 'bi-bell text-success','id' => 'PES58', 'tanggal' => '20 Apr','jam' => '18:35'];
$transaksi[] = ['jenis' => 'Pemesanan Kamar','ikon' => 'bi-bell text-success','id' => 'PES57', 'tanggal' => '20 Apr','jam' => '16:41'];
$transaksi = array_slice($transaksi, 0, 6);

function rupiah($angka) {
    return 'Rp' . number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Owner - QosKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body style="background-color: #f8f9fa;">
<div class="container mt-5">
    <div class="row g-4 justify-content-center">

        <!-- Statistik Keuangan -->
        <div class="col-md-7 bg-white rounded-4 p-4 shadow-sm">
            <h6 class="fw-semibold mb-3">Keuntungan enam bulan terakhir (dalam jutaan rupiah)</h6>
            <div style="background: linear-gradient(to right, #0f2027, #203a43, #2c5364); border-radius: 15px; height: 160px;" class="mb-4 d-flex align-items-center justify-content-around text-white">
                <div style="height: 100px; width: 10px; background: white; border-radius: 4px;"></div>
                <div style="height: 140px; width: 10px; background: white; border-radius: 4px;"></div>
                <div style="height: 100px; width: 10px; background: white; border-radius: 4px;"></div>
                <div style="height: 120px; width: 10px; background: white; border-radius: 4px;"></div>
                <div style="height: 160px; width: 10px; background: white; border-radius: 4px;"></div>
            </div>
            <p class="fw-semibold mb-1">Kamar Terpakai</p>
            <p class="text-success small mb-3">(<?= $selisihKamar >= 0 ? '+' : '' ?><?= $selisihKamar ?>) dari bulan lalu</p>

            <div class="row text-center mb-4">
                <div class="col"><i class="bi bi-cash-coin me-1 text-success"></i><br><span class="fw-bold"><?= rupiah($profit) ?></span><br><span class="text-muted small">Profit</span></div>
                <div class="col"><i class="bi bi-bar-chart me-1 text-info"></i><br><span class="fw-bold"><?= rupiah($omset) ?></span><br><span class="text-muted small">Omset</span></div>
                <div class="col"><i class="bi bi-wallet2 me-1 text-danger"></i><br><span class="fw-bold"><?= rupiah($pengeluaran) ?></span><br><span class="text-muted small">Pengeluaran</span></div>
                <div class="col"><i class="bi bi-door-closed me-1 text-primary"></i><br><span class="fw-bold"><?= $jumlahKamar ?></span><br><span class="text-muted small">Total Kamar</span></div>
            </div>

            <div class="d-grid gap-2">
                <button class="btn btn-info text-white">Unduh Laporan Bulanan</button>
                <button class="btn btn-info text-white">Unduh Laporan Tahunan</button>
            </div>
        </div>

        <!-- Riwayat Transaksi -->
        <div class="col-md-5 bg-white rounded-4 p-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-semibold">Riwayat Transaksi</h5>
                <span class="text-success small fw-semibold">+15% keuntungan bulan ini</span>
            </div>
            <ul class="list-unstyled">
                <?php foreach ($transaksi as $item): ?>
                    <li class="mb-3 d-flex align-items-start">
                        <i class="bi <?= $item['ikon'] ?> fs-5 me-2"></i>
                        <div>
                            <div class="fw-semibold">
                                <?= $item['jenis'] ?>
                                <?php if ($item['id']) echo "#{$item['id']}"; ?>
                            </div>
                            <div class="text-muted small"><?= $item['tanggal'] ?> <?= $item['jam'] ?></div>
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
</body>
</html>
