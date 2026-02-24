<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineReminder;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MedicationReminderController extends Controller
{
    private $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Buat pengingat obat baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'medicine_id' => 'required|exists:medicines,id_medicine',
            'symptoms' => 'nullable|string',
            'frequency_per_day' => 'required|integer|min:1|max:10',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string'
        ]);

        try {
            $user = auth()->user();
            $medicine = Medicine::where('id_medicine', $request->medicine_id)->firstOrFail();

            // Generate jadwal otomatis berdasarkan frekuensi
            $scheduleTimes = $this->generateScheduleTimes($request->frequency_per_day);

            $reminder = MedicineReminder::create([
                'id_user' => $user->id_user,
                'id_medicine' => $request->medicine_id,
                'dosage' => $medicine->dosage_info,
                'frequency_per_day' => $request->frequency_per_day,
                'schedule_times' => $scheduleTimes,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_active' => true,
                'notes' => $request->notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengingat obat berhasil dibuat.',
                'data' => $reminder->load('medicine')
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal membuat pengingat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pengingat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Catat konsumsi obat (tandai sudah diminum)
     */
    public function log(Request $request)
    {
        $request->validate([
            'reminder_id' => 'required|exists:medicine_reminders,id_reminder',
            'status' => 'required|in:taken,skipped,late',
            'notes' => 'nullable|string'
        ]);

        try {
            $user = auth()->user();

            $reminder = MedicineReminder::where('id_reminder', $request->reminder_id)
                ->where('id_user', $user->id_user)
                ->firstOrFail();

            // Tandai sebagai sudah diminum di model
            $reminder->markAsTaken();

            // Simpan log ke tabel medication_logs
            DB::table('medication_logs')->insert([
                'id_reminder' => $reminder->id_reminder,
                'id_user' => $user->id_user,
                'taken_at' => now(),
                'status' => $request->status,
                'notes' => $request->notes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Obat berhasil ditandai sebagai ' . $request->status,
                'data' => $reminder->fresh()
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mencatat konsumsi obat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil semua pengingat aktif milik user
     */
    public function index()
    {
        try {
            $user = auth()->user();

            $reminders = MedicineReminder::with('medicine')
                ->where('id_user', $user->id_user)
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $reminders
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal memuat pengingat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat pengingat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate jadwal minum obat otomatis berdasarkan frekuensi per hari
     */
    private function generateScheduleTimes(int $frequency): array
    {
        $times = [];
        switch ($frequency) {
            case 1:
                $times = ['08:00'];
                break;
            case 2:
                $times = ['08:00', '20:00'];
                break;
            case 3:
                $times = ['08:00', '14:00', '20:00'];
                break;
            case 4:
                $times = ['07:00', '12:00', '17:00', '22:00'];
                break;
            default:
                $interval = intval(24 / $frequency);
                for ($i = 0; $i < $frequency; $i++) {
                    $hour = (7 + ($i * $interval)) % 24;
                    $times[] = sprintf('%02d:00', $hour);
                }
                break;
        }
        return $times;
    }

    /**
     * Hapus pengingat obat
     */
    public function destroy($id)
    {
        try {
            $user = auth()->user();
            $reminder = MedicineReminder::where('id_reminder', $id)
                ->where('id_user', $user->id_user)
                ->firstOrFail();

            $reminder->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pengingat obat berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pengingat: ' . $e->getMessage()
            ], 500);
        }
    }
}
