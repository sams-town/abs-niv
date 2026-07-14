<?php

namespace App\Http\Controllers;

use App\Models\settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    public function index()
    {
        $title = 'Settings';
        $data = settings::first();
        return view('settings.index', compact(
            'title',
            'data'
        ));
    }

    public function store(Request $request)
    {
        $settings = settings::first();

        $validated = $request->validate([
            'name' => 'required',
            'logo' => 'image|file|max:10240|nullable',
            'alamat' => 'nullable',
            'phone' => 'nullable',
            'whatsapp' => 'nullable',
            'api_url' => 'nullable',
            'api_whatsapp' => 'nullable',
            'email' => 'nullable',
            'template_cuti' => 'file|max:20480|nullable',
            'template_lembur' => 'file|max:20480|nullable',
            'template_slip_gaji' => 'file|max:20480|nullable',
        ]);

        if ($request->file('logo')) {
            $file = $request->file('logo');

            // Simpan ke storage/app/public/logo/ (untuk storage link)
            $storagePath = $file->store('logo', 'public');
            $validated['logo'] = $storagePath;

            // Juga salin ke public/assets/img/logo.png (fallback tanpa symlink)
            $file->move(public_path('assets/img'), 'logo.png');
        }

        if ($request->file('template_cuti')) {
            $validated['template_cuti'] = $request->file('template_cuti')->store('templates');
        }
        if ($request->file('template_lembur')) {
            $validated['template_lembur'] = $request->file('template_lembur')->store('templates');
        }
        if ($request->file('template_slip_gaji')) {
            $validated['template_slip_gaji'] = $request->file('template_slip_gaji')->store('templates');
        }

        $settings->update($validated);

        // Pastikan storage link ada
        if (!file_exists(public_path('storage'))) {
            try {
                Artisan::call('storage:link');
            } catch (\Exception $e) {
                // Abaikan jika gagal, logo tetap tersimpan di public/assets/img/logo.png
            }
        }

        return back()->with('success', 'Data Berhasil Ditambahkan');
    }
}
