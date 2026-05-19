# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **CodeIgniter 4 quiz application** for electronic/electrical topics. Users answer questions by drawing paths between points on circuit diagram images. The system has two roles: **admin** (manages categories, questions, and users) and **user** (takes quizzes and tracks progress).

## Commands

### Development
```bash
php spark serve          # Run at http://localhost:8080
```

### Database
```bash
php spark migrate          # Run all migrations
php spark db:seed QuizSeeder  # Seed test users
php spark db:seed          # Run all seeders
```

### Tests
```bash
./vendor/bin/phpunit                      # All tests
./vendor/bin/phpunit tests/Unit/HealthTest.php  # Specific test
```

### Cache
```bash
php spark cache:clear
```

## Architecture

### User Flow
1. User visits `/` → redirected to `/login`
2. Register or login (session-based auth via `AuthFilter`)
3. Role determines routing:
   - Admin → `/admin/*` routes
   - User → `/user/*` routes

### Quiz System (Core Concept)
```
Admin creates: Gambar (circuit image) → Titik (connection points) → JalurJawaban (correct paths)
User draws: JawabanUser (user's submitted paths)
Scoring: Compare JawabanUser.paths against JalurJawaban.paths
```

Admin workflow:
1. Upload circuit diagram image
2. Place "Titik" (clickable connection points) on the image
3. Draw "JalurJawaban" (correct answer paths between points)
4. User sees image with points, draws their answer paths
5. System compares user's paths against correct paths, calculates score

### Role-Based Access
- `AuthFilter` checks `session->get('role')` against route groups
- `role === 'admin'` → `/admin/*` routes
- `role === 'user'` → `/user/*` routes

### Key Route Files
- `app/Config/Routes.php` - Route definitions
- `app/Filters/AuthFilter.php` - Session authentication
- `app/Controllers/Auth.php` - Login/register/logout
- `app/Controllers/Admin.php` - Admin CRUD (kategori, soal, users)
- `app/Controllers/User.php` - User quiz flow (kategori, kerjaka, riwayat)

### Database Schema (from db.sql)
```
users (id, username, email, password, nama_lengkap, role, status, created_at, updated_at)
kategori (id, nama_kategori, deskripsi, icon, status, created_at, updated_at)
soal (id, id_kategori, nama_soal, deskripsi, gambar, img_width, img_height, status, created_at, updated_at)
titik (id, id_soal, x, y, label, created_at)
jalur_jawaban (id, id_soal, titik_a_id, titik_b_id, style, control_points, created_at)
jawaban_user (id, id_user, id_soal, titik_a_id, titik_b_id, created_at)
```
Foreign keys cascade on delete. The `soal.id_kategori` FK should be added via migration if missing.

## Important Code Patterns

### Controllers return JSON for AJAX endpoints
```php
return $this->response->setJSON(['success' => true, 'message' => '...']);
```

### Models use CodeIgniter Model
```php
$userModel->find($id);           // Find by PK
$userModel->where('role', 'user')->findAll(); // Query
$userModel->insert($data);        // Insert
$userModel->update($id, $data);   // Update
$userModel->delete($id);         // Hard delete
```

### Session auth
```php
session()->set(['user_id' => $user['id'], 'role' => $user['role'], 'logged_in' => true]);
session()->get('user_id');
```

### File uploads
- Images stored in `FCPATH . 'uploads/soal/`
- Always verify upload: `$gambar->isValid()`
- Delete old file before overwriting: `unlink($oldPath)`
- Use transactions for multi-table operations:
```php
$db = \Config\Database::connect();
$db->transStart();
// ... operations
$db->transComplete();
if ($db->transStatus() === false) { /* handle error */ }
```

### CSRF Protection
- AJAX requests must include CSRF token from meta tags or cookie
- Meta tags: `csrf_token()` and `csrf_hash()` in views
- FormData: `formData.append(csrfName, csrfHash)`

### JavaScript Utilities
- `public/js/path-utils.js` - Auto-routing algorithms (path avoidance)
- `public/js/canvas-utils.js` - Drawing functions (points, paths, hit detection)
- Canvas uses `touch-action: none` CSS for mobile touch handling

### Test Users (after seeding)
- Admin: `admin` / `admin123`
- User: `user1` / `user123`

## Database Config
MySQL database `quiz_elektronika` configured in `.env` under `database.default.*`

## Security Notes
- Passwords hashed with `password_hash(PASSWORD_DEFAULT)`
- CSRF protection enabled (cookie-based)
- Session expiry: 7200 seconds (2 hours)
- File uploads should validate MIME type and size before processing
