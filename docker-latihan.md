# Docker Laravel Backend

Catatan ringkas dari proses belajar Docker untuk backend Laravel ini.

## 1. Tujuan Setup

Targetnya ada 3 service:

- `app` untuk Laravel
- `postgres` untuk database
- `adminer` untuk lihat isi database

Alur umumnya:

```text
Browser -> app -> postgres
Browser -> adminer -> postgres
```

## 2. Docker Compose

`docker-compose.yml` dipakai untuk mendeskripsikan service, port, volume, dan network.

Contoh penting:

- `app` build dari `Dockerfile`
- `postgres` pakai image `postgres:16`
- `adminer` pakai image `adminer`
- `ports` dipakai untuk akses dari laptop
- `depends_on` dipakai untuk urutan start
- `healthcheck` dipakai supaya `postgres` dianggap siap

## 3. Database di Docker

Postgres menyimpan data ke volume:

```yaml
volumes:
  - logbook_postgres_data:/var/lib/postgresql/data
```

Artinya data database tidak hilang kalau container dihapus.

Hal penting:

- data baru langsung masuk ke volume
- `docker compose down` tidak menghapus volume biasa
- `docker compose down -v` berisiko menghapus data

## 4. Adminer

Adminer adalah web UI ringan untuk database.

Fungsinya:

- login ke Postgres
- lihat tabel
- cek data
- bantu debugging database

Saat login dari container, host database harus pakai nama service:

```text
postgres
```

bukan `127.0.0.1`.

## 5. App Laravel di Docker

Dockerfile dipakai untuk membangun image Laravel.

Inti prosesnya:

```text
Dockerfile -> image -> container
```

Bagian penting Dockerfile:

- install extension PHP yang dibutuhkan
- copy Composer
- copy source code
- jalankan `composer install`
- start server

## 6. `vendor`

`vendor` adalah folder dependency PHP hasil `composer install`.

Laravel butuh `vendor/autoload.php` supaya class package bisa dipakai.

Best practice untuk development:

- source code pakai bind mount
- `vendor` pakai named volume

Contoh:

```yaml
volumes:
  - ./:/var/www/html
  - logbook_vendor:/var/www/html/vendor
```

## 7. Network

Network dipakai supaya container bisa saling komunikasi.

Di Compose, service dalam project yang sama bisa saling akses pakai nama service:

```text
app -> postgres:5432
adminer -> postgres:5432
```

Untuk frontend dan backend beda repo, tidak selalu harus satu network. Kalau request API dilakukan dari browser, cukup expose port backend ke host.

## 8. Container Name

`container_name` boleh dipakai, tapi bukan best practice utama.

Lebih rapi kalau dibiarkan otomatis oleh Compose.

Alasannya:

- lebih fleksibel
- tidak gampang bentrok
- lebih enak untuk environment berbeda

## 9. Start, Stop, Recreate, Build

Bedanya:

- `up` = buat dan jalankan container
- `start` = nyalakan container yang sudah ada
- `stop` = matikan container
- `restart` = stop lalu start lagi
- `down` = hapus container dan network project
- `up --force-recreate` = paksa bikin container baru
- `up --build` = rebuild image dari Dockerfile

Patokan praktis:

- ubah code Laravel -> cukup refresh browser
- ubah `docker-compose.yml` -> `up -d --force-recreate`
- ubah `Dockerfile` -> `up -d --build`
- container macet -> `restart`

## 10. Build Cache

Docker build berjalan per layer.

Yang jarang berubah sebaiknya ditaruh di atas:

- install package sistem
- install extension PHP
- copy `composer.json` dan `composer.lock`
- `composer install`

Yang sering berubah ditaruh di bawah:

- copy seluruh source code

Tujuannya supaya build lebih cepat.

## 11. `composer install` vs `composer update`

- `composer install` = pakai versi yang terkunci di `composer.lock`
- `composer update` = cari versi terbaru dan bisa mengubah lock file

Di Docker, biasanya pakai `composer install`.

## 12. Inspect, Logs, Exec, Top

Perintah debugging yang sering dipakai:

```powershell
docker compose --env-file .env.docker ps
docker compose --env-file .env.docker logs app
docker inspect backend-app-1
docker top backend-app-1
docker compose --env-file .env.docker exec app sh
```

Fungsi masing-masing:

- `logs` = lihat output aplikasi
- `inspect` = lihat detail container
- `top` = lihat proses yang sedang jalan
- `exec` = masuk ke container

## 13. Ringkasan Alur Kerja

Alur kerja yang dipakai sekarang:

```text
1. Edit code Laravel
2. Refresh browser
3. Kalau ubah Compose -> recreate
4. Kalau ubah Dockerfile -> build
5. Kalau butuh dependency baru -> composer install di container
```

## 14. Kesimpulan

Setup backend ini sekarang terdiri dari:

- Laravel app container
- Postgres container
- Adminer container
- volume untuk data Postgres
- volume untuk `vendor`

Semua itu dipakai supaya development Laravel di Docker tetap konsisten, mudah di-debug, dan data database aman.
