<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sign Up | QosKu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    html, body {
      margin: 0; padding: 0; height: 100%;
      font-family: 'Segoe UI', sans-serif;
      background: #fff;
      color: #000;
    }

    body {
      padding-top: 0; /* Navbar tidak fixed, jadi ini gak perlu */
    }

    .navbar {
      /* posisi statis supaya ikut scroll */
      position: static;
      background: url('./img/backgroundPanjang.png') no-repeat center top;
      background-size: cover;
      padding: 1rem 2rem;
      box-shadow: rgba(0,0,0,0.1) 0 2px 8px -3px;
      z-index: 1050;
      display: flex;
      align-items: center;
    }

    .top-half {
      height: 50vh;
      background: url('./img/backgroundPanjang.png') no-repeat center top;
      background-size: cover;
      text-align: center;
      padding-top: 1rem;
      color: #000;
      position: relative;
      z-index: 0;
    }

    .card-signup {
      max-width: 420px;
      background: #fff;
      border-radius: 16px;
      padding: 32px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
      margin: -300px auto 2rem;
      position: relative;
      z-index: 10;
    }

    .bottom-half {
      background-color: #ffffff;
      min-height: 50vh;
      padding-top: 4rem;
    }

    .btn-dark-custom {
      background-color: #0B0F2F;
      color: #fff;
      border: none;
    }

    .btn-dark-custom:hover {
      background-color: #1d234d;
    }

    .social-btn {
      width: 48px;
      height: 48px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border: 1px solid #ccc;
      border-radius: 8px;
      margin: 0 6px;
      background-color: white;
    }

    .footer {
      font-size: 0.8rem;
      color: #888;
      text-align: center;
      margin-top: 2rem;
      padding-bottom: 1rem;
    }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg">
    <a class="navbar-brand fw-bold" href="#">
      <img src="./img/QosKuIMG.png" alt="Logo" style="height: 50px;" /> QosKu
    </a>
    <div class="collapse navbar-collapse justify-content-center">
      <ul class="navbar-nav">
        <li class="nav-item mx-2"><a class="nav-link fw-semibold" href="#">DASHBOARD</a></li>
        <li class="nav-item mx-2"><a class="nav-link fw-semibold" href="#">PROFILE</a></li>
        <li class="nav-item mx-2"><a class="nav-link fw-semibold" href="#">SIGN UP</a></li>
        <li class="nav-item mx-2"><a class="nav-link fw-semibold" href="#">SIGN IN</a></li>
      </ul>
    </div>
    <div>
      <a href="#" class="btn btn-dark-custom btn-sm">Download Gratis</a>
    </div>
  </nav>

  <div class="top-half">
    <h3>Selamat Datang!</h3>
    <p>Silahkan daftar terlebih dahulu</p>
  </div>

  <div class="card-signup">
    <h6 class="text-center mb-3 fw-semibold">Daftar Dengan</h6>
    <div class="text-center mb-3">
      <div class="d-inline-block social-btn"><img src="https://img.icons8.com/ios-filled/24/facebook.png" /></div>
      <div class="d-inline-block social-btn"><img src="https://img.icons8.com/ios-filled/24/mac-os.png" /></div>
      <div class="d-inline-block social-btn"><img src="https://img.icons8.com/ios-filled/24/google-logo.png" /></div>
    </div>

    <p class="text-center text-muted">atau</p>

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
      <button type="submit" class="btn btn-dark-custom w-100">DAFTAR</button>
    </form>

    <p class="text-center mt-3">
      Sudah memiliki akun? <a href="#" class="text-decoration-none fw-semibold">Masuk</a>
    </p>
  </div>

  <div class="bottom-half"></div>

  <div class="footer">
    &copy; 2025, Made with ❤️ for QosKu
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
