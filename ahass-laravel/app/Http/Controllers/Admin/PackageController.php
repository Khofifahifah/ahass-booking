<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PackageController extends Controller
{
    public function index(Request $request): View
    {
        $edit = null;
        if ($request->filled('edit')) {
            $edit = Package::query()->find($request->integer('edit'));
        }

        return view('admin.packages', [
            'edit' => $edit,
            'rows' => Package::query()->orderBy('jenis')->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        Package::query()->create($data);

        return redirect()->route('admin.packages.index')->with('ok', 'Paket baru ditambahkan.');
    }

    public function update(Request $request, Package $package): RedirectResponse
    {
        if ($request->input('action') === 'toggle') {
            $package->update(['aktif' => ! $package->aktif]);

            return redirect()->route('admin.packages.index')->with('ok', 'Status paket diperbarui.');
        }

        $package->update($this->validated($request));

        return redirect()->route('admin.packages.index')->with('ok', 'Paket diperbarui.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'jenis' => ['required', 'in:servis,part'],
            'nama' => ['required', 'string', 'max:120'],
            'deskripsi' => ['nullable', 'string'],
            'harga' => ['required', 'integer', 'min:0'],
            'durasi_menit' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
