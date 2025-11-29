# Troubleshooting - Masalah Reload Page

## Masalah: Reload Page di Browser Tidak Bisa

Jika Anda mengalami masalah saat reload/refresh halaman di browser, berikut beberapa solusi:

### 1. Pastikan Web Server Mengarahkan ke `public/index.php`

**Laravel harus diakses melalui folder `public`:**

❌ **SALAH:**
```
http://localhost/myportofolio/
```

✅ **BENAR:**
```
http://localhost/myportofolio/public/
```

Atau setup virtual host yang mengarah ke folder `public`.

### 2. Cek File `.htaccess` di Folder `public`

Pastikan file `public/.htaccess` ada dan berisi:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### 3. Clear Route Cache

Jalankan command berikut:

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 4. Pastikan Route Terdaftar

Cek route yang terdaftar:

```bash
php artisan route:list
```

Pastikan route `/posts` muncul di daftar.

### 5. Cek Error di Browser Console

Buka Developer Tools (F12) dan cek:
- **Console tab**: Apakah ada JavaScript error?
- **Network tab**: Apakah request ke server berhasil?
- **Response**: Apa yang dikembalikan server?

### 6. Cek Laravel Log

Cek file `storage/logs/laravel.log` untuk melihat error detail:

```bash
tail -f storage/logs/laravel.log
```

### 7. Test dengan `php artisan serve`

Coba jalankan development server Laravel:

```bash
php artisan serve
```

Kemudian akses: `http://localhost:8000/posts`

Jika ini bekerja, berarti masalahnya di konfigurasi web server (Apache/Nginx).

### 8. Cek Permission Folder

Pastikan folder `storage` dan `bootstrap/cache` bisa ditulis:

```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache

# Windows
# Pastikan folder tidak read-only
```

### 9. Cek Konfigurasi Web Server

**Apache:**
Pastikan `mod_rewrite` enabled dan `AllowOverride All` di konfigurasi.

**Nginx:**
Pastikan konfigurasi mengarah ke `public/index.php`:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 10. Cek Environment Variables

Pastikan file `.env` ada dan konfigurasi benar:

```env
APP_URL=http://localhost:8000
```

### 11. Test Route Langsung

Test apakah route bekerja:

```bash
# Test via curl
curl http://localhost:8000/posts

# Atau test di browser
# Buka: http://localhost:8000/posts
```

### 12. Cek Vue.js Build

Jika menggunakan Vite, pastikan assets sudah di-build:

```bash
npm run dev
# atau
npm run build
```

### 13. Hard Refresh Browser

Coba hard refresh:
- **Windows/Linux**: `Ctrl + Shift + R` atau `Ctrl + F5`
- **Mac**: `Cmd + Shift + R`

### 14. Clear Browser Cache

Clear cache browser atau gunakan Incognito/Private mode.

## Solusi Cepat

Jika semua di atas tidak membantu, coba:

1. **Restart web server**
2. **Restart Laravel development server** (jika menggunakan `php artisan serve`)
3. **Clear semua cache:**
   ```bash
   php artisan optimize:clear
   ```
4. **Rebuild assets:**
   ```bash
   npm run build
   ```

## Masalah Umum

### Error 404 saat reload
- **Penyebab**: Route tidak terdaftar atau cache route
- **Solusi**: `php artisan route:clear`

### Blank page
- **Penyebab**: JavaScript error atau assets tidak ter-load
- **Solusi**: Cek browser console dan pastikan Vite dev server berjalan

### 500 Internal Server Error
- **Penyebab**: Error di server-side
- **Solusi**: Cek `storage/logs/laravel.log`


