# 🌟 Halalytics: 5 Fitur Unggulan Baru

Berikut ini adalah penjelasan detail mengenai 5 fitur baru yang diusulkan untuk dikembangkan di Halalytics:

---

## 1. 🧑‍⚕️ Health Profile (Profil Kesehatan Personal)
**Konteks Dasar:** Pengguna seringkali memiliki kondisi medis unik (misalnya: hipertensi, diabetes, alergi gluten, atau intoleransi laktosa) yang memerlukan pantangan bahan-bahan tertentu.

**Cara Kerja & Detail Fitur:**
- **Onboarding Interaktif:** Saat pengguna pertama kali mendaftar atau mengatur profil, aplikasi akan menanyakan data dasar (tinggi, berat badan, umur) dan **riwayat medis/alergi**.
- **Peringatan Otomatis (Auto-Warning):** Saat pengguna memindai barcode atau kandungan dari suatu produk makanan/skincare, **Gemini AI** tidak hanya akan menganalisis apakah produk itu "Aman" secara umum, tapi juga **"Aman untuk Anda secara spesifik"**. 
  - *Contoh:* Jika profil pengguna memiliki "Alergi Kacang", produk dengan jejak kacang akan langsung ditandai dengan peringatan 🔴 **BAHAYA: Mengandung Alergen Anda**.
- **Personalisasi Rekomendasi AI:** Asisten AI di Halalytics akan otomatis menyesuaikan batas aman konsumsi (Gula, Garam, Lemak) harian berdasarkan profil kesehatan ini.

---

## 2. ⚖️ Comparison (Perbandingan Produk Cerdas)
**Konteks Dasar:** Saat berada di lorong supermarket, pengguna sering bingung memilih antara 2 produk sejenis (Misal: Kecap A vs Kecap B). Mana yang lebih sehat? Mana yang titik kritis halalnya lebih sedikit?

**Cara Kerja & Detail Fitur:**
- **Mode Split Screen:** Pengguna dapat memasukkan 2 hingga 3 produk ke dalam "Versus Mode".
- **Skoring Head-to-Head:** AI akan memberikan tabel perbandingan head-to-head yang mencakup:
  1. Skor Keamanan BPOM
  2. Kadar Gula & Kalori
  3. Status Halal (serta jumlah bahan kritis/syubhat)
  4. Kecocokan dengan "Health Profile" pengguna
- **Kesimpulan AI:** Bagian paling bawah akan memberikan kesimpulan cepat *"Halalytics menyarankan Anda memilih Produk A karena produk B memiliki kandungan pengawet buatan yang tinggi."*

---

## 3. 👨‍👩‍👧‍👦 Family Box (Manajemen Kesehatan Keluarga)
**Konteks Dasar:** Ibu rumah tangga atau kepala keluarga biasanya berbelanja untuk konsumsi seluruh anggota keluarga, di mana setiap anggota mungkin memiliki profil kesehatan yang berbeda.

**Cara Kerja & Detail Fitur:**
- **Satu Akun, Banyak Profil:** Pengguna utama (misal: "Ibu") bisa menambahkan profil "Ayah" (Hipertensi) dan "Adik" (Alergi Susu Sapi).
- **Pemindaian Universal:** Cukup memindai barcode satu produk, Halalytics akan memberikan analisis keamanan untuk **setiap anggota keluarga sekaligus**. 
  - *Contoh Tampilan:* 
    - 🟢 Aman untuk Ibu 
    - 🔴 Bahaya untuk Ayah (Tinggi Natrium) 
    - 🔴 Bahaya untuk Adik (Mengandung Laktosa)
- **Shared Medicine Schedule:** Untuk pemakaian tingkat lanjut, pengingat minum obat juga bisa diatur dan dipantau untuk masing-masing anggota keluarga di dalam Family Box.

---

## 4. 🚨 Fake Report (Laporan Pemalsuan & Crowd-Sourced Safety)
**Konteks Dasar:** Banyak beredar produk kosmetik palsu atau makanan tanpa izin edar resmi yang mencatut nomor BPOM palsu di pasar bebas.

**Cara Kerja & Detail Fitur:**
- **Tombol "Laporkan Kejanggalan":** Jika pengguna memindai produk yang nomor registrasinya terdeteksi *valid* di sistem (misal: Skincare Merk A), namun kemasan fisik yang dipegang pengguna tampak mencurigakan (tidak rapi, warna pudar, bau menyengat), pengguna bisa melaporkannya.
- **Unggah Foto Pembanding:** Pengguna dapat memfoto produk yang dicurigai palsu. 
- **Admin Dashboard Integration:** Laporan ini akan masuk langsung ke dashboard Admin Halalytics (dengan status 'Pending Review'). 
- **Peringatan Crowd-Sourced:** Jika jumlah laporan atas suatu merk/nomor BPOM tertentu meningkat (misal: >5 laporan), sistem otomatis menurunkan status produk menjadi 🟡 **WASPADA: Banyak Laporan Pemalsuan Beredar**, untuk memperingatkan pengguna lain.

---

## 5. 📸 OCR Scanner (Pendeteksi Teks Komposisi / Skincare)
**Konteks Dasar:** Banyak jajanan pasar, produk UMKM lokal, atau obat import (Korea/Jepang/China) yang tidak memiliki barcode atau tidak terdaftar di database manapun (OpenFoodFacts / BPOM), tetapi mereka mencantumkan "Ingredients" atau komposisi pada kemasannya.

**Cara Kerja & Detail Fitur:**
- **Pindai Teks (Bukan Barcode):** Pengguna cukup memfoto bagian tulisan "Ingredients" atau "Komposisi" di label kemasan.
- **AI Text Extraction (OCR):** Gemini Vision AI akan membaca teks panjang pada label kemasan (meskipun tulisannya kecil, terlipat, atau menggunakan bahasa asing).
- **Analisis Instan:** Sistem mengekstrak nama kimia (Misal: *Carmine, Gelatin, Alcohol, Sodium Laureth Sulfate*) dan langsung membuat laporan:
  - Mana bahan yang **Haram/Syubhat** (contoh: *Carmine* dari serangga, *Gelatin* yang tidak jelas sumber hewannya).
  - Mana bahan **Bahaya/Keras** untuk kosmetik (contoh: *Hydroquinone*, *Mercury*, Paraben).
- Fitur ini sangat cocok untuk **jajanan impor** atau **skincare abal-abal** yang hanya menampilkan komposisi tanpa didukung data barcode internasional.
