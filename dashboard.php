<?php
require_once 'config/config.php';
requireLogin();

require_once 'models/Penjualan.php';
require_once 'models/Barang.php';
require_once 'models/Grooming.php';
require_once 'models/Kandang.php';
require_once 'models/Inap.php';

$database = new Database();
$db = $database->getConnection();

$penjualanModel = new Penjualan($db);
$barangModel = new Barang($db);
$groomingModel = new Grooming($db);
$kandangModel = new Kandang($db);
$inapModel = new Inap($db);

$role = $_SESSION['user_role'];
$user_id = $_SESSION['user_id'];

// Data Analitik
$totalPenjualanHari = $penjualanModel->getTotalPenjualanHari();
$totalPenjualanBulan = $penjualanModel->getTotalPenjualanBulan();
$totalTransaksiHari = $penjualanModel->getTotalTransaksiHari();
$totalProduk = $barangModel->getTotalBarang();
$stokMenipis = $barangModel->getStokMenipis(5);
$occupancy = $kandangModel->getOccupancyStats();

// Antrean Grooming Aktif
$groomer_filter = ($role === 'groomer') ? $user_id : null;
$activeQueues = $groomingModel->readAll('aktif', $groomer_filter);

// Komisi Groomer
$commissionReport = $groomingModel->getCommissionReport($groomer_filter);
$totalKomisi = 0;
$commissionsList = [];
while ($comm = $commissionReport->fetch(PDO::FETCH_ASSOC)) {
    $commissionsList[] = $comm;
    $totalKomisi += (float)$comm['nominal_komisi'];
}

// Transaksi Terbaru
$recentSales = $penjualanModel->readRecent(5);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dynamic.php">
</head>
<body>
    <div class="main-container">
        <?php require_once 'sidebar.php'; ?>
        <main class="main-content">
            <header class="top-nav">
                <h1>📊 Dashboard PetCare Analytics</h1>
                <div class="user-info">
                    <div class="user-details">
                        <div class="user-name"><?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username']); ?></div>
                        <div class="user-role"><?php echo ucfirst($_SESSION['user_role']); ?></div>
                    </div>
                </div>
            </header>

            <div class="content">
                <!-- Banner Selamat Datang -->
                <div style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: white; padding: 25px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(2, 132, 199, 0.2);">
                    <h2 style="margin: 0 0 8px 0; font-size: 1.5rem;">
                        Selamat Datang di PetCare System, <?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username']); ?>! 🐾
                    </h2>
                    <p style="margin: 0; opacity: 0.9;">
                        Sistem terpadu operasional kasir ritel, konversi repack pakan, antrean grooming, dan manajemen pet hotel.
                    </p>
                </div>

                <!-- Kartu Statistik -->
                <div class="dashboard-cards" style="margin-bottom: 25px;">
                    <?php if ($role === 'admin' || $role === 'kasir'): ?>
                    <div class="card">
                        <div class="card-icon success">💰</div>
                        <div class="card-info">
                            <h3>Omzet Hari Ini</h3>
                            <p class="card-value"><?php echo formatCurrency($totalPenjualanHari); ?></p>
                            <small><?php echo $totalTransaksiHari; ?> transaksi selesai</small>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-icon primary">📈</div>
                        <div class="card-info">
                            <h3>Omzet Bulan Ini</h3>
                            <p class="card-value"><?php echo formatCurrency($totalPenjualanBulan); ?></p>
                            <small>Bulan <?php echo date('F Y'); ?></small>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-icon warning">🏨</div>
                        <div class="card-info">
                            <h3>Okupansi Hotel</h3>
                            <p class="card-value"><?php echo $occupancy['terisi'] ?? 0; ?> / <?php echo $occupancy['total'] ?? 0; ?> Kamar</p>
                            <small><?php echo $occupancy['tersedia'] ?? 0; ?> kamar siap pakai</small>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-icon danger">📦</div>
                        <div class="card-info">
                            <h3>Stok Menipis (&le; 5)</h3>
                            <p class="card-value"><?php echo $stokMenipis; ?> Item</p>
                            <small>Perlu pengadaan supplier</small>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- Kartu Khusus Groomer -->
                    <div class="card">
                        <div class="card-icon success">💵</div>
                        <div class="card-info">
                            <h3>Total Hak Komisi Saya</h3>
                            <p class="card-value"><?php echo formatCurrency($totalKomisi); ?></p>
                            <small>Dari <?php echo count($commissionsList); ?> layanan grooming</small>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- Antrean Grooming Aktif -->
                    <div class="card">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <h3 style="margin: 0; font-size: 1.15rem;">✂️ Antrean Grooming Berjalan</h3>
                            <a href="antrean_grooming.php" class="btn btn-sm btn-secondary">Buka Papan Antrean &rarr;</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Anabul</th>
                                        <th>Layanan</th>
                                        <th>Groomer</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $hasQueue = false;
                                    while ($q = $activeQueues->fetch(PDO::FETCH_ASSOC)): 
                                        $hasQueue = true;
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($q['nama_hewan']); ?></strong>
                                            <div style="font-size: 0.75rem; color: #64748b;"><?php echo htmlspecialchars($q['nama_customer']); ?></div>
                                        </td>
                                        <td><?php echo htmlspecialchars($q['nama_layanan']); ?></td>
                                        <td><?php echo htmlspecialchars($q['nama_groomer']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo ($q['status_pengerjaan'] === 'Antre') ? 'warning' : 
                                                     (($q['status_pengerjaan'] === 'Mandi') ? 'info' : 
                                                     (($q['status_pengerjaan'] === 'Pengeringan') ? 'primary' : 'success')); 
                                            ?>">
                                                <?php echo str_replace('_', ' ', $q['status_pengerjaan']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>

                                    <?php if (!$hasQueue): ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center; color: #94a3b8; padding: 20px;">
                                            Tidak ada antrean pengerjaan aktif saat ini.
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Riwayat Transaksi / Rekap Komisi -->
                    <div class="card">
                        <?php if ($role === 'admin' || $role === 'kasir'): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <h3 style="margin: 0; font-size: 1.15rem;">🧾 Transaksi Penjualan Terkini</h3>
                                <a href="penjualan.php" class="btn btn-sm btn-primary">+ Kasir Baru</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Faktur</th>
                                            <th>Pelanggan</th>
                                            <th>Kasir</th>
                                            <th>Total</th>
                                            <th>Nota</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $hasSale = false;
                                        while ($s = $recentSales->fetch(PDO::FETCH_ASSOC)): 
                                            $hasSale = true;
                                        ?>
                                        <tr>
                                            <td><code><?php echo htmlspecialchars($s['no_faktur']); ?></code></td>
                                            <td><?php echo htmlspecialchars($s['nama_customer']); ?></td>
                                            <td><?php echo htmlspecialchars($s['kasir']); ?></td>
                                            <td><strong><?php echo formatCurrency($s['total_bayar']); ?></strong></td>
                                            <td>
                                                <a href="struk.php?id=<?php echo $s['id_penjualan']; ?>" target="_blank" class="btn btn-sm btn-secondary">Struk</a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>

                                        <?php if (!$hasSale): ?>
                                        <tr>
                                            <td colspan="5" style="text-align: center; color: #94a3b8; padding: 20px;">
                                                Belum ada data penjualan tercatat.
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <!-- Komisi Khusus Groomer -->
                            <h3 style="margin: 0 0 15px 0; font-size: 1.15rem;">✂️ Riwayat Pembagian Komisi</h3>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Waktu</th>
                                            <th>Layanan</th>
                                            <th>Persentase</th>
                                            <th>Komisi Diterima</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($commissionsList, 0, 5) as $cm): ?>
                                        <tr>
                                            <td><?php echo formatTanggalWaktu($cm['tgl_transaksi']); ?></td>
                                            <td><?php echo htmlspecialchars($cm['nama_layanan']); ?></td>
                                            <td><span class="badge badge-info"><?php echo $cm['persentase_komisi']; ?>%</span></td>
                                            <td><strong style="color: #166534;"><?php echo formatCurrency($cm['nominal_komisi']); ?></strong></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($commissionsList)): ?>
                                        <tr>
                                            <td colspan="4" style="text-align: center; color: #94a3b8; padding: 20px;">
                                                Belum ada komisi tercatat.
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
