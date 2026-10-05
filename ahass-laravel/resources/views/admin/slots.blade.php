@extends('layouts.admin', ['title' => 'Slot jam', 'active' => 'slots'])

@section('content')
<h1>Ketersediaan slot</h1>
<form class="toolbar" method="get">
    <input type="date" name="tanggal" value="{{ $tanggal }}">
    <button class="btn" type="submit">Lihat</button>
</form>
<div class="cards">
    @foreach ($timeSlots as $jam)
        @php
            $used = (int) ($usage[$jam] ?? 0);
            $sisa = max(0, (int) config('ahass.slot_capacity') - $used);
        @endphp
        <article class="card">
            <h3>{{ $jam }}</h3>
            <p>{{ $used }} / {{ config('ahass.slot_capacity') }} terisi · {{ $sisa }} sisa</p>
            @forelse ($bookings[$jam] ?? [] as $row)
                <p>
                    <a href="{{ route('admin.bookings.show', $row) }}">{{ $row->kode }}</a>
                    · {{ $row->nama_pelanggan }}
                    · <span class="badge {{ $row->status }}">{{ status_label($row->status) }}</span>
                </p>
            @empty
                <p class="muted">Belum ada booking.</p>
            @endforelse
        </article>
    @endforeach
</div>
@endsection
