<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Partners - Halalytics</title>
  <script type="module" src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.mjs"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <style>
    body{margin:0;background:#E0F2F1;color:#0F172A;font-family:Inter,system-ui,sans-serif}
    .top{background:#004D40;color:#E0F2F1;padding:16px 20px;font-weight:800}
    .wrap{padding:24px}
  </style>
</head>
<body>
  <div class="top">Halalytics Admin — Partner Slider Management Preview</div>
  <div class="wrap">
    @include('components.partner-slider', ['partners' => $partners])
  </div>

<script type="module">
  import Swiper from 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.mjs'
  const swiper = new Swiper('.partnerSwiper', {
      loop: true,
      slidesPerView: 4,
      spaceBetween: 16,
      speed: 4500,
      autoplay: {
          delay: 0,
          disableOnInteraction: false,
          pauseOnMouseEnter: true,
      },
      breakpoints: {
          320: { slidesPerView: 1.2 },
          768: { slidesPerView: 2.3 },
          1024: { slidesPerView: 3.5 },
      }
  })
</script>
</body>
</html>
