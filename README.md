# Renang Time Keeper - Laravel 11
Aplikasi Time Keeper untuk kompetisi renang dengan Laravel 11

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

---

## 📲 API Endpoints

### Get Dashboard Stats (AJAX)
```
GET /api/dashboard-stats
Response:
{
  "totalPeserta": 15,
  "totalAthletes": 10,
  "fastestTime": "00:14.048",
  "averageTime": "00:18.250",
  "slowestTime": "00:26.954"
}
```

### Get Chart Data
```
GET /api/dashboard-chart-data
Response:
{
  "labels": ["Player 2", "Player 8", "Player 3", ...],
  "data": [14, 6, 18, ...]  // waktu dalam detik
}
```

### Get Results Table HTML
```
GET /api/results-table
Response: HTML table rows
```

---

## ⚙️ Configuration

File: `.env`
```
APP_NAME=RenangApp
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db-renang
DB_USERNAME=root
DB_PASSWORD=
```

---

## 🧪 Testing / Menjalankan Project

### Opsi 1: Menggunakan Laravel Serve (Recommended)
```bash
cd c:\Users\user\Downloads\renang-laravel
php artisan serve
```
Buka browser: http://localhost:8000

### Opsi 2: Menggunakan Laragon
1. Copy folder `renang-laravel` ke folder websites Laragon
2. Buka Laragon, klik "Stop All", lalu "Start All"
3. Di Laragon, right-click pada folder → "Open in Browser"

### Opsi 3: Setup Virtual Host Manual
```apache
<VirtualHost *:80>
    ServerName renang.local
    DocumentRoot "c:/Users/user/Downloads/renang-laravel/public"
    
    <Directory "c:/Users/user/Downloads/renang-laravel/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

---

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

## 📁 Backup Data dari Project Lama

Data dari file PHP lama (`renang-edit1`) dapat diimpor:

```php
// Import hasil_lomba dari file lama
$oldData = include 'path/to/old_data.php';
foreach ($oldData as $item) {
    HasilLomba::create($item);
}
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

## 📞 Support
Untuk pertanyaan atau issue, hubungi pembimbing magang.

---

**Last Updated:** March 14, 2026
**Version:** Laravel 11.0
**Status:** ✅ Ready for Development
