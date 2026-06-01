<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ScanModel;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:user']);
    }

    public function index()
    {
        $user = Auth::user();
        $recentScans = ScanModel::with('product')
            ->where('id_user', $user->id_user)
            ->latest('tanggal_scan')
            ->limit(5)
            ->get();
            
        $recommendedArticles = Article::where('status', 'published')
            ->latest()
            ->limit(3)
            ->get();

        return view('user.dashboard', compact('user', 'recentScans', 'recommendedArticles'));
    }
}
