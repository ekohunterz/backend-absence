# Presensi Siswa Berbasis Lokasi

## 📸 Preview


 <img width="1912" height="936" alt="Cuplikan layar 2025-11-14 122815" src="https://github.com/user-attachments/assets/2f1c691e-f2ea-4b46-ba5b-dbcf409d084d" />  <img width="1910" height="941" alt="Cuplikan layar 2025-11-14 123259" src="https://github.com/user-attachments/assets/e761311b-ab0f-4787-89c0-68c7e6f51c74" />
<img width="1903" height="935" alt="Cuplikan layar 2025-11-14 123520" src="https://github.com/user-attachments/assets/dbf990dc-d165-4c26-a702-fc2c406937de" />

                                  

## 🚀 Fitur Utama

- **Autentikasi & Otorisasi**: Sistem login dengan role-based access control
- **Manajemen Pengguna**: CRUD pengguna dengan permission management
- **Manajemen Siswa & Kelas**: CRUD data siswa dan kelas
- **Theme Switching**: Dark/Light mode dengan penyimpanan preferensi
- **Modern UI**: Interface yang responsif dengan Filament 4.x
- **Presensi Siswa berbasis Geolocation**: Fitur presensi yang memanfaatkan data lokasi untuk validasi kehadiran siswa.
- **Notifikasi WhatsApp Otomatis (Menggunakan Fonnte)**: Sistem notifikasi yang mengirimkan pesan WhatsApp secara otomatis kepada orang tua siswa terkait kehadiran atau ketidakhadiran anak mereka.
- **Presensi manual oleh Guru**: Fitur yang memungkinkan guru untuk melakukan presensi manual bagi siswa yang mungkin tidak dapat melakukan presensi mandiri.
- **Laporan Kehadiran**: Fitur untuk menghasilkan laporan kehadiran siswa dalam format yang mudah dibaca dan diunduh.

## 📋 Requirements

- PHP >= 8.2
- Composer
- Node.js & Bun (untuk asset compilation)
- MySQL/PostgreSQL

## 🛠️ Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/ekohunterz/backend-absence.git
cd backend-absence
```

### 2. Konfigurasi Environment

```bash
cp .env.example .env
```

Edit file `.env` dan sesuaikan konfigurasi:

```env
APP_NAME="Backend Absence"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=backend_absence
DB_USERNAME=root
DB_PASSWORD=

# Jika deploy di sub-folder
ASSET_PREFIX=/subfolder

#Jika ingin menggunakan REST API
JWT_SECRET=
JWT_SHOW_BLACKLIST_EXCEPTION=true

#Untuk whatsapp notification (disini menggunakan Fonnte)
WHATSAPP_API_URL=
WHATSAPP_TOKEN=
```

### 3. Install Dependencies

```bash
composer install
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Link Storage

```bash
php artisan storage:link
```

### 6. Initialize Project

Jalankan command berikut untuk migrasi database, seeder, dan permission setup:

```bash
php artisan migrate
```

### 7. Build Assets

```bash
bun install && bun run build
```

### 8. Cache Configuration

```bash
php artisan project:cache
```

## 🚀 Menjalankan Aplikasi

### Development Server

```bash
php artisan serve
```

### Development dengan Vite

Untuk development dengan hot-reload:

```bash
bun run dev
```

## 👤 Default User

Admin

```
Email: admin@admin.com
Password: password
```

## 🔒 Permission Management

Project ini menggunakan **Filament Shield** untuk role & permission management. Setiap Resource dan Page secara otomatis mendapatkan permissions:

- `view_[resource]`
- `view_any_[resource]`
- `create_[resource]`
- `update_[resource]`
- `delete_[resource]`
- `delete_any_[resource]`

Kelola permissions melalui dashboard: **Settings → Roles**

## 🐛 Debugging

### Debug Bar

Laravel Debugbar akan aktif secara otomatis di environment `local`:

```env
DEBUGBAR_ENABLED=true
```

### Log Viewer

Akses log viewer melalui menu di sidebar atau:

```
/admin/logs
```

## 📝 Commands Cheat Sheet

```bash


# Build assets for production
bun run build

# Watch assets for development
bun run dev
```

## 🤝 Contributing

Contributions are welcome! Silakan buat Pull Request atau laporkan issues di GitHub repository.

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buka Pull Request

## 📄 License

Proyek ini dilisensikan di bawah [MIT License](LICENSE.md).

## 🙏 Credits

- **Filament** - [filamentphp.com](https://filamentphp.com)
- **Laravel** - [laravel.com](https://laravel.com)
- **Fila Starter Kit** - [github.com/raugadh/fila-starter](https://filamentphp.com/plugins/raugadh-fila-starter)
- Semua package maintainers yang terlibat

## 💬 Support

Jika Anda menemukan proyek ini berguna, berikan ⭐ di [GitHub](https://github.com/ekohunterz/backend-absence)!

Untuk pertanyaan atau issues, silakan buat issue di GitHub repository.

---

Made with ❤️ using Laravel & Filament
