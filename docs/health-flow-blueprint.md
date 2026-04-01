# Blueprint Alur Fitur Kesehatan APK Halalytics

Dokumen ini menyusun alur fitur kesehatan yang setara secara kedalaman dengan aplikasi seperti Halodoc, tetapi disesuaikan dengan fondasi backend Halalytics yang sudah ada saat ini. Blueprint ini sengaja memetakan 5 lapisan sekaligus:

1. `Halaman / UX`
2. `Alur user`
3. `Alur sistem`
4. `Endpoint API`
5. `Tabel / model backend`

Dokumen ini berangkat dari kode yang sudah ada di backend Laravel Halalytics, terutama area berikut:

- `routes/api.php`
- `app/Http/Controllers/Api/MedicineController.php`
- `app/Http/Controllers/Api/MedicationReminderController.php`
- `app/Http/Controllers/Api/MedicalRecordController.php`
- `app/Http/Controllers/Api/HealthMetricController.php`
- `app/Http/Controllers/Api/UserHealthInsightController.php`
- `app/Http/Controllers/Api/AIAssistantController.php`
- `app/Http/Controllers/Api/SkincareController.php`
- `app/Http/Controllers/Api/HealthArticleController.php`
- `app/Http/Controllers/Api/HealthEncyclopediaController.php`
- `app/Http/Controllers/Api/NotificationController.php`
- `app/Http/Controllers/Api/FcmController.php`
- `app/Http/Controllers/Api/FamilyController.php`

## 1. Tujuan Modul Kesehatan

Target modul kesehatan di Halalytics bukan hanya "tambahan menu", tetapi sebuah health layer yang menyatu dengan kekuatan utama aplikasi:

- `Food halal + nutrition scan`
- `Skincare / cosmetic halal & safety check`
- `AI advisor`
- `Personal health profile`
- `Reminder`
- `Medical records`
- `Family health profile`
- `Daily health tracking`

Dengan begitu, Halalytics tidak sekadar mengecek produk halal, tetapi menjadi aplikasi `halal lifestyle + personal health assistant`.

## 2. Peta Besar Aplikasi

Untuk APK, modul kesehatan paling rapi jika diposisikan seperti ini:

- `Beranda`
- `Scan`
- `AI Assistant` sebagai tombol utama tengah
- `Kesehatan`
- `Profil`

Jika tim ingin tetap mempertahankan bottom nav sekarang, maka minimal menu `Kesehatan` perlu menjadi satu hub besar yang menampung:

- `Ringkasan Kesehatan`
- `Profil Kesehatan`
- `Keluarga`
- `Pengingat Obat`
- `Tracking Harian`
- `Skincare Safety`
- `Nutrition & Meal Insight`
- `Artikel & Ensiklopedia`
- `Catatan Medis`
- `Notifikasi Kesehatan`

## 3. Struktur Besar Modul Kesehatan

### 3.1 Health Hub

Ini adalah halaman pertama saat user membuka tab `Kesehatan`.

Komponen yang direkomendasikan:

- `Header`: sapaan user + health score harian
- `Card Daily Insight`: insight AI singkat
- `Card Next Reminder`: obat berikutnya dan jam dosis berikutnya
- `Card Quick Actions`: tambah pengingat, scan skincare, scan makanan, tambah catatan medis
- `Card Family Switcher`: pilih diri sendiri atau anggota keluarga
- `Section Tracking`: berat badan, gula darah, kolesterol, diary
- `Section Artikel`: artikel kesehatan terbaru
- `Section Ensiklopedia`: obat, penyakit, hidup sehat, keluarga

Alur user:

1. User buka tab `Kesehatan`.
2. App memuat ringkasan kesehatan.
3. User bisa langsung masuk ke reminder, tracking, AI, atau scan.

Alur sistem:

1. App ambil token user aktif.
2. App panggil data berikut secara paralel:
   - `GET /api/health/score`
   - `GET /api/ai/daily-insight`
   - `GET /api/health/metrics/summary`
   - `GET /api/medicines/reminders/{userId}/next-dose`
   - `GET /api/articles?limit=5`
3. App render health hub berdasarkan hasil gabungan.

Backend yang terlibat:

- `UserHealthInsightController@getHealthScore`
- `UserHealthInsightController@getDailyInsight`
- `HealthMetricController@summary`
- `MedicineController@getNextDose`
- `HealthArticleController@index`

Tabel utama:

- `users`
- `health_trackings`
- `medicine_reminders`
- `medication_logs`
- `notifications`

## 4. Halaman Profil Kesehatan / Informasi Medis

Fitur ini adalah versi Halalytics dari halaman `Informasi Medis`.

### 4.1 Tujuan halaman

Mengumpulkan konteks kesehatan yang akan dipakai oleh:

- AI assistant
- analisis skincare
- analisis gejala
- reminder obat
- personalisasi insight
- mode keluarga

### 4.2 Field yang sebaiknya tampil di APK

Field yang sudah selaras dengan struktur `users`:

- `Nama lengkap`
- `Tanggal lahir`
- `Usia`
- `Gender`
- `Nomor HP`
- `Golongan darah`
- `Alergi`
- `Riwayat penyakit`
- `Tinggi badan`
- `Berat badan`
- `BMI`
- `Goal kesehatan`
- `Diet preference`
- `Activity level`
- `Alamat`
- `Bahasa`
- `Show health tips`
- `Notification enabled`

Tambahan UX yang disarankan:

- `Textarea "Alergi"` untuk versi teks bebas
- `Multi-select "Alergen umum"` bila mobile team ingin chip UI
- `Toggle "Sedang hamil"` bila ingin dipakai pada analisis skincare
- `Badge completion` untuk menilai kelengkapan profil kesehatan

### 4.3 Alur user

1. User masuk ke `Profil` atau `Kesehatan`.
2. User pilih `Informasi Medis`.
3. Form tampil berisi data existing jika sudah pernah diisi.
4. User edit data lalu tap `Simpan`.
5. App refresh ringkasan kesehatan dan AI context.

### 4.4 Alur sistem

1. `GET /api/user/profile`
2. App isi seluruh field form dari payload profile.
3. Saat submit, app kirim `POST /api/user/profile`
4. Backend update tabel `users`.
5. Data baru otomatis dipakai oleh:
   - `MedicineController@analyzeSymptoms`
   - `SkincareController@analyzeIngredients`
   - `AIAssistantController@analyzeIngredients`
   - `UserHealthInsightController@getDailyInsight`

### 4.5 Endpoint

- `GET /api/user/profile`
- `POST /api/user/profile`
- `POST /api/user/allergies`

### 4.6 Model / tabel

- `App\Models\User`
- `users`

## 5. Halaman Profil Keluarga

Fitur ini adalah pengganti konteks `keluarga / dependent profile`, sehingga reminder, AI, dan scan bisa diarahkan ke anggota keluarga.

### 5.1 Komponen halaman

- `List anggota keluarga`
- `Card ringkas`: nama, relasi, umur, gender
- `Badge alergi / riwayat penyakit`
- `CTA tambah anggota`
- `Action edit`
- `Action hapus`

### 5.2 Alur user

1. User buka `Kesehatan > Profil Keluarga`.
2. User tambah anggota keluarga.
3. Saat memakai fitur AI, skincare scan, atau reminder, user bisa memilih target profil:
   - `Saya`
   - `Anak`
   - `Pasangan`
   - `Orang tua`

### 5.3 Alur sistem

1. `GET /api/user/family`
2. `POST /api/user/family`
3. `POST /api/user/family/{id}` untuk edit
4. `DELETE /api/user/family/{id}` untuk hapus
5. ID family dikirim sebagai `family_id` pada endpoint yang mendukung konteks keluarga.

Endpoint yang sudah mendukung `family_id`:

- `POST /api/medicines/analyze-symptoms`
- `POST /api/medicines/reminders`
- `POST /api/skincare/analyze`
- `POST /api/ai/analyze`

### 5.4 Tabel

- `family_profiles`

### 5.5 Catatan implementasi

Backend sudah mendukung konsep ini, tetapi mobile perlu selalu menampilkan `profile switcher` yang jelas. Jika tidak, user akan bingung analisis sedang berlaku untuk siapa.

## 6. Halaman Pengingat Obat

Ini adalah modul yang paling dekat dengan flow Halodoc.

### 6.1 Struktur halaman

Tab harian yang disarankan:

- `Kemarin`
- `Hari Ini`
- `Besok`

State yang wajib:

- `Empty state`
- `List aktif`
- `Upcoming dose`
- `Missed dose`
- `Sudah diminum`

### 6.2 Komponen card reminder

Setiap item reminder sebaiknya memuat:

- nama obat
- dosis
- frekuensi
- jam minum hari ini
- status halal obat
- relation terhadap makan
- target profil: saya / anggota keluarga
- tombol:
  - `Sudah diminum`
  - `Lewati`
  - `Lihat detail`

### 6.3 Empty state

Jika belum ada reminder aktif:

- ilustrasi reminder
- teks utama: `Belum ada pengingat obat`
- subteks: `Tambahkan pengingat agar jadwal minum obat tidak terlewat`
- tombol `Buat Pengingat Baru`

### 6.4 Alur user

1. User buka `Kesehatan > Pengingat Obat`.
2. App menampilkan reminder aktif.
3. Jika kosong, app tampilkan empty state.
4. User tap `Buat Pengingat Baru`.
5. User masuk ke `Search Obat`.
6. User pilih obat.
7. User isi frekuensi, durasi, jam, catatan.
8. User simpan.
9. Reminder masuk ke daftar aktif dan notifikasi dijadwalkan.

### 6.5 Alur sistem

1. `GET /api/medicines/reminders/{userId}`
2. App tampilkan daftar reminder aktif.
3. Ketika user tandai minum:
   - `POST /api/medicines/reminders/mark-taken`
4. Ketika user buka health hub:
   - `GET /api/medicines/reminders/{userId}/next-dose`
5. Untuk versi yang lebih detail, log konsumsi juga bisa memakai:
   - `POST /api/ai/reminders/log`

### 6.6 Endpoint

Endpoint reminder yang tersedia saat ini:

- `POST /api/medicines/reminders`
- `GET /api/medicines/reminders/{userId}`
- `POST /api/medicines/reminders/mark-taken`
- `GET /api/medicines/reminders/{userId}/next-dose`

Endpoint reminder versi alternatif:

- `POST /api/ai/reminders`
- `POST /api/ai/reminders/log`
- `GET /api/ai/reminders`
- `DELETE /api/ai/reminders/{id}`

### 6.7 Tabel

- `medicine_reminders`
- `medication_logs`
- `medicines`
- `notifications`
- `user_fcm_tokens`
- `user_notification_preferences`

### 6.8 Rekomendasi implementasi APK

Untuk mobile, gunakan satu jalur saja agar tidak duplikatif:

- `Search + preview schedule`: pakai `POST /api/medicines/safe-schedule`
- `Create reminder`: pakai `POST /api/medicines/reminders`
- `Mark taken`: pakai `POST /api/medicines/reminders/mark-taken`

Jangan mencampur UI reminder utama dengan dua controller berbeda tanpa aturan, karena sekarang backend punya dua entry point reminder.

## 7. Halaman Search Obat

### 7.1 Komponen

- search bar real-time
- recent search
- daftar hasil
- badge status halal
- info singkat obat
- tombol tambah reminder

### 7.2 Alur user

1. User mengetik nama obat.
2. Hasil muncul real-time.
3. User pilih item.
4. User diarahkan ke halaman `Tambah Pengingat`.

### 7.3 Alur sistem

Pilihan endpoint:

- `POST /api/medicines/search` untuk hybrid search
- `GET /api/medicines?search=...` untuk basic list
- `GET /api/medicines/{id}` untuk detail tunggal

Search hybrid menarik karena menggabungkan:

- local medicine database
- OpenFDA / DailyMed lewat `FDAService`
- fallback AI ingredient extraction bila hasil kosong

### 7.4 Tabel / sumber

- `medicines`
- OpenFDA / DailyMed external source

## 8. Halaman Tambah Pengingat Baru

Ini adalah layar yang paling mirip dengan contoh Halodoc.

### 8.1 Field wajib

- `Obat yang dipilih`
- `Berapa kali sehari?`
- `Mulai tanggal berapa?`
- `Berapa lama dikonsumsi?`

### 8.2 Field disarankan

- `Dosis`
- `Waktu bangun`
- `Waktu tidur`
- `Terkait makan`
  - sebelum makan
  - sesudah makan
  - saat makan
  - bebas
- `Catatan`
- `Target profil`

### 8.3 Real-time preview

Begitu user isi frekuensi dan jam bangun/tidur, app tampilkan preview:

- `Kamu akan diingatkan pada 07:00, 13:00, 19:00`
- `Pengingat berlaku dari 24 Maret 2026 sampai 30 Maret 2026`
- `Minum 30 menit sebelum makan`

### 8.4 Alur sistem

1. App kirim preview ke:
   - `POST /api/medicines/safe-schedule`
2. Backend hitung:
   - schedule times
   - end date
   - meal instruction
   - disclaimer medis
3. App menampilkan preview.
4. Saat user menekan `Simpan`, app kirim:
   - `POST /api/medicines/reminders`
5. Backend simpan ke `medicine_reminders`.
6. Mobile app menjadwalkan local notifications.
7. Jika ada backend push scheduler nanti, server juga bisa kirim FCM.

### 8.5 Catatan penting

Saat ini backend `createReminder` masih membuat jam otomatis sederhana berdasarkan frekuensi. Karena itu UI sebaiknya memakai hasil `safe-schedule` sebagai sumber preview, lalu simpan reminder dengan payload yang konsisten.

## 9. Halaman Detail Pengingat / Checklist Dosis

### 9.1 Isi halaman

- nama obat
- generik / brand
- dosis
- status halal
- alasan konsumsi / gejala
- jadwal per hari
- histori `taken / skipped / late`
- catatan pribadi

### 9.2 Aksi user

- tandai sudah diminum
- tandai terlambat
- lewati
- jeda reminder
- nonaktifkan reminder
- hapus reminder

### 9.3 Sistem

Untuk versi minimum:

- `POST /api/medicines/reminders/mark-taken`

Untuk versi lengkap log status:

- `POST /api/ai/reminders/log`

Kalau ingin status `skipped` dan `late` di layar ini, jalur yang lebih tepat adalah `MedicationReminderController` karena sudah menyimpan ke `medication_logs`.

## 10. Halaman AI Health Assistant

Ini adalah versi Halalytics dari `HILDA AI`, tetapi fokusnya bukan hanya keluhan umum. Ia harus menyatu dengan halal, nutrisi, skincare, dan health context user.

### 10.1 Fungsi utama AI assistant

- analisis gejala
- saran self-care awal
- rekomendasi kapan harus ke dokter
- analisis bahan makanan
- analisis bahan skincare
- daily insight
- personal risk score
- rekomendasi produk halal yang lebih aman

### 10.2 Komponen halaman

- header AI assistant
- info keamanan sesi
- sapaan awal
- quick prompts
- chat input
- card jawaban AI
- rating jawaban
- disclaimer medis

### 10.3 Quick prompts yang cocok untuk Halalytics

- `Saya demam 3 hari, sebaiknya apa?`
- `Apakah bahan skincare ini aman untuk kulit sensitif?`
- `Saya punya alergi kacang, apakah produk ini aman?`
- `Apa arti health score saya hari ini?`
- `Ada konflik antara obat saya dan makanan yang saya makan?`

### 10.4 Jalur AI yang sudah tersedia

- `POST /api/medicines/analyze-symptoms`
- `POST /api/ai/analyze`
- `GET /api/ai/daily-intake`
- `GET /api/ai/personal-risk-score`
- `GET /api/ai/daily-insight`
- `POST /api/ai/interactions`
- `POST /api/ai/pill-identify`
- `POST /api/ai/lab-analysis`

### 10.5 Konteks user yang sudah dipakai backend

Pada beberapa controller, backend sudah membaca:

- `age`
- `gender`
- `medical_history`
- `allergy` / `allergies`
- `diet_preference`
- `family_id`

Artinya AI di Halalytics sudah siap dibangun sebagai `personalized assistant`, bukan chatbot generik.

### 10.6 Alur sistem

1. User buka AI assistant.
2. App ambil context user aktif atau anggota keluarga terpilih.
3. App kirim pertanyaan ke endpoint yang sesuai.
4. Backend gunakan `GeminiService` + health context user.
5. Response dikembalikan ke UI.
6. Hasil bisa memicu action lanjutan:
   - tambah reminder
   - cek interaksi obat
   - buka artikel
   - scan produk / skincare

## 11. Halaman Tracking Kesehatan

Modul ini mengubah Halalytics dari sekadar scanner menjadi personal health journal.

### 11.1 Jenis tracking yang sudah didukung backend

- `weight`
- `blood_pressure`
- `blood_sugar`
- `cholesterol`
- `health_diary`

### 11.2 Struktur layar

- tab per metrik
- chart history
- latest value
- form tambah catatan
- insight AI dari tinggi/berat/BMI

### 11.3 Alur user

1. User masuk ke `Tracking`.
2. User pilih metrik.
3. User input nilai dan catatan.
4. App simpan.
5. Chart dan summary langsung diperbarui.

### 11.4 Alur sistem

- simpan metrik:
  - `POST /api/health/metrics`
- ambil history:
  - `GET /api/health/metrics/history?metric_type=weight`
- ambil summary:
  - `GET /api/health/metrics/summary`
- ambil diary:
  - `GET /api/health/diary`
- analisis BMI:
  - `POST /api/health/analyze`

### 11.5 Tabel

- `health_trackings`
- `users`

### 11.6 Adaptasi layar BMI

Jika ingin seperti halaman BMI pada Halodoc:

- buat screen `Apa itu BMI?`
- lanjut ke screen `Hitung BMI`
- submit tinggi + berat
- backend hitung / AI interpretasi
- tampilkan status:
  - kurus
  - normal
  - overweight
  - obesitas

## 12. Halaman Skincare Safety & Halal Check

Karena Halalytics juga punya fokus kosmetik, ini harus menjadi salah satu fitur unggulan dalam modul kesehatan.

### 12.1 Komponen

- input manual ingredients
- upload foto komposisi
- pilih target profil
- hasil analisis bahan
- skor keamanan
- status halal
- daftar bahan kritis
- disclaimer

### 12.2 Alur user

1. User pilih `Skincare Check`.
2. User foto kemasan atau paste ingredients.
3. User pilih target:
   - saya
   - anggota keluarga
4. App kirim ke backend.
5. User melihat:
   - skor keamanan
   - bahan yang perlu diwaspadai
   - status halal
   - ringkasan AI

### 12.3 Endpoint

- `POST /api/skincare/analyze`
- `POST /api/skincare/safety`
- `POST /api/skincare/halal`

### 12.4 Context personalization

Backend sudah menggabungkan konteks user:

- age
- gender
- medical_history
- allergies
- pregnancy proxy dari diet preference
- family_id

### 12.5 Tabel / model

- `bpom_data`
- `family_profiles`
- `users`

## 13. Halaman Nutrition Scan & Meal Insight

Ini adalah sisi kesehatan untuk makanan dan minuman.

### 13.1 Fungsi utama

- scan label komposisi
- baca status halal
- deteksi bahan sensitif
- skor kesehatan
- estimasi kalori / gula
- analisis makanan dari foto
- pencatatan intake harian

### 13.2 Dua mode yang sudah tersedia

#### Mode A. Scan label produk

Endpoint:

- `POST /api/nutrition-scans`

Hasil:

- halal status
- health score
- ingredients concern
- kalori
- gula

#### Mode B. Scan foto makanan

Endpoint:

- `POST /api/meal/analyze`

Hasil:

- nama makanan
- estimasi nutrisi
- analisis halal
- log meal

### 13.3 Dashboard intake

Untuk health hub, tambahkan kartu:

- gula hari ini
- sodium hari ini
- kalori hari ini
- risk score

Endpoint:

- `GET /api/ai/daily-intake`
- `GET /api/ai/personal-risk-score`

### 13.4 Tabel

- `nutrition_scans`
- `intake_logs`
- `meal_logs`
- `scan_histories`

## 14. Halaman Catatan Medis

Ini versi Halalytics dari `medical records vault`.

### 14.1 Jenis catatan

Backend sudah menyiapkan:

- `Lab`
- `Resep`
- `Diagnosis`
- `Vaksinasi`
- `Operasi`

### 14.2 Komponen halaman

- list catatan medis
- filter berdasarkan jenis
- unggah file / foto
- detail catatan
- arsipkan catatan

### 14.3 Alur user

1. User masuk ke `Catatan Medis`.
2. App tampilkan seluruh catatan aktif.
3. User tap `Tambah`.
4. User isi judul, tipe, tanggal, rumah sakit, dokter, deskripsi, file.
5. App simpan.

### 14.4 Alur sistem

- ambil list:
  - `GET /api/medical-records`
- simpan:
  - `POST /api/medical-records`

### 14.5 Tabel

- `medical_records`

## 15. Halaman Artikel & Ensiklopedia

Ini modul edukasi kesehatan di APK.

### 15.1 Artikel

Sumber yang sudah ada:

- artikel lokal dari `PromoBlog`
- feed eksternal RSS

Endpoint:

- `GET /api/articles`
- `GET /api/articles/{slug}`

Komponen UI:

- search bar
- filter kategori
- hero article
- list artikel
- sticky CTA terkait AI / scan

### 15.2 Ensiklopedia

Jenis yang sudah didukung:

- `obat`
- `penyakit`
- `hidup_sehat`
- `keluarga`

Endpoint:

- `GET /api/health-encyclopedia`
- `GET /api/health-encyclopedia/{id}`

### 15.3 Rekomendasi flow

- dari jawaban AI, user bisa diarahkan ke artikel terkait
- dari artikel obat, user bisa diarahkan ke search obat / reminder
- dari artikel hidup sehat, user bisa diarahkan ke tracking

## 16. Halaman Notifikasi Kesehatan

### 16.1 Tipe notifikasi yang relevan

- reminder obat
- hasil scan makanan
- hasil scan skincare
- artikel baru
- insight mingguan
- warning watchlist
- emergency / security alerts

### 16.2 Endpoint

- `POST /api/fcm/register`
- `GET /api/notifications`
- `GET /api/notifications/unread-count`
- `POST /api/notifications/{id}/read`
- `POST /api/notifications/read-all`
- `GET /api/user/notification-preferences`
- `PUT /api/user/notification-preferences`

### 16.3 Tabel

- `user_fcm_tokens`
- `notifications`
- `user_notification_preferences`

### 16.4 UX yang disarankan

Di halaman pengaturan notifikasi, pisahkan toggle:

- `Pengingat Obat`
- `Laporan Mingguan`
- `Promo`
- `Update Produk Favorit`
- `Watchlist Alert`
- `Security Alert`

## 17. Halaman Emergency / P3K

Fitur ini belum setara konsultasi dokter, tetapi sangat berguna sebagai `first response`.

### 17.1 Komponen

- tombol darurat besar
- pilih jenis darurat
- lokasi otomatis
- langkah P3K instan
- tombol `Hubungi nomor darurat`

### 17.2 Alur user

1. User tap `Darurat`.
2. Pilih kondisi: tersedak, pingsan, luka bakar, kejang, dan seterusnya.
3. App mengirim lokasi jika diizinkan.
4. AI mengembalikan maksimal 3 langkah P3K.
5. Event dikirim ke admin dashboard realtime.

### 17.3 Endpoint

- `POST /api/emergency/trigger`

### 17.4 Tabel

- `emergency_logs`

## 18. Rekomendasi Urutan User Journey End-to-End

### 18.1 Journey 1. Onboarding sehat

1. Register / login
2. Isi profil dasar
3. Lengkapi profil kesehatan
4. Tambah anggota keluarga jika perlu
5. Aktifkan notifikasi
6. Health hub tampil pertama kali

### 18.2 Journey 2. User sakit ringan

1. Buka AI assistant
2. Tulis gejala
3. AI analisis gejala
4. User diarahkan ke rekomendasi obat / tindakan
5. User cari obat
6. User buat reminder
7. User menerima notifikasi
8. User tandai obat sudah diminum

### 18.3 Journey 3. User cek produk skincare

1. Buka `Skincare Check`
2. Foto ingredients
3. Backend analisis bahan
4. Tampil skor keamanan + status halal
5. User simpan hasil atau cari alternatif

### 18.4 Journey 4. User cek makanan

1. Scan label komposisi atau foto makanan
2. Backend analisis halal + nutrisi
3. Hasil masuk ke intake log
4. Risk score harian diperbarui
5. Daily insight ikut menyesuaikan

### 18.5 Journey 5. User menyimpan dokumen medis

1. Buka `Catatan Medis`
2. Unggah hasil lab / resep
3. Backend simpan file
4. Dokumen bisa dipakai untuk rujukan AI atau konsultasi di masa depan

## 19. Pemetaan Screen ke Endpoint

| Screen | Endpoint utama | Tabel utama | Status |
| --- | --- | --- | --- |
| Health Hub | `/api/health/score`, `/api/ai/daily-insight`, `/api/health/metrics/summary`, `/api/articles` | `users`, `health_trackings`, `medicine_reminders` | Siap komposisi |
| Profil Kesehatan | `/api/user/profile` | `users` | Siap |
| Profil Keluarga | `/api/user/family` | `family_profiles` | Siap |
| Search Obat | `/api/medicines/search` | `medicines` | Siap |
| Preview Reminder | `/api/medicines/safe-schedule` | none langsung | Siap |
| Create Reminder | `/api/medicines/reminders` | `medicine_reminders` | Siap |
| Reminder List | `/api/medicines/reminders/{userId}` | `medicine_reminders` | Siap |
| Next Dose | `/api/medicines/reminders/{userId}/next-dose` | `medicine_reminders` | Siap |
| Mark Taken | `/api/medicines/reminders/mark-taken` | `medicine_reminders` | Siap minimum |
| AI Symptom Analysis | `/api/medicines/analyze-symptoms` | `medicines`, `users`, `family_profiles` | Siap |
| Drug-Food Conflict | `/api/medicines/drug-food-conflict` | `scan_histories`, `intake_logs` | Siap |
| Health Tracking | `/api/health/metrics`, `/api/health/metrics/history`, `/api/health/diary` | `health_trackings` | Siap |
| BMI / Health Analyze | `/api/health/analyze` | `users` | Siap |
| Skincare Analysis | `/api/skincare/analyze` | `bpom_data` | Siap |
| Skincare Halal | `/api/skincare/halal` | none langsung / `bpom_data` | Siap |
| Nutrition Scan | `/api/nutrition-scans` | `nutrition_scans` | Siap |
| Meal AI | `/api/meal/analyze` | `meal_logs` | Siap backend parsial |
| Daily Intake | `/api/ai/daily-intake` | `intake_logs` | Siap |
| Risk Score | `/api/ai/personal-risk-score` | `intake_logs`, `scan_histories` | Siap |
| Medical Records | `/api/medical-records` | `medical_records` | Siap |
| Articles | `/api/articles` | `promo_blogs` / feed eksternal | Siap |
| Health Encyclopedia | `/api/health-encyclopedia` | `health_encyclopedias` | Siap |
| Notifications | `/api/notifications` | `notifications` | Siap |
| Notification Preferences | `/api/user/notification-preferences` | `user_notification_preferences` | Siap |
| Emergency | `/api/emergency/trigger` | `emergency_logs` | Siap |

## 20. Arsitektur Sistem yang Direkomendasikan

```text
APK Halalytics
  -> Auth token Sanctum
  -> Health UI Layer
  -> Local notification scheduler
  -> FCM receiver
  -> Image capture / OCR input

Laravel API
  -> Auth / Profile
  -> Medicines
  -> Reminder
  -> Health Tracking
  -> Medical Records
  -> AI Assistant
  -> Skincare
  -> Nutrition / Meal Analysis
  -> Articles / Encyclopedia
  -> Notifications
  -> Emergency

Services
  -> GeminiService
  -> FDAService
  -> ActivityEventService
  -> Firebase / FCM

Database
  -> users
  -> family_profiles
  -> medicines
  -> medicine_reminders
  -> medication_logs
  -> health_trackings
  -> medical_records
  -> nutrition_scans
  -> intake_logs
  -> notifications
  -> user_fcm_tokens
  -> user_notification_preferences
```

## 21. Catatan Teknis Penting

### 21.1 Canonical identity

Gunakan `id_user` sebagai ID user utama untuk semua flow kesehatan. Di beberapa controller masih ada campuran `Auth::id()`, `id`, dan `id_user`. Untuk mobile integration, payload dan parsing response harus konsisten memakai `id_user`.

### 21.2 Reminder flow sebaiknya disederhanakan

Saat ini ada dua jalur:

- `MedicineController`
- `MedicationReminderController`

Untuk APK, pilih satu sebagai jalur utama agar state dan log tidak pecah.

### 21.3 Notifikasi reminder belum sepenuhnya server-driven

Backend sudah punya:

- `notifications`
- `user_fcm_tokens`
- `notification preferences`

Tetapi agar reminder benar-benar andal di APK, minimal harus ada:

- local notification di device
- sync status ke backend saat user mark taken

### 21.4 Mental health belum menjadi modul khusus

Jika ingin 1:1 seperti Halodoc, fitur berikut belum terlihat spesifik di backend:

- topik konseling mental
- daftar psikolog / psikiater
- kuis GAD-7 / PHQ-9
- booking konseling

Untuk versi Halalytics saat ini, posisi yang paling aman adalah membuat `Wellness Hub` berbasis:

- `health_diary`
- artikel
- AI daily insight
- tracking kebiasaan

### 21.5 AI masih task-based, belum unified chat session

Halalytics sudah punya banyak endpoint AI, tetapi pola yang ada sekarang adalah `task-based AI`, misalnya:

- analisis gejala
- analisis ingredients
- analisis lab
- risk score
- daily insight

Belum terlihat satu endpoint chat terpadu dengan konsep:

- session start
- riwayat percakapan
- streaming response
- quick replies
- feedback per pesan

Kalau tim mobile ingin pengalaman seperti `HILDA`, maka perlu layer baru, misalnya:

- `POST /api/ai/chat/session`
- `POST /api/ai/chat/message`
- `GET /api/ai/chat/session/{id}`
- `POST /api/ai/chat/feedback`

Sebelum endpoint unified chat dibuat, layar AI assistant sebaiknya diposisikan sebagai `smart action center`, bukan pure chat bot penuh.

## 22. Prioritas Implementasi APK

### Phase 1. Wajib

- health hub
- profil kesehatan
- family profiles
- search obat
- tambah reminder
- reminder list + mark taken
- health tracking
- skincare analysis
- nutrition scan
- articles
- notifications

### Phase 2. Sangat disarankan

- AI assistant screen penuh
- medical records
- emergency flow
- risk score dashboard
- history per anggota keluarga

### Phase 3. Penyempurnaan premium

- voice reminder
- weekly health report PDF
- gamified streak kesehatan
- wellness / mental health hub
- rekomendasi alternatif halal yang lebih aman

## 23. Blueprint Screen Order yang Disarankan

Urutan screen yang paling rapi untuk tim mobile:

1. `Health Hub`
2. `Health Profile`
3. `Family Profiles`
4. `Medicine Reminder List`
5. `Medicine Search`
6. `Add Reminder`
7. `Reminder Detail`
8. `AI Health Assistant`
9. `Health Tracking`
10. `BMI / Health Analyze`
11. `Skincare Analysis`
12. `Nutrition Scan`
13. `Meal Insight`
14. `Medical Records`
15. `Articles`
16. `Health Encyclopedia`
17. `Notifications`
18. `Notification Preferences`
19. `Emergency`

## 24. Kesimpulan Produk

Kalau disusun dengan benar, modul kesehatan Halalytics tidak perlu menyalin Halodoc secara mentah. Posisi yang paling kuat justru:

- `halal lifestyle assistant`
- `personal health tracker`
- `AI ingredient advisor`
- `family-safe scanner`

Artinya value proposition Halalytics bisa lebih spesifik:

- aman untuk tubuh
- aman untuk keyakinan
- aman untuk keluarga

Itu yang membuat fitur kesehatan Halalytics punya identitas sendiri, bukan sekadar imitasi aplikasi kesehatan umum.
