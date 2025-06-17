<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$is_room_related = in_array($current_page, ['dashboard', 'detailkamar']);
$is_order_related = in_array($current_page, ['listkamar', 'pesankamar']);
$is_profile_related = in_array($current_page, ['profil', 'riwayattransaksi']);
?>

<nav class="bg-transparent p-3 me-4 d-flex flex-column flex-shrink-0" style="width: 250px;">
    <a class="navbar-brand fw-bold fs-3 pt-3 border-bottom" href="dashboard.php" style="color: #2D3748;">
        <img src="../../assets/img/QosKuIMG.png" class="mb-1" alt="Logo" height="80">QosKu
    </a>
    <div class="flex-grow-1 mt-3 d-flex flex-column justify-content-between h-100">
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a href="dashboard.php" class="nav-link p-0">
                    <div class="rounded-4 py-3 px-2 d-flex align-items-center <?php echo $is_room_related ? 'bg-white shadow-sm' : 'bg-transparent'; ?>">
                        <span class="d-flex justify-content-center align-items-center rounded-3 <?php echo $is_room_related ? 'bg-utama' : 'bg-white'; ?>"
                            style="width: 32px; height: 32px;">
                            <i class="bi bi-house-door-fill <?php echo $is_room_related ? 'text-white' : 'text-utama'; ?>"></i>
                        </span>
                        <span class="ms-2 <?php echo $is_room_related ? 'fw-bold text-dark' : 'text-secondary'; ?>">Kamar Anda</span>
                    </div>
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="listkamar.php" class="nav-link p-0">
                    <div class="rounded-4 py-3 px-2 d-flex align-items-center <?php echo $is_order_related ? 'bg-white shadow-sm' : 'bg-transparent'; ?>">
                        <span class="d-flex justify-content-center align-items-center rounded-3 <?php echo $is_order_related ? 'bg-utama' : 'bg-white'; ?>"
                            style="width: 32px; height: 32px;">
                            <i class="bi bi-credit-card-fill <?php echo $is_order_related ? 'text-white' : 'text-utama'; ?>"></i>
                        </span>
                        <span class="ms-2 <?php echo $is_order_related ? 'fw-bold text-dark' : 'text-secondary'; ?>">Pesan Kamar</span>
                    </div>
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="profil.php" class="nav-link p-0">
                    <div class="rounded-4 py-3 px-2 d-flex align-items-center <?php echo $is_profile_related ? 'bg-white shadow-sm' : 'bg-transparent'; ?>">
                        <span class="d-flex justify-content-center align-items-center rounded-3 <?php echo $is_profile_related ? 'bg-utama' : 'bg-white'; ?>"
                            style="width: 32px; height: 32px;">
                            <i class="bi bi-person-fill <?php echo $is_profile_related ? 'text-white' : 'text-utama'; ?>"></i>
                        </span>
                        <span class="ms-2 <?php echo $is_profile_related ? 'fw-bold text-dark' : 'text-secondary'; ?>">Profil</span>
                    </div>
                </a>
            </li>
        </ul>
        <div class="position-relative mt-auto rounded-4"
            style="height: 180px; background-image: url('../../assets/img/backgroundHelp.png'); background-size: cover; background-position: center;">
            <div class="text-white position-absolute bottom-0 w-100 start-0 px-3 pb-3 text-white">
                <p class="fw-bold fs-6 mb-0">Butuh Bantuan?</p>
                <p class="fs-6 mt-0 mb-1">Hubungi Kami</p>
                <a class="btn btn-sm btn-light w-100 rounded-3 fw-bold" href="https://wa.me/+6282137970627" target="_blank">Kontak</a>
            </div>
        </div>
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