<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CertificateVerification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateValidatorController extends Controller
{
    /**
     * Verify a certificate number or QR data
     */
    public function verify(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        $user = Auth::user();
        $qrData = $request->qr_data;

        // Mock BPJPH Verification Logic
        // In a real app, this would call external API: https://info.halal.go.id/cari/
        
        $certificateData = $this->lookupCertificate($qrData);

        if (!$certificateData) {
            return response()->json([
                'success' => false,
                'message' => 'Sertifikat tidak ditemukan atau tidak valid.',
                'status' => 'invalid'
            ]);
        }

        // Log the verification
        CertificateVerification::create([
            'user_id' => $user->id_user,
            'certificate_number' => $certificateData['certificate_number'],
            'product_name' => $certificateData['product_name'],
            'manufacturer' => $certificateData['manufacturer'],
            'expiry_date' => $certificateData['expiry_date'],
            'status' => $certificateData['status'],
            'issuer' => $certificateData['issuer'],
            'raw_data' => ['qr_scanned' => $qrData]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sertifikat Terverifikasi',
            'data' => $certificateData
        ]);
    }

    /**
     * Mock certificate lookup
     */
    private function lookupCertificate($query)
    {
        // Sample data for demo
        $certificates = [
            'ID00110000216670821' => [
                'certificate_number' => 'ID00110000216670821',
                'product_name' => 'Indomie Rasa Ayam Bawang',
                'manufacturer' => 'PT Indofood CBP Sukses Makmur Tbk',
                'expiry_date' => '2027-12-31',
                'status' => 'valid',
                'issuer' => 'BPJPH'
            ],
            'ID32110000002530121' => [
                'certificate_number' => 'ID32110000002530121',
                'product_name' => 'Kopi Kapal Api Special',
                'manufacturer' => 'PT Santos Jaya Abadi',
                'expiry_date' => '2026-05-20',
                'status' => 'valid',
                'issuer' => 'BPJPH'
            ]
        ];

        // Search by exact match or if it's a URL containing the number
        foreach ($certificates as $number => $data) {
            if (str_contains($query, $number)) {
                return $data;
            }
        }

        return null;
    }

    /**
     * Get user's verification history
     */
    public function history()
    {
        $user = Auth::user();
        $history = CertificateVerification::where('user_id', $user->id_user)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }
}
