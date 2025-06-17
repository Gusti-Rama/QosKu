<?php
$page_titles = [
    'dashboard' => 'Kamar Anda',
    'listkamar' => 'Pesan Kamar',
    'profil' => 'Profil',
    'pesankamar' => 'Pesan Kamar',
    'detailkamar' => 'Detail Kamar',
    'riwayattransaksi' => 'Riwayat Transaksi',
];
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$is_special_page = in_array($current_page, ['profil', 'riwayattransaksi']);
?>
<?php if ($is_special_page): ?>
<div class="d-flex justify-content-between mt-4 px-4 pt-3 bg-transparent">
    <div>
        <p class="mb-0 fs-6 text-white">Pages <b>/ <?php echo $page_titles[$current_page] ?? ''; ?></b></p>
        <p class="fs-5 fw-bold text-white"><?php echo $page_titles[$current_page] ?? ''; ?></p>
    </div>
    <div class="d-flex align-items-start gap-3">
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-white rounded-4 border-end-0 rounded-end-0">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" class="form-control border-start-0 rounded-4 rounded-start-0" placeholder="Pencarian">
        </div>

        <div class="dropdown-center">
            <div class="d-flex align-items-center gap-1 text-secondary profile-trigger" style="cursor: pointer;">
                <div class="profile-container position-relative">
                    <div class=" d-flex align-items-center gap-1 text-secondary" style="cursor: pointer;">
                        <i class="bi bi-person-fill fs-5 text-white"></i>
                        <span class="fs-6 text-white">Profil</span>
                    </div>

                    <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 p-0"
                        id="profileDropdown"
                        aria-labelledby="profileDropdown"
                        onmouseover="this.classList.add('show')"
                        onmouseout="this.classList.remove('show')"
                        style="min-width: 150px; margin-top: 10px;">
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3"
                            href="profil.php">
                            <i class="bi bi-person fs-6"></i>
                            <span>Profil Saya</span>
                        </a>
                        <div class="dropdown-divider my-0"></div>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 text-danger"
                            href="../../php/logout.php">
                            <i class="bi bi-box-arrow-right fs-6"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="d-flex justify-content-between mt-4 px-4 pt-3 bg-transparent">
    <div>
        <p class="mb-0 fs-6 text-secondary">Pages <b>/ <?php echo $page_titles[$current_page] ?? ''; ?></b></p>
        <p class="fs-5 fw-bold"><?php echo $page_titles[$current_page] ?? ''; ?></p>
    </div>
    <div class="d-flex align-items-start gap-3">
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-white rounded-4 border-end-0 rounded-end-0">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" class="form-control border-start-0 rounded-4 rounded-start-0" placeholder="Pencarian">
        </div>

        <div class="dropdown-center">
            <div class="d-flex align-items-center gap-1 text-secondary profile-trigger" style="cursor: pointer;">
                <div class="profile-container position-relative">
                    <div class=" d-flex align-items-center gap-1 text-secondary" style="cursor: pointer;">
                        <i class="bi bi-person-fill fs-5"></i>
                        <span class="fs-6">Profil</span>
                    </div>

                    <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 p-0"
                        id="profileDropdown"
                        aria-labelledby="profileDropdown"
                        onmouseover="this.classList.add('show')"
                        onmouseout="this.classList.remove('show')"
                        style="min-width: 150px; margin-top: 10px;">
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3"
                            href="profil.php">
                            <i class="bi bi-person fs-6"></i>
                            <span>Profil Saya</span>
                        </a>
                        <div class="dropdown-divider my-0"></div>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 text-danger"
                            href="../../php/logout.php">
                            <i class="bi bi-box-arrow-right fs-6"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
    .dropdown-menu {
        display: none;
        transition: all 0.2s ease;
        margin-top: 0;
        padding-top: 5px;
        position: absolute;
        right: 0;
        top: 100%;
        display: none;
        transition: opacity 0.2s;

    }

    .dropdown-menu.show {
        display: block;
    }

    .dropdown-item {
        transition: background-color 0.2s ease;
        border-radius: 10px;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
        border-radius: 10px;
    }

    .profile-trigger:hover {
        color: #4FD1C5 !important;
    }

    .profile-container:hover .dropdown-menu {
        display: block !important;
    }
</style>

<script>
    let dropdownTimeout;

    document.querySelector('.profile-container').addEventListener('mouseover', function() {
        clearTimeout(dropdownTimeout);
        document.getElementById('profileDropdown').classList.add('show');
    });

    document.querySelector('.profile-container').addEventListener('mouseout', function() {
        dropdownTimeout = setTimeout(() => {
            document.getElementById('profileDropdown').classList.remove('show');
        }, 300);
    });

    document.getElementById('profileDropdown').addEventListener('mouseover', function() {
        clearTimeout(dropdownTimeout);
    });

    document.getElementById('profileDropdown').addEventListener('mouseout', function() {
        dropdownTimeout = setTimeout(() => {
            this.classList.remove('show');
        }, 300);
    });
</script>