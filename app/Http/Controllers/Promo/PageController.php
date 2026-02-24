<?php

namespace App\Http\Controllers\Promo;

use App\Http\Controllers\Controller;
use App\Models\PromoBlog;
use App\Models\PromoSetting;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        $settings = PromoSetting::getAllSettings();
        $latestBlogs = PromoBlog::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
            
        return view('promo.home', compact('settings', 'latestBlogs'));
    }

    public function features()
    {
        $settings = PromoSetting::getAllSettings();
        return view('promo.features', compact('settings'));
    }

    public function about()
    {
        $settings = PromoSetting::getAllSettings();
        return view('promo.about', compact('settings'));
    }

    public function download()
    {
        $settings = PromoSetting::getAllSettings();
        return view('promo.download', compact('settings'));
    }

    public function privacy()
    {
        $settings = PromoSetting::getAllSettings();
        return view('promo.privacy', compact('settings'));
    }
}
