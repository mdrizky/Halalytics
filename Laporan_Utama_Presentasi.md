# Laporan Komprehensif Presentasi Projek Halalytics

**Disusun untuk keperluan pembuatan Slide Presentasi (AI Prompting) & Materi Expo**

---

## 1. Judul Projek dan Tema Projek
- **Judul Projek:** **HALALYTICS** - Smart AI-Based Halal & Healthy Food Scanner
- **Tema Projek:** Solusi Cerdas Gaya Hidup Halal dan Sehat Berbasis Kecerdasan Buatan (AI) Terintegrasi.

## 2. Latar Belakang (Masalah)
Pengembangan Halalytics didasari oleh tiga pilar permasalahan utama yang dihadapi masyarakat saat ini:
1. **Kesulitan Verifikasi Kehalalan Secara Cepat:** Banyak produk di pasaran (sekitar 40%) tidak memiliki label halal yang jelas. Terlebih, 70% konsumen muslim merasa kesulitan dan kebingungan dengan istilah bahan kimia atau *E-numbers* pada komposisi makanan.
2. **Minimnya Integrasi Data Kesehatan & Halal:** Tidak ada platform yang menyatukan antara verifikasi halal produk dengan pemantauan nutrisi harian (gula, garam, kalori, dll) dalam satu aplikasi mobile.
3. **Ketergantungan Internet (Kesenjangan Akses):** Banyak masyarakat di daerah dengan sinyal internet rendah tidak bisa mengakses database produk halal secara langsung saat berbelanja.

## 3. Tujuan (Solusi)
Halalytics hadir sebagai platform ekosistem lengkap (Mobile App & Website Admin) untuk memberikan kemudahan bagi konsumen muslim:
- **Verifikasi Instan & Akurat:** Menyediakan fitur *Smart Scanner* (Barcode & OCR) yang terintegrasi dengan database lokal (50.000+ produk), API BPOM, dan MUI secara *real-time*.
- **Analisis AI untuk Komposisi:** Menggunakan AI untuk menganalisis teks bahan baku (ingredients) demi mendeteksi kandungan syubhat/haram secara otomatis, bahkan pada produk yang belum terdaftar.
- **Asisten Kesehatan Pribadi & Keluarga:** Menghadirkan *Health Dashboard* dan *Family Box* untuk memantau asupan harian (air, gula, garam, kalori) berdasarkan riwayat medis dan alergi tiap anggota keluarga.

## 3.5. Fitur Utama & Kegunaan Berdasarkan Peran (Role)
Aplikasi Halalytics didesain menggunakan sistem akses multi-peran (*Role-Based*) yang memberikan layar dan fitur berbeda sesuai dengan *login* pengguna:

### 1. Pengguna Umum (User / Konsumen)
Mengakses Halalytics via aplikasi Android untuk mempermudah hidup sehari-hari:
- **Smart Scanner & AI Analysis:** Kamera pintar untuk memindai barcode atau daftar bahan makanan (OCR). Kegunaannya untuk mengetahui status halal dan nutrisi dalam hitungan detik.
- **Health Tracker & Family Box:** Kegunaannya sebagai buku harian digital untuk memantau asupan air, gula, garam, dan kalori secara otomatis untuk diri sendiri maupun anggota keluarga yang didaftarkan.
- **Community Hub & Konsultasi:** Wadah untuk tanya jawab seputar makanan halal dan meminta saran pola makan langsung kepada para ahli.

### 2. Ahli Gizi (Nutritionist)
Para ahli kesehatan dan gizi mendapatkan antarmuka (UI) khusus di dalam aplikasi mobile:
- **Patient Monitoring:** Kegunaannya untuk melihat dan menganalisis *Scan History* (riwayat pemindaian) dari para klien/user, sehingga ahli gizi tahu apa saja yang mereka konsumsi sehari-hari.
- **Tele-Consultation:** Fitur khusus untuk menjawab *chat* konsultasi gizi dari pengguna umum, memberikan resep alternatif sehat yang halal, serta edukasi gizi secara langsung.
- **Health Risk Assessment:** Membantu pengguna menghitung risiko penyakit (seperti diabetes/hipertensi) berdasarkan profil data kesehatan pengguna.

### 3. Administrator Utama (Admin Panel via Web)
Tim pengelola Halalytics mengatur *backend* melalui Web Dashboard khusus (*Filament Laravel*):
- **Product Verification:** Kegunaannya untuk menyetujui (approve) atau menolak produk baru yang di-*scan* dan disumbangkan (*crowdsource*) oleh para *User*.
- **Database & Ingredient Management:** Mengelola puluhan ribu data E-Numbers, memperbarui status halal MUI/BPOM, serta melatih ulang database bahan.
- **System Analytics:** Memantau statistik grafik pengguna (*User Growth*), jumlah *scan* per hari, mengelola artikel blog/promo, hingga mengatur siapa saja yang berhak menjadi "Ahli Gizi" di dalam sistem.

## 4. Cara Kerja & Alur Sistem
Sistem Halalytics bekerja dalam sebuah ekosistem *hybrid* (Mobile & Web):
1. **Fase Pemindaian (Scanning):** Pengguna memindai *barcode* produk atau memfoto komposisi produk (OCR) menggunakan aplikasi mobile.
2. **Pengecekan Berlapis (Multi-layer Check):** 
   - Sistem akan mengecek *database* lokal (Offline Mode).
   - Jika tidak ditemukan, sistem mengecek API resmi (BPOM & MUI).
   - Jika masih tidak terdeteksi, AI (Gemini) akan membaca teks dari OCR untuk mengkategorikan bahan menjadi halal, haram, atau syubhat.
3. **Penyajian Hasil:** Hasil ditampilkan meliputi status halal, penjelasan detail *E-numbers*, dan *Health Score* nutrisi.
4. **Sinkronisasi Data:** Sistem otomatis menambahkan riwayat nutrisi pengguna ke *Health Dashboard*. Jika status *offline*, data disimpan lokal (Room DB) dan disinkronkan ke server Laravel di latar belakang saat internet tersedia.

## 5. Teknologi yang Digunakan Beserta Alasan
### Mobile App (Android)
- **Kotlin & Jetpack Compose:** Untuk antarmuka (UI) modern yang reaktif dan pengembangan yang lebih cepat.
- **Room Database & WorkManager:** Memberikan fitur **Offline Capability**, memungkinkan pengguna memindai tanpa internet dan melakukan sinkronisasi otomatis.
- **ML Kit (Vision):** Memproses *barcode* dan OCR secara *on-device* dengan cepat tanpa membebani server secara terus-menerus.

### Backend & Website (Admin Panel)
- **Laravel 11 & Filament 3:** Framework PHP yang matang dan stabil. Filament digunakan karena memberikan *dashboard* admin yang sangat kuat dan rapi dengan cepat.
- **MySQL/PostgreSQL & Redis:** Manajemen *database* relasional yang tangguh, dipadukan dengan Redis untuk *caching* respons API yang cepat.

### Kecerdasan Buatan (AI) Terintegrasi
- **Gemini AI:** Digunakan sebagai otak utama untuk **AI Ingredients Analysis**. Alasannya: Gemini memiliki kemampuan pemahaman bahasa (NLP) yang superior untuk mengenali konteks bahan kimia dan mengklasifikasikan status kehalalannya secara cepat dan efisien.

## 6. Permasalahan Saat Pengerjaan (Kendala & Tantangan)
1. **Biaya Infrastruktur Server & API AI:** Pemrosesan AI secara massal dan database yang besar membutuhkan biaya *cloud hosting* dan kuota API yang cukup tinggi. Hal ini menuntut tim untuk mengoptimalkan *caching* (Redis) agar panggilan API tidak berulang untuk produk yang sama.
2. **Keterbatasan API Eksternal:** Sering terjadinya *rate limit* atau gangguan pada API publik BPOM/OpenFoodFacts sehingga sistem harus dibuat tangguh (menggunakan *fallback* AI).
3. **Manajemen Penyimpanan Lokal:** Menjaga ukuran aplikasi Android tetap ringan meskipun harus menyimpan ribuan *database* produk untuk mode *offline*.
4. **Akurasi OCR pada Kemasan:** Teks pada kemasan produk yang melengkung atau buram menyulitkan deteksi OCR, yang membutuhkan kalibrasi kamera dan *auto-focus* yang presisi.

## 7. Fitur Mendatang (Roadmap & Pengembangan Maksimal)
Untuk menjadikan Halalytics sebagai Super-App di masa depan, berikut adalah fitur yang dapat dikembangkan:
1. **Integrasi WearOS (Smartwatch):** Memungkinkan notifikasi pengingat minum air, obat, dan pemantauan kalori langsung dari pergelangan tangan.
2. **Fitur Augmented Reality (AR):** Pengguna cukup mengarahkan kamera ke rak minimarket, dan layar akan memunculkan *highlight* hijau/merah pada produk-produk yang halal dan sehat.
3. **Marketplace & Komunitas UMKM Terverifikasi:** Mewadahi produk lokal dan UMKM halal untuk berjualan langsung di dalam aplikasi.

## 8. Penutup
Halalytics bukan sekadar aplikasi pemindai halal biasa, melainkan asisten cerdas proaktif yang menjembatani kebutuhan spiritual (halal) dan jasmani (sehat). Melalui adopsi AI dan teknologi offline-first, Halalytics siap memberikan dampak nyata bagi jutaan masyarakat Indonesia untuk hidup yang lebih baik dan berkah.

---

# 🎨 Konsep Materi Stan Expo (Poster Lengkap)

Berikut adalah ringkasan padat yang dapat dijadikan konten untuk X-Banner, Poster, dan Selebaran di stan Expo Anda:

### **[POSTER 1: Pengenalan & Masalah]**
- **Headline:** HALALYTICS: Gaya Hidup Halal & Sehat dalam Satu Genggaman.
- **Visual:** Gambar orang kebingungan membaca label komposisi makanan di swalayan.
- **Rangkuman Masalah:** 70% konsumen bingung dengan E-Numbers kimia. 40% produk di pasaran minim kejelasan halal. 
- **Solusi Kami:** Scan, Analisis AI, dan Pantau Kesehatan Anda Secara Otomatis!

### **[POSTER 2: Fitur Inti (Core Features)]**
- **Smart Scanner (Offline-Ready):** Scan barcode atau foto komposisi tanpa perlu internet.
- **Gemini AI Analysis:** AI yang mampu membedah bahan kimia rumit menjadi status Halal, Haram, atau Syubhat secara real-time.
- **Family Health Box:** Satu klik untuk melihat apakah suatu produk aman untuk riwayat alergi dan penyakit anak/keluarga Anda.

### **[POSTER 3: Visi Masa Depan (Fitur Mendatang)]**
- **Augmented Reality (AR) Shopping:** Lihat status produk langsung di rak supermarket!
- **WearOS Integration:** Pantau kesehatan halal dari Smartwatch Anda.
- **UMKM Halal Marketplace:** Ekosistem bisnis islami terpadu.

### 💡 **Tips Strategi Presentasi (Sesuai PDF):**
1. **Gunakan Bahasa Resmi & Tetap Formal:** Gunakan intonasi yang jelas, bicara pelan namun meyakinkan, hindari terburu-buru (10 menit adalah waktu yang sangat cukup).
2. **Tetap Tenang:** Gunakan *slide* hanya sebagai petunjuk visual; pastikan Anda memahami alur secara utuh di luar kepala.
3. **Siapkan FAQ (Pertanyaan Umum):** Siapkan jawaban untuk pertanyaan kritis dari juri, seperti: *"Bagaimana jika AI salah menganalisis?"* (Jawab: AI hanya memberi *warning/syubhat* dan sistem mengedepankan data BPOM/MUI terlebih dahulu sebagai *single source of truth*).
4. **Skenario Terburuk:** Jika aplikasi *error/crash* saat live demo, siapkan *video backup demo* yang sudah direkam sebelumnya.

> *Catatan untuk AI Pembuat Slide:* Gunakan teks di atas untuk membagi setiap bagian menjadi 1-2 slide. Gunakan poin-poin (bullet points) agar slide tidak terlalu penuh dengan teks. Visualisasikan alur pada poin 4 dengan diagram alir (flowchart).
