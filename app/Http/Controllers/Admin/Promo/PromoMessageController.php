<?php

namespace App\Http\Controllers\Admin\Promo;

use App\Http\Controllers\Controller;
use App\Models\PromoContact;
use Illuminate\Http\Request;

class PromoMessageController extends Controller
{
    public function index()
    {
        $messages = PromoContact::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.promo.messages.index', compact('messages'));
    }

    public function show($id)
    {
        $message = PromoContact::findOrFail($id);
        
        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }
        
        return view('admin.promo.messages.show', compact('message'));
    }

    public function destroy($id)
    {
        PromoContact::findOrFail($id)->delete();
        return redirect()->route('admin.promo.messages.index')->with('success', 'Pesan berhasil dihapus');
    }
}
