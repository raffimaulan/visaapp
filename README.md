Berikut README untuk project AKUVISA:

---

# 🛂 AKUVISA — Sistem Manajemen Pengajuan Visa

Aplikasi berbasis web untuk mengelola pengajuan visa secara efisien, mulai dari data pemohon, proses pengajuan, pembayaran, hingga laporan.

## ✨ Fitur Utama

- **Dashboard** — Ringkasan data pengajuan, pembayaran, dan status terkini
- **Manajemen Pengajuan** — Tambah, edit, dan pantau status pengajuan visa
- **Manajemen Pemohon** — Kelola data pemohon beserta nomor passport
- **Pembayaran** — Catat pembayaran penuh, DP, dan pelunasan dengan riwayat lengkap
- **Dokumen** — Upload dan kelola dokumen pendukung pengajuan
- **Laporan** — Export laporan pengajuan dan pembayaran dalam format PDF & Excel
- **Pencarian Cepat** — Dropdown dengan fitur ketik untuk pencarian data yang lebih mudah

## 🛠️ Teknologi

- **Backend** — PHP Native
- **Database** — MySQL
- **Frontend** — HTML, CSS, Bootstrap
- **Server** — Apache (XAMPP/Laragon)

## 🚀 Cara Instalasi

1. Clone repository ini
   ```bash
   git clone https://github.com/raffimaulan/visaapp
   ```
2. Pindahkan folder ke direktori server (htdocs / www)
3. Import database dari file `.sql` yang tersedia
4. Sesuaikan konfigurasi koneksi di `helper/connection.php`
5. Akses melalui browser: `http://localhost/visaapp`

## 📁 Struktur Folder

```
visaapp/
├── applications/     # Modul pengajuan visa
├── applicants/       # Modul data pemohon
├── payments/         # Modul pembayaran
├── documents/        # Modul dokumen
├── reports/          # Export laporan PDF & Excel
├── models/           # Model database
├── helper/           # Koneksi & autentikasi
└── uploads/          # File yang diupload
```

## 👤 Akun Default

| Role  | Username | Password |
|-------|----------|----------|
| Admin | admin    | admin123 |

> ⚠️ Segera ganti password setelah instalasi pertama.

## 📄 Lisensi

Project ini dibuat untuk keperluan manajemen internal pengajuan visa.

---

Tinggal sesuaikan username GitHub dan kredensial default jika berbeda. Mau ada yang ditambah atau diubah?
