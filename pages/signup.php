<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>QosKu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../assets/css/style.css" />
  <link rel="icon" href="../assets/img/QosKuIMG.png" type="image/png" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body class="d-flex flex-column min-vh-100 bg-light">
  <div class="background-half position-absolute top-0 start-0 w-100 z-n1 rounded-bottom-4"></div>
  <div class="position-fixed top-0 start-50 translate-middle-x mt-4 mx-4" style="z-index: 10; width: 70%;">
    <nav class="navbar navbar-expand-lg navbar-light bg-white bg-opacity-50 rounded-3 pe-5 shadow-sm">
      <a class="navbar-brand fw-bold" href="#" style="color: #2D3748;">
        <img src="../assets/img/QosKuIMG.png" alt="Logo" height="60">
        QosKu
      </a>
      <div class="collapse navbar-collapse justify-content-center me-5 pe-5" id="navbarMenu">
        <ul class="navbar-nav mb-2 mb-lg-0">
          <li class="nav-item px-2">
            <a class="nav-link fw-medium" href="dashboardKamar.php"><i class="bi bi-box-fill"></i> DASHBOARD</a>
          </li>
          <li class="nav-item px-2">
            <a class="nav-link fw-medium" href="#"><i class="bi bi-person-fill"></i> PROFILE</a>
          </li>
          <li class="nav-item px-2">
            <a class="nav-link fw-medium active" href="#"><i class="bi bi-person-fill-add"></i> SIGN UP</a>
          </li>
          <li class="nav-item px-2">
            <a class="nav-link fw-medium" href="signin.php"><i class="bi bi-key-fill"></i> SIGN IN</a>
          </li>
        </ul>
      </div>
    </nav>
  </div>

  <div class="text-center mt-5 pt-5">
    <h1 class="fw-bold text-dark pt-5 mt-5">Selamat Datang</h1>
    <p class="mb-4">Silahkan daftar terlebih dahulu</p>
  </div>

  <div class="flex-grow-1 d-flex justify-content-center align-items-center">
    <div class="z-1 bg-white p-5 shadow rounded-4 w-25">
      <form>
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" class="form-control" placeholder="Username anda" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" placeholder="Alamat email anda" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" class="form-control" placeholder="Password anda" required />
        </div>
        <div class="form-check form-switch mb-3">
          <input class="form-check-input" type="checkbox" id="rememberMe" />
          <label class="form-check-label" for="rememberMe">Ingat saya</label>
        </div>
        <button type="submit" class="btn btn-dark w-100">DAFTAR</button>
      </form>
      <p class="text-center mt-3">
        Sudah memiliki akun? <a href="#" class="text-decoration-none fw-semibold text-dark">Masuk</a>
      </p>
    </div>
  </div>

  <footer class="text-center py-3 bg-light text-muted">
    &copy; 2025, Made with ❤️ for QosKu
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>