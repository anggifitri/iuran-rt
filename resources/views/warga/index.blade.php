@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Data Warga (Kepala Keluarga)</h2>
            <p class="text-muted">Klik pada nama Kepala Keluarga untuk melihat anggota keluarganya.</p>
        </div>
        <a href="{{ route('warga.create') }}" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-2"></i>Tambah Warga</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="accordion" id="accordionWarga">
        @if($wargas->isEmpty())
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted">
                <i class="fas fa-users fa-3x mb-3 text-light"></i>
                <h5>Belum ada data warga terdaftar.</h5>
                <p>Silakan klik tombol Tambah Warga di atas.</p>
            </div>
        @else
            @foreach ($wargas as $kk)
                <div class="card mb-3 border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header bg-white p-3 d-flex justify-content-between align-items-center"
                         id="heading{{ $kk->id }}"
                         data-bs-toggle="collapse"
                         data-bs-target="#collapse{{ $kk->id }}"
                         style="cursor: pointer; border-bottom: none;">

                        <div class="d-flex align-items-center gap-3">
                            @if($kk->profile_photo)
                                <img src="{{ asset('storage/' . $kk->profile_photo) }}" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 50px; height: 50px;">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">{{ $kk->nama }}</h5>
                                <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>Blok: {{ $kk->blok_rumah }} | RT {{ $kk->rt_number }} / RW {{ $kk->rw_number }}</small>
                            </div>
                        </div>

                        <div class="text-end">
                            <span class="badge bg-purple rounded-pill mb-1">{{ $kk->anggotaKeluarga->count() }} Anggota Keluarga</span><br>
                            <form action="{{ route('warga.destroy', $kk->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data KK beserta anggotanya?')">
                                @csrf @method('DELETE')
                                <a href="{{ route('warga.edit', $kk->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i> Edit</a>
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> Hapus</button>
                            </form>
                        </div>
                    </div>

                    <div id="collapse{{ $kk->id }}" class="collapse" aria-labelledby="heading{{ $kk->id }}" data-bs-parent="#accordionWarga">
                        <div class="card-body bg-light border-top p-0">
                            <div class="p-3 bg-white border-bottom">
                                <small class="text-secondary fw-semibold">ALAMAT KK:</small>
                                <p class="mb-0 small">{{ $kk->alamat }}</p>
                            </div>
                            <table class="table table-hover table-borderless mb-0">
                                <thead class="table-light">
                                    <tr class="text-muted text-uppercase" style="font-size: 0.75rem;">
                                        <th class="ps-4">Nama Anggota</th>
                                        <th>Gender</th>
                                        <th>Umur</th>
                                        <th>NIK</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($kk->anggotaKeluarga as $anggota)
                                        <tr>
                                            <td class="ps-4 fw-semibold text-secondary">{{ $anggota->nama }}</td>
                                            <td>{{ $anggota->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                            <td>{{ $anggota->umur }} Tahun</td>
                                            <td>{{ $anggota->nik ?? '-' }}</td>
                                            <td class="text-end pe-4">
                                                <form action="{{ route('warga.destroy', $anggota->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus anggota ini?')">
                                                    @csrf @method('DELETE')
                                                    <a href="{{ route('warga.edit', $anggota->id) }}" class="btn btn-sm btn-link text-primary p-0 me-2"><i class="fas fa-edit"></i></a>
                                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0 border-0 bg-transparent"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-3">Belum ada anggota keluarga yang terdaftar.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        <div class="mt-4">
            {{ $wargas->links() }}
        </div>
    </div>
</div>
@endsection
