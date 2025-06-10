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

<body class="bg-light">
    <div class="d-flex min-vh-100 ms-4 me-4">
        <nav class="bg-transparent p-3 me-4 d-flex flex-column" style="width: 250px;">
            <a class="navbar-brand fw-bold fs-3 pt-3 border-bottom" href="#" style="color: #2D3748;">
                <img src="../assets/img/QosKuNoBG.png" class="mb-1" alt="Logo" height="80">QosKu
            </a>
            <div class="flex-grow-1 mt-3 d-flex flex-column justify-content-between h-100">
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <div class="bg-white rounded-4 shadow-sm py-2 px-2 d-flex align-items-center">
                            <a href="#" class="nav-link text-dark fw-bold d-flex align-items-center gap-2">
                                <span class="d-flex justify-content-center align-items-center rounded-3"
                                    style="width: 32px; height: 32px; background-color: #4FD1C5;">
                                    <i class="bi bi-house-door-fill text-white"></i>
                                </span>
                                Kamar Anda
                            </a>
                        </div>
                    </li>
                    <li class="nav-item mb-2">
                        <div class="bg-transparent rounded-4 py-2 px-2 d-flex align-items-center">
                            <a href="#" class="nav-link text-secondary d-flex align-items-center gap-2">
                                <span class="d-flex justify-content-center align-items-center rounded-3 bg-white"
                                    style="width: 32px; height: 32px;">
                                    <i class="bi bi-credit-card-fill" style="color: #4FD1C5;"></i>
                                </span>
                                Pesan Kamar
                            </a>
                        </div>
                    </li>
                    <li class="nav-item mb-2">
                        <div class="bg-transparent rounded-4 py-2 px-2 d-flex align-items-center">
                            <a href="#" class="nav-link text-secondary d-flex align-items-center gap-2">
                                <span class="d-flex justify-content-center align-items-center rounded-3 bg-white"
                                    style="width: 32px; height: 32px;">
                                    <i class="bi bi-person-fill" style="color: #4FD1C5;"></i>
                                </span>
                                Profil
                            </a>
                        </div>
                    </li>
                </ul>
                <div class="position-relative mt-auto rounded-4"
                    style="height: 180px; background-image: url('../assets/img/backgroundHelp.png'); background-size: cover; background-position: center;">
                    <div class="text-white position-absolute bottom-0 w-100 start-0 px-3 pb-3 text-white">
                        <p class="fw-bold fs-6 mb-0">Butuh Bantuan?</p>
                        <p class="fs-6 mt-0 mb-1">Hubungi Kami</p>
                        <button class="btn btn-sm btn-light w-100 rounded-3 fw-bold">Kontak</button>
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex-grow-1">
            <div class="position-relative rounded-4"
                style="background-image:url('../assets/img/backgroundProfil.png'); background-size:cover; background-position:center;">
                <div class="d-flex justify-content-between align-items-start mt-4" style="min-height:250px;">
                    <div class="d-flex mt-3 px-4 bg-transparent">
                        <div>
                            <p class="mb-0 fs-6 text-white">Pages <b>/ Profil</b></p>
                            <h3 class="fs-5 text-white fw-bold">Riwayat Transaksi</h3>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 pt-4 pe-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white rounded-4 border-end-0 rounded-end-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 rounded-4 rounded-start-0"
                                placeholder="Pencarian" />
                        </div>
                        <i class="bi bi-person-fill fs-5 text-white"></i>
                        <i class="bi bi-gear-fill fs-5 text-white"></i>
                        <i class="bi bi-bell-fill fs-5 text-white"></i>
                    </div>
                </div>

            </div>
            <div class="mt-4 container-fluid">
                <div class="row">
                    <div class="col-12 bg-white rounded-4 p-4 shadow-sm">

                        <!-- Controls: rows per page + top pagination -->
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <label for="rowsPerPage" class="form-label me-2">Show</label>
                                <select id="rowsPerPage" class="form-select form-select-sm d-inline-block" style="width:auto">
                                    <option value="5">5</option>
                                    <option value="10" selected>10</option>
                                    <option value="25">25</option>
                                </select>
                                <span class="ms-2">entries</span>
                            </div>
                            <nav aria-label="table pagination">
                                <ul id="paginationTop" class="pagination pagination-sm mb-0"></ul>
                            </nav>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle rounded-4 shadow-sm bg-white">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Jenis</th>
                                        <th>ID Transaksi</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <!-- rows injected here -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Bottom pagination -->
                        <nav aria-label="table pagination">
                            <ul id="paginationBottom" class="pagination pagination-sm"></ul>
                        </nav>

                    </div>
                </div>
            </div>

        </div> <!-- /.flex-grow-1 -->
    </div> <!-- /.d-flex.min-vh-100 -->

    <div class="footer text-center mt-5 pt-5">
        &copy; 2025, Made with ❤️ for QosKu
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sample data array
        const transactions = [{
                nama: 'John Doe',
                jenis: 'Bulanan',
                id: 'TXN123456',
                tanggal: '01 Jun 2025',
                status: 'Lunas'
            },
            {
                nama: 'Jane Smith',
                jenis: 'Mingguan',
                id: 'TXN123457',
                tanggal: '25 Mei 2025',
                status: 'Pending'
            },
            {
                nama: 'Bob Adams',
                jenis: 'Harian',
                id: 'TXN123458',
                tanggal: '10 Jun 2025',
                status: 'Lunas'
            },
            // … add more sample rows as needed …
        ];

        // State
        let currentPage = 1;
        let rowsPerPage = parseInt(document.getElementById('rowsPerPage').value, 10);

        // DOM refs
        const tableBody = document.getElementById('tableBody');
        const paginationTop = document.getElementById('paginationTop');
        const paginationBottom = document.getElementById('paginationBottom');
        const rowsSelect = document.getElementById('rowsPerPage');

        // Render a status badge
        function renderStatusBadge(status) {
            if (status === 'Lunas') {
                return `<span class="badge bg-success">${status}</span>`;
            } else if (status === 'Pending') {
                return `<span class="badge bg-warning text-dark">${status}</span>`;
            } else {
                return `<span class="badge bg-secondary">${status}</span>`;
            }
        }

        // Render table rows for current page
        function renderTable() {
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            const pageItems = transactions.slice(start, end);

            tableBody.innerHTML = pageItems.map(tx => `
    <tr>
      <td>${tx.nama}</td>
      <td>${tx.jenis}</td>
      <td>${tx.id}</td>
      <td>${tx.tanggal}</td>
      <td>${renderStatusBadge(tx.status)}</td>
    </tr>
  `).join('');
        }

        // Render pagination controls
        function renderPagination(container) {
            const totalPages = Math.ceil(transactions.length / rowsPerPage);
            const pages = [];
            // Previous button
            pages.push(`
    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
    </li>
  `);
            // Page numbers
            for (let p = 1; p <= totalPages; p++) {
                pages.push(`
      <li class="page-item ${p === currentPage ? 'active' : ''}">
        <a class="page-link" href="#" data-page="${p}">${p}</a>
      </li>
    `);
            }
            // Next button
            pages.push(`
    <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
    </li>
  `);
            container.innerHTML = pages.join('');
        }

        // Handle clicks on pagination (top & bottom)
        function onPageClick(e) {
            e.preventDefault();
            const link = e.target.closest('a.page-link');
            if (!link) return;
            const page = parseInt(link.dataset.page, 10);
            const totalPages = Math.ceil(transactions.length / rowsPerPage);
            if (page >= 1 && page <= totalPages) {
                currentPage = page;
                updateTableAndPagination();
            }
        }

        // When rows-per-page changes
        rowsSelect.addEventListener('change', () => {
            rowsPerPage = parseInt(rowsSelect.value, 10);
            currentPage = 1;
            updateTableAndPagination();
        });

        paginationTop.addEventListener('click', onPageClick);
        paginationBottom.addEventListener('click', onPageClick);

        function updateTableAndPagination() {
            renderTable();
            renderPagination(paginationTop);
            renderPagination(paginationBottom);
        }

        // Initial render
        updateTableAndPagination();
    </script>
</body>

</html>