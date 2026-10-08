<?php
// Dynamic CSS Generator based on Settings & Adminator System
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once dirname(__DIR__, 2) . '/models/Pengaturan.php';

$database = new Database();
$db = $database->getConnection();
$pengaturan = new Pengaturan($db);

// Gunakan 1 query getAll() daripada 8 query terpisah
$allSettings = $pengaturan->getAll();

$warna_primary = $allSettings['warna_primary'] ?? '#0284c7';
$warna_secondary = $allSettings['warna_secondary'] ?? '#0369a1';
$warna_sidebar = $allSettings['warna_sidebar'] ?? '#ffffff';
$warna_sidebar_header = $allSettings['warna_sidebar_header'] ?? '#ffffff';
$warna_success = $allSettings['warna_success'] ?? '#10b981';
$warna_danger = $allSettings['warna_danger'] ?? '#ef4444';
$warna_warning = $allSettings['warna_warning'] ?? '#f59e0b';
$warna_info = $allSettings['warna_info'] ?? '#06b6d4';

function hexToRgba($hex, $alpha = 0.15) {
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    return "rgba($r, $g, $b, $alpha)";
}

header('Content-Type: text/css; charset=UTF-8');
header('Cache-Control: public, max-age=86400'); // Cache di browser selama 24 jam agar tidak request ulang saat pindah menu
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
?>
/* Dynamic Adminator Palette Overrides */
:root {
    --color-primary: <?php echo $warna_primary; ?>;
    --color-primary-dark: <?php echo $warna_secondary; ?>;
    --color-primary-soft: <?php echo hexToRgba($warna_primary, 0.12); ?>;
    --color-success: <?php echo $warna_success; ?>;
    --color-success-soft: <?php echo hexToRgba($warna_success, 0.12); ?>;
    --color-warning: <?php echo $warna_warning; ?>;
    --color-warning-soft: <?php echo hexToRgba($warna_warning, 0.15); ?>;
    --color-danger: <?php echo $warna_danger; ?>;
    --color-danger-soft: <?php echo hexToRgba($warna_danger, 0.12); ?>;
    --color-info: <?php echo $warna_info; ?>;
    --color-info-soft: <?php echo hexToRgba($warna_info, 0.12); ?>;
}

.btn-primary {
    background-color: var(--color-primary);
    border-color: var(--color-primary);
}

.btn-primary:hover {
    background-color: var(--color-primary-dark);
    border-color: var(--color-primary-dark);
}

.nav-link.active {
    color: var(--color-primary);
    background-color: var(--color-primary-soft);
}

.nav-link.active i {
    color: var(--color-primary);
}

.stat-icon.primary {
    background-color: var(--color-primary-soft);
    color: var(--color-primary);
}

.stat-icon.success {
    background-color: var(--color-success-soft);
    color: var(--color-success);
}

.stat-icon.warning {
    background-color: var(--color-warning-soft);
    color: var(--color-warning);
}

.stat-icon.danger {
    background-color: var(--color-danger-soft);
    color: var(--color-danger);
}
