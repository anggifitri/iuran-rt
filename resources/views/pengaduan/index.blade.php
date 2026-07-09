@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold">Pengaduan Warga</h4>
            <p class="text-muted">Laporkan masalah lingkungan atau administrasi ke RT.</p>
        </div>
        <a href="{{ route('pengaduan.create') }}" class="btn btn-primary">Buat Pengaduan</a>
    </div>

    @if($pengaduans->isEmpty())
        <div class="card p-4 text-center text-muted">
            Belum ada pengaduan.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-borderless align-middle">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengaduans as $pengaduan)
                        <tr>
                            <td>{{ $pengaduan->title }}</td>
                            <td>{{ ucfirst($pengaduan->status) }}</td>
                            <td>{{ $pengaduan->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
