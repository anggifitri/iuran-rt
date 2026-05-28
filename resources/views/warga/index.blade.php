@extends('layouts.app')

@section('content')
<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show bg-success text-white border-0 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-white fw-bold mb-0">Daftar Data Warga</h2>
        <a href="{{ route('warga.create') }}" class="btn btn-primary px-3 fw-bold">
            <i class="fas fa-plus me-2"></i>Tambah Warga
        </a>
    </div>

    <div class="card shadow border-0 bg-dark text-white">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr class="border-bottom border-secondary text-secondary">
                            <th class="py-3 px-4" style="width: 80px;">No</th>
                            <th class="py-3">Nama</th>
                            <th class="py-3">Blok Rumah</th>
                            <th class="py-3">No. KK</th>
                            <th class="py-3">NIK</th>
                            <th class="py-3">Gender</th>
                            <th class="py-3">No. HP</th>
                            <th class="py-3 text-center" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wargas as $index => $warga)
                            <tr class="border-bottom border-transparent">
                                <td class="py-3 px-4 text-secondary">{{ $wargas->firstItem() + $index }}</td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $warga->profile_photo_url }}" alt="Foto {{ $warga->nama }}" width="42" height="42" class="rounded-circle" style="object-fit: cover;">
                                        <span class="fw-semibold text-white">{{ $warga->nama }}</span>
                                    </div>
                                </td>
                                <td class="py-3 text-light">{{ $warga->blok_rumah }}</td>
                                <td class="py-3 text-light">{{ $warga->no_kk ?? '-' }}</td>
                                <td class="py-3 text-light">{{ $warga->nik ?? '-' }}</td>
                                <td class="py-3 text-light">{{ $warga->gender ?? '-' }}</td>
                                <td class="py-3 text-light">{{ $warga->nomor_hp ?? '-' }}</td>
                                <td class="py-3 text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('warga.edit', $warga->id) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('warga.destroy', $warga->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-secondary">
                                    <i class="fas fa-users-slash fa-2x mb-3 d-block"></i>
                                    Belum ada data warga.
                                </td>
                            </tr>
                        @endempty
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary px-3 fw-bold">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>

        <div>
            {{ $wargas->links() }}
        </div>
    </div>
</div>
@endsection
