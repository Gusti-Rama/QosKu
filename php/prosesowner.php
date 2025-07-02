<?php
session_start();
include 'connect.php';

// Verify owner role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    $_SESSION['error'] = "Unauthorized access!";
    header("Location: ../pages/login.php");
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_room'])) {
        $errors = [];
        $nomorKamar = (int)($_POST['nomorKamar'] ?? '');
        $tipeKamar = trim($_POST['tipeKamar'] ?? '');
        $harga = (int)($_POST['harga'] ?? 0);
        $deskripsi = trim($_POST['deskripsi'] ?? '');

        // Basic validation
        if ($nomorKamar <= 0) $errors[] = "Nomor kamar harus berupa angka positif";
        if (empty($tipeKamar)) $errors[] = "Tipe kamar harus diisi";
        if ($harga <= 0) $errors[] = "Harga harus lebih dari 0";

        // Enhanced duplicate room number check
        $checkStmt = $connect->prepare("SELECT idKamar FROM kamar_kos WHERE nomorKamar = ?");
        $checkStmt->bind_param("i", $nomorKamar);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            $errors[] = "Nomor kamar $nomorKamar sudah ada. Gunakan nomor lain.";
            $_SESSION['form_data'] = [
                'nomorKamar' => $nomorKamar,
                'tipeKamar' => $tipeKamar,
                'harga' => $harga,
                'deskripsi' => $deskripsi
            ];
        }

        // Handle file upload
        $gambar = '';
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (isset($_FILES['fotoKamar']) && $_FILES['fotoKamar']['error'] === UPLOAD_ERR_OK) {
            $fileType = $_FILES['fotoKamar']['type'];
            $fileSize = $_FILES['fotoKamar']['size'];

            if (!in_array($fileType, $allowedTypes)) {
                $errors[] = "Hanya file JPG, PNG, atau WebP yang diizinkan";
            }

            if ($fileSize > $maxSize) {
                $errors[] = "Ukuran file terlalu besar. Maksimal 2MB";
            }

            if (empty($errors)) {
                $targetDir = "../assets/img/";

                if (!is_dir($targetDir)) {
                    $errors[] = "Upload directory does not exist";
                } elseif (!is_writable($targetDir)) {
                    $errors[] = "Upload directory is not writable";
                } else {
                    $ext = pathinfo($_FILES['fotoKamar']['name'], PATHINFO_EXTENSION);
                    $fileName = 'kamar_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    $targetFile = $targetDir . $fileName;

                    if (move_uploaded_file($_FILES['fotoKamar']['tmp_name'], $targetFile)) {
                        $gambar = $fileName;
                    } else {
                        $errors[] = "Gagal mengupload gambar";
                    }
                }
            }
        }

        if (empty($errors)) {
            $stmt = $connect->prepare("INSERT INTO kamar_kos (nomorKamar, tipeKamar, harga, statusKetersediaan, deskripsi, gambar) VALUES (?, ?, ?, 'Tersedia', ?, ?)");
            $stmt->bind_param("isiss", $nomorKamar, $tipeKamar, $harga, $deskripsi, $gambar);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Kamar berhasil ditambahkan!";
                unset($_SESSION['form_data']); // Clear saved form data on success
            } else {
                $_SESSION['error'] = "Gagal menambahkan kamar: " . $stmt->error;
            }
        } else {
            $_SESSION['error'] = implode("<br>", $errors);
        }

        header("Location: ../pages/pemilik/dashboard.php");
        exit;
    }

    if (isset($_POST['edit_room'])) {
        $errors = [];
        $idKamar = (int)$_POST['idKamar'];
        $nomorKamar = (int)$_POST['nomorKamar'];
        $tipeKamar = trim($_POST['tipeKamar'] ?? '');
        $harga = (int)$_POST['harga'];
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $luasKamar = trim($_POST['luasKamar'] ?? '');
        $perabotan = trim($_POST['perabotan'] ?? '');
        $kamarMandi = trim($_POST['kamarMandi'] ?? 'dalam');

        // Check if room number is being changed to an existing one
        $currentRoomStmt = $connect->prepare("SELECT nomorKamar FROM kamar_kos WHERE idKamar = ?");
        $currentRoomStmt->bind_param("i", $idKamar);
        $currentRoomStmt->execute();
        $currentRoom = $currentRoomStmt->get_result()->fetch_assoc();

        if ($currentRoom['nomorKamar'] != $nomorKamar) {
            $checkStmt = $connect->prepare("SELECT idKamar FROM kamar_kos WHERE nomorKamar = ? AND idKamar != ?");
            $checkStmt->bind_param("ii", $nomorKamar, $idKamar);
            $checkStmt->execute();
            if ($checkStmt->get_result()->num_rows > 0) {
                $errors[] = "Nomor kamar $nomorKamar sudah digunakan oleh kamar lain";
            }
        }

        // Handle main image update
        $gambar = null;
        if (isset($_FILES['fotoKamar']) && $_FILES['fotoKamar']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $fileType = $_FILES['fotoKamar']['type'];
            $fileSize = $_FILES['fotoKamar']['size'];

            if (!in_array($fileType, $allowedTypes)) {
                $errors[] = "Hanya file JPG, PNG, atau WebP yang diizinkan";
            }

            if ($fileSize > $maxSize) {
                $errors[] = "Ukuran file terlalu besar. Maksimal 2MB";
            }

            if (empty($errors)) {
                $targetDir = "../assets/img/";
                $ext = pathinfo($_FILES['fotoKamar']['name'], PATHINFO_EXTENSION);
                $fileName = 'kamar_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $targetFile = $targetDir . $fileName;

                if (move_uploaded_file($_FILES['fotoKamar']['tmp_name'], $targetFile)) {
                    $gambar = $fileName;

                    // Delete old image if exists
                    $oldImageStmt = $connect->prepare("SELECT gambar FROM kamar_kos WHERE idKamar = ?");
                    $oldImageStmt->bind_param("i", $idKamar);
                    $oldImageStmt->execute();
                    $oldImage = $oldImageStmt->get_result()->fetch_assoc();

                    if (!empty($oldImage['gambar'])) {
                        $oldFilePath = $targetDir . $oldImage['gambar'];
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }
                } else {
                    $errors[] = "Gagal mengupload gambar";
                }
            }
        }

        if (empty($errors)) {
            // Update kamar_kos
            $query = "UPDATE kamar_kos SET 
                      nomorKamar = ?, 
                      tipeKamar = ?, 
                      harga = ?, 
                      deskripsi = ?" .
                ($gambar ? ", gambar = ?" : "") .
                " WHERE idKamar = ?";

            $stmt = $connect->prepare($query);

            if ($gambar) {
                $stmt->bind_param("ssissi", $nomorKamar, $tipeKamar, $harga, $deskripsi, $gambar, $idKamar);
            } else {
                $stmt->bind_param("ssisi", $nomorKamar, $tipeKamar, $harga, $deskripsi, $idKamar);
            }

            $stmt->execute();

            // Update fasilitas
            $facilityStmt = $connect->prepare("
                INSERT INTO fasilitas (idKamar, luasKamar, perabotan, kamarMandi)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                luasKamar = VALUES(luasKamar),
                perabotan = VALUES(perabotan),
                kamarMandi = VALUES(kamarMandi)
            ");
            $facilityStmt->bind_param("isss", $idKamar, $luasKamar, $perabotan, $kamarMandi);
            $facilityStmt->execute();

            // Handle additional images
            if (!empty($_FILES['additionalImages'])) {
                foreach ($_FILES['additionalImages']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['additionalImages']['error'][$key] === UPLOAD_ERR_OK) {
                        $fileType = $_FILES['additionalImages']['type'][$key];

                        if (in_array($fileType, $allowedTypes)) {
                            $ext = pathinfo($_FILES['additionalImages']['name'][$key], PATHINFO_EXTENSION);
                            $fileName = 'room_extra_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                            $targetFile = $targetDir . $fileName;

                            if (move_uploaded_file($tmp_name, $targetFile)) {
                                $insertStmt = $connect->prepare("INSERT INTO kamar_images (idKamar, image_path) VALUES (?, ?)");
                                $insertStmt->bind_param("is", $idKamar, $fileName);
                                $insertStmt->execute();
                            }
                        }
                    }
                }
            }

            $_SESSION['success'] = "Kamar berhasil diperbarui!";
        } else {
            $_SESSION['error'] = implode("<br>", $errors);
        }

        header("Location: ../pages/pemilik/detailkamar.php?id=$idKamar");
        exit;
    }
    // Add additional cost
    if (isset($_POST['add_cost'])) {
        $idPelanggan = (int)$_POST['idPelanggan'];
        $idKamar = (int)$_POST['idKamar'];
        $namaBiaya = trim($_POST['namabiaya']);
        $jenisBiaya = trim($_POST['jenisbiaya']);
        $jumlahBiaya = (int)$_POST['jumlahbiaya'];
        $periode = date('Y-m');

        $stmt = $connect->prepare("
            INSERT INTO biaya_tambahan (namaBiaya, jumlahBiaya, Periode, statusPembayaran, idPelanggan, jenisBiaya) 
            VALUES (?, ?, ?, 'belum_lunas', ?, ?)
        ");
        $stmt->bind_param("sisis", $namaBiaya, $jumlahBiaya, $periode, $idPelanggan, $jenisBiaya);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Biaya tambahan berhasil ditambahkan!";
        } else {
            $_SESSION['error'] = "Gagal menambahkan biaya tambahan!";
        }

        header("Location: ../pages/pemilik/detailkamar.php?id=$idKamar");
        exit;
    }
    // Delete room
    if (isset($_POST['delete_room'])) {
        $idKamar = (int)($_POST['idKamar'] ?? 0);

        if ($idKamar <= 0) {
            $_SESSION['error'] = "ID kamar tidak valid";
            header("Location: ../pages/pemilik/dashboard.php");
            exit;
        }

        // Get image path first
        $stmt = $connect->prepare("SELECT gambar FROM kamar_kos WHERE idKamar = ?");
        $stmt->bind_param("i", $idKamar);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $room = $result->fetch_assoc();
            $gambar = $room['gambar'];

            // Delete from database
            $deleteStmt = $connect->prepare("DELETE FROM kamar_kos WHERE idKamar = ?");
            $deleteStmt->bind_param("i", $idKamar);

            if ($deleteStmt->execute()) {
                // Delete image file if exists
                if (!empty($gambar)) {
                    $filePath = "../../assets/img/" . $gambar;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                $_SESSION['success'] = "Kamar berhasil dihapus!";
            } else {
                $_SESSION['error'] = "Gagal menghapus kamar: " . $deleteStmt->error;
            }
        } else {
            $_SESSION['error'] = "Kamar tidak ditemukan";
        }

        header("Location: ../pages/pemilik/dashboard.php");
        exit;
    }
}
