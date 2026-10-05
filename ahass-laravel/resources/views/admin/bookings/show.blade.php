@extends('layouts.admin', ['title' => 'Detail booking', 'active' => 'bookings'])

@section('content')
<h1>{{ $booking->kode }}</h1>
@if (session('ok')) <div class="alert ok">{{ session('ok') }}</div> @endif
@if (session('error')) <div class="alert error">{{ session('error') }}</div> @endif
<div class="grid-2">
    <div class="panel">
        <p><strong>{{ $booking->nama_pelanggan }}</strong><br>{{ $booking->telepon }}</p>
        <p>{{ $booking->no_polisi }} · {{ $booking->tipe_motor }}</p>
        <p>Jadwal: {{ $booking->tanggal->format('d/m/Y') }} pukul {{ $booking->jam }}</p>
        <p>Keluhan: {{ $booking->keluhan ?: '-' }}</p>
        <h3>Item</h3>
        <ul>
            @foreach ($booking->items as $item)
                <li>{{ $item->package->jenis }} · {{ $item->package->nama }} — {{ format_rupiah($item->harga) }}</li>
            @endforeach
        </ul>
        <p><strong>Total {{ format_rupiah($booking->total) }}</strong></p>
    </div>
    <form class="panel" method="post" action="{{ route('admin.bookings.update', $booking) }}">
        @csrf
        @method('PUT')
        <h2>Ubah status</h2>
        <div class="field">
            <label for="status">Status</label>
            <select id="status" name="status">
                @foreach (config('ahass.statuses') as $st => $label)
                    <option value="{{ $st }}" @selected($booking->status === $st)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label for="catatan_admin">Catatan admin</label>
            <textarea id="catatan_admin" name="catatan_admin">{{ $booking->catatan_admin }}</textarea>
        </div>
        <button class="btn" type="submit">Simpan</button>
    </form>
</div>
@endsection
