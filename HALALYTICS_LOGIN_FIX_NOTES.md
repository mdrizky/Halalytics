# Halalytics Login Fix Notes

## Perbaikan login halaman user

Implementasi backend login sudah dirapikan agar fokus **username + password** saja (tanpa pilihan demo role dari UI):

- Request login sekarang wajib:
  - `username`
  - `password`
- Autentikasi menggunakan field `username` secara langsung.
- Role tetap di-return dari backend setelah login sukses, sehingga routing role dilakukan otomatis oleh app (bukan dipilih user di form login).

## Dampak ke UI Login Android

Agar UI tidak kaku dan sesuai permintaan:

1. Hapus chip demo akun (`Ad`, `Pengg`, `Pak`) di layar login.
2. Label input ubah menjadi `Username` (bukan email/username campur).
3. Tampilkan pesan helper kecil:
   - `Gunakan username akun Anda untuk masuk`.
4. Setelah login sukses, route berdasarkan field `user.role` dari response API.

## Aturan warna (maks 3 warna)

Sudah ditambahkan palet 3 warna resmi di `ColorPalette.kt`:

1. Primary: `#004D40`
2. Background: `#E0F2F1`
3. Text: `#0F172A`

Semua screen diminta hanya memakai ketiga warna ini (dengan alpha/opacity dari warna yang sama jika perlu variasi).
