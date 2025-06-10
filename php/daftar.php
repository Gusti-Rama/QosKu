<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $username = trim($_POST['username']);
    $namalengkap = trim($_POST['namalengkap']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Enhanced validation
    $errors = [];

    if (empty($username)) {
        $errors[] = "Username wajib diisi!";
    } elseif (strlen($username) < 4) {
        $errors[] = "Username minimal 4 karakter!";
    }

    if (empty($namalengkap)) {
        $errors[] = "Nama lengkap wajib diisi!";
    } 

    if (empty($email)) {
        $errors[] = "Email wajib diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid!";
    }

    if (empty($password)) {
        $errors[] = "Password wajib diisi!";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password minimal 8 karakter!";
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
        $error = ($existing['username'] === $username)
            ? "Username sudah terdaftar!"
            : "Email sudah terdaftar!";
        echo "<script>alert('$error'); window.history.back();</script>";
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
            // Auto-login after registration (optional)
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'pelanggan';

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
