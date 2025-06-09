<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QosKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" href="../assets/img/QosKuIMG.png" type="image/png">
</head>

<body>
    <div class="position-fixed top-0 start-50 translate-middle-x mt-4 mx-4 pe-5" style="z-index: 1030; width: 70%;">
        <nav class="navbar navbar-expand-lg navbar-light bg-light shadow rounded-3 pe-5">
            <!-- Logo -->
            <a class="navbar-brand fw-bold" href="#" style="color: #2D3748;">
                <img src="../assets/img/QosKuIMG.png" alt="Logo" height="60">
                QosKu
            </a>    

            <!-- Menu links -->
            <div class="collapse navbar-collapse justify-content-center me-5 pe-5" id="navbarMenu">
                <ul class="navbar-nav mb-2 mb-lg-0">
                    <li class="nav-item px-2">
                        <a class="nav-link active fw-medium" href="#">DASHBOARD</a>
                    </li>
                    <li class="nav-item px-2">
                        <a class="nav-link fw-medium" href="#">PROFILE</a>
                    </li>
                    <li class="nav-item px-2">
                        <a class="nav-link fw-medium" href="#">SIGN UP</a>
                    </li>
                    <li class="nav-item px-2">
                        <a class="nav-link fw-medium" href="#">SIGN IN</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    <div class="container-fluid full-height">
        <div class="row h-100">
            <div class="col-md-6 d-flex justify-content-center align-items-center bg-white">
                <form class="w-50">
                    <h2 class="text-center mb-4 fw-bold">Selamat Datang Admin</h2>
                    <p class="fw-bold">Masukkan username dan password untuk login</p>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" placeholder="Username anda">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" placeholder="Password anda">
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Login</button>
                    <p class="mt-2">Belum memiliki akun? <a href="#">Daftar</a></p>
                </form>
            </div>
            <div class="col-md-6 d-flex justify-content-center align-items-center bg-white position-relative" style="background-image: url('../assets/img/background.png'); background-size: cover; background-position: center;">
                <img src="../assets/img/QosKuNoBG.png" class="img-fluid" alt="Logo" >
            </div>

        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.min.js" integrity="sha384-RuyvpeZCxMJCqVUGFI0Do1mQrods/hhxYlcVfGPOfQtPJh0JCw12tUAZ/Mv10S7D" crossorigin="anonymous"></script>

</html>