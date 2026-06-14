# QR Code Generator

> **Custom QR Code Generation Engine with Brand Identity Support**
>
> Production-ready Laravel web utility for generating, customizing, and managing QR codes. Designed for marketing campaigns, personal branding, and asset digitization with full color and logo customization.

## About This Project

A focused, single-purpose web application built to simplify QR code creation while maintaining full visual customization. Users can generate scannable QR codes from URLs or plain text, personalize them with brand colors, and embed a logo at the center — all within a clean, responsive interface.

The project demonstrates practical application of QR code rendering libraries, GD-based image manipulation, and Laravel storage architecture in a compact, easy-to-deploy package.

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v3-06B6D4?style=flat&logo=tailwindcss)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## Table of Contents

- [Live Demo](#live-demo)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Screenshots](#screenshots)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [Troubleshooting](#troubleshooting)
- [License](#license)

---

## Live Demo

- **Public Application**: [https://qrcode.pindahkedigital.com](https://qrcode.pindahkedigital.com) *(placeholder — update when deployed)*
- **Local Preview**: `http://127.0.0.1:8001`

---

## Features

### QR Code Generation

- **Text & URL Input**: Generate QR codes from any string or URL.
- **Scannable Output**: PNG export optimized for both digital and print use.
- **History Tracking**: Automatically saves generated QR codes for re-download.

### Visual Customization

- **Finder Patterns (Outer)**: Customize the color of the three corner positioning squares.
- **Finder Patterns (Inner)**: Customize the color of the inner squares inside the finder patterns.
- **Data Modules**: Customize the color of the main data dots.
- **Background**: Customize the QR code background color.
- **Logo Integration**: Upload a PNG/JPG logo to place at the center of the QR code.

### User Experience

- **Single-Page Interface**: Built with Tailwind CSS for fast, responsive interaction.
- **Live Preview**: Generated QR code displayed immediately after submission.
- **Download Support**: One-click PNG download for any generated QR code.
- **Asset Storage**: QR codes and uploaded logos stored under `storage/app/public`.

---

## Tech Stack

| Layer         | Technology         | Version     |
|---------------|--------------------|-------------|
| Framework     | Laravel            | 12.x        |
| Language      | PHP                | 8.2+        |
| Frontend      | Tailwind CSS       | v3          |
| Templating    | Blade              | -           |
| QR Engine     | chillerlan/php-qrcode | 5.x / 6.x |
| Image Backend | GD Extension       | -           |
| Database      | SQLite / MySQL     | -           |
| Storage       | Laravel Filesystem | local/public |

---

## Screenshots

### Generator Page

![QR Generator](docs/screenshots/generator.png)
_Input form with color pickers and optional logo upload_

### Generated Result

![QR Result](docs/screenshots/result.png)
_Customized QR code with centered logo and brand colors_

### Generation History

![QR History](docs/screenshots/history.png)
_History table for quick access to previous QR codes_

---

## Installation

### Prerequisites

- PHP >= 8.2
- Composer
- GD PHP extension enabled
- Node.js & NPM (for Tailwind CSS asset building)
- MySQL or SQLite

### Step 1: Clone & Install

```bash
git clone https://github.com/otnaylus/qrcode.git
cd qrcode
composer install
npm install
```

### Step 2: Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```env
APP_NAME="QR Code Generator"
APP_URL=http://127.0.0.1:8001

DB_CONNECTION=sqlite
# Or MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=qrcode
# DB_USERNAME=your_db_user
# DB_PASSWORD=your_db_password
```

### Step 3: Database & Storage

```bash
php artisan migrate
php artisan storage:link
```

### Step 4: Build Assets

```bash
npm run build
```

### Step 5: Start Server

```bash
php artisan serve --port=8001
```

Then open `http://127.0.0.1:8001/qrcode` in your browser.

---

## Configuration

### Enable GD Extension

This application uses `chillerlan/php-qrcode` with the GD image backend. Ensure GD is enabled in your `php.ini`:

```ini
extension=gd
```

Verify with:

```bash
php -m | grep gd
```

### Storage Symlink

QR code images are saved to `storage/app/public/qr_codes/` and served through the `public/storage` symlink. If images return 403 errors, recreate the symlink:

```bash
php artisan storage:link
```

---

## Usage

1. Open the application at `/qrcode`.
2. Enter the text or URL you want to encode.
3. Set width and height (recommended: 400x400 px).
4. Choose colors for:
   - Finder Patterns (outer)
   - Finder Patterns (inner)
   - Data Modules
   - Background
5. Optionally upload a logo image (PNG/JPG, max 2MB).
6. Click **Generate QR Code**.
7. Preview, download, or view the saved entry in the history table.

---

## Project Structure

```
qrcode/
|-- app/
|   |-- Http/
|   |   |-- Controllers/
|   |   |   |-- QRCodeController.php    # Form handling & generation flow
|   |-- Models/
|   |   |-- QRCode.php                   # Eloquent model for generated records
|   |-- Services/
|   |   |-- QRCodeGenerator.php          # QR generation & logo overlay logic
|-- database/
|   |-- migrations/
|   |   |-- 2026_05_29_234713_create_q_r_codes_table.php
|   |   |-- 2026_05_29_235712_add_color_fields_to_qr_codes_table.php
|   |   |-- 2026_06_14_155958_add_finder_inner_color_to_qr_codes_table.php
|-- resources/
|   |-- views/
|   |   |-- qrcode/
|   |   |   |-- index.blade.php          # Main application UI
|-- routes/
|   |-- web.php                          # Application routes
|-- public/
|   |-- images/
|   |   |-- logo.png                     # Header logo (left)
|   |   |-- qrcode.png                   # Header QR sample (right)
|-- composer.json
|-- README.md
```

---

## Key Implementation Details

### QR Code Engine

The generator uses `chillerlan/php-qrcode` with the `QRGdImagePNG` output backend. This avoids dependency on the Imagick extension and keeps deployment simple on standard shared hosting.

### Logo Overlay

A logo space is reserved in the center of the QR matrix using `setLogoSpace()` with ECC level H (30% error correction). The uploaded logo is then resized and composited into that reserved area using GD functions, ensuring the QR code remains scannable.

### Color Mapping

All QR matrix module types are mapped to user-selected colors:

- `M_FINDER_DARK` → Finder outer color
- `M_FINDER_DOT` → Finder inner color
- `M_DATA_DARK` and functional dark modules → Data color
- Light modules (`M_DATA`, `M_FINDER`, `M_SEPARATOR`, etc.) → Background color

---

## Troubleshooting

### Issue: `You need to install the imagick extension` or `Call to undefined function imagecreatetruecolor()`

**Solution:** Enable the GD extension in `php.ini`:

```ini
extension=gd
```

Then restart the development server.

### Issue: Generated QR code images return 403 or 404

**Solution:** Ensure the storage symlink exists:

```bash
php artisan storage:link
```

### Issue: QR code with logo cannot be scanned

**Solution:** The logo may be too large relative to the QR code. Try:

- Using a smaller logo file.
- Increasing the QR code size (e.g., 500x500 px or larger).
- Ensuring sufficient contrast between data modules and background.

### Issue: `sessions` or `cache` table not found

**Solution:** Run Laravel default migrations:

```bash
php artisan migrate
```

---

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## Contact

**Project Maintainer**: [Dwi Sulyanto](mailto:otnaylus@gmail.com)

**LinkedIn**: [www.linkedin.com/in/dwi-sulyanto](https://www.linkedin.com/in/dwi-sulyanto)

**Project Link**: [https://github.com/otnaylus/qrcode](https://github.com/otnaylus/qrcode)

---

<p align="center">
  <strong>Built for fast, branded QR code generation.</strong>
  <br>
  <em>Developed with Laravel, Tailwind CSS, and chillerlan/php-qrcode</em>
</p>
