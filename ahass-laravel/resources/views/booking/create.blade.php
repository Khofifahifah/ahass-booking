@extends('layouts.app', ['title' => 'Daftar Servis', 'active' => 'booking'])

@section('content')
<section class="section">
    <div class="container grid-2">
        <form class="panel" method="post" action="{{ route('booking.store') }}">
            @csrf
            <h2>Form pendaftaran servis</h2>
            <p class="muted">Isi data pelanggan, pilih jam, lalu tentukan paket.</p>
            @if (session('error'))
                <div class="alert error">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert error">{{ $errors->first() }}</div>
            @endif

            <div class="grid-2">
                <div class="field">
                    <label for="nama">Nama pelanggan</label>
                    <input id="nama" name="nama" required value="{{ old('nama') }}">
                </div>
                <div class="field">
                    <label for="telepon">Nomor HP</label>
                    <input id="telepon" name="telepon" required value="{{ old('telepon') }}">
                </div>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label for="no_polisi">Nomor polisi</label>
                    <input id="no_polisi" name="no_polisi" required value="{{ old('no_polisi') }}">
                </div>
                <div class="field">
                    <label for="tipe_motor">Tipe motor</label>
                    <select id="tipe_motor" name="tipe_motor" required>
                        @foreach (config('ahass.motor_types') as $type)
                            <option @selected(old('tipe_motor') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="field">
                <label for="keluhan">Keluhan / permintaan</label>
                <textarea id="keluhan" name="keluhan">{{ old('keluhan') }}</textarea>
            </div>
            <div class="field">
                <label for="tanggal">Tanggal servis</label>
                <input id="tanggal" type="date" name="tanggal" required min="{{ now()->toDateString() }}" value="{{ old('tanggal', now()->toDateString()) }}">
            </div>
            <div class="field">
                <label>Slot jam</label>
                <div id="slot-list" class="slots"></div>
            </div>

            <h3>Paket servis</h3>
            @foreach ($packages as $pkg)
                <label class="pkg">
                    <input type="radio" name="package_id" value="{{ $pkg->id }}" required @checked((string) old('package_id') === (string) $pkg->id)>
                    <span>
                        <strong>{{ $pkg->nama }}</strong>
                        <div class="muted">{{ $pkg->deskripsi }} · {{ $pkg->durasi_menit }} menit</div>
                    </span>
                    <span class="price">{{ format_rupiah($pkg->harga) }}</span>
                </label>
            @endforeach

            <h3>Part tambahan (opsional)</h3>
            @foreach ($parts as $part)
                <label class="pkg">
                    <input type="checkbox" name="parts[]" value="{{ $part->id }}" @checked(in_array((string) $part->id, array_map('strval', old('parts', [])), true))>
                    <span>
                        <strong>{{ $part->nama }}</strong>
                        <div class="muted">{{ $part->deskripsi }}</div>
                    </span>
                    <span class="price">{{ format_rupiah($part->harga) }}</span>
                </label>
            @endforeach

            <button class="btn" type="submit">Kirim pendaftaran</button>
        </form>
        <aside class="panel">
            <h2>Ketersediaan slot</h2>
            <p class="muted">Setiap jam menampung maksimal {{ config('ahass.slot_capacity') }} motor. Istirahat bengkel pukul 12.00.</p>
            <p class="muted">Setelah terkirim, catat kode booking untuk cek status.</p>
        </aside>
    </div>
</section>
@endsection

@section('scripts')
<script>
const endpoint = @json(route('slots.index'));
const selected = @json(old('jam', ''));
const tanggal = document.getElementById('tanggal');
const list = document.getElementById('slot-list');

async function loadSlots() {
    const res = await fetch(endpoint + '?tanggal=' + encodeURIComponent(tanggal.value));
    const data = await res.json();
    list.innerHTML = '';
    (data.slots || []).forEach((slot) => {
        const label = document.createElement('label');
        label.className = 'slot' + (slot.sisa === 0 ? ' is-full' : '');
        label.innerHTML = `
            <input type="radio" name="jam" value="${slot.jam}" ${slot.sisa === 0 ? 'disabled' : ''} ${selected === slot.jam ? 'checked' : ''} required>
            <strong>${slot.jam}</strong>
            <small>${slot.sisa === 0 ? 'Penuh' : slot.sisa + ' sisa'}</small>
        `;
        list.appendChild(label);
    });
}
tanggal.addEventListener('change', loadSlots);
loadSlots();
</script>
@endsection
