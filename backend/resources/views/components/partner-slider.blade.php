<section class="partner-showcase py-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="m-0" style="color:#004D40;font-weight:800;">Official Collaboration</h3>
        <small style="color:#0F172A;opacity:.75;">Trusted by institutions & industry partners</small>
    </div>

    <div class="swiper partnerSwiper">
        <div class="swiper-wrapper">
            @foreach($partners as $partner)
                <div class="swiper-slide">
                    <a href="{{ $partner['url'] ?? '#' }}" class="partner-card" target="_blank" rel="noopener">
                        <img src="{{ $partner['logo'] }}" alt="{{ $partner['name'] }}">
                        <span>{{ $partner['name'] }}</span>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<style>
    .partner-showcase{position:relative}
    .partner-card{
        display:flex;align-items:center;gap:12px;
        background:#E0F2F1;border:1px solid #004D40;border-radius:999px;
        padding:14px 18px;text-decoration:none;transition:.25s ease;
    }
    .partner-card:hover{transform:translateY(-3px)}
    .partner-card img{
        width:48px;height:48px;object-fit:contain;border-radius:50%;
        background:#fff;padding:6px;border:1px solid #004D40;
        filter: grayscale(100%);
        transition: .25s ease;
    }
    .partner-card:hover img{filter: grayscale(0%)}
    .partner-card span{color:#0F172A;font-weight:700;font-size:14px}
</style>
