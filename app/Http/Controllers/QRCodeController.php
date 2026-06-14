<?php

namespace App\Http\Controllers;

use App\Models\QRCode;
use App\Services\QRCodeGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QRCodeController extends Controller
{
    public function index()
    {
        $qrCodes = QRCode::latest()->get();

        return view('qrcode.index', compact('qrCodes'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'data' => 'required|string',
            'width' => 'required|integer|min:100|max:1000',
            'height' => 'required|integer|min:100|max:1000',
            'finder_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'finder_inner_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'data_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'bg_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $data = $validated['data'];
        $size = min($validated['width'], $validated['height']);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $logoPath = storage_path('app/public/'.$logoPath);
        }

        $generator = new QRCodeGenerator;
        $qrCodeImage = $generator->generate(
            $data,
            $size,
            $validated['finder_color'],
            $validated['finder_inner_color'],
            $validated['data_color'],
            $validated['bg_color'],
            $logoPath
        );

        $filename = 'qr_codes/'.Str::random(16).'.png';
        Storage::disk('public')->put($filename, $qrCodeImage);

        $qrCode = QRCode::create([
            'data' => $data,
            'width' => $validated['width'],
            'height' => $validated['height'],
            'logo_path' => $logoPath ? str_replace(storage_path('app/public/'), '', $logoPath) : null,
            'finder_color' => $validated['finder_color'],
            'finder_inner_color' => $validated['finder_inner_color'],
            'data_color' => $validated['data_color'],
            'bg_color' => $validated['bg_color'],
            'generated_image_path' => $filename,
        ]);

        return redirect()->route('qrcode.index')->with([
            'success' => 'QR Code berhasil digenerate!',
            'qr_code_url' => asset('storage/'.$filename),
            'qr_code_id' => $qrCode->id,
        ]);
    }

    public function download($id)
    {
        $qrCode = QRCode::findOrFail($id);
        $path = storage_path('app/public/'.$qrCode->generated_image_path);

        if (! file_exists($path)) {
            abort(404);
        }

        return response()->download($path, 'qrcode_'.$qrCode->id.'.png');
    }
}
