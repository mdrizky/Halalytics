<?php

namespace App\Http\Controllers\Admin;

use App\Models\HalalCertificate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class HalalCertificateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = HalalCertificate::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%")
                  ->orWhere('manufacturer', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('issuing_body') && $request->issuing_body) {
            $query->where('issuing_body', $request->issuing_body);
        }

        $certificates = $query->orderByDesc('created_at')->paginate(20);
        $stats = [
            'total' => HalalCertificate::count(),
            'active' => HalalCertificate::where('status', 'active')->count(),
            'expired' => HalalCertificate::where('status', 'expired')->count(),
            'revoked' => HalalCertificate::where('status', 'revoked')->count(),
            'expiring_soon' => HalalCertificate::where('status', 'active')
                ->whereBetween('expires_at', [now(), now()->addDays(30)])->count(),
        ];

        return view('admin.certificates.index', compact('certificates', 'stats'));
    }

    public function create()
    {
        return view('admin.certificates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'certificate_number' => 'required|string|unique:halal_certificates',
            'product_name' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'issuing_body' => 'required|in:MUI,LPPOM,BPJPH',
            'issued_at' => 'required|date',
            'expires_at' => 'required|date|after:issued_at',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
        ]);

        if ($request->hasFile('certificate_file')) {
            $validated['certificate_file'] = $request->file('certificate_file')
                ->store('certificates', 'public');
        }

        $validated['status'] = now()->gt($validated['expires_at']) ? 'expired' : 'active';

        HalalCertificate::create($validated);

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Sertifikat halal berhasil ditambahkan.');
    }

    public function edit(HalalCertificate $certificate)
    {
        return view('admin.certificates.edit', compact('certificate'));
    }

    public function update(Request $request, HalalCertificate $certificate)
    {
        $validated = $request->validate([
            'certificate_number' => 'required|string|unique:halal_certificates,certificate_number,' . $certificate->id,
            'product_name' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'issuing_body' => 'required|in:MUI,LPPOM,BPJPH',
            'issued_at' => 'required|date',
            'expires_at' => 'required|date|after:issued_at',
            'status' => 'required|in:active,expired,revoked',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
        ]);

        if ($request->hasFile('certificate_file')) {
            // Delete old file
            if ($certificate->certificate_file) {
                Storage::disk('public')->delete($certificate->certificate_file);
            }
            $validated['certificate_file'] = $request->file('certificate_file')
                ->store('certificates', 'public');
        }

        $certificate->update($validated);

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Sertifikat halal berhasil diperbarui.');
    }

    public function destroy(HalalCertificate $certificate)
    {
        if ($certificate->certificate_file) {
            Storage::disk('public')->delete($certificate->certificate_file);
        }
        $certificate->delete();

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Sertifikat halal berhasil dihapus.');
    }

    /**
     * Bulk import from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');
        $header = fgetcsv($handle);
        $imported = 0;
        $skipped = 0;

        while ($row = fgetcsv($handle)) {
            $data = array_combine($header, $row);

            if (HalalCertificate::where('certificate_number', $data['certificate_number'] ?? '')->exists()) {
                $skipped++;
                continue;
            }

            try {
                HalalCertificate::create([
                    'certificate_number' => $data['certificate_number'],
                    'product_name' => $data['product_name'],
                    'manufacturer' => $data['manufacturer'] ?? 'Unknown',
                    'issuing_body' => $data['issuing_body'] ?? 'MUI',
                    'issued_at' => $data['issued_at'] ?? now(),
                    'expires_at' => $data['expires_at'] ?? now()->addYears(2),
                    'status' => 'active',
                ]);
                $imported++;
            } catch (\Exception $e) {
                $skipped++;
            }
        }

        fclose($handle);

        return redirect()->route('admin.certificates.index')
            ->with('success', "Import selesai: {$imported} berhasil, {$skipped} dilewati.");
    }
}
