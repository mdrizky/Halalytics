<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HalalCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CertificateController extends Controller
{
    /** POST /api/certificate/verify — Verify a halal certificate number. */
    public function verify(Request $request)
    {
        $request->validate([
            'certificate_number' => 'required|string|max:100',
        ]);

        $certNumber = trim($request->certificate_number);
        $result = HalalCertificate::verify($certNumber);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Sertifikat tidak ditemukan di database kami.',
                'data'    => null,
            ], 404);
        }

        // Log verification attempt
        Log::info('Certificate verification', [
            'cert_number' => $certNumber,
            'valid'       => $result['valid'],
            'user_id'     => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $result['valid']
                ? 'Sertifikat halal VALID dan masih berlaku.'
                : 'Sertifikat ditemukan tetapi sudah ' . $result['status'] . '.',
            'data'    => $result,
        ]);
    }

    /** GET /api/certificate/history — User's verification history. */
    public function history(Request $request)
    {
        $user = Auth::user();

        // Pull from certificate_verifications table (existing)
        $history = \DB::table('certificate_verifications')
            ->where('user_id', $user->id_user)
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $history,
        ]);
    }
}
