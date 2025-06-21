<?php
session_start();
require "../../php/connect.php";

if (!isset($_SESSION['role'])) {
    header("Location: ../../pages/login.php?error=not_logged_in");
    exit;
}

$peran = strtolower($_SESSION['role']);

$diperbolehkan = ['owner'];

// cek peran usernya
if (!in_array($peran, $diperbolehkan)) {
    header("Location: ../../pages/login.php?error=Akses_Ditolak");
    exit;
}

// Handle payment actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['payment_id'])) {
        $paymentId = intval($_POST['payment_id']);
        $action = $_POST['action'];

        // Verify CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Invalid CSRF token");
        }

        // Update payment status
        $validActions = ['approve', 'decline'];
        if (in_array($action, $validActions)) {
            $newStatus = ($action === 'approve') ? 'Lunas' : 'Ditolak';

            $stmt = $connect->prepare("UPDATE pembayaran SET statusPembayaran = ? WHERE idPembayaran = ?");
            $stmt->bind_param("si", $newStatus, $paymentId);
            $stmt->execute();

            // Enhanced approval logic
            if ($action === 'approve') {
                // Get booking and payment details - CORRECTED
                $bookingQuery = $connect->query("
        SELECT p.*, 
               p.jenis_sewa,
               p.lamaSewa,
               k.idKamar,
               p.idPelanggan
        FROM pemesanan p
        JOIN pembayaran pb ON p.idPemesanan = pb.idPemesanan
        JOIN kamar_kos k ON p.idKamar = k.idKamar
        WHERE pb.idPembayaran = $paymentId
    ");
                $booking = $bookingQuery->fetch_assoc();

                // Calculate dates
                $startDate = date('Y-m-d');
                $endDate = date('Y-m-d', strtotime($startDate . ' + ' . $booking['lamaSewa'] . ' ' . $booking['jenis_sewa']));

                // Update booking status
                $connect->query("
            UPDATE pemesanan SET 
                statusPemesanan = 'Terkonfirmasi',
                is_active = TRUE,
                idPelanggan_aktif = {$booking['idPelanggan']},
                tanggal_mulai = '$startDate',
                tanggal_selesai = '$endDate'
            WHERE idPemesanan = {$booking['idPemesanan']}
        ");

                // Update room status
                $connect->query("
            UPDATE kamar_kos 
            SET statusKetersediaan = 'Ditempati' 
            WHERE idKamar = {$booking['idKamar']}
        ");
            }

            $_SESSION['payment_message'] = "Payment #$paymentId has been " . ($action === 'approve' ? 'approved and room assigned!' : 'declined');
            header("Location: pesanan.php");
            exit;
        }
    }
}

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch pending payments with related data using your exact schema
$query = "SELECT p.*, 
          k.nomorKamar, 
          k.tipeKamar,
          k.harga,
          pl.namaLengkap AS namaPelanggan,
          pm.tanggalPemesanan,
          pm.statusPemesanan
          FROM pembayaran p
          JOIN pemesanan pm ON p.idPemesanan = pm.idPemesanan
          JOIN pelanggan pl ON pm.idPelanggan = pl.idPelanggan
          JOIN kamar_kos k ON pm.idKamar = k.idKamar
          WHERE p.statusPembayaran = 'Menunggu Konfirmasi'
          ORDER BY p.tanggalPembayaran DESC";

$payments = $connect->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QosKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="icon" href="../../assets/img/QosKuIMG.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">
    <div class="d-flex min-vh-100 ms-4 me-4">
        <?php include '../../layout/pemilikNavbar.php'; ?>

        <div class="flex-grow-1">
            <?php include '../../layout/pemilikHeader.php'; ?>

            <div class="container-fluid pt-4">
                <!-- Success message -->
                <?php if (isset($_SESSION['payment_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['payment_message'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['payment_message']); ?>
                <?php endif; ?>

                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="fw-bold">Managemen Pesanan</h5>
                        <p class="text-secondary mb-0">Kelola dan verifikasi pembayaran pelanggan</p>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Customer</th>
                                        <th>Room</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($payments->num_rows > 0): ?>
                                        <?php while ($payment = $payments->fetch_assoc()): ?>
                                            <tr>
                                                <td>#<?= $payment['idPembayaran'] ?></td>
                                                <td><?= htmlspecialchars($payment['namaPelanggan']) ?></td>
                                                <td>
                                                    <?= htmlspecialchars($payment['nomorKamar']) ?> (<?= htmlspecialchars($payment['tipeKamar']) ?>)
                                                </td>
                                                <td>Rp <?= number_format($payment['jumlahPembayaran'], 0, ',', '.') ?></td>
                                                <td><?= ucfirst($payment['metodePembayaran']) ?></td>
                                                <td><?= date('d M Y', strtotime($payment['tanggalPembayaran'])) ?></td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <!-- View Button -->
                                                        <button class="btn btn-sm btn-success view-payment"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#paymentModal"
                                                            data-payment-id="<?= $payment['idPembayaran'] ?>"
                                                            data-amount="<?= number_format($payment['jumlahPembayaran'], 0, ',', '.') ?>"
                                                            data-method="<?= $payment['metodePembayaran'] ?>"
                                                            data-date="<?= date('d M Y H:i', strtotime($payment['tanggalPembayaran'])) ?>"
                                                            data-customer="<?= htmlspecialchars($payment['namaPelanggan']) ?>"
                                                            data-room="<?= htmlspecialchars($payment['nomorKamar']) . ' (' . htmlspecialchars($payment['tipeKamar']) . ')' ?>"
                                                            data-proof="<?= isset($payment['bukti_transfer']) ? '../../assets/payment_proofs/' . htmlspecialchars($payment['bukti_transfer']) : '#' ?>">
                                                            <i class="bi bi-eye"></i> View
                                                        </button>

                                                        <!-- Approve Form -->
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                            <input type="hidden" name="payment_id" value="<?= $payment['idPembayaran'] ?>">
                                                            <input type="hidden" name="action" value="approve">
                                                            <button type="submit" class="btn btn-sm btn-primary">
                                                                <i class="bi bi-check-circle"></i> Approve
                                                            </button>
                                                        </form>

                                                        <!-- Decline Form -->
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                            <input type="hidden" name="payment_id" value="<?= $payment['idPembayaran'] ?>">
                                                            <input type="hidden" name="action" value="decline">
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="bi bi-x-circle"></i> Decline
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                                                <p class="mt-2 mb-0">No pending payments to verify</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Detail Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Payment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Payment ID:</strong> <span id="modalPaymentId"></span></p>
                            <p><strong>Customer:</strong> <span id="modalCustomer"></span></p>
                            <p><strong>Room:</strong> <span id="modalRoom"></span></p>
                            <p><strong>Amount:</strong> Rp <span id="modalAmount"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Payment Method:</strong> <span id="modalMethod"></span></p>
                            <p><strong>Payment Date:</strong> <span id="modalDate"></span></p>
                            <p><strong>Status:</strong> <span class="badge bg-warning">Pending</span></p>
                        </div>
                    </div>

                    <div class="mt-4" id="proofContainer">
                        <h6>Payment Proof</h6>
                        <img id="paymentProofImage" src="" class="img-fluid rounded-3 border" alt="Payment Proof" style="max-height: 300px; display: none;">
                        <p class="text-muted mt-2" id="noProofMessage">No payment proof available</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Payment modal handler
        document.querySelectorAll('.view-payment').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('modalPaymentId').textContent = this.dataset.paymentId;
                document.getElementById('modalCustomer').textContent = this.dataset.customer;
                document.getElementById('modalRoom').textContent = this.dataset.room;
                document.getElementById('modalAmount').textContent = this.dataset.amount;
                document.getElementById('modalMethod').textContent = this.dataset.method;
                document.getElementById('modalDate').textContent = this.dataset.date;

                const proofImage = document.getElementById('paymentProofImage');
                const noProofMessage = document.getElementById('noProofMessage');

                if (this.dataset.proof && this.dataset.proof !== '#') {
                    proofImage.src = this.dataset.proof;
                    proofImage.style.display = 'block';
                    noProofMessage.style.display = 'none';
                } else {
                    proofImage.style.display = 'none';
                    noProofMessage.style.display = 'block';
                }
            });
        });

        // Auto-close success alert after 5 seconds
        const alert = document.querySelector('.alert');
        if (alert) {
            setTimeout(() => {
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 150);
            }, 5000);
        }
    </script>
</body>

</html>