#!/bin/sh
# Pembantu untuk server klien: menjalankan docker compose dengan konfigurasi produksi.
#   ./prod.sh up -d --build      jalankan / perbarui situs
#   ./prod.sh ps                 status container
#   ./prod.sh logs -f app        lihat log aplikasi
#   ./prod.sh backup             simpan backup database ke folder backups/
set -e
cd "$(dirname "$0")"

if [ ! -f .env ]; then
    echo "File .env belum ada. Salin dulu: cp .env.example .env lalu isi nilainya." >&2
    exit 1
fi

PROFILE=""
if grep -q '^CLOUDFLARE_TUNNEL_TOKEN=.\+' .env; then
    PROFILE="--profile tunnel"
fi

if [ "$1" = "backup" ]; then
    mkdir -p backups
    FILE="backups/db-$(date +%Y%m%d-%H%M).sql.gz"
    docker compose -f docker-compose.yml -f docker-compose.prod.yml exec -T db \
        sh -c 'mariadb-dump -u root -p"$MARIADB_ROOT_PASSWORD" --single-transaction digispacekom' | gzip > "$FILE"
    # simpan 14 backup terakhir saja
    ls -1t backups/db-*.sql.gz | tail -n +15 | xargs -r rm --
    # logo klien dari CMS ikut dicadangkan
    if [ -d src/public/uploads ]; then
        tar czf "backups/uploads-$(date +%Y%m%d-%H%M).tgz" -C src/public uploads
        ls -1t backups/uploads-*.tgz | tail -n +15 | xargs -r rm --
    fi
    echo "Backup tersimpan: $FILE"
    exit 0
fi

exec docker compose -f docker-compose.yml -f docker-compose.prod.yml $PROFILE "$@"
