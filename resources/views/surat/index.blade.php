@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold">Penerbitan Surat</h4>
            <p class="text-muted">Ajukan permintaan surat administratif RT di sini.</p>
        </div>
        <a href="{{ route('surat.create') }}" class="btn btn-primary">Buat Permintaan Surat</a>
    </div>

    @if($surats->isEmpty())
        <div class="card p-4 text-center text-muted">
            Belum ada permintaan surat.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-borderless align-middle">
                <thead>
                    <tr>
                        <th>Jenis Surat</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($surats as $surat)
                        <tr>
                            <td>{{ $surat->jenis }}</td>
                            <td>{{ ucfirst($surat->status) }}</td>
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
