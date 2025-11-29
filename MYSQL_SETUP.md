# Setup MySQL untuk Laravel

## Konfigurasi Database

### 1. Setup Environment Variables

Tambahkan konfigurasi berikut ke file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

**Catatan:** 
- Sesuaikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` dengan konfigurasi MySQL Anda
- Jika menggunakan XAMPP/WAMP, biasanya username adalah `root` dan password kosong

### 2. Buat Database MySQL

**Via MySQL Command Line:**
```sql
CREATE DATABASE laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Via phpMyAdmin:**
1. Buka phpMyAdmin (biasanya di `http://localhost/phpmyadmin`)
2. Klik "New" untuk membuat database baru
3. Nama database: `laravel`
4. Collation: `utf8mb4_unicode_ci`
5. Klik "Create"

### 3. Jalankan Migration

Setelah database dibuat, jalankan migration:

```bash
php artisan migrate
```

Ini akan membuat table `posts` dan table lainnya yang diperlukan.

### 4. Test Koneksi

**Via Tinker:**
```bash
php artisan tinker
```

Kemudian:
```php
use App\Models\Post;

// Test create
$post = Post::create([
    'title' => 'Test Post',
    'content' => 'Ini adalah test',
    'author' => 'Test Author',
    'tags' => ['test', 'mysql']
]);

// Test read
Post::all();
```

**Via API:**
```bash
# Get all posts
curl http://localhost:8000/api/posts

# Create post
curl -X POST http://localhost:8000/api/posts \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Judul Post",
    "content": "Konten post",
    "author": "Nama Author",
    "tags": ["laravel", "mysql"]
  }'
```

## Struktur Table Posts

Table `posts` memiliki struktur berikut:

- `id` - Primary key (auto increment)
- `title` - String (required)
- `content` - Text (required)
- `author` - String (required)
- `tags` - JSON (nullable) - Array of tags
- `created_at` - Timestamp
- `updated_at` - Timestamp

## Troubleshooting

### Error: "Access denied for user"
- Pastikan username dan password di `.env` benar
- Pastikan user MySQL memiliki permission untuk database

### Error: "Unknown database"
- Pastikan database sudah dibuat
- Cek nama database di `.env` sesuai dengan yang dibuat

### Error: "Table doesn't exist"
- Jalankan migration: `php artisan migrate`

### Error: "PDOException: could not find driver"
- Install PHP MySQL extension:
  - Windows: Enable `php_pdo_mysql` di `php.ini`
  - Linux: `sudo apt-get install php-mysql`
  - Mac: Extension biasanya sudah terinstall dengan Homebrew

## Migration Commands

```bash
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Refresh (rollback + migrate)
php artisan migrate:refresh

# Fresh (drop all tables + migrate)
php artisan migrate:fresh
```

## Seeder (Opsional)

Jika ingin membuat data dummy:

```bash
php artisan make:seeder PostSeeder
```

Kemudian edit `database/seeders/PostSeeder.php` dan jalankan:
```bash
php artisan db:seed --class=PostSeeder
```


