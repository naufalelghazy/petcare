<?php
// Deteksi halaman saat ini
$current_page = basename($_SERVER['PHP_SELF']);
$user_role = $_SESSION['user_role'] ?? 'guest';
?>
<nav class="sidebar">
    <div class="sidebar-header">
        <h2 style="font-size: 1.15rem; display: flex; align-items: center; gap: 8px;">
            <span>🐾</span> <?php echo APP_NAME; ?>
        </h2>
        <span class="badge badge-info" style="font-size: 0.75rem; text-transform: uppercase;">
            <?php echo htmlspecialchars($user_role); ?>
        </span>
    </div>
    
    <ul class="sidebar-nav">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link <?php echo ($current_page === 'dashboard.php') ? 'active' : ''; ?>">
                <i>📊</i> Dashboard Analitik
            </a>
        </li>
        
        <!-- TRANSAKSI & FRONT-DESK -->
        <?php if ($user_role === 'admin' || $user_role === 'kasir'): ?>
        <li class="nav-item">
            <a href="penjualan.php" class="nav-link <?php echo ($current_page === 'penjualan.php') ? 'active' : ''; ?>">
                <i>🛒</i> Kasir POS Hibrida
            </a>
        </li>
        <li class="nav-item">
            <a href="booking_inap.php" class="nav-link <?php echo ($current_page === 'booking_inap.php') ? 'active' : ''; ?>">
                <i>🏨</i> Reservasi Pet Hotel
            </a>
        </li>
        <?php endif; ?>

        <!-- LAYANAN & PERAWATAN -->
        <li class="nav-item">
            <a href="antrean_grooming.php" class="nav-link <?php echo ($current_page === 'antrean_grooming.php') ? 'active' : ''; ?>">
                <i>✂️</i> Antrean Grooming
            </a>
        </li>

        <?php if ($user_role === 'admin' || $user_role === 'kasir'): ?>
        <li class="nav-item">
            <a href="customer.php" class="nav-link <?php echo ($current_page === 'customer.php') ? 'active' : ''; ?>">
                <i>🐶</i> Pelanggan & Hewan
            </a>
        </li>
        <?php endif; ?>

        <!-- LOGISTIK & REPACK -->
        <?php if ($user_role === 'admin'): ?>
        <li class="nav-item">
            <a href="repack.php" class="nav-link <?php echo ($current_page === 'repack.php') ? 'active' : ''; ?>">
                <i>⚖️</i> Konversi Repack Pakan
            </a>
        </li>
        <li class="nav-item">
            <a href="stok.php" class="nav-link <?php echo ($current_page === 'stok.php') ? 'active' : ''; ?>">
                <i>📦</i> Monitoring Stok
            </a>
        </li>
        <?php endif; ?>

        <!-- MASTER DATA -->
        <?php if ($user_role === 'admin' || $user_role === 'kasir'): ?>
        <li class="nav-item">
            <a href="barang.php" class="nav-link <?php echo ($current_page === 'barang.php') ? 'active' : ''; ?>">
                <i>🏷️</i> Master Produk & Jasa
            </a>
        </li>
        <?php endif; ?>

        <?php if ($user_role === 'admin'): ?>
        <li class="nav-item">
            <a href="kandang.php" class="nav-link <?php echo ($current_page === 'kandang.php') ? 'active' : ''; ?>">
                <i>🏠</i> Fasilitas Kandang
            </a>
        </li>
        <li class="nav-item">
            <a href="kategori.php" class="nav-link <?php echo ($current_page === 'kategori.php') ? 'active' : ''; ?>">
                <i>📁</i> Kategori Produk
            </a>
        </li>
        <li class="nav-item">
            <a href="pembelian.php" class="nav-link <?php echo ($current_page === 'pembelian.php') ? 'active' : ''; ?>">
                <i>📥</i> Pengadaan / Pembelian
            </a>
        </li>
        <li class="nav-item">
            <a href="vendor.php" class="nav-link <?php echo ($current_page === 'vendor.php') ? 'active' : ''; ?>">
                <i>🏢</i> Data Vendor
            </a>
        </li>
        <?php endif; ?>

        <!-- LAPORAN -->
        <?php if ($user_role === 'admin' || $user_role === 'kasir'): ?>
        <li class="nav-item">
            <a href="laporan_penjualan.php" class="nav-link <?php echo ($current_page === 'laporan_penjualan.php') ? 'active' : ''; ?>">
                <i>📈</i> Laporan Penjualan
            </a>
        </li>
        <?php endif; ?>

        <?php if ($user_role === 'admin'): ?>
        <li class="nav-item">
            <a href="laporan_pembelian.php" class="nav-link <?php echo ($current_page === 'laporan_pembelian.php') ? 'active' : ''; ?>">
                <i>📉</i> Laporan Pengadaan
            </a>
        </li>
        <li class="nav-item">
            <a href="users.php" class="nav-link <?php echo ($current_page === 'users.php') ? 'active' : ''; ?>">
                <i>👥</i> Staf & Pengguna
            </a>
        </li>
        <li class="nav-item">
            <a href="pengaturan.php" class="nav-link <?php echo ($current_page === 'pengaturan.php') ? 'active' : ''; ?>">
                <i>⚙️</i> Pengaturan Sistem
            </a>
        </li>
        <?php endif; ?>

        <li class="nav-item" style="margin-top: 15px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 10px;">
            <a href="portal/index.php" target="_blank" class="nav-link" style="color: #38bdf8;">
                <i>🌐</i> Portal Pelanggan ↗
            </a>
        </li>

        <li class="nav-item">
            <a href="logout.php" class="nav-link">
                <i>🚪</i> Logout Keluar
            </a>
        </li>
    </ul>
</nav>