<?php
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "qosku";

    $connect = new mysqli($hostname,$username,$password,$database);

    if ($connect->connect_error){
        die('Koneksi ke Database Gagal...' . $connect->connect_error);
    }

    function getUserActiveRooms($userId, $connect) {
    $currentDate = date('Y-m-d');
    $query = "
        SELECT k.*, p.tanggal_mulai, p.tanggal_selesai
        FROM kamar_kos k
        JOIN pemesanan p ON k.idKamar = p.idKamar
        WHERE p.idPelanggan_aktif = $userId
        AND p.statusPemesanan = 'Terkonfirmasi'
        AND p.is_active = TRUE
        AND '$currentDate' BETWEEN p.tanggal_mulai AND p.tanggal_selesai
    ";
    return $connect->query($query);
}
?>