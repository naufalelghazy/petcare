#!/bin/bash
set -e

# Bersihkan modul MPM ganda yang memicu AH00534
rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

# Konfigurasi port Apache sesuai variabel $PORT yang diinject Railway
PORT="${PORT:-80}"
sed -i "s/Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost .*/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

echo "Starting Apache on port $PORT with mpm_prefork..."
exec apache2-foreground
