<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminNutritionistController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    // Menampilkan semua ahli gizi
    public function index(Request $request)
    {
        $totalNutritionists = User::where('role', 'nutritionist')->count();
        $activeNutritionists = User::where('role', 'nutritionist')->where('active', 1)->count();
        
        $stats = [
            'total_nutritionists' => $totalNutritionists,
            'active_nutritionists' => $activeNutritionists,
        ];
        
        $query = User::where('role', 'nutritionist')->orderByDesc('created_at');
        
        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%");
            });
        }
        
        $nutritionists = $query->paginate(10)->withQueryString();
        
        return view('admin.nutritionists.index', [
            'nutritionists' => $nutritionists,
            'stats' => $stats,
        ]);
    }

    // Show ahli gizi detail
    public function show($id)
    {
        $nutritionist = User::where('role', 'nutritionist')->findOrFail($id);
        
        // Custom statistics for nutritionist (e.g., number of patients/consultations)
        // For now, let's use a simple view
        return view('admin.nutritionists.show', compact('nutritionist'));
    }

    // Toggle status active/non-active
    public function toggleStatus(Request $request, $id)
    {
        $nutritionist = User::where('role', 'nutritionist')->findOrFail($id);
        $nutritionist->active = $nutritionist->active == 1 ? 0 : 1;
        $nutritionist->save();

        return redirect()->route('admin.nutritionists.index')->with('success', 'Status ahli gizi berhasil diubah!');
    }

    // Hapus ahli gizi
    public function destroy($id)
    {
        $nutritionist = User::where('role', 'nutritionist')->findOrFail($id);
        $nutritionist->delete();

        return redirect()->route('admin.nutritionists.index')->with('success', 'Ahli gizi berhasil dihapus!');
    }
}
