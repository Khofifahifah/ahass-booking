@extends('layouts.admin', ['title' => 'Paket & Part', 'active' => 'packages'])

@section('content')
<h1>Paket servis & part</h1>
@if (session('ok')) <div class="alert ok">{{ session('ok') }}</div> @endif
@if (session('error')) <div class="alert error">{{ session('error') }}</div> @endif
@if ($errors->any()) <div class="alert error">{{ $errors->first() }}</div> @endif
<div class="grid-2">
    <form class="panel" method="post" action="{{ $edit ? route('admin.packages.update', $edit) : route('admin.packages.store') }}">
        @csrf
        @if ($edit) @method('PUT') @endif
        <h2>{{ $edit ? 'Ubah paket' : 'Tambah paket' }}</h2>
        <div class="field">
            <label>Jenis</label>
            <select name="jenis">
                <option value="servis" @selected(($edit->jenis ?? '') === 'servis')>Servis</option>
                <option value="part" @selected(($edit->jenis ?? '') === 'part')>Part</option>
            </select>
        </div>
        <div class="field">
            <label>Nama</label>
            <input name="nama" required value="{{ $edit->nama ?? '' }}">
        </div>
        <div class="field">
            <label>Deskripsi</label>
            <textarea name="deskripsi">{{ $edit->deskripsi ?? '' }}</textarea>
        </div>
        <div class="grid-2">
            <div class="field">
                <label>Harga</label>
                <input type="number" name="harga" min="0" required value="{{ $edit->harga ?? 0 }}">
            </div>
            <div class="field">
                <label>Durasi (menit)</label>
                <input type="number" name="durasi_menit" min="0" value="{{ $edit->durasi_menit ?? 60 }}">
            </div>
        </div>
        <button class="btn" type="submit">Simpan</button>
    </form>
    <div class="panel">
        <h2>Daftar</h2>
        <table>
            <thead><tr><th>Nama</th><th>Harga</th><th></th></tr></thead>
            <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td>
                        <strong>{{ $row->nama }}</strong><br>
                        <small class="muted">{{ $row->jenis }} · {{ $row->aktif ? 'aktif' : 'nonaktif' }}</small>
                    </td>
                    <td>{{ format_rupiah($row->harga) }}</td>
                    <td>
                        <a href="{{ route('admin.packages.index', ['edit' => $row->id]) }}">Ubah</a>
                        <form method="post" action="{{ route('admin.packages.update', $row) }}" style="display:inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="toggle">
                            <button class="btn ghost" type="submit">{{ $row->aktif ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
