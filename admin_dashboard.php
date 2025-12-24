<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

require_once 'config/database.php';
$conn = getDBConnection();

// Ambil semua pesanan dengan informasi user
$query = "SELECT o.*, u.nama as user_nama, u.email as user_email 
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          ORDER BY o.order_date DESC";
$orders = $conn->query($query);

$contacts_query = "SELECT * FROM contacts ORDER BY created_at DESC";
$contacts = $conn->query($contacts_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Rumah Makan Sederhana</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" rel="stylesheet"/>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0a0a0a;
            color: #ffffff;
        }
        
        .admin-container {
            min-height: 100vh;
            background: #0a0a0a;
            color: #ffffff;
            padding: 2rem 1rem;
        }
        
        .admin-header {
            max-width: 1400px;
            margin: 0 auto 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            background: #1a1a1a;
            border-radius: 0.75rem;
            border: 1px solid #2a2a2a;
        }
        
        .admin-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .admin-title h1 {
            font-size: 1.5rem;
            margin: 0;
        }
        
        .admin-badge {
            background: #870909;
            padding: 0.35rem 0.85rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .admin-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .admin-user-info {
            text-align: right;
        }
        
        .admin-user-name {
            font-weight: 600;
            color: #ffffff;
        }
        
        .admin-user-email {
            font-size: 0.85rem;
            color: #888;
        }
        
        .admin-content {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: #1a1a1a;
            padding: 1.5rem;
            border-radius: 0.75rem;
            border: 1px solid #2a2a2a;
        }
        
        .stat-label {
            color: #888;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #ffffff;
        }
        
        .orders-section {
            background: #1a1a1a;
            padding: 1.5rem;
            border-radius: 0.75rem;
            border: 1px solid #2a2a2a;
            margin-bottom: 2rem;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .section-title {
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .table-wrapper {
            overflow-x: auto;
        }
        
        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .orders-table thead {
            background: #0a0a0a;
        }
        
        .orders-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #888;
            border-bottom: 1px solid #2a2a2a;
        }
        
        .orders-table td {
            padding: 1rem;
            border-bottom: 1px solid #2a2a2a;
        }
        
        .orders-table tbody tr:hover {
            background: #111;
        }
        
        .order-id {
            font-weight: 600;
            color: #870909;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.35rem 0.85rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #ffa50055;
            color: #ffa500;
        }
        
        .status-processing {
            background: #2196f355;
            color: #2196f3;
        }
        
        .status-completed {
            background: #4caf5055;
            color: #4caf50;
        }
        
        .status-cancelled {
            background: #f4433655;
            color: #f44336;
        }

        /* Added status badge for contact messages */
        .status-unread {
            background: #ffa50055;
            color: #ffa500;
        }
        
        .status-read {
            background: #88888855;
            color: #888888;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-small {
            padding: 0.5rem 0.75rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        
        .btn-view {
            background: #2196f3;
            color: white;
        }
        
        .btn-view:hover {
            background: #1976d2;
        }
        
        .btn-update {
            background: #4caf50;
            color: white;
        }
        
        .btn-update:hover {
            background: #388e3c;
        }
        
        .btn-delete {
            background: #f44336;
            color: white;
        }
        
        .btn-delete:hover {
            background: #d32f2f;
        }
        
        .btn-logout {
            padding: 0.75rem 1.5rem;
            background: #870909;
            color: white;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }
        
        .btn-logout:hover {
            background: #a00a0a;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #888;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: #1a1a1a;
            padding: 2rem;
            border-radius: 0.75rem;
            border: 1px solid #2a2a2a;
            max-width: 500px;
            width: 90%;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .btn-close {
            background: none;
            border: none;
            color: #888;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0;
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.25rem;
            transition: all 0.2s;
        }
        
        .btn-close:hover {
            background: #2a2a2a;
            color: #fff;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #888;
            font-size: 0.875rem;
        }
        
        .form-select {
            width: 100%;
            padding: 0.75rem;
            background: #0a0a0a;
            border: 1px solid #2a2a2a;
            border-radius: 0.5rem;
            color: #fff;
            font-size: 1rem;
        }
        
        .btn-submit {
            width: 100%;
            padding: 0.75rem;
            background: #870909;
            color: white;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.2s;
        }
        
        .btn-submit:hover {
            background: #a00a0a;
        }

        /* Added styles for contact message detail modal */
        .message-detail {
            line-height: 1.6;
        }

        .message-detail p {
            margin-bottom: 1rem;
        }

        .message-detail strong {
            color: #888;
            display: block;
            margin-bottom: 0.25rem;
        }

        .message-text {
            background: #0a0a0a;
            padding: 1rem;
            border-radius: 0.5rem;
            border: 1px solid #2a2a2a;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div class="admin-title">
                <span class="admin-badge"><i class="ri-shield-star-line"></i> ADMIN</span>
                <h1>Dashboard</h1>
            </div>
            <div class="admin-user">
                <div class="admin-user-info">
                    <div class="admin-user-name"><?= htmlspecialchars($_SESSION['admin_nama']) ?></div>
                    <div class="admin-user-email"><?= htmlspecialchars($_SESSION['admin_email']) ?></div>
                </div>
                <form method="POST" action="admin_logout.php" style="margin: 0;">
                    <button type="submit" class="btn-logout">
                        <i class="ri-logout-box-line"></i> Logout
                    </button>
                </form>
            </div>
        </div>
        
        <div class="admin-content">
            <?php
            // Hitung statistik dengan filter untuk cancelled orders
            $total_orders = $orders->num_rows;
            $total_revenue = 0;
            $pending_count = 0;
            $completed_count = 0;
            
            $orders->data_seek(0);
            while ($order = $orders->fetch_assoc()) {
                // Hanya hitung revenue untuk pesanan yang tidak cancelled
                if ($order['status'] != 'cancelled') {
                    $total_revenue += $order['total_amount'];
                }
                if ($order['status'] == 'pending') $pending_count++;
                if ($order['status'] == 'completed') $completed_count++;
            }

            $total_contacts = $contacts->num_rows;
            $unread_contacts = 0;
            $contacts->data_seek(0);
            while ($contact = $contacts->fetch_assoc()) {
                if ($contact['status'] == 'unread') $unread_contacts++;
            }
            ?>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Pesanan</div>
                    <div class="stat-value"><?= $total_orders ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Pesanan Pending</div>
                    <div class="stat-value"><?= $pending_count ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Pesanan Selesai</div>
                    <div class="stat-value"><?= $completed_count ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Pendapatan</div>
                    <div class="stat-value">Rp <?= number_format($total_revenue, 0, ',', '.') ?></div>
                </div>
                <!-- Added contact message statistics -->
                <div class="stat-card">
                    <div class="stat-label">Pesan Masuk</div>
                    <div class="stat-value"><?= $total_contacts ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Pesan Belum Dibaca</div>
                    <div class="stat-value"><?= $unread_contacts ?></div>
                </div>
            </div>
            
            <div class="orders-section">
                <div class="section-header">
                    <h2 class="section-title"><i class="ri-file-list-3-line"></i> Semua Pesanan</h2>
                </div>
                
                <?php if ($total_orders > 0): ?>
                    <div class="table-wrapper">
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Mode</th>
                                    <th>Total</th>
                                    <th>Pembayaran</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $orders->data_seek(0);
                                while ($order = $orders->fetch_assoc()): 
                                ?>
                                    <tr>
                                        <td class="order-id">#<?= $order['id'] ?></td>
                                        <td>
                                            <div><?= htmlspecialchars($order['customer_name']) ?></div>
                                            <div style="font-size: 0.75rem; color: #888;">
                                                <?= htmlspecialchars($order['user_email'] ?? 'Guest') ?>
                                            </div>
                                        </td>
                                        <td><?= ucfirst($order['order_mode']) ?></td>
                                        <td>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                                        <td><?= ucfirst($order['payment_method']) ?></td>
                                        <td>
                                            <span class="status-badge status-<?= $order['status'] ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </td>
                                        <td style="font-size: 0.85rem; color: #888;">
                                            <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-small btn-view" onclick="viewOrder(<?= $order['id'] ?>)">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                                <button class="btn-small btn-update" onclick="openStatusModal(<?= $order['id'] ?>, '<?= $order['status'] ?>')">
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                                <button class="btn-small btn-delete" onclick="deleteOrder(<?= $order['id'] ?>)">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="ri-inbox-line"></i>
                        <p>Belum ada pesanan</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Added contact messages section -->
            <div class="orders-section">
                <div class="section-header">
                    <h2 class="section-title"><i class="ri-message-3-line"></i> Pesan Kontak</h2>
                </div>
                
                <?php if ($total_contacts > 0): ?>
                    <div class="table-wrapper">
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Pesan</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $contacts->data_seek(0);
                                while ($contact = $contacts->fetch_assoc()): 
                                ?>
                                    <tr>
                                        <td class="order-id">#<?= $contact['id'] ?></td>
                                        <td><?= htmlspecialchars($contact['nama']) ?></td>
                                        <td><?= htmlspecialchars($contact['email']) ?></td>
                                        <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <?= htmlspecialchars(substr($contact['pesan'], 0, 50)) ?><?= strlen($contact['pesan']) > 50 ? '...' : '' ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?= $contact['status'] ?>">
                                                <?= ucfirst($contact['status']) ?>
                                            </span>
                                        </td>
                                        <td style="font-size: 0.85rem; color: #888;">
                                            <?= date('d/m/Y H:i', strtotime($contact['created_at'])) ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-small btn-view" onclick="viewContact(<?= $contact['id'] ?>)">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                                <button class="btn-small btn-update" onclick="markAsRead(<?= $contact['id'] ?>)">
                                                    <i class="ri-check-line"></i>
                                                </button>
                                                <button class="btn-small btn-delete" onclick="deleteContact(<?= $contact['id'] ?>)">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="ri-inbox-line"></i>
                        <p>Belum ada pesan kontak</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Modal Update Status -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Update Status Pesanan</h3>
                <button class="btn-close" onclick="closeStatusModal()">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <form id="statusForm">
                <input type="hidden" name="order_id" id="orderId">
                <div class="form-group">
                    <label class="form-label">Status Pesanan</label>
                    <select name="status" id="orderStatus" class="form-select">
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit">Update Status</button>
            </form>
        </div>
    </div>

    <!-- Added contact detail modal -->
    <div id="contactModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Detail Pesan Kontak</h3>
                <button class="btn-close" onclick="closeContactModal()">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div id="contactDetail" class="message-detail">
                <!-- Contact details will be loaded here -->
            </div>
        </div>
    </div>
    
    <script>
        let refreshInterval;
        
        function startAutoRefresh() {
            refreshInterval = setInterval(() => {
                location.reload();
            }, 5000);
        }
        
        function stopAutoRefresh() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        }
        
        startAutoRefresh();
        
        function openStatusModal(orderId, currentStatus) {
            stopAutoRefresh();
            document.getElementById('orderId').value = orderId;
            document.getElementById('orderStatus').value = currentStatus;
            document.getElementById('statusModal').classList.add('active');
        }
        
        function closeStatusModal() {
            document.getElementById('statusModal').classList.remove('active');
            startAutoRefresh();
        }
        
        document.getElementById('statusForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            
            try {
                const response = await fetch('update_order_status.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Status pesanan berhasil diupdate!');
                    location.reload();
                } else {
                    alert('Gagal mengupdate status: ' + result.message);
                }
            } catch (error) {
                alert('Terjadi kesalahan: ' + error.message);
            }
        });
        
        async function deleteOrder(orderId) {
            stopAutoRefresh();
            
            if (!confirm('Apakah Anda yakin ingin menghapus pesanan ini?')) {
                startAutoRefresh();
                return;
            }
            
            try {
                const response = await fetch('delete_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'order_id=' + orderId
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Pesanan berhasil dihapus!');
                    location.reload();
                } else {
                    alert('Gagal menghapus pesanan: ' + result.message);
                    startAutoRefresh();
                }
            } catch (error) {
                alert('Terjadi kesalahan: ' + error.message);
                startAutoRefresh();
            }
        }
        
        async function viewOrder(orderId) {
            stopAutoRefresh();
            
            try {
                const response = await fetch('get_order_details.php?order_id=' + orderId);
                const result = await response.json();
                
                if (result.success) {
                    let itemsHtml = '';
                    result.items.forEach(item => {
                        itemsHtml += `${item.item_name} x${item.quantity} - Rp ${item.price.toLocaleString('id-ID')}\n`;
                    });
                    
                    alert(`Detail Pesanan #${orderId}\n\nItems:\n${itemsHtml}\nTotal: Rp ${result.order.total_amount.toLocaleString('id-ID')}`);
                    startAutoRefresh();
                } else {
                    alert('Gagal mengambil detail pesanan');
                    startAutoRefresh();
                }
            } catch (error) {
                alert('Terjadi kesalahan: ' + error.message);
                startAutoRefresh();
            }
        }

        async function viewContact(contactId) {
            stopAutoRefresh();
            
            try {
                const response = await fetch('get_contact_details.php?contact_id=' + contactId);
                const result = await response.json();
                
                if (result.success) {
                    const contact = result.contact;
                    document.getElementById('contactDetail').innerHTML = `
                        <div class="message-detail">
                            <p><strong>Nama:</strong> ${contact.nama}</p>
                            <p><strong>Email:</strong> ${contact.email}</p>
                            <p><strong>Waktu:</strong> ${new Date(contact.created_at).toLocaleString('id-ID')}</p>
                            <p><strong>Pesan:</strong></p>
                            <div class="message-text">${contact.pesan}</div>
                        </div>
                    `;
                    document.getElementById('contactModal').classList.add('active');
                } else {
                    alert('Gagal mengambil detail pesan');
                    startAutoRefresh();
                }
            } catch (error) {
                alert('Terjadi kesalahan: ' + error.message);
                startAutoRefresh();
            }
        }

        function closeContactModal() {
            document.getElementById('contactModal').classList.remove('active');
            startAutoRefresh();
        }

        async function markAsRead(contactId) {
            try {
                const response = await fetch('mark_contact_read.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'contact_id=' + contactId
                });
                
                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                } else {
                    alert('Gagal menandai sebagai dibaca: ' + result.message);
                }
            } catch (error) {
                alert('Terjadi kesalahan: ' + error.message);
            }
        }

        async function deleteContact(contactId) {
            stopAutoRefresh();
            
            if (!confirm('Apakah Anda yakin ingin menghapus pesan ini?')) {
                startAutoRefresh();
                return;
            }
            
            try {
                const response = await fetch('delete_contact.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'contact_id=' + contactId
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Pesan berhasil dihapus!');
                    location.reload();
                } else {
                    alert('Gagal menghapus pesan: ' + result.message);
                    startAutoRefresh();
                }
            } catch (error) {
                alert('Terjadi kesalahan: ' + error.message);
                startAutoRefresh();
            }
        }
        
        document.getElementById('statusModal').addEventListener('click', (e) => {
            if (e.target.id === 'statusModal') {
                closeStatusModal();
            }
        });

        document.getElementById('contactModal').addEventListener('click', (e) => {
            if (e.target.id === 'contactModal') {
                closeContactModal();
            }
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>