<?php
session_start();
include "../../php/connect.php";

// Cek apakah sudah login sebagai admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "<script>
        alert('Silakan login sebagai admin terlebih dahulu.');
        window.location.href = '../../auth/login.php';
    </script>";
    exit;
}

if (isset($_POST['submit'])) {
    $listrik = $_POST['listrik'];
    $air = $_POST['air'];
    $lainnya = $_POST['lainnya'];
    $tanggal = $_POST['tanggal'];

    $query = "INSERT INTO pengeluaran (listrik, air, lainnya, tanggal) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, 'iiis', $listrik, $air, $lainnya, $tanggal);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Data pengeluaran berhasil disimpan.'); window.location.href='form_pengeluaran.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data. Cek kembali inputan.');</script>";
    }

    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Form Pengeluaran</title>
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
            <h4 class="mb-0">Form Tambah Pengeluaran</h4>
        </div>
        <form method="POST">
            <div class="card-body">
                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="listrik" class="form-label">Pengeluaran Listrik (Rp)</label>
                    <input type="number" name="listrik" id="listrik" class="form-control" required min="0">
                </div>
                <div class="mb-3">
                    <label for="air" class="form-label">Pengeluaran Air (Rp)</label>
                    <input type="number" name="air" id="air" class="form-control" required min="0">
                </div>
                <div class="mb-3">
                    <label for="lainnya" class="form-label">Pengeluaran Lainnya (Rp)</label>
                    <input type="number" name="lainnya" id="lainnya" class="form-control" required min="0">
                </div>
            </div>

            <div class="form-footer">
                <button type="submit" name="submit" class="btn btn-primary px-4">Simpan</button>
            </div>
        </form>

    </div>

</body>

</html>
