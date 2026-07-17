@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- HEADER & SWITCH VIEW MODE -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark" style="color: var(--text-main) !important;">Daftar Data Warga</h2>
            <p class="text-muted mb-0">Manajemen data kependudukan warga RT 006-010 RW 018.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <!-- View Mode Switch -->
            <div class="btn-group shadow-sm me-2" role="group" style="border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color);">
                <a href="{{ route('warga.index', ['view' => 'family']) }}" class="btn btn-sm {{ $viewMode === 'family' ? 'btn-primary text-white' : 'btn-light text-dark' }} px-3 py-2" style="font-weight: 500; font-size: 0.85rem; background-color: {{ $viewMode === 'family' ? 'var(--primary)' : 'var(--bg-card)' }}; border-color: var(--border-color);">
                    <i class="fas fa-users me-1"></i> Tampilan Keluarga
                </a>
                <a href="{{ route('warga.index', ['view' => 'table']) }}" class="btn btn-sm {{ $viewMode === 'table' ? 'btn-primary text-white' : 'btn-light text-dark' }} px-3 py-2" style="font-weight: 500; font-size: 0.85rem; background-color: {{ $viewMode === 'table' ? 'var(--primary)' : 'var(--bg-card)' }}; border-color: var(--border-color);">
                    <i class="fas fa-table me-1"></i> Tampilan Tabel Warga
                </a>
            </div>
            
            <a href="{{ route('warga.create') }}" class="btn btn-primary shadow-sm px-3 py-2" style="border-radius: 10px; font-weight: 600;"><i class="fas fa-plus me-1"></i>Tambah Warga</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        </div>
    @endif

    <!-- 1. TAMPILAN TABEL WARGA (FLAT DATATABLE - SESUAI FORM TAMBAH WARGA) -->
    @if($viewMode === 'table')
        @if($wargas->isEmpty())
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted" style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
                <i class="fas fa-users fa-3x mb-3 text-light"></i>
                <h5>Belum ada data warga terdaftar.</h5>
                <p>Silakan klik tombol Tambah Warga di atas.</p>
            </div>
        @else
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="color: var(--text-main);">
                        <thead style="background-color: rgba(59,130,246,0.03); color: var(--text-main); border-bottom: 2px solid var(--border-color);">
                            <tr>
                                <th class="ps-4" style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8;">Foto</th>
                                <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8;">Nama Lengkap</th>
                                <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8;">NIK</th>
                                <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8;">No. KK</th>
                                <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8;">Hubungan</th>
                                <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8;">Gender</th>
                                <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8;">Tgl Lahir / Usia</th>
                                <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8;">No. HP</th>
                                <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8;">RT/RW</th>
                                <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8;">Blok</th>
                                <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8;">Alamat Lengkap</th>
                                <th class="pe-4 text-end" style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody style="border-top: none;">
                            @foreach ($wargas as $warga)
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td class="ps-4">
                                        <img src="{{ $warga->profile_photo_url }}" class="rounded-circle border shadow-sm" style="width: 40px; height: 40px; object-fit: cover; border-color: var(--border-color) !important;">
                                    </td>
                                    <td>
                                        <span class="fw-bold" style="color: var(--text-main);">{{ $warga->nama }}</span>
                                    </td>
                                    <td style="font-family: monospace; font-size: 0.8rem; opacity: 0.9;">{{ $warga->nik ?? '-' }}</td>
                                    <td style="font-family: monospace; font-size: 0.8rem; opacity: 0.9;">{{ $warga->no_kk ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $warga->is_kk ? 'bg-primary' : ($warga->status_hubungan === 'Istri' ? 'bg-success' : 'bg-info') }} rounded-pill" style="font-size: 0.65rem; padding: 4px 10px;">
                                            {{ $warga->status_hubungan }}
                                        </span>
                                    </td>
                                    <td style="font-size: 0.85rem;">{{ $warga->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                    <td style="font-size: 0.85rem;">
                                        <small class="d-block">{{ $warga->tanggal_lahir ? \Carbon\Carbon::parse($warga->tanggal_lahir)->translatedFormat('d M Y') : '-' }}</small>
                                        <span class="badge bg-secondary-subtle text-secondary border mt-1" style="font-size: 0.65rem;">{{ $warga->umur_formatted }}</span>
                                    </td>
                                    <td style="font-size: 0.85rem;">{{ $warga->nomor_hp ?? '-' }}</td>
                                    <td style="font-size: 0.85rem;">RT {{ $warga->rt_number }} / RW {{ $warga->rw_number }}</td>
                                    <td><span class="badge bg-secondary-subtle text-dark border px-2 py-1" style="font-size: 0.75rem;">{{ $warga->blok_rumah ?? '-' }}</span></td>
                                    <td>
                                        <span class="d-inline-block text-truncate" style="max-width: 140px; font-size: 0.8rem;" title="{{ $warga->alamat }}">
                                            {{ $warga->alamat ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="{{ route('warga.edit', $warga->id) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 6px;" title="Edit"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('warga.destroy', $warga->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data warga ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;" title="Hapus"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    <!-- 2. TAMPILAN KELUARGA (ACCORDION DEKSTRUKTUR GRIDS) -->
    @else
        <div class="accordion" id="accordionWarga">
            @if($wargas->isEmpty())
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted" style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
                    <i class="fas fa-users fa-3x mb-3 text-light"></i>
                    <h5>Belum ada data warga terdaftar.</h5>
                    <p>Silakan klik tombol Tambah Warga di atas.</p>
                </div>
            @else
                @foreach ($wargas as $kk)
                    <div class="card mb-3 border-0 shadow-sm" style="border-radius: 12px; overflow: hidden; background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
                        <div class="card-header bg-white p-3 d-flex justify-content-between align-items-center"
                             id="heading{{ $kk->id }}"
                             data-bs-toggle="collapse"
                             data-bs-target="#collapse{{ $kk->id }}"
                             style="cursor: pointer; border-bottom: none;">

                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $kk->profile_photo_url }}" class="rounded-circle shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                <div>
                                    <h5 class="fw-bold mb-0 text-dark" style="color: var(--text-main) !important;">{{ $kk->nama }}</h5>
                                    <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>Blok: {{ $kk->blok_rumah }} | RT {{ $kk->rt_number }} / RW {{ $kk->rw_number }}</small>
                                </div>
                            </div>

                            <div class="text-end">
                                <span class="badge bg-purple rounded-pill mb-1 px-3 py-1.5" style="background-color: var(--primary) !important;">{{ $kk->anggotaKeluarga->count() }} Anggota Keluarga</span><br>
                                <form action="{{ route('warga.destroy', $kk->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data KK beserta anggotanya?')">
                                    @csrf @method('DELETE')
                                    <a href="{{ route('warga.edit', $kk->id) }}" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation();" style="border-radius: 8px;"><i class="fas fa-edit"></i> Edit</a>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation();" style="border-radius: 8px;"><i class="fas fa-trash"></i> Hapus</button>
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
                                                                    <span class="d-block" style="font-size: 0.7rem; opacity: 0.8;">Blok Rumah</span>
                                                                    <span class="fw-semibold" style="color: var(--text-main);">{{ $anggota->blok_rumah ?? '-' }}</span>
                                                                </div>
                                                                
                                                                <div class="col-12">
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
        </div>
    @endif

    <!-- PAGINATION -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $wargas->appends(['view' => $viewMode])->links() }}
    </div>
</div>
@endsection
