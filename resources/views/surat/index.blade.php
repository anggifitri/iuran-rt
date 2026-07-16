@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold">NexaNest · Penerbitan Surat Online</h4>
            <p class="text-muted">Ajukan, verifikasi, dan unduh surat resmi RW 018 dalam satu alur digital.</p>
        </div>
        <a href="{{ route('surat.create') }}" class="btn btn-primary">Ajukan Surat</a>
    </div>

    @if($surats->isEmpty())
        <div class="card p-4 text-center text-muted">Belum ada permohonan surat.</div>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Jenis Surat</th>
                        <th>RT</th>
                        <th>Status</th>
                        <th>Dokumen</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($surats as $surat)
                        <tr>
                            <td>{{ $surat->jenis_surat }}</td>
                            <td>{{ $surat->rt_number ?? '-' }}</td>
                            <td><span class="badge bg-info-subtle text-info-emphasis">{{ str_replace('_', ' ', $surat->status) }}</span></td>
                            <td>
                                @if($surat->pdf_path)
                                    <a href="{{ asset($surat->pdf_path) }}" class="btn btn-sm btn-outline-primary">Unduh PDF</a>
                                @else
                                    <span class="text-muted">Menunggu approval</span>
                                @endif
                            </td>
                            <td>{{ $surat->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $surats->links() }}
    @endif
</div>
@endsection
