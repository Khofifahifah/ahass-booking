@extends('layouts.app', ['title' => 'Booking berhasil', 'active' => 'booking'])

@section('content')
<section class="section">
    <div class="container">
        <div class="panel">
            <div class="alert ok">Pendaftaran servis berhasil dikirim.</div>
            <h2>Kode booking: {{ $booking->kode }}</h2>
            <p>{{ $booking->nama_pelanggan }} · {{ $booking->no_polisi }} · {{ $booking->tipe_motor }}</p>
            <p><strong>{{ $booking->tanggal->format('d/m/Y') }} pukul {{ $booking->jam }}</strong></p>
            <p>Total estimasi: {{ format_rupiah($booking->total) }}</p>
            <p class="muted">Simpan kode ini untuk cek status. Admin bengkel akan mengonfirmasi slot Anda.</p>
            <a class="btn" href="{{ route('status.form') }}">Cek status booking</a>
        </div>
    </div>
</section>
@endsection
