@extends('layouts.app', ['title' => 'Beranda', 'active' => 'home'])

@section('content')
<section class="hero">
    <div class="container">
        <p class="muted" style="color:#ffd2ce;margin:0 0 8px">Bengkel resmi Honda</p>
        <h1>Booking servis motor Honda tanpa antrian di bengkel.</h1>
        <p>Pilih tanggal, cek slot jam yang masih kosong, lalu tentukan paket servis dan part original AHM.</p>
        <div class="hero-actions">
            <a class="btn" href="{{ route('booking.create') }}">Daftar servis sekarang</a>
            <a class="btn ghost" href="{{ route('status.form') }}">Cek status booking</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="grid-3">
            <article class="card">
                <h3>1. Isi data kendaraan</h3>
                <p class="muted">Nama, nomor HP, plat, dan tipe motor Honda Anda.</p>
            </article>
            <article class="card">
                <h3>2. Pilih slot jam</h3>
                <p class="muted">Kapasitas {{ config('ahass.slot_capacity') }} unit per jam. Slot penuh tidak bisa dipilih.</p>
            </article>
            <article class="card">
                <h3>3. Paket & part</h3>
                <p class="muted">Pilih servis berkala, tune up, atau tambah oli dan suku cadang.</p>
            </article>
        </div>

        <div class="panel" style="margin-top:24px">
            <h2>Slot hari ini</h2>
            <p class="muted">{{ $open }} tempat masih tersedia untuk {{ \Carbon\Carbon::parse($today)->format('d/m/Y') }}.</p>
            <div class="slots" style="margin-top:12px">
                @foreach ($slots as $slot)
                    <div class="slot {{ $slot['sisa'] === 0 ? 'is-full' : '' }}">
                        <strong>{{ $slot['jam'] }}</strong>
                        <small>{{ $slot['sisa'] === 0 ? 'Penuh' : $slot['sisa'].' sisa' }}</small>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection
