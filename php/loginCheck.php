<?php
session_start();
$connect = new mysqli('localhost', 'root', '', 'qosku');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST["username"]);
    $password = htmlspecialchars($_POST['password']);

    $queryAdmin = "SELECT * FROM `admin` WHERE username = ?";
    $stmtAdmin = $connect->prepare($queryAdmin);
    $stmtAdmin->bind_param("s", $username);
    $stmtAdmin->execute();
    $resultAdmin = $stmtAdmin->get_result();

    if ($resultAdmin->num_rows === 1) {
        $admin = $resultAdmin->fetch_assoc();
        // if (password_verify($password, hash: $admin["password"])) { (PAKE KALO UDAH ADA HASHING PASSWORD)
        if ($password === $admin["password"]) {
            if (isset($_POST['remember'])) {
                setcookie("username", $admin['username'], time() + (86400 * 30), "/");
                setcookie("role", "admin", time() + (86400 * 30), "/");
            }
            header("location: ../pages/admin/dashboardAdmin.php");
            exit;
        }
    }

    $queryPelanggan = "SELECT * FROM `pelanggan` WHERE username = ?";
    $stmtPelanggan = $connect->prepare($queryPelanggan);
    $stmtPelanggan->bind_param("s", $username);
    $stmtPelanggan->execute();
    $resultPelanggan = $stmtPelanggan->get_result();

    if ($resultPelanggan->num_rows === 1) {
        $user = $resultPelanggan->fetch_assoc();
        // if (password_verify($password, $user["password"])) { (PAKE KALO UDAH ADA HASHING PASSWORD)
        if ($password === $user["password"]) {
            if (isset($_POST['remember'])) {
                setcookie("username", $admin['username'], time() + (86400 * 30), "/"); // 30 days
                setcookie("role", "pelanggan", time() + (86400 * 30), "/");
            }
            header("location: ../pages/dashboardKamar.php");
            exit;
        }
    }
    header("location: ../pages/signin.php?pesan=gagal");
    exit;
}
