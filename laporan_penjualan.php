<?php
require_once 'config/config.php';
requireRole(['admin', 'kasir']);

require_once 'models/Penjualan.php';

$database = new Database();
$db = $database->getConnection();
$penjualan = new Penjualan($db);

// Get filter parameters
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Get laporan data
$stmt = $penjualan->getLaporanPenjualan($start_date, $end_date);

$total_penjualan = 0;
$total_transaksi = 0;
$laporan_data = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $total_penjualan += (float)$row['total_bayar'];
    $total_transaksi++;
    $laporan_data[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dynamic.php">
</head>
<body>
    <div class="main-container">
        <?php 
        $role = $_SESSION['user_role'];
        require_once 'sidebar.php'; 
        ?>
        <main class="main-content">
            <header class="top-nav">
                <h1>📈 Laporan Transaksi Penjualan & Layanan</h1>
                <div class="user-info">
                    <div class="user-details">
                        <div class="user-name"><?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username']); ?></div>
                        <div class="user-role"><?php echo ucfirst($_SESSION['user_role']); ?></div>
                    </div>
                </div>
            </header>

            <div class="content">
                <!-- Filter Section -->
                <div class="card" style="margin-bottom: 20px;">
                    <form method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="start_date">Dari Tanggal</label>
                            <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>" class="form-control" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="end_date">Sampai Tanggal</label>
                            <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Filter Periode</button>
                        <button type="button" onclick="window.print()" class="btn btn-secondary">🖨️ Cetak Rekap</button>
                    </form>
                </div>

                <!-- Summary Cards -->
                <div class="dashboard-cards" style="margin-bottom: 25px;">
                    <div class="card">
                        <div class="card-icon success">💰</div>
                        <div class="card-info">
                            <h3>Total Omzet Penjualan</h3>
                            <p class="card-value"><?php echo formatCurrency($total_penjualan); ?></p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-icon primary">🧾</div>
                        <div class="card-info">
                            <h3>Total Transaksi</h3>
                            <p class="card-value"><?php echo $total_transaksi; ?> Transaksi</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-icon info">📊</div>
                        <div class="card-info">
                            <h3>Rata-rata Nilai Transaksi</h3>
                            <p class="card-value">
                                <?php echo formatCurrency($total_transaksi > 0 ? ($total_penjualan / $total_transaksi) : 0); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Laporan Table -->
                <div class="card">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No. Faktur</th>
                                    <th>Tanggal</th>
                                    <th>Pelanggan</th>
                                    <th>Kasir</th>
                                    <th>Total Bayar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($laporan_data)): ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 25px; color: #94a3b8;">
                                            Tidak ada data transaksi untuk rentang tanggal yang dipilih.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($laporan_data as $row): ?>
                                    <tr>
                                        <td><code><?php echo htmlspecialchars($row['no_faktur']); ?></code></td>
                                        <td><?php echo formatTanggal($row['tgl_penjualan']); ?></td>
                                        <td><strong><?php echo htmlspecialchars($row['nama_customer']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($row['kasir']); ?></td>
                                        <td><strong><?php echo formatCurrency($row['total_bayar']); ?></strong></td>
                                        <td>
                                            <a href="struk.php?id=<?php echo $row['id_penjualan']; ?>" 
                                               class="btn btn-sm btn-info" target="_blank">Lihat Struk</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
