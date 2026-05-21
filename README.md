# AsetSimulator - Elektronik Quiz App

Aplikasi quiz elektronika berbasis web menggunakan CodeIgniter 4. Pengguna menjawab soal dengan cara menggambar jalur koneksi antara titik-titik pada diagram rangkaian elektronika.

![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white)
![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.x-EE4623?style=flat-square&logo=codeigniter&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind%20CSS-3.x-38B2AC?style=flat-square&logo=tailwindcss&logoColor=white)

## Fitur

### Untuk Admin
- **Manajemen Kategori** - Buat dan kelola kategori soal
- **Manajemen Soal** - Upload gambar rangkaian, tempatkan titik koneksi, gambar jalur jawaban
- **Auto-Routing** - Sistem otomatis membuat jalur避开 titik-titik lain
- **Pengaturan Ukuran** - Ukuran titik customizable per soal (1-48px)
- **Manajemen User** - Kelola akun pengguna

### Untuk User
- **Dashboard Quiz** - Lihat daftar quiz per kategori
- **Canvas Interaktif** - Gambar jalur koneksi antar titik
- **Drag & Drop** - Sambungkan titik dengan drag atau klik
- **Fullscreen Mode** - Mode layar penuh dengan dukungan pinch-to-zoom (Android & iOS)
- **Cek Jawaban** - Periksa jawaban dan lihat skor
- **Riwayat** - Lihat history pengerjaan

### Fitur Teknis
- **Role-Based Access** - Admin dan User dengan route terpisah
- **CSRF Protection** - Keamanan form dengan CSRF token
- **Session Auth** - Login/register dengan session
- **Responsive Design** - Mendukung desktop dan mobile
- **Touch Support** - Drag, click, dan pinch-to-zoom untuk mobile
- **Auto-Routing Algorithms** - A* pathfinding, elbow, bezier curves

## Screenshots

```
┌─────────────────────────────────────────┐
│           Circuit Diagram               │
│                                         │
│    ●───path────●                        │
│    ↑            ↓                       │
│    ●────────────●                       │
│                                         │
│    Titik: ●    Jalur: ───               │
└─────────────────────────────────────────┘
```

## Tech Stack

- **Backend:** PHP 8.2+ dengan CodeIgniter 4
- **Frontend:** HTML5 Canvas, Vanilla JavaScript, Tailwind CSS
- **Database:** MySQL 8.0
- **Icons:** Font Awesome 6
- **Styling:** Tailwind CSS via CDN

## Instalasi

### Prerequisites
- PHP 8.2 or higher
- MySQL 8.0 atau MariaDB 10.4+
- Composer
- Web server (Apache/Nginx) atau PHP built-in server

### Steps

1. **Clone repository**
```bash
git clone <repository-url>
cd soal
```

2. **Install dependencies**
```bash
composer install
```

3. **Setup database**
```bash
# Buat database MySQL
mysql -u root -p -e "CREATE DATABASE quiz_elektronika"

# Edit .env dengan credentials database kamu
cp env .env
```

4. **Configure .env**
```env
# Database
database.default.hostname = localhost
database.default.database = quiz_elektronika
database.default.username = root
database.default.password = your_password

# App base URL
app.baseURL = 'http://localhost:8080'
```

5. **Run migrations**
```bash
php spark migrate
```

6. **Seed test data (optional)**
```bash
php spark db:seed QuizSeeder
```

7. **Start server**
```bash
php spark serve
```

Buka `http://localhost:8080` di browser.

## Test Accounts

Setelah seeding, kamu bisa login dengan:

| Role  | Username | Password |
|-------|----------|----------|
| Admin | admin    | admin123 |
| User  | user1    | user123  |

## Cara Kerja

### Admin Membuat Soal

```
1. Upload Gambar Rangkaian
   ↓
2. Tempatkan Titik Koneksi (klik untuk menambah)
   ↓
3. Atur Ukuran Titik (slider 1-48px)
   ↓
4. Edit Label Titik (R, Y, W, G, +, -, dll)
   ↓
5. Gambar Jalur Jawaban (klik titik A → titik B)
   ↓
6. Sistem auto-routing membuat jalur避开 titik lain
   ↓
7. Simpan Soal
```

### User Mengerjakan Soal

```
1. Pilih Kategori → Pilih Soal
   ↓
2. Lihat gambar rangkaian dengan titik-titik
   ↓
3. Klik/Drag titik A ke titik B untuk menggambar jalur
   ↓
4. Klik jalur untuk hapus jika salah
   ↓
5. Klik "Periksa" untuk cek jawaban
   ↓
6. Lihat skor dan hasil
```

## Database Schema

```
┌─────────────┐     ┌──────────────┐
│   users     │     │   kategori   │
├─────────────┤     ├──────────────┤
│ id          │     │ id           │
│ username    │     │ nama_kategori │
│ email       │     │ deskripsi     │
│ password    │     │ icon         │
│ nama_lengkap│     │ status       │
│ role        │     └──────────────┘
│ status      │            │
└─────────────┘            │
       │                   │
       │              ┌────┴────┐
       │              │         │
       │         ┌────┴────┐    │
       │         │  soal   │◄───┘
       │         ├─────────┤
       │         │ id      │
       │         │ id_kategori
       │         │ nama_soal
       │         │ deskripsi
       │         │ gambar
       │         │ img_width
       │         │ img_height
       │         │ status
       │         └────┬────┘
       │              │
       │    ┌─────────┴─────────┐
       │    │                   │
       │    ▼                   ▼
       │ ┌──────────┐   ┌──────────────┐
       │ │  titik   │   │ jalur_jawaban│
       │ ├──────────┤   ├──────────────┤
       │ │ id       │   │ id           │
       │ │ id_soal  │   │ id_soal      │
       │ │ x, y     │   │ titik_a_id  │
       │ │ label    │   │ titik_b_id  │
       │ │ ukuran   │   │ style       │
       │ └──────────┘   │ control_points
       │                └──────────────┘
       │
       │              ┌──────────────┐
       └─────────────►│ jawaban_user │
                      ├──────────────┤
                      │ id           │
                      │ id_user      │
                      │ id_soal      │
                      │ jawaban_json │
                      │ skor         │
                      │ created_at   │
                      └──────────────┘
```

## Struktur Folder

```
soal/
├── app/
│   ├── Config/          # Konfigurasi CodeIgniter
│   ├── Controllers/     # Logic controller
│   │   ├── Admin.php     # CRUD admin
│   │   ├── Auth.php      # Login/Register
│   │   └── User.php      # Quiz user
│   ├── Filters/         # Auth filter
│   ├── Models/          # Database models
│   └── Views/           # View templates
│       ├── admin/       # Admin views
│       ├── layouts/     # Layout templates
│       └── user/        # User views
├── database/
│   ├── migrations/     # Database migrations
│   └── seeds/          # Test data seeds
├── public/
│   └── uploads/        # Uploaded images
├── tests/             # Unit tests
└── README.md
```

## API Endpoints

### Admin
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/admin/dashboard` | Dashboard admin |
| GET | `/admin/kategori` | Daftar kategori |
| POST | `/admin/kategori/simpan` | Tambah kategori |
| GET | `/admin/soal` | Daftar soal |
| GET | `/admin/soal/tambah` | Form tambah soal |
| POST | `/admin/soal/simpan` | Simpan soal baru |
| GET | `/admin/soal/edit/{id}` | Form edit soal |
| POST | `/admin/soal/update/{id}` | Update soal |

### User
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/user/kategori` | Daftar kategori |
| GET | `/user/dashboard/{id}` | Daftar soal |
| GET | `/user/kerjakan/{id}` | Halaman pengerjaan |
| POST | `/user/soal/simpan-jawaban` | Simpan jawaban |
| GET | `/user/riwayat` | Riwayat pengerjaan |

## Konfigurasi Penting

### Ukuran Titik
Ukuran titik disimpan per-soal di kolom `ukuran` tabel `titik`. Nilai 1-48px.

### Jalur Jawaban
Style jalur: `straight`, `elbow`, atau `bezier`. Control points disimpan sebagai JSON.

### Auto-Routing
Sistem otomatis membuat jalur避开 titik-titik lain menggunakan algoritma:
1. Straight line (jika tidak ada collision)
2. Elbow path (L-shaped)
3. Bezier curve
4. A* pathfinding (sebagai fallback)

## Troubleshooting

### Zoom tidak work di Android fullscreen?
Pastikan viewport meta tag mengijinkan zoom:
```html
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
```

### Titik tidak clickable?
Cek apakah touch-action CSS tidak block events:
```css
.main-canvas {
    touch-action: none;
}
```

## License

MIT License - Bebas digunakan untuk keperluan apapun.

## Contributing

1. Fork repository
2. Buat branch baru (`git checkout -b feature/fitur-baru`)
3. Commit perubahan (`git commit -m 'Menambah fitur baru'`)
4. Push ke branch (`git push origin feature/fitur-baru`)
5. Buat Pull Request

---

Made with ❤️ for learning electronics
