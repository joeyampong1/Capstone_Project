# 🐾 PetNanny: Web-based Responsive System for On-Demand Pet Sitting

PetNanny is a comprehensive web-based platform designed to connect pet owners with trusted pet sitters. It features secure ID verification, real-time booking management, complaint handling, and a weighted matching algorithm to find the perfect sitter for your furry family.

---

## 🛠️ Tech Stack

*   **Backend:** Laravel 11.x (PHP 8.2+)
*   **Frontend:** Blade, Tailwind CSS, Vite, Alpine.js
*   **Database:** MySQL / MariaDB
*   **AI & Machine Learning:** Python 3.10+, dlib, OpenCV (Facial Recognition & Liveness)
*   **Third-Party Integrations:** Firebase (FCM), Cloudinary (CDN), OCR Space (Text Extraction), Google OAuth, Brevo (SMTP)

---

## 🧠 Core Algorithms & Services

### 1. Weighted Matching Algorithm (Multi-Criteria Decision-Making)

The platform uses a custom **Weighted Scoring Algorithm** (derived from the capstone's Table 7) to rank pet sitters. It calculates a match percentage based on 7 factors, utilizing the **Haversine Formula** for geospatial distance.

**Formula:**

`Score = (Wv × V) + (We × E) + (Wr × R) + (Wc × C) + (Wd × D) + (Wh × H) + (Wt × T)`

**Weight Distribution (Total = 100%):**

| Factor | Weight | Computation |
|--------|--------|-------------|
| **V** (Verified ID) | 20% | Binary: 1 or 0 |
| **D** (Distance) | 20% | Haversine Formula: `minDistance / actualDistance` |
| **R** (Rating) | 15% | `rating / 5.0` |
| **C** (Capability) | 15% | Binary: matches pet type & size |
| **E** (Experience) | 15% | `experience_years / maxExperience` |
| **H** (Health Compatibility) | 10% | Binary: matches special needs / vaccination |
| **T** (Temperament Compatibility) | 5% | Binary: matches pet temperament |

### 2. Face Matching & ID Verification

The system performs facial recognition to verify the user's identity by comparing the uploaded Government ID against their live selfie.

*   **Technology:** Python, `dlib`, `face_recognition`, OpenCV.
*   **Process:** The `FaceMatchService` triggers a Python script (`verify_face.py`) which extracts 128-D facial embeddings.
*   **Formula:** Uses **Euclidean Distance** to calculate the similarity between the two faces. A lower distance indicates a higher confidence match.

### 3. OCR Text Extraction (Optical Character Recognition)

Extracts user details (Name, ID Number, Date of Birth) from uploaded IDs.

*   **API:** OCR Space API.
*   **Process:** The `OcrService` uses a multi-attempt fallback mechanism (testing Engine 1 & 2, with 0°, 90°, and 270° image rotations) to ensure the highest text extraction score. It uses **Regular Expressions (Regex)** and a custom **OCR Misread Correction** algorithm to fix common text errors (e.g., "OR" to "JR", "0" to "O").

---

## 📋 Prerequisites

Before installing, make sure you have the following on your machine:

*   PHP >= 8.2
*   Composer
*   Node.js & NPM
*   MySQL or MariaDB
*   Python >= 3.10
*   **CMake** & **Visual Studio Build Tools (C++)**: required for compiling `dlib` on Windows

---

## 🚀 Installation & Setup

### 1. Clone the Repository

```bash
git clone https://github.com/joeyampong1/Capstone_Project.git
cd Capstone_Project
```

### 2. Install PHP & Node Dependencies

```bash
composer install
npm install
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Open `.env` and configure the following:

*   **Database:** `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
*   **Queue & Cache:** `QUEUE_CONNECTION=database`, `CACHE_STORE=database`
*   **Admin account:** `ADMIN_EMAIL`, `ADMIN_PASSWORD`
*   **API keys:** Firebase (FCM), Cloudinary, OCR Space, Google OAuth, Brevo (SMTP)

### 4. Python Setup (Face Verification)

```bash
python -m venv venv
venv\Scripts\activate
pip install -r requirements.txt
```

> **Note:** On Linux/macOS, activate the virtual environment with `source venv/bin/activate`.
> Installing `dlib` requires **CMake** and **Visual Studio Build Tools (C++)** on Windows.

### 5. Database & Storage

```bash
php artisan migrate --seed
php artisan storage:link
```

### 6. Run the Application

Open **three separate terminals** in the project root:

**Terminal 1: Frontend assets**

```bash
npm run dev
```

**Terminal 2: Laravel server**

```bash
php artisan serve
```

**Terminal 3: Queue worker (emails & notifications)**

```bash
php artisan queue:work
```

Then visit **http://127.0.0.1:8000**.

> **Important:** This system uses the `database` driver for queues. If the queue worker (Terminal 3) is not running, background jobs such as emails and push notifications will stay in the `jobs` table and will **not** be processed.

---

## 👑 Admin Account

The default admin account is automatically generated during database seeding, based on the credentials in your `.env` file. To log in, use the `ADMIN_EMAIL` and `ADMIN_PASSWORD` you configured **before** running `php artisan migrate --seed`.

> **Note:** The dummy credentials used for development are `example@gmail.com` and `example@password`. Never use these in production.

### Creating a Custom Admin Manually

You can also create an admin account through Tinker:

```bash
php artisan tinker
```

```php
\App\Models\User::create([
    'f_name'   => 'Admin',
    'l_name'   => 'User',
    'email'    => 'admin@example.com',
    'password' => bcrypt('your-secure-password'),
    'role'     => 'admin',
]);
```

---

## 🧪 Useful Commands

```bash
php artisan migrate:fresh --seed   # Reset and reseed the database
php artisan view:clear             # Clear compiled Blade views
php artisan optimize:clear         # Clear all cached files
php artisan queue:restart          # Restart queue workers after code changes
npm run build                      # Build assets for production
```

---

## ⚙️ Production Deployment (Queue Worker)

This system uses the `database` driver for Queues and Cache. In production, background tasks (sending emails, push notifications, processing images) **will not run automatically** without a queue worker. You must set up one of the following on your live server:

### Option 1: Supervisor (Recommended for VPS / Ubuntu)

Supervisor keeps `php artisan queue:work` running in the background and restarts it automatically if it crashes. Example config (`/etc/supervisor/conf.d/petnanny-worker.conf`), updating the paths to match your server:

```ini
[program:petnanny-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path-to-your-project/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
numprocs=1
user=www-data
redirect_stderr=true
stdout_logfile=/path-to-your-project/storage/logs/worker.log
```

Then start it:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start petnanny-worker:*
```

### Option 2: Cron Job (For Shared Hosting / cPanel)

If your host does not allow long-running processes, add a Cron Job that runs every minute. Update the path to match your server's directory:

```bash
* * * * * cd /path-to-your-project && php artisan queue:work --stop-when-empty >> /dev/null 2>&1
```

> **After every deployment**, run `php artisan queue:restart` so the workers load the latest code.

---

## 👥 Authors

*   **Joey Ampong**: [@joeyampong1](https://github.com/joeyampong1)

---

## 📄 License

This project was developed as a capstone project. All rights reserved.
