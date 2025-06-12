<?php
session_start(); // WAJIB agar session bisa digunakan
include "../../php/connect.php"; // Ganti path sesuai letak koneksi kamu

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pelanggan') {
    echo "<script>
        alert('Silakan login sebagai pelanggan terlebih dahulu.');
        window.location.href = '../../auth/login.php'; // sesuaikan path
    </script>";
    exit;
}

if (isset($_POST['submit'])) {
    $tanggalPembayaran = $_POST['tanggalPembayaran'];
    $lamasewa = $_POST['lamasewa'];
    $metodePembayaran = $_POST['metodePembayaran'];
    $jumlahPembayaran = $_POST['jumlahPembayaran'];
    $statusPembayaran = 'Belum Lunas';

    // Ambil dari session
    if (!isset($_SESSION['idPelanggan'])) {
        echo "<script>alert('Anda belum login!'); window.location.href = '../../login.php';</script>";
        exit;
    }

    $idPelanggan = $_SESSION['idPelanggan'];

    // Query insert
    $query = "INSERT INTO pembayaran (tanggalPembayaran, lamasewa, metodePembayaran, jumlahPembayaran, statusPembayaran, idPelanggan) 
              VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, 'sssisi', $tanggalPembayaran, $lamasewa, $metodePembayaran, $jumlahPembayaran, $statusPembayaran, $idPelanggan);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Pembayaran berhasil!'); window.location.href='detailkamar.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan pembayaran. Cek kembali data.');</script>";
    }

    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Form Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .form-card {
            max-width: 700px;
            margin: 50px auto;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .form-header {
            background-color: #2dd4bf;
            color: white;
            padding: 20px;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }

        .form-footer {
            padding: 20px;
            text-align: right;
        }

        .btn-submit {
            background-color: #2dd4bf;
            border: none;
        }

        .btn-submit:hover {
            background-color: #1cb3a5;
        }
    </style>
</head>

<body>

    <div class="card form-card">
        <div class="form-header">
            <h4 class="mb-0">Form Pembayaran Kamar</h4>
        </div>
        <form method="POST">
            <div class="card-body">
                <div class="mb-3">
                    <label for="tanggalPembayaran" class="form-label">Tanggal Pembayaran</label>
                    <input type="date" name="tanggalPembayaran" id="tanggalPembayaran" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="lamasewa" class="form-label">Lama Sewa</label>
                    <select name="lamasewa" id="lamasewa" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <option value="24:00:00">Harian</option>
                        <option value="168:00:00">Mingguan</option>
                        <option value="720:00:00">Bulanan</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="metodePembayaran" class="form-label">Metode Pembayaran</label>
                    <select name="metodePembayaran" id="metodePembayaran" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <option value="Uang Tunai">Uang Tunai</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="jumlahPembayaran" class="form-label">Jumlah Pembayaran (Rp)</label>
                    <input type="number" name="jumlahPembayaran" id="jumlahPembayaran" class="form-control" placeholder="600000" min="1" required>
                </div>
            </div>

            <div class="form-footer">
                <button type="submit" name="submit" class="btn btn-primary px-4">Bayar</button>
            </div>
        </form>

    </div>

</body>

</html>