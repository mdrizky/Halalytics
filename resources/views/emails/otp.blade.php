<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; background: #f5f5f5; padding: 20px;">
  <div style="max-width: 480px; margin: auto; background: #fff;
              border-radius: 12px; padding: 32px; text-align: center;">

    <img src="{{ asset('images/logo_halalytics.png') }}" height="60" alt="Halalytics">

    <h2 style="color: #1a5c38; margin-top: 24px;">Reset Password</h2>
    <p style="color: #555;">Halo <strong>{{ $name }}</strong>,</p>
    <p style="color: #555;">Gunakan kode berikut untuk reset password akun Halalytics kamu:</p>

    <div style="background: #f0faf5; border: 2px dashed #1D9E75;
                border-radius: 12px; padding: 20px; margin: 24px 0;">
      <span style="font-size: 42px; font-weight: bold;
                   letter-spacing: 12px; color: #1a5c38;">
        {{ $otp }}
      </span>
    </div>

    <p style="color: #e53935; font-size: 13px;">
      Kode ini berlaku selama <strong>10 menit</strong>.
      Jangan bagikan kode ini kepada siapapun.
    </p>
    <p style="color: #aaa; font-size: 12px; margin-top: 32px;">
      Jika kamu tidak meminta reset password, abaikan email ini.
    </p>
  </div>
</body>
</html>
