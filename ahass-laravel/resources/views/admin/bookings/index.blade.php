@extends('layouts.admin', ['title' => 'Pendaftaran', 'active' => 'bookings'])

@section('content')
<h1>Pendaftaran servis</h1>
<form id="admin-filter" class="toolbar">
    <input id="admin-q" name="q" placeholder="Cari kode, nama, plat" value="{{ $q }}">
    <select id="admin-status" name="status">
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
        <tbody id="admin-booking-rows">
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

@section('scripts')
<script>
$('#admin-filter').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
        url: @json(route('admin.bookings.index')),
        method: 'GET',
        dataType: 'json',
        data: {
            q: $('#admin-q').val(),
            status: $('#admin-status').val()
        }
    }).done(function (res) {
        const $body = $('#admin-booking-rows').empty();
        if (!res.data.length) {
            $body.append('<tr><td colspan="6" class="muted">Tidak ada data.</td></tr>');
            return;
        }
        res.data.forEach(function (row) {
            $body.append(
                '<tr>' +
                '<td><a href="' + row.url + '">' + row.kode + '</a></td>' +
                '<td>' + row.nama_pelanggan + '<br><small class="muted">' + row.telepon + ' · ' + row.no_polisi + '</small></td>' +
                '<td>' + row.jadwal + '</td>' +
                '<td>' + row.paket + '</td>' +
                '<td>' + row.total + '</td>' +
                '<td><span class="badge ' + row.status + '">' + row.status_label + '</span></td>' +
                '</tr>'
            );
        });
    });
});
</script>
@endsection
