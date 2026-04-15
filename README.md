# Renang Dashboard Laravel 
Web based kompetisi renang 11

## 📋 Struktur Project Laravel

```
renang-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php    # Dashboard stats & charts
│   │   │   ├── AthleteController.php      # Manage peserta
│   │   │   ├── ResultController.php       # Hasil lomba
│   │   │   └── RegistrationController.php # Registrasi event
│   │   ├── Kernel.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── Event.php
│   │   ├── Athlete.php
│   │   ├── Registration.php
│   │   └── HasilLomba.php
│   ├── Console/
│   ├── Providers/
│   │   └── AppServiceProvider.php
├── database/
│   ├── migrations/
│   │   ├── create_events_table.php
│   │   ├── create_athletes_table.php
│   │   ├── create_registrations_table.php
│   │   └── create_hasil_lomba_table.php
│   └── seeders/
│       └── EventSeeder.php
├── resources/
│   ├── views/
│   │   ├── dashboard/
│   │   │   └── index.blade.php           # Dashboard utama ⭐
│   │   ├── athletes/
│   │   │   └── index.blade.php           # List peserta
│   │   └── results/
│   │       └── index.blade.php           # Hasil waktu
│   ├── css/
│   └── js/
├── routes/
│   ├── web.php                           # Web routes
│   └── console.php
├── public/
│   └── index.php                         # Entry point
├── bootstrap/
│   └── app.php
├── storage/
├── vendor/                               # Dependencies (setelah install)
├── config/
│   └── database.php
├── .env                                  # Environment config
├── .env.example
├── artisan                               # Laravel CLI
├── composer.json
└── README.md (file ini)
```

## 🚀 Installation & Setup

### Prasyarat:
- PHP 8.2+ (sudah ada: 8.3.28)
- Composer (ada di Laragon)
- MySQL (Laragon)
- Database: `db-renang` (existing)

### Step-by-step Installation:

#### 1. Install Dependencies
```bash
cd renang-laravel
php "C:\laragon\bin\composer\composer.phar" install --prefer-dist --no-dev
```

#### 2. Generate Application Key
```bash
php artisan key:generate
```

#### 3. Database Migration (menggunakan DB Renang yang sudah ada)
```bash
php artisan migrate
```

Jika ada error karena table sudah ada, jalankan:
```bash
php artisan migrate --force
```

#### 4. Seed Events Data (opsional)
```bash
php artisan db:seed --class=EventSeeder
```

#### 5. Start Development Server
```bash
php artisan serve
```

Server akan berjalan di: `http://localhost:8000`

---

## 📊 Fitur Dashboard

### 1. **Statistics Box**
- ✅ Total Peserta Finish
- ✅ Waktu Tercepat
- ✅ Rata-rata Waktu
- ✅ Total Atlet Terdaftar

### 2. **Grafik Visualisasi**
- ✅ Bar Chart: Waktu peserta (semua)
- ✅ Leaderboard: Top 5 Tercepat

### 3. **Real-time Table**
- ✅ Hasil Waktu Timekeeper (auto-refresh setiap 2 detik)
- ✅ Kolom: No, Player, Waktu (menit/detik/ms), Format, Waktu Input

### 4. **Navigation Menu**
- Dashboard
- Peserta (Tambah & List)
- Hasil Waktu

---

## 📝 Database Schema

Database: `db-renang`

### Table: events
```sql
- id (PK)
- nama_event VARCHAR(255) UNIQUE
- created_at, updated_at
```

### Table: athletes
```sql
- id (PK)
- nama VARCHAR(255)
- tanggal_lahir DATE
- jenis_kelamin ENUM('L', 'P')
- id_card_path VARCHAR(500)
- asal_club_sekolah VARCHAR(255)
- created_at, updated_at
```

### Table: registrations
```sql
- id (PK)
- athlete_id (FK)
- event_id (FK)
- kategori_umur VARCHAR(50)
- seed_time VARCHAR(50)
- created_at, updated_at
```

### Table: hasil_lomba
```sql
- id (PK)
- player VARCHAR(100)
- waktu_ms INT (milliseconds)
- waktu_detik INT (seconds)
- waktu_menit INT (minutes)
- waktu_format VARCHAR(50) - format "MM:SS.ms"
- timestamp TIMESTAMP
```

---

## 🔄 Routes

```
GET      /                           - Dashboard (Show stats)
GET      /dashboard                  - Dashboard alias
GET      /athletes                   - List all athletes
POST     /athletes                   - Store new athlete
GET      /athletes/{athlete}         - Show athlete details
GET      /registrations              - List registrations
POST     /registrations              - Store registration
GET      /results                    - List results
GET      /results/data               - Get results JSON
POST     /results                    - Store result
GET      /api/results-table          - AJAX table data
GET      /api/athletes-table         - AJAX athletes data
GET      /api/dashboard-stats        - AJAX stats
GET      /api/dashboard-chart-data   - AJAX chart data
```


## 🧪 Testing / Menjalankan Project

### Opsi 1: Menggunakan Laravel Serve (Recommended)
```bash
cd c:\Users\user\Downloads\renang-laravel
php artisan serve
```
Buka browser: http://localhost:8000


## 🔧 Commands Berguna

```bash
# Generate key
php artisan key:generate

# Run migrations
php artisan migrate

# Run migrations dengan reset
php artisan migrate:fresh

# Seed data
php artisan db:seed

# Clear cache
php artisan cache:clear
php artisan config:cache

# List routes
php artisan route:list

# Tinker (interactive shell)
php artisan tinker
```

---


## 🐛 Troubleshooting

### Error: "Class not found"
```bash
php artisan dump-autoload
composer dump-autoload
```

### Error: "Column not found"
```bash
php artisan migrate:fresh  # Reset migrations
```

### Permission Denied (storage/logs, bootstrap/cache)
```bash
# Windows
attrib -R storage bootstrap
```

### Database Connection Error
- Pastikan MySQL running di Laragon
- Check `.env` database credentials
- Pastikan database `db-renang` exist

---

## 📚 Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [Blade Templates](https://laravel.com/docs/blade)
- [Chart.js Documentation](https://www.chartjs.org/)

---

## 🎯 Next Steps untuk Pengembangan

### Fitur yang bisa ditambahkan:
- [ ] Authentication & Authorization
- [ ] Export ke Excel/PDF
- [ ] Advanced Filtering & Search
- [ ] Real-time notification
- [ ] Mobile responsive improvements
- [ ] API Documentation (Swagger)
- [ ] Unit & Feature Tests


---

**Last Updated:** March 14, 2026
**Version:** Laravel 11.0
**Status:** ✅ Ready for Development
