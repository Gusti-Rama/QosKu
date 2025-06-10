<?php
session_start();
if (isset($_COOKIE['username']) && isset($_COOKIE['role'])) {
    $_SESSION['username'] = $_COOKIE['username'];
    $_SESSION['role'] = $_COOKIE['role'];

    if ($_COOKIE['role'] === 'admin') {
        header("Location: ../pages/admin/dashboardAdmin.php");
    } else {
        header("Location: ../pages/dashboardKamar.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QosKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" href="../assets/img/QosKuIMG.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body>
    <?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'gagal') : ?>
        <div class="position-fixed top-50 start-50 translate-middle alert alert-danger text-center fw-semibold rounded-4 shadow" style="z-index: 1050; width: 300px;">
            Username atau password salah!
        </div>
    <?php endif; ?>
    <div class="position-fixed top-0 start-50 translate-middle-x mt-4 mx-4" style="z-index: 10; width: 70%;">
        <nav class="navbar navbar-expand-lg navbar-light bg-light shadow rounded-3 pe-5">
            <a class="navbar-brand fw-bold" href="#" style="color: #2D3748;">
                <img src="../assets/img/QosKuIMG.png" alt="Logo" height="60">
                QosKu
            </a>

            <!-- Menu links -->
            <div class="collapse navbar-collapse justify-content-center me-5 pe-5" id="navbarMenu">
                <ul class="navbar-nav mb-2 mb-lg-0">
                    <li class="nav-item px-2">
                        <a class="nav-link fw-medium" href="dashboardKamar.php"><i class="bi bi-box-fill"></i> DASHBOARD</a>
                    </li>
                    <li class="nav-item px-2">
                        <a class="nav-link fw-medium" href="#"><i class="bi bi-person-fill"></i> PROFILE</a>
                    </li>
                    <li class="nav-item px-2">
                        <a class="nav-link fw-medium" href="signup.php"><i class="bi bi-person-fill-add"></i> SIGN UP</a>
                    </li>
                    <li class="nav-item px-2">
                        <a class="nav-link active fw-medium" href="#"><i class="bi bi-key-fill"></i> SIGN IN</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    <div class="container-fluid full-height">
        <div class="row h-100">
            <div class="col-md-6 d-flex justify-content-center align-items-center bg-white">
                <form class="w-50 mt-5" method="POST" action="../php/loginCheck.php">
                    <h2 class="mb-3 mt-5 fw-bold">Selamat Datang</h2>
                    <p class="mb-5 fw-bold text-secondary">Masukkan username dan password untuk login</p>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" class="form-control rounded-3" id="username" placeholder="Username anda">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label mt-2">Password</label>
                        <input type="password" name="password" class="form-control rounded-3" id="password" placeholder="Password anda">
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="ingat">
                        <label class="form-check-label mb-3" name="ingat" for="ingat">Ingat Saya</label>
                    </div>
                    <button type="submit" class="btn btn-lg btn-dark w-100 fs-6">Masuk</button>
                    <p class="mt-2 text-center text-secondary">Belum memiliki akun? <a href="#" class="text-dark fw-semibold text-decoration-none">Daftar</a></p>
                </form>
            </div>
            <div class="col-md-6 d-flex justify-content-center align-items-center bg-white position-relative" style="background-image: url('../assets/img/background.png'); background-size: cover; background-position: center; border-bottom-left-radius: 2rem;">
                <img src="../assets/img/QosKuNoBG.png" class="img-fluid" alt="Logo">
            </div>
        </div>
    </div>
    <div class="footer text-center mt-5 pt-5">
        &copy; 2025, Made with ❤️ for QosKu
    </div>

</body>
<script>
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease';
            setTimeout(() => alert.remove(), 500);
        }
    }, 3000); // Hide after 3 seconds
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.min.js" integrity="sha384-RuyvpeZCxMJCqVUGFI0Do1mQrods/hhxYlcVfGPOfQtPJh0JCw12tUAZ/Mv10S7D" crossorigin="anonymous"></script>

</html>