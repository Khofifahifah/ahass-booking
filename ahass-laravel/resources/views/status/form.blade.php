@extends('layouts.app', ['title' => 'Cek Booking', 'active' => 'cek'])

@section('content')
<section class="section">
    <div class="container" style="max-width:720px">
        <form class="panel" method="post" action="{{ route('status.show') }}">
            @csrf
            <h2>Cek status booking</h2>
            @if (session('error') || isset($error))
                <div class="alert error">{{ session('error') ?? $error }}</div>
            @endif
            <div class="field">
                <label for="kode">Kode booking</label>
                <input id="kode" name="kode" required placeholder="AH..." value="{{ old('kode') }}">
            </div>
            <div class="field">
                <label for="telepon">Nomor HP</label>
                <input id="telepon" name="telepon" required value="{{ old('telepon') }}">
            </div>
            <button class="btn" type="submit">Lihat status</button>
        </form>

        @if (!empty($booking))
            <div class="panel" style="margin-top:16px">
                <span class="badge {{ $booking->status }}">{{ status_label($booking->status) }}</span>
                <h2>{{ $booking->kode }}</h2>
                <p>{{ $booking->nama_pelanggan }} · {{ $booking->no_polisi }}</p>
                <p>{{ $booking->tanggal->format('d/m/Y') }} pukul {{ $booking->jam }}</p>
                <ul>
                    @foreach ($booking->items as $item)
                        <li>{{ $item->package->nama }} — {{ format_rupiah($item->harga) }}</li>
                    @endforeach
                </ul>
                <p><strong>Total {{ format_rupiah($booking->total) }}</strong></p>
            </div>
        @endif
    </div>
</section>
@endsection
