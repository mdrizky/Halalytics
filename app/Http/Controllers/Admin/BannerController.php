<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function __construct(
        private readonly \App\Services\AdminBroadcastNotificationService $notificationService
    ) {}

    public function index()
    {
        $banners = Banner::orderBy('position', 'asc')->get();
        $global_user_count = \App\Models\User::count();
        return view('admin.banner', compact('banners', 'global_user_count'));
    }

    public function store(Request $request)
    {
        $uploadError = $this->checkUploadError($request, 'image');
        if ($uploadError) {
            return redirect()->back()->withInput()->with('error', $uploadError);
        }

        $postError = $this->checkPostMaxSize();
        if ($postError) {
            return redirect()->back()->withInput()->with('error', $postError);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'nullable|boolean',
            'position' => 'nullable|integer',
        ]);

        $data = $request->except('image');
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/banners');
            $data['image'] = str_replace('public/', 'storage/', $path);
        }

        $banner = Banner::create($data);

        if ($banner->is_active) {
            $this->notificationService->broadcast(
                'Poster baru tersedia',
                'Lihat poster terbaru: ' . $banner->title,
                'poster',
                [
                    'banner_id' => (string)$banner->id,
                    'action_type' => 'open_home_banner',
                    'action_value' => (string)$banner->id,
                ]
            );
        }

        return redirect()->route('admin.banner')->with('success', 'Banner berhasil ditambahkan');
    }

    public function update(Request $request, Banner $banner)
    {
        $uploadError = $this->checkUploadError($request, 'image');
        if ($uploadError) {
            return redirect()->back()->withInput()->with('error', $uploadError);
        }

        $postError = $this->checkPostMaxSize();
        if ($postError) {
            return redirect()->back()->withInput()->with('error', $postError);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'nullable|boolean',
            'position' => 'nullable|integer',
        ]);

        $data = $request->except('image');
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            if ($banner->image) {
                Storage::delete(str_replace('storage/', 'public/', $banner->image));
            }
            $path = $request->file('image')->store('public/banners');
            $data['image'] = str_replace('public/', 'storage/', $path);
        }

        $banner->update($data);

        if ($banner->is_active) {
            $this->notificationService->broadcast(
                'Poster diperbarui',
                'Update poster: ' . $banner->title,
                'poster',
                [
                    'banner_id' => (string)$banner->id,
                    'action_type' => 'open_home_banner',
                    'action_value' => (string)$banner->id,
                ]
            );
        }

        return redirect()->route('admin.banner')->with('success', 'Banner berhasil diperbarui');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image) {
            Storage::delete(str_replace('storage/', 'public/', $banner->image));
        }
        $banner->delete();

        return redirect()->route('admin.banner')->with('success', 'Banner berhasil dihapus');
    }

    private function checkPostMaxSize(): ?string
    {
        $contentLength = $_SERVER['CONTENT_LENGTH'] ?? 0;
        $postMaxSize = $this->parseBytes(ini_get('post_max_size'));
        if ($contentLength > $postMaxSize) {
            $maxSize = ini_get('post_max_size');
            return "Ukuran total data terlalu besar. Maksimal ukuran POST: {$maxSize}. Upload gambar maksimal 2MB.";
        }
        return null;
    }

    private function parseBytes(string $value): int
    {
        $value = trim($value);
        $unit = strtolower(substr($value, -1));
        $bytes = (int) $value;
        return match ($unit) {
            'g' => $bytes * 1024 * 1024 * 1024,
            'm' => $bytes * 1024 * 1024,
            'k' => $bytes * 1024,
            default => $bytes,
        };
    }

    private function checkUploadError(Request $request, string $field): ?string
    {
        $file = $request->file($field);
        if ($file === null) {
            return null;
        }
        if ($file->isValid()) {
            return null;
        }
        $maxSize = ini_get('upload_max_filesize');
        $messages = [
            UPLOAD_ERR_INI_SIZE => "File gambar terlalu besar. Maksimal ukuran upload: {$maxSize} per file.",
            UPLOAD_ERR_FORM_SIZE => "File gambar terlalu besar (melebihi batas form).",
            UPLOAD_ERR_PARTIAL => "File gambar hanya terupload sebagian. Coba upload ulang.",
            UPLOAD_ERR_NO_TMP_DIR => "Folder temporary server tidak ditemukan.",
            UPLOAD_ERR_CANT_WRITE => "Gagal menyimpan file ke disk server.",
            UPLOAD_ERR_EXTENSION => "Upload file dihentikan oleh ekstensi server.",
        ];
        return $messages[$file->getError()] ?? 'Terjadi kesalahan saat upload gambar. Coba lagi.';
    }
}
