<?php
session_start();

if (isset($_COOKIE['username']) && isset($_COOKIE['role'])) {
    // Validate role against known values
    $valid_roles = ['admin', 'owner', 'pelanggan'];

    if (in_array($_COOKIE['role'], $valid_roles)) {
        $_SESSION['username'] = $_COOKIE['username'];
        $_SESSION['role'] = $_COOKIE['role'];

        // Redirect to appropriate dashboard
        switch ($_COOKIE['role']) {
            case 'admin':
                header("Location: ../pages/admin/dashboard.php");
                break;
            case 'owner':
                header("Location: ../pages/pemilik/dashboard.php");
                break;
            case 'pelanggan':
                header("Location: ../pages/pelanggan/dashboard.php");
                break;
        }
        exit;
    } else {
        // Invalid role - clear corrupted cookies
        setcookie("username", "", time() - 3600, "/");
        setcookie("role", "", time() - 3600, "/");
    }
}
?>
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
<style>
  .password-strength {
    height: 5px;
    margin-top: 5px;
    border-radius: 3px;
    transition: all 0.3s ease;
  }

  .strength-weak {
    width: 25%;
    background-color: #dc3545;
  }

  .strength-medium {
    width: 50%;
    background-color: #ffc107;
  }

  .strength-strong {
    width: 75%;
    background-color: #28a745;
  }

  .strength-very-strong {
    width: 100%;
    background-color: #20c997;
  }

  .error-message {
    color: #dc3545;
    font-size: 0.875em;
  }
</style>

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
            <a class="nav-link fw-medium" href="./pelanggan/dashboard.php"><i class="bi bi-box-fill"></i> DASHBOARD</a>
          </li>
          <li class="nav-item px-2">
            <a class="nav-link fw-medium" href="./pelanggan/profil.php"><i class="bi bi-person-fill"></i> PROFILE</a>
          </li>
          <li class="nav-item px-2">
            <a class="nav-link fw-medium active" href="#"><i class="bi bi-person-fill-add"></i> SIGN UP</a>
          </li>
          <li class="nav-item px-2">
            <a class="nav-link fw-medium" href="login.php"><i class="bi bi-key-fill"></i> SIGN IN</a>
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
    <div class="z-1 bg-white p-5 shadow rounded-4 w-50">
      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
          <?php
          $errors = [
            'username_exists' => 'Username sudah terdaftar',
            'email_exists' => 'Email sudah terdaftar',
            'password_mismatch' => 'Password tidak cocok',
            'invalid_email' => 'Email tidak valid',
            'short_username' => 'Username minimal 4 karakter',
            'short_password' => 'Password minimal 8 karakter'
          ];
          echo $errors[$_GET['error']] ?? 'Registrasi gagal';
          ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <form method="POST" action="../php/daftarCheck.php" id="formDaftar">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" class="form-control" name="username" id="username" placeholder="Username anda (minimal 4 karakter)" required />
          <small class="error-message" id="usernameError"></small>
        </div>
        <div class="mb-3">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" class="form-control" name="namalengkap" id="namalengkap" placeholder="Nama lengkap anda" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" name="email" id="email" placeholder="Alamat email anda" required />
          <small class="error-message" id="emailError"></small>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" class="form-control" name="password" id="password" placeholder="Password (minimal 8 Karakter)" required />
          <div class="password-strength" id="passwordStrength"></div>
          <small class="error-message" id="passwordError"></small>
          <div>
            <small class="form-text text-muted">Gunakan kombinasi huruf, angka, dan simbol</small>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Konfirmasi Password</label>
          <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
          <small class="error-message" id="confirmError"></small>
        </div>
        <button type="submit" class="btn btn-dark w-100">DAFTAR</button>
      </form>
      <p class="text-center mt-3">
        Sudah memiliki akun? <a href="login.php" class="text-decoration-none fw-semibold text-dark">Masuk</a>
      </p>
    </div>
  </div>

  <footer class="text-center py-3 bg-light text-muted">
    &copy; 2025, Made with ❤️ for QosKu
  </footer>
  <script>
    let lastScroll = 0;
    const navbar = document.querySelector('.navbar');

    window.addEventListener('scroll', () => {
      const currentScroll = window.pageYOffset;

      if (currentScroll <= 0) {
        // At top of page - always show navbar
        navbar.classList.remove('navbar-hidden');
        return;
      }

      if (currentScroll > lastScroll) {
        // Scrolling down - hide navbar
        navbar.classList.add('navbar-hidden');
      } else if (currentScroll < lastScroll) {
        // Scrolling up - show navbar
        navbar.classList.remove('navbar-hidden');
      }

      lastScroll = currentScroll;
    });
    // Real-time validation
    document.getElementById('username').addEventListener('input', function() {
      if (this.value.length < 4 && this.value.length > 0) {
        document.getElementById('usernameError').textContent = 'Minimal 4 karakter';
        this.classList.add('is-invalid');
      } else {
        document.getElementById('usernameError').textContent = '';
        this.classList.remove('is-invalid');
      }
    });

    document.getElementById('email').addEventListener('input', function() {
      if (!this.value.includes('@') && this.value.length > 0) {
        document.getElementById('emailError').textContent = 'Email tidak valid';
        this.classList.add('is-invalid');
      } else {
        document.getElementById('emailError').textContent = '';
        this.classList.remove('is-invalid');
      }
    });

    document.getElementById('password').addEventListener('input', function() {
      const strengthMeter = document.getElementById('passwordStrength');

      if (this.value.length < 8 && this.value.length > 0) {
        document.getElementById('passwordError').textContent = 'Minimal 8 karakter';
        this.classList.add('is-invalid');
      } else {
        document.getElementById('passwordError').textContent = '';
        this.classList.remove('is-invalid');

        // Update strength meter
        const strength = calculateStrength(this.value);
        strengthMeter.className = 'password-strength strength-' + strength;
      }
    });

    document.getElementById('confirm_password').addEventListener('input', function() {
      const password = document.getElementById('password').value;

      if (this.value !== password && this.value.length > 0) {
        document.getElementById('confirmError').textContent = 'Password tidak cocok';
        this.classList.add('is-invalid');
      } else {
        document.getElementById('confirmError').textContent = '';
        this.classList.remove('is-invalid');
      }
    });

    function calculateStrength(password) {
      let strength = 0;

      if (password.length >= 8) strength++;
      if (password.length >= 12) strength++;
      if (/[A-Z]/.test(password)) strength++;
      if (/\d/.test(password)) strength++;
      if (/[^A-Za-z0-9]/.test(password)) strength++;

      const levels = ['weak', 'medium', 'strong', 'very-strong'];
      return levels[Math.min(strength, 3)];
    }

    // Final validation before submission
    document.getElementById('registerForm').addEventListener('submit', function(e) {
      let isValid = true;
      const username = document.getElementById('username');
      const password = document.getElementById('password');
      const confirmPass = document.getElementById('confirm_password');

      if (username.value.length < 4) {
        document.getElementById('usernameError').textContent = 'Minimal 4 karakter';
        username.classList.add('is-invalid');
        isValid = false;
      }

      if (password.value.length < 8) {
        document.getElementById('passwordError').textContent = 'Minimal 8 karakter';
        password.classList.add('is-invalid');
        isValid = false;
      }

      if (password.value !== confirmPass.value) {
        document.getElementById('confirmError').textContent = 'Password tidak cocok';
        confirmPass.classList.add('is-invalid');
        isValid = false;
      }

      if (!isValid) {
        e.preventDefault();
      }
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>