<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$is_room_related = in_array($current_page, ['dashboard', 'detailkamar']); 
$is_laporan_related = in_array($current_page, ['laporan', 'riwayattransaksi']); 
?>

<nav class="bg-transparent p-3 me-4 d-flex flex-column flex-shrink-0" style="width: 250px;">
    <a class="navbar-brand fw-bold fs-3 pt-3 border-bottom" href="dashboard.php" style="color: #2D3748;">
        <img src="../../assets/img/QosKuIMG.png" class="mb-1" alt="Logo" height="80">QosKu
    </a>
    <div class="flex-grow-1 mt-3 d-flex flex-column justify-content-between h-100">
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a href="dashboard.php" class="nav-link p-0">
                    <div class="rounded-4 py-3 px-2 d-flex align-items-center <?= $is_room_related ? 'bg-white shadow-sm' : 'bg-transparent' ?>">
                        <span class="d-flex justify-content-center align-items-center rounded-3 <?= $is_room_related ? 'bg-utama' : 'bg-white' ?>"
                            style="width: 32px; height: 32px;">
                            <i class="bi bi-house-door-fill <?= $is_room_related ? 'text-white' : 'text-utama' ?>"></i>
                        </span>
                        <span class="ms-2 <?= $is_room_related ? 'fw-bold text-dark' : 'text-secondary' ?>">List Kamar</span>
                    </div>
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="pesanan.php" class="nav-link p-0">
                    <div class="rounded-4 py-3 px-2 d-flex align-items-center <?= $current_page === 'pesanan' ? 'bg-white shadow-sm' : 'bg-transparent' ?>">
                        <span class="d-flex justify-content-center align-items-center rounded-3 <?= $current_page === 'pesanan' ? 'bg-utama' : 'bg-white' ?>" style="width: 32px; height: 32px;">
                            <i class="bi bi-credit-card-2-front-fill <?= $current_page === 'pesanan' ? 'text-white' : 'text-utama' ?>"></i>
                        </span>
                        <span class="ms-2 <?= $current_page === 'pesanan' ? 'fw-bold text-dark' : 'text-secondary' ?>">Manajemen Pesanan</span>
                    </div>
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="laporan.php" class="nav-link p-0">
                    <div class="rounded-4 py-3 px-2 d-flex align-items-center <?= $current_page === 'laporan' ? 'bg-white shadow-sm' : 'bg-transparent' ?>">
                        <span class="d-flex justify-content-center align-items-center rounded-3 <?= $current_page === 'laporan' ? 'bg-utama' : 'bg-white' ?>"
                            style="width: 32px; height: 32px;">
                            <i class="bi bi-bar-chart-fill <?= $current_page === 'laporan' ? 'text-white' : 'text-utama' ?>"></i>
                        </span>
                        <span class="ms-2 <?= $current_page === 'laporan' ? 'fw-bold text-dark' : 'text-secondary' ?>">Laporan</span>
                    </div>
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="pengeluaran.php" class="nav-link p-0">
                    <div class="rounded-4 py-3 px-2 d-flex align-items-center <?= $current_page === 'pengeluaran' ? 'bg-white shadow-sm' : 'bg-transparent' ?>">
                        <span class="d-flex justify-content-center align-items-center rounded-3 <?= $current_page === 'pengeluaran' ? 'bg-utama' : 'bg-white' ?>"
                            style="width: 32px; height: 32px;">
                            <i class="bi bi-cash-stack <?= $current_page === 'pengeluaran' ? 'text-white' : 'text-utama' ?>"></i>
                        </span>
                        <span class="ms-2 <?= $current_page === 'pengeluaran' ? 'fw-bold text-dark' : 'text-secondary' ?>">Pengeluaran</span>
                    </div>
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="listakun.php" class="nav-link p-0">
                    <div class="rounded-4 py-3 px-2 d-flex align-items-center <?= $current_page === 'listakun' ? 'bg-white shadow-sm' : 'bg-transparent' ?>">
                        <span class="d-flex justify-content-center align-items-center rounded-3 <?= $current_page === 'listakun' ? 'bg-utama' : 'bg-white' ?>"
                            style="width: 32px; height: 32px;">
                            <i class="bi bi-people <?= $current_page === 'listakun' ? 'text-white' : 'text-utama' ?>"></i>
                        </span>
                        <span class="ms-2 <?= $current_page === 'listakun' ? 'fw-bold text-dark' : 'text-secondary' ?>">List Akun</span>
                    </div>
                </a>
            </li>
        </ul>
    </div>
</nav>

<style>
    .nav-item a:hover .rounded-4 {
        background-color: white !important;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }

    .nav-item a:hover .rounded-3 {
        background-color: #4FD1C5 !important;
    }

    .nav-item a:hover .bi {
        color: white !important;
    }

    .nav-item a:hover span:not(.rounded-3) {
        color: black !important;
        font-weight: bold !important;
    }

    .text-utama {
        color: #4FD1C5 !important;
    }

    .bg-utama {
        background-color: #4FD1C5 !important;
    }
</style>