@extends('layouts.admin', ['title' => 'Pendaftaran', 'active' => 'bookings'])

@section('content')
<h1>Pendaftaran servis</h1>
<form class="toolbar" method="get">
    <input name="q" placeholder="Cari kode, nama, plat" value="{{ $q }}">
    <select name="status">
        <option value="">Semua status</option>
        @foreach (config('ahass.statuses') as $st => $label)
            <option value="{{ $st }}" @selected($status === $st)>{{ $label }}</option>
        @endforeach
    </select>
    <button class="btn" type="submit">Filter</button>
</form>
<div class="panel">
    <table>
        <thead>
            <tr><th>Kode</th><th>Pelanggan</th><th>Jadwal</th><th>Paket</th><th>Total</th><th>Status</th></tr>
        </thead>
        <tbody>
        @forelse ($rows as $row)
            <tr>
                <td><a href="{{ route('admin.bookings.show', $row) }}">{{ $row->kode }}</a></td>
                <td>{{ $row->nama_pelanggan }}<br><small class="muted">{{ $row->telepon }} · {{ $row->no_polisi }}</small></td>
                <td>{{ $row->tanggal->format('d/m/Y') }} {{ $row->jam }}</td>
                <td>{{ $row->package->nama }}</td>
                <td>{{ format_rupiah($row->total) }}</td>
                <td><span class="badge {{ $row->status }}">{{ status_label($row->status) }}</span></td>
            </tr>
        @empty
            <tr><td colspan="6" class="muted">Tidak ada data.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
