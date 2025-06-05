<?php
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "qosku";

    $connect = new mysqli($hostname,$username,$password,$database);

    if ($connect->connect_error){
        die('Koneksi ke Database Gagal...' . $connect->connect_error);
    }
?>