<?php

namespace App\Http\Controllers;

use App\Models\ActivityModel;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = ActivityModel::with('user')->orderBy('waktu', 'desc')->get();
        return view('admin.activities.index', compact('activities'));
    }
}
