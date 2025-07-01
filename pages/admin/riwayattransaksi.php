<?php
session_start();
require_once "../../php/connect.php";

if (!isset($_SESSION['role'])) {
  header(header: "Location: ../../pages/login.php?pesan=not_logged_in");
  exit;
}

$peran = strtolower($_SESSION['role']);

$diperbolehkan = ['admin'];

// cek peran usernya
if (!in_array($peran, $diperbolehkan)) {
  header("Location: ../../pages/login.php?pesan=Akses_Ditolak");
  exit;
}

// Fetch all orders with related information
$ordersQuery = "
SELECT 
    p.idPemesanan,
    p.tanggalPemesanan,
    p.lamaSewa,
    p.jenis_sewa,
    p.harga_per_periode,
    p.totalHarga,
    p.statusPemesanan,
    p.tanggal_mulai,
    p.tanggal_selesai,
    p.jenisPemesanan,
    pl.namaLengkap AS namaPelanggan,
    pl.nomorHp,
    k.nomorKamar,
    k.tipeKamar,
    k.harga AS hargaKamar
FROM 
    pemesanan p
JOIN 
    pelanggan pl ON p.idPelanggan = pl.idPelanggan
JOIN 
    kamar_kos k ON p.idKamar = k.idKamar
ORDER BY 
    p.tanggalPemesanan DESC
";

$ordersResult = $connect->query($ordersQuery);
$allOrders = [];

while ($row = $ordersResult->fetch_assoc()) {
    $allOrders[] = $row;
}

// Fetch all payments
$paymentsQuery = "
SELECT 
    pb.idPembayaran,
    pb.tanggalPembayaran,
    pb.metodePembayaran,
    pb.jumlahPembayaran,
    pb.statusPembayaran,
    pb.bukti_transfer,
    pb.idPemesanan,
    pl.namaLengkap AS namaPelanggan,
    k.nomorKamar
FROM 
    pembayaran pb
LEFT JOIN 
    pemesanan p ON pb.idPemesanan = p.idPemesanan
LEFT JOIN 
    pelanggan pl ON p.idPelanggan = pl.idPelanggan
LEFT JOIN 
    kamar_kos k ON p.idKamar = k.idKamar
ORDER BY 
    pb.tanggalPembayaran DESC
";

$paymentsResult = $connect->query($paymentsQuery);
$allPayments = [];

while ($row = $paymentsResult->fetch_assoc()) {
    $allPayments[] = $row;
}

// Fetch all expenses
$expensesQuery = "
SELECT 
    idPengeluaran,
    tanggal,
    namaPengeluaran,
    keterangan,
    jenisPengeluaran,
    jumlah
FROM 
    pengeluaran
ORDER BY 
    tanggal DESC
";

$expensesResult = $connect->query($expensesQuery);
$allExpenses = [];

while ($row = $expensesResult->fetch_assoc()) {
    $allExpenses[] = $row;
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
    <title>Laporan Lengkap - QosKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="icon" href="../../assets/img/QosKuIMG.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">
    <div class="d-flex min-vh-100 ms-4 me-4">
        <?php include '../../layout/adminNavbar.php'; ?>

        <div class="flex-grow-1">
            <?php include '../../layout/adminHeader.php'; ?>

            <div class="container-fluid pt-4 pb-3">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-body">
                                <h4 class="mb-4">Laporan Lengkap</h4>

                                <!-- Orders Tab -->
                                <div class="mb-5">
                                    <h5 class="mb-3">Data Pemesanan</h5>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle rounded-4 shadow-sm bg-white">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Tanggal</th>
                                                    <th>Pelanggan</th>
                                                    <th>Kamar</th>
                                                    <th>Jenis Sewa</th>
                                                    <th>Lama</th>
                                                    <th>Total</th>
                                                    <th>Status</th>
                                                    <th>Periode</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($allOrders as $order): ?>
                                                    <tr>
                                                        <td><?= $order['idPemesanan'] ?></td>
                                                        <td><?= date('d M Y', strtotime($order['tanggalPemesanan'])) ?></td>
                                                        <td><?= $order['namaPelanggan'] ?><br><small><?= $order['nomorHp'] ?></small></td>
                                                        <td>Kamar <?= $order['nomorKamar'] ?> (<?= $order['tipeKamar'] ?>)</td>
                                                        <td><?= ucfirst($order['jenis_sewa']) ?></td>
                                                        <td><?= $order['lamaSewa'] ?> <?= $order['jenis_sewa'] ?></td>
                                                        <td><?= rupiah($order['totalHarga']) ?></td>
                                                        <td>
                                                            <span class="badge bg-<?=
                                                                                    $order['statusPemesanan'] === 'Terkonfirmasi' ? 'success' : ($order['statusPemesanan'] === 'Tertunda' ? 'warning text-dark' : 'danger')
                                                                                    ?>">
                                                                <?= $order['statusPemesanan'] ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?= $order['tanggal_mulai'] ? date('d M Y', strtotime($order['tanggal_mulai'])) : '-' ?>
                                                            <?= $order['tanggal_selesai'] && $order['tanggal_selesai'] != '1970-01-01' ? ' - ' . date('d M Y', strtotime($order['tanggal_selesai'])) : '' ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Payments Tab -->
                                <div class="mb-5">
                                    <h5 class="mb-3">Data Pembayaran</h5>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle rounded-4 shadow-sm bg-white">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Tanggal</th>
                                                    <th>ID Pemesanan</th>
                                                    <th>Pelanggan</th>
                                                    <th>Kamar</th>
                                                    <th>Metode</th>
                                                    <th>Jumlah</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($allPayments as $payment): ?>
                                                    <tr>
                                                        <td><?= $payment['idPembayaran'] ?></td>
                                                        <td><?= date('d M Y', strtotime($payment['tanggalPembayaran'])) ?></td>
                                                        <td><?= $payment['idPemesanan'] ?></td>
                                                        <td><?= $payment['namaPelanggan'] ?? '-' ?></td>
                                                        <td><?= isset($payment['nomorKamar']) ? 'Kamar ' . $payment['nomorKamar'] : '-' ?></td>
                                                        <td><?= ucfirst($payment['metodePembayaran']) ?></td>
                                                        <td><?= rupiah($payment['jumlahPembayaran']) ?></td>
                                                        <td>
                                                            <span class="badge bg-<?=
                                                                                    $payment['statusPembayaran'] === 'Lunas' ? 'success' : ($payment['statusPembayaran'] === 'Menunggu Konfirmasi' ? 'warning text-dark' : 'secondary')
                                                                                    ?>">
                                                                <?= $payment['statusPembayaran'] ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Expenses Tab -->
                                <div class="mb-5">
                                    <h5 class="mb-3">Data Pengeluaran</h5>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle rounded-4 shadow-sm bg-white">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Tanggal</th>
                                                    <th>Nama</th>
                                                    <th>Jenis</th>
                                                    <th>Keterangan</th>
                                                    <th>Jumlah</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($allExpenses as $expense): ?>
                                                    <tr>
                                                        <td><?= $expense['idPengeluaran'] ?></td>
                                                        <td><?= date('d M Y', strtotime($expense['tanggal'])) ?></td>
                                                        <td><?= $expense['namaPengeluaran'] ?></td>
                                                        <td><?= $expense['jenisPengeluaran'] ?></td>
                                                        <td><?= $expense['keterangan'] ?? '-' ?></td>
                                                        <td><?= rupiah($expense['jumlah']) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <a href="laporan.php" class="btn btn-secondary mt-4">Kembali</a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>