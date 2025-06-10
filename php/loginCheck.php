<?php
session_start();
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST["username"]);
    $password = htmlspecialchars($_POST['password']);

    // Check admin table first
    $queryAdmin = "SELECT * FROM `admin` WHERE username = ?";
    $stmtAdmin = $connect->prepare($queryAdmin);
    $stmtAdmin->bind_param("s", $username);
    $stmtAdmin->execute();
    $resultAdmin = $stmtAdmin->get_result();

    if ($resultAdmin->num_rows === 1) {
        $admin = $resultAdmin->fetch_assoc();
        if (password_verify($password, $admin["password"])) {
            $_SESSION['username'] = $admin['username'];
            $_SESSION['role'] = $admin['peran']; 

            if (isset($_POST['ingat'])) {
                setcookie("username", $admin['username'], time() + (86400 * 30), "/");
                $role = ($admin['peran'] === 'owner') ? 'owner' : 'admin';
                setcookie("role", $role, time() + (86400 * 30), "/");
            }

            if ($admin['peran'] === 'owner') {
                header("location: ../pages/pemilik/dashboard.php");
            } else {
                header("location: ../pages/admin/dashboard.php");
            }
            exit;
        }
    }

    // Check pelanggan table
    $queryPelanggan = "SELECT * FROM `pelanggan` WHERE username = ?";
    $stmtPelanggan = $connect->prepare($queryPelanggan);
    $stmtPelanggan->bind_param("s", $username);
    $stmtPelanggan->execute();
    $resultPelanggan = $stmtPelanggan->get_result();

    if ($resultPelanggan->num_rows === 1) {
        $user = $resultPelanggan->fetch_assoc();
        if (password_verify($password, $user["password"])) {
            // Set session variables
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = 'pelanggan';

            if (isset($_POST['ingat'])) {
                setcookie("username", $user['username'], time() + (86400 * 30), "/");
                setcookie("role", "pelanggan", time() + (86400 * 30), "/");
            }
            header("location: ../pages/pelanggan/dashboard.php");
            exit;
        }
    }

    header("location: ../pages/login.php?pesan=gagal");
    exit;
}