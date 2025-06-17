<?php
session_start();
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $username = trim($_POST['username']);
    $namalengkap = trim($_POST['namalengkap']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    if (strlen($username) < 4) {
        header("Location: ../pages/daftar.php?error=short_username");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../pages/daftar.php?error=invalid_email");
        exit;
    }

    if (strlen($password) < 8) {
        header("Location: ../pages/daftar.php?error=short_password");
        exit;
    }

    if ($password !== $confirm_password) {
        header("Location: ../pages/daftar.php?error=password_mismatch");
        exit;
    }

    if (!empty($errors)) {
        echo "<script>alert('" . implode("\\n", $errors) . "'); window.history.back();</script>";
        exit;
    }

    // Check if username or email already exists
    $stmt = $connect->prepare("SELECT * FROM pelanggan WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $existing = $result->fetch_assoc();
        $error = ($existing['username'] === $username) ? 'username_exists' : 'email_exists';
        header("Location: ../pages/daftar.php?error=$error");
        exit;
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Default values
    $nomorHp = "-";
    $alamat = "-";

    // Insert with transaction for safety
    $connect->begin_transaction();

    try {
        $stmt = $connect->prepare("INSERT INTO pelanggan (username, password, namaLengkap, nomorHp, email, alamat) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $password_hash, $namalengkap, $nomorHp, $email, $alamat);

        if ($stmt->execute()) {
            $connect->commit();
            // Get the idPelanggan of the newly registered user
            $idPelanggan = $connect->insert_id;
            
            // Auto-login after registration
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'pelanggan';
            $_SESSION['idPelanggan'] = $idPelanggan;

            echo "<script>
                alert('Registrasi berhasil!');
                window.location.href = '../pages/pelanggan/dashboard.php';
            </script>";
        } else {
            throw new Exception("Gagal menyimpan data");
        }
    } catch (Exception $e) {
        $connect->rollback();
        error_log("Registration error: " . $e->getMessage());
        echo "<script>
            alert('Registrasi gagal. Silakan coba lagi.');
            window.history.back();
        </script>";
    }
}
