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
        <?php include '../../layout/pelangganNavbar.php'; ?>

        <div class="flex-grow-1">
            <div class="position-relative rounded-4"
                style="background-image:url('../../assets/img/backgroundProfil.png'); height:200px;background-size:cover; background-position:center;">
                
                <?php include '../../layout/pelangganHeader.php'; ?>

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