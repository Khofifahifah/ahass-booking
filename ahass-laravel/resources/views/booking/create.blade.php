@extends('layouts.app', ['title' => 'Daftar Servis', 'active' => 'booking'])

@section('content')
<section class="section">
    <div class="container grid-2">
        <form id="booking-form" class="panel" method="post" action="{{ route('booking.store') }}">
            @csrf
            <h2>Form pendaftaran servis</h2>
            <p class="muted">Isi data pelanggan, pilih jam, lalu tentukan paket.</p>
            <div id="ajax-alert" hidden></div>
            <div id="ajax-success" class="alert ok" hidden></div>

            <div class="grid-2">
                <div class="field">
                    <label for="nama">Nama pelanggan</label>
                    <input id="nama" name="nama" required>
                </div>
                <div class="field">
                    <label for="telepon">Nomor HP</label>
                    <input id="telepon" name="telepon" required>
                </div>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label for="no_polisi">Nomor polisi</label>
                    <input id="no_polisi" name="no_polisi" required>
                </div>
                <div class="field">
                    <label for="tipe_motor">Tipe motor</label>
                    <select id="tipe_motor" name="tipe_motor" required>
                        @foreach (config('ahass.motor_types') as $type)
                            <option>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="field">
                <label for="keluhan">Keluhan / permintaan</label>
                <textarea id="keluhan" name="keluhan"></textarea>
            </div>
            <div class="field">
                <label for="tanggal">Tanggal servis</label>
                <input id="tanggal" type="date" name="tanggal" required min="{{ now()->toDateString() }}" value="{{ now()->toDateString() }}">
            </div>
            <div class="field">
                <label>Slot jam</label>
                <div id="slot-list" class="slots"></div>
            </div>

            <h3>Paket servis</h3>
            @foreach ($packages as $pkg)
                <label class="pkg">
                    <input type="radio" name="package_id" value="{{ $pkg->id }}" required>
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
                    <input type="checkbox" name="parts[]" value="{{ $part->id }}">
                    <span>
                        <strong>{{ $part->nama }}</strong>
                        <div class="muted">{{ $part->deskripsi }}</div>
                    </span>
                    <span class="price">{{ format_rupiah($part->harga) }}</span>
                </label>
            @endforeach

            <button id="submit-booking" class="btn" type="submit">Kirim pendaftaran</button>
        </form>
        <aside class="panel">
            <h2>Daftar booking</h2>
            <p class="muted">Maksimal {{ config('ahass.slot_capacity') }} motor per jam. Filter tanpa reload halaman.</p>
            <form id="filter-form" class="toolbar">
                <input id="filter-q" name="q" placeholder="Cari kode, nama, plat">
                <input id="filter-tanggal" type="date" name="tanggal" value="{{ now()->toDateString() }}">
                <button class="btn" type="submit">Filter</button>
            </form>
            <table>
                <thead>
                    <tr><th>Kode</th><th>Pelanggan</th><th>Jadwal</th><th>Status</th></tr>
                </thead>
                <tbody id="booking-rows">
                    <tr><td colspan="4" class="muted">Memuat data...</td></tr>
                </tbody>
            </table>
        </aside>
    </div>
</section>
@endsection

@section('scripts')
<script>
(function () {
    const slotsUrl = @json(route('slots.index'));
    const storeUrl = @json(route('booking.store'));
    const listUrl = @json(route('bookings.list'));

    function showAlert(message, type) {
        const $box = $('#ajax-alert');
        $box.removeAttr('hidden').attr('class', 'alert ' + type).text(message);
        $('#ajax-success').attr('hidden', true);
    }

    function showSuccess(html) {
        $('#ajax-alert').attr('hidden', true);
        $('#ajax-success').removeAttr('hidden').html(html);
    }

    function loadSlots() {
        const tanggal = $('#tanggal').val();
        $.ajax({
            url: slotsUrl,
            method: 'GET',
            dataType: 'json',
            data: { tanggal: tanggal }
        }).done(function (data) {
            const $list = $('#slot-list').empty();
            (data.slots || []).forEach(function (slot) {
                const full = slot.sisa === 0;
                const $label = $('<label/>', { class: 'slot' + (full ? ' is-full' : '') });
                $label.append(
                    $('<input/>', {
                        type: 'radio',
                        name: 'jam',
                        value: slot.jam,
                        required: true,
                        disabled: full
                    })
                );
                $label.append($('<strong/>').text(slot.jam));
                $label.append($('<small/>').text(full ? 'Penuh' : slot.sisa + ' sisa'));
                $list.append($label);
            });
        }).fail(function () {
            showAlert('Gagal memuat slot jam.', 'error');
        });
    }

    function loadBookings() {
        $.ajax({
            url: listUrl,
            method: 'GET',
            dataType: 'json',
            data: {
                q: $('#filter-q').val(),
                tanggal: $('#filter-tanggal').val()
            }
        }).done(function (res) {
            const $body = $('#booking-rows').empty();
            if (!res.data || !res.data.length) {
                $body.append('<tr><td colspan="4" class="muted">Tidak ada data.</td></tr>');
                return;
            }
            res.data.forEach(function (row) {
                $body.append(
                    '<tr>' +
                    '<td>' + row.kode + '</td>' +
                    '<td>' + row.nama_pelanggan + '<br><small class="muted">' + row.no_polisi + '</small></td>' +
                    '<td>' + row.tanggal + ' ' + row.jam + '</td>' +
                    '<td><span class="badge ' + row.status + '">' + row.status_label + '</span></td>' +
                    '</tr>'
                );
            });
        });
    }

    $('#tanggal').on('change', loadSlots);
    $('#filter-form').on('submit', function (e) {
        e.preventDefault();
        loadBookings();
    });

    $('#booking-form').on('submit', function (e) {
        e.preventDefault();
        const $btn = $('#submit-booking').prop('disabled', true).text('Mengirim...');

        $.ajax({
            url: storeUrl,
            method: 'POST',
            dataType: 'json',
            data: $(this).serialize()
        }).done(function (res) {
            const b = res.booking;
            showSuccess(
                res.message +
                ' Kode booking: <strong>' + b.kode + '</strong> · ' +
                b.tanggal + ' pukul ' + b.jam + ' · Total ' + b.total
            );
            $('#booking-form')[0].reset();
            $('#tanggal').val(@json(now()->toDateString()));
            loadSlots();
            loadBookings();
        }).fail(function (xhr) {
            const res = xhr.responseJSON || {};
            let message = res.message || 'Pendaftaran gagal. Coba lagi.';
            if (res.errors) {
                message = Object.values(res.errors)[0][0];
            }
            showAlert(message, 'error');
            if (res.error === 'slot_penuh') {
                loadSlots();
            }
        }).always(function () {
            $btn.prop('disabled', false).text('Kirim pendaftaran');
        });
    });

    loadSlots();
    loadBookings();
})();
</script>
@endsection
