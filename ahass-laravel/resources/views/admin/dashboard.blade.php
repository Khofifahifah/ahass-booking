@extends('layouts.admin', ['title' => 'Dashboard', 'active' => 'dash'])

@section('content')
<h1>Dashboard</h1>
<p class="muted">Halo, {{ auth()->user()->name }}.</p>
<div class="stats">
    <div class="stat"><span class="muted">Booking hari ini</span><b>{{ $hariIni }}</b></div>
    <div class="stat"><span class="muted">Menunggu konfirmasi</span><b>{{ $pending }}</b></div>
    <div class="stat"><span class="muted">Total pendaftaran</span><b>{{ $total }}</b></div>
    <div class="stat"><span class="muted">Estimasi omzet</span><b>{{ format_rupiah($omzet) }}</b></div>
</div>
<div class="panel">
    <h2>Pendaftaran terbaru</h2>
    <table>
        <thead>
            <tr><th>Kode</th><th>Pelanggan</th><th>Jadwal</th><th>Paket</th><th>Status</th></tr>
        </thead>
        <tbody>
        @forelse ($recent as $row)
            <tr>
                <td><a href="{{ route('admin.bookings.show', $row) }}">{{ $row->kode }}</a></td>
                <td>{{ $row->nama_pelanggan }}<br><small class="muted">{{ $row->no_polisi }}</small></td>
                <td>{{ $row->tanggal->format('d/m/Y') }} {{ $row->jam }}</td>
                <td>{{ $row->package->nama }}</td>
                <td><span class="badge {{ $row->status }}">{{ status_label($row->status) }}</span></td>
            </tr>
        @empty
            <tr><td colspan="5" class="muted">Belum ada pendaftaran.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
