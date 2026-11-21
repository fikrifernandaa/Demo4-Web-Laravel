# FlipPlay REST API (Laravel 12) — Demo Modul 4

Lanjutan FlipPlay Modul 3 (PHP OOP terminal) menjadi REST API Laravel 12 dengan database MySQL.

## Pemetaan Modul 3 -> Modul 4

| Modul 3 (PHP OOP)                          | Modul 4 (Laravel)                                        |
|--------------------------------------------|----------------------------------------------------------|
| `Media` / `Anime` (abstract + inheritance) | Model `Media` + tabel `media` (kolom `type` = jenis)     |
| `User` + array `$collection`               | Model `User` + pivot `media_user` (belongsToMany)        |
| `MediaLibrary::getAllMedia()`              | `GET /api/media`                                         |
| `MediaLibrary::createAnime()`              | `POST /api/media`                                        |
| `MediaLibrary::searchByTitle()`            | `GET /api/media?title=...` (query scope `titleLike`)     |
| `MediaLibrary::findById()`                 | `GET /api/media/{media}`                                 |
| `User::addToCollection()`                  | `POST /api/users/{user}/collection`                      |
| `User::showCollection()`                   | `GET /api/users/{user}/collection`                       |
| (baru) hapus dari koleksi                  | `DELETE /api/users/{user}/collection/{media}`            |

## Langkah pengerjaan

### 1. Buat project Laravel 12 di repositori GitHub
```bash
composer create-project laravel/laravel flipplay-api
cd flipplay-api
php artisan install:api        # wajib, agar routes/api.php dibuat
git init && git add . && git commit -m "init laravel 12"
```

### 2. Database MySQL
1. Jalankan MySQL (Laragon: Start All / XAMPP: start MySQL).
2. Buat database kosong `flipplay` lewat HeidiSQL / phpMyAdmin.
3. Edit `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=flipplay
DB_USERNAME=root
DB_PASSWORD=
```
4. `php artisan config:clear`

### 3. Salin file dari paket ini ke project (timpa jika ditanya)
```
app/Models/Media.php
app/Models/User.php                      (menimpa bawaan Laravel, + relasi media())
app/Http/Controllers/MediaController.php
app/Http/Controllers/UserController.php
app/Http/Requests/StoreMediaRequest.php
app/Http/Requests/AddToCollectionRequest.php
app/Http/Resources/MediaResource.php
app/Http/Resources/UserResource.php
routes/api.php                           (menimpa hasil install:api)
database/migrations/2026_11_21_000001_create_media_table.php
database/migrations/2026_11_21_000002_create_media_user_table.php
database/seeders/DatabaseSeeder.php
```
Folder `app/Http/Controllers` sudah memiliki `Controller.php` bawaan; jangan dihapus.

### 4. Agar error selalu berupa JSON (404 / 422 tidak redirect)
Di `bootstrap/app.php`, ubah bagian `->withExceptions(...)` menjadi:
```php
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->shouldRenderJsonWhen(
        fn ($request, $e) => $request->is('api/*') || $request->expectsJson()
    );
})
```

### 5. Migrate + seed, lalu jalankan
```bash
php artisan migrate:fresh --seed
php artisan route:list --path=api      # verifikasi semua endpoint terdaftar
php artisan serve
```

### 6. Uji di Postman
Import `docs/FlipPlay.postman_collection.json` (variabel `base_url` = `http://127.0.0.1:8000/api`).
Setiap request sudah memakai header `Accept: application/json`. Jalankan urut dari atas;
nama request mencantumkan status code yang diharapkan.

### 7. Push ke GitHub
```bash
git add . && git commit -m "Demo Modul 4: FlipPlay REST API"
git remote add origin <url-repo> && git push -u origin main
```

## Endpoint

| Method | Endpoint                                  | Keterangan                          | Status        |
|--------|-------------------------------------------|-------------------------------------|---------------|
| GET    | /api/media                                | daftar media, paginate 10           | 200           |
| GET    | /api/media?title=death                    | cari berdasarkan judul              | 200           |
| GET    | /api/media/{media}                        | detail + pengguna pengoleksi        | 200 / 404     |
| POST   | /api/media                                | tambah media                        | 201 / 422     |
| PATCH  | /api/media/{media}                        | ubah sebagian data                  | 200 / 404 / 422 |
| DELETE | /api/media/{media}                        | hapus media                         | 200 / 404     |
| GET    | /api/users                                | daftar pengguna                     | 200           |
| GET    | /api/users/{user}                         | detail pengguna + koleksi           | 200 / 404     |
| GET    | /api/users/{user}/collection              | koleksi media pengguna              | 200           |
| POST   | /api/users/{user}/collection              | tambah ke koleksi `{"media_id":1}`  | 201 / 409 / 422 |
| DELETE | /api/users/{user}/collection/{media}      | hapus dari koleksi                  | 200 / 404     |

## Catatan teknis yang sering jadi bahan pertanyaan
- `Route::apiResource('media', ...)` butuh `->parameters(['media' => 'media'])` karena Laravel menyingularkan "media" jadi "medium".
- Judul unik dicek di Form Request (`Rule::unique(...)->ignore($this->route('media'))`) agar PATCH dengan judul sendiri tidak ditolak.
- Duplikat koleksi dicek dua lapis: `exists()` -> 409, dan unique index `(media_id, user_id)` di pivot sebagai pengaman race condition (Modul 4.4).
- `whenLoaded('users')` / `whenCounted()` / `whenPivotLoaded()` membuat field relasi hanya muncul jika datanya memang dimuat.
