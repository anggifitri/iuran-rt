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
                            <img src="{{ $kk->profile_photo_url }}" class="rounded-circle shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <h5 class="fw-bold mb-0" style="color: var(--text-main);">{{ $kk->nama }}</h5>
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
                        <div class="card-body bg-light border-top p-4">
                            <div class="row g-3">
                                @php
                                    // Menggabungkan Kepala Keluarga itu sendiri dan anggotanya untuk ditampilkan lengkap
                                    $allMembers = collect([$kk])->concat($kk->anggotaKeluarga);
                                @endphp
                                @foreach ($allMembers as $anggota)
                                    <div class="col-md-6 col-12">
                                        <div class="card border shadow-sm h-100" style="border-radius: 14px; background-color: var(--bg-card); border-color: var(--border-color) !important;">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-start gap-3">
                                                    <!-- Foto Profil & Badge Status -->
                                                    <div class="text-center" style="width: 80px; flex-shrink: 0;">
                                                        <img src="{{ $anggota->profile_photo_url }}" class="rounded-circle shadow-sm border border-2" style="width: 70px; height: 70px; object-fit: cover; border-color: var(--border-color) !important;">
                                                        <span class="badge mt-2 px-2 py-1 rounded-pill {{ $anggota->is_kk ? 'bg-primary' : ($anggota->status_hubungan === 'Istri' ? 'bg-success' : 'bg-info') }}" style="font-size: 0.65rem; display: inline-block;">
                                                            {{ $anggota->status_hubungan }}
                                                        </span>
                                                    </div>
                                                    
                                                    <!-- Detail Data Warga -->
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <h6 class="fw-bold mb-1" style="color: var(--text-main) !important;">{{ $anggota->nama }}</h6>
                                                            <div>
                                                                <a href="{{ route('warga.edit', $anggota->id) }}" class="btn btn-sm btn-link text-primary p-0 me-2" title="Edit"><i class="fas fa-edit"></i></a>
                                                                <form action="{{ route('warga.destroy', $anggota->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus anggota keluarga ini?')">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0 border-0 bg-transparent" title="Hapus"><i class="fas fa-trash"></i></button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                        <hr class="my-2" style="border-color: var(--border-color); opacity: 0.5;">
                                                        
                                                        <div class="row g-2" style="font-size: 0.8rem; color: var(--text-muted);">
                                                            <div class="col-6">
                                                                <span class="d-block" style="font-size: 0.7rem; opacity: 0.8;">NIK</span>
                                                                <span class="fw-semibold" style="color: var(--text-main);">{{ $anggota->nik ?? '-' }}</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <span class="d-block" style="font-size: 0.7rem; opacity: 0.8;">No. KK</span>
                                                                <span class="fw-semibold" style="color: var(--text-main);">{{ $anggota->no_kk ?? '-' }}</span>
                                                            </div>
                                                            
                                                            <div class="col-6">
                                                                <span class="d-block" style="font-size: 0.7rem; opacity: 0.8;">No. HP</span>
                                                                <span class="fw-semibold" style="color: var(--text-main);">{{ $anggota->nomor_hp ?? '-' }}</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <span class="d-block" style="font-size: 0.7rem; opacity: 0.8;">Jenis Kelamin</span>
                                                                <span class="fw-semibold" style="color: var(--text-main);">{{ $anggota->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                                                            </div>
                                                            
                                                            <div class="col-6">
                                                                <span class="d-block" style="font-size: 0.7rem; opacity: 0.8;">Tanggal Lahir</span>
                                                                <span class="fw-semibold" style="color: var(--text-main);">{{ $anggota->tanggal_lahir ? \Carbon\Carbon::parse($anggota->tanggal_lahir)->translatedFormat('d F Y') : '-' }} ({{ $anggota->umur_formatted }})</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <span class="d-block" style="font-size: 0.7rem; opacity: 0.8;">RT / RW</span>
                                                                <span class="fw-semibold" style="color: var(--text-main);">RT {{ $anggota->rt_number }} / RW {{ $anggota->rw_number }}</span>
                                                            </div>
                                                            
                                                            <div class="col-12 mt-1">
                                                                <span class="d-block" style="font-size: 0.7rem; opacity: 0.8;">Alamat</span>
                                                                <span class="fw-semibold d-block" style="color: var(--text-main); line-height: 1.2;">{{ $anggota->alamat ?? '-' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
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
