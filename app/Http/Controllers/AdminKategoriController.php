<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriModel;

class AdminKategoriController extends Controller
{
    public function index()
    {
        $kategori = KategoriModel
        ::all();
        return view('admin.kategori', compact('kategori'));
    }
}
