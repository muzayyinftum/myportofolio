# Cara Melihat Data di Database MySQL

Ada beberapa cara untuk melihat data di database MySQL:

## 1. Via phpMyAdmin (Paling Mudah - GUI)

### Langkah-langkah:

1. **Buka phpMyAdmin:**
   - Jika menggunakan XAMPP: `http://localhost/phpmyadmin`
   - Jika menggunakan WAMP: `http://localhost/phpmyadmin`
   - Jika menggunakan Laragon: `http://localhost/phpmyadmin`

2. **Login:**
   - Username: `root` (default)
   - Password: (kosong, atau sesuai konfigurasi Anda)

3. **Pilih Database:**
   - Klik database `laravel` di sidebar kiri

4. **Lihat Table:**
   - Klik table `posts`
   - Data akan ditampilkan dalam bentuk tabel
   - Anda bisa edit, delete, atau tambah data langsung dari sini

## 2. Via Laravel Tinker (Command Line)

### Melihat Semua Posts:

```bash
php artisan tinker
```

Kemudian:
```php
// Lihat semua posts
use App\Models\Post;
Post::all();

// Atau dengan format yang lebih readable
Post::all()->toArray();

// Lihat dengan pagination
Post::paginate(10);

// Lihat post pertama
Post::first();

// Lihat post terakhir
Post::latest()->first();

// Hitung jumlah posts
Post::count();

// Cari berdasarkan ID
Post::find(1);

// Cari berdasarkan author
Post::where('author', 'Test Author')->get();

// Cari berdasarkan title
Post::where('title', 'like', '%test%')->get();
```

### Format Output yang Lebih Baik:

```php
// Dengan foreach
Post::all()->each(function($post) {
    echo "ID: {$post->id}\n";
    echo "Title: {$post->title}\n";
    echo "Author: {$post->author}\n";
    echo "Tags: " . json_encode($post->tags) . "\n";
    echo "---\n";
});
```

## 3. Via API Endpoint (Browser/Postman)

### Get All Posts:

**Via Browser:**
```
http://localhost:8000/api/posts
```

**Via curl:**
```bash
curl http://localhost:8000/api/posts
```

**Via Postman:**
- Method: `GET`
- URL: `http://localhost:8000/api/posts`

### Get Single Post:

```
http://localhost:8000/api/posts/1
```

## 4. Via Frontend (Vue.js Component)

Akses halaman:
```
http://localhost:8000/posts
```

Halaman ini akan menampilkan semua posts dalam bentuk UI yang user-friendly.

## 5. Via MySQL Command Line

### Connect ke MySQL:

```bash
mysql -u root -p
```

Kemudian:
```sql
-- Pilih database
USE laravel;

-- Lihat semua posts
SELECT * FROM posts;

-- Lihat dengan format yang lebih baik
SELECT id, title, author, created_at FROM posts;

-- Lihat posts terbaru
SELECT * FROM posts ORDER BY created_at DESC;

-- Hitung jumlah posts
SELECT COUNT(*) FROM posts;

-- Cari berdasarkan author
SELECT * FROM posts WHERE author = 'Test Author';

-- Cari berdasarkan title
SELECT * FROM posts WHERE title LIKE '%test%';
```

## 6. Via Database Client (GUI Tools)

### MySQL Workbench:
1. Download: https://dev.mysql.com/downloads/workbench/
2. Connect ke database
3. Query: `SELECT * FROM posts;`

### DBeaver:
1. Download: https://dbeaver.io/download/
2. Connect ke MySQL database
3. Browse table `posts`

### TablePlus (Mac/Windows):
1. Download: https://tableplus.com/
2. Connect ke MySQL
3. Browse table `posts`

## 7. Via Laravel Route (Custom)

Buat route khusus untuk melihat data:

**Tambahkan di `routes/web.php`:**
```php
Route::get('/admin/posts', function () {
    $posts = \App\Models\Post::all();
    return view('admin.posts', compact('posts'));
});
```

## Tips & Trik

### Format JSON yang Lebih Readable di Tinker:

```php
// Di Tinker
Post::all()->toJson(JSON_PRETTY_PRINT);
```

### Export Data ke JSON:

```php
// Di Tinker
file_put_contents('posts.json', Post::all()->toJson(JSON_PRETTY_PRINT));
```

### Lihat Data dengan Relasi (jika ada):

```php
// Jika Post punya relasi dengan User
Post::with('user')->get();
```

### Debug Query SQL:

```php
// Lihat SQL query yang dijalankan
DB::enableQueryLog();
Post::all();
dd(DB::getQueryLog());
```

## Quick Commands

### Via Tinker (Paling Cepat):

```bash
php artisan tinker
```

Kemudian:
```php
// Semua posts
Post::all();

// Post pertama
Post::first();

// Jumlah posts
Post::count();

// Posts dengan pagination
Post::paginate(5);
```

### Via Browser (Paling Mudah):

Buka: `http://localhost:8000/api/posts`

### Via phpMyAdmin (Paling Visual):

Buka: `http://localhost/phpmyadmin` → Pilih database → Klik table `posts`


