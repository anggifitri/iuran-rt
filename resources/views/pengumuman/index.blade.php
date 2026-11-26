@extends('layouts.app')

@section('title', 'Pusat Pengumuman — NexaNest')

@section('content')
<!-- ==================== A. HEADER HALAMAN ==================== -->
<div class="nx-page-header mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
            <span class="badge rounded-pill px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
                <i class="fas fa-bullhorn me-1"></i> Komunikasi &amp; Informasi Resmi RW 018
            </span>
            @if($isAdminRw)
                <span class="badge rounded-pill px-2.5 py-1 bg-dark" style="font-size: 0.72rem;">
                    <i class="fas fa-shield-halved me-1"></i> Admin RW Mode
                </span>
            @elseif($isAdminRt)
                <span class="badge rounded-pill px-2.5 py-1" style="background: #1E4B6D; color: #fff; font-size: 0.72rem;">
                    <i class="fas fa-user-shield me-1"></i> Admin RT {{ Auth::user()->rt_number }} Mode
                </span>
            @else
                <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-mint); color: var(--nx-forest); font-size: 0.72rem;">
                    <i class="fas fa-house-chimney-user me-1"></i> Warga RW 018
                </span>
            @endif
        </div>
        <h1 class="nx-page-title mb-1">Pusat Pengumuman Warga</h1>
        <p class="nx-page-subtitle mb-0">
            Kanal resmi penyampaian warta warga, surat edaran, imbauan ketertiban, agenda kegiatan lingkungan, transparansi keuangan, dan peringatan darurat terpadu RW 018.
        </p>
    </div>
    
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <!-- Tombol Tandai Semua Dibaca -->
        <form action="{{ route('pengumuman.mark_all_read') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-secondary fw-bold" style="border-radius: 12px;" title="Tandai Seluruh Pengumuman Telah Dibaca">
                <i class="fas fa-envelope-open-text me-1.5 text-success"></i>
                <span class="d-none d-sm-inline">Tandai Semua Dibaca</span>
            </button>
        </form>

        @if(!$isWarga)
        <a href="{{ route('pengumuman.create') }}" class="btn btn-primary fw-bold" style="border-radius: 12px; background: var(--nx-forest); border-color: var(--nx-forest);">
            <i class="fas fa-circle-plus me-1.5"></i> Buat Pengumuman Baru
        </a>
        @endif
    </div>
</div>

<!-- ==================== ALERTS ==================== -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert" style="background: var(--nx-mint-soft); border-color: var(--nx-sage); color: var(--nx-forest); border-radius: 14px;">
    <i class="fas fa-check-circle me-2 fs-5"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert" style="border-radius: 14px;">
    <i class="fas fa-exclamation-circle me-2 fs-5"></i>
    <div>{{ session('error') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- ==================== B. RINGKASAN PENGUMUMAN (4 STAT CARDS) ==================== -->
<div class="row g-3 mb-4">
    <!-- 1. Total Pengumuman Aktif -->
    <div class="col-lg-3 col-md-6 col-6">
        <x-stat-card 
            title="Pengumuman Aktif" 
            value="{{ $totalAktif }}" 
            sub="Sedang berlaku saat ini" 
            icon="fas fa-newspaper" 
            theme="forest" 
        />
    </div>

    <!-- 2. Warta Darurat & Penting -->
    <div class="col-lg-3 col-md-6 col-6">
        <x-stat-card 
            title="Darurat & Penting" 
            value="{{ $totalPenting }}" 
            sub="Prioritas respon warga" 
            icon="fas fa-triangle-exclamation" 
            theme="warning" 
            valColor="#b45309"
        />
    </div>

    <!-- 3. Total Dipublikasikan -->
    <div class="col-lg-3 col-md-6 col-6">
        <x-stat-card 
            title="Total Terpublikasi" 
            value="{{ $totalDipublikasikan }}" 
            sub="Warta resmi terbit" 
            icon="fas fa-bullhorn" 
            theme="primary" 
            valColor="#0284c7"
        />
    </div>

    <!-- 4. Belum Dibaca -->
    <div class="col-lg-3 col-md-6 col-6">
        <x-stat-card 
            title="{{ $isWarga ? 'Warta Belum Dibaca' : 'Perhatian Belum Dibaca' }}" 
            value="{{ $unreadPengumumanCount }}" 
            sub="{{ $unreadPengumumanCount > 0 ? 'Warta baru tersedia' : 'Semua warta telah dibaca' }}" 
            icon="{{ $unreadPengumumanCount > 0 ? 'fas fa-envelope' : 'fas fa-check-double' }}" 
            theme="{{ $unreadPengumumanCount > 0 ? 'danger' : 'success' }}" 
            valColor="{{ $unreadPengumumanCount > 0 ? '#e11d48' : '#059669' }}"
        />
    </div>
</div>

<!-- ==================== TAB NAVIGATION BAR ==================== -->
<div class="card p-2 border-0 shadow-sm mb-4" style="border-radius: 16px; border: 1px solid var(--nx-border) !important;">
    <ul class="nav nav-pills gap-1 flex-wrap">
        <li class="nav-item">
            <a href="{{ route('pengumuman.index', array_merge(request()->except('tab', 'page'), ['tab' => 'aktif'])) }}"
               class="nav-link py-2 px-3 fw-bold {{ $tab === 'aktif' ? 'active' : 'text-secondary' }}"
               style="{{ $tab === 'aktif' ? 'background: var(--nx-forest); border-radius: 12px;' : 'border-radius: 12px;' }}">
                <i class="fas fa-list-check me-1.5"></i> Pengumuman Aktif
                <span class="badge ms-1.5 {{ $tab === 'aktif' ? 'bg-light text-dark' : 'bg-secondary bg-opacity-10 text-secondary' }}">{{ $totalAktif }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pengumuman.index', array_merge(request()->except('tab', 'page'), ['tab' => 'penting'])) }}"
               class="nav-link py-2 px-3 fw-bold {{ $tab === 'penting' ? 'active' : 'text-secondary' }}"
               style="{{ $tab === 'penting' ? 'background: #d97706; border-radius: 12px;' : 'border-radius: 12px;' }}">
                <i class="fas fa-bell me-1.5"></i> Darurat & Penting
                <span class="badge ms-1.5 {{ $tab === 'penting' ? 'bg-light text-dark' : 'bg-warning bg-opacity-10 text-warning-emphasis' }}">{{ $totalPenting }}</span>
            </a>
        </li>
        @if(!$isWarga)
        <li class="nav-item">
            <a href="{{ route('pengumuman.index', array_merge(request()->except('tab', 'page'), ['tab' => 'draft'])) }}"
               class="nav-link py-2 px-3 fw-bold {{ $tab === 'draft' ? 'active' : 'text-secondary' }}"
               style="{{ $tab === 'draft' ? 'background: #475569; border-radius: 12px;' : 'border-radius: 12px;' }}">
                <i class="fas fa-file-pen me-1.5"></i> Konsep / Draft
                @if($totalDraft > 0)
                <span class="badge ms-1.5 {{ $tab === 'draft' ? 'bg-light text-dark' : 'bg-secondary bg-opacity-10 text-secondary' }}">{{ $totalDraft }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pengumuman.index', array_merge(request()->except('tab', 'page'), ['tab' => 'terjadwal'])) }}"
               class="nav-link py-2 px-3 fw-bold {{ $tab === 'terjadwal' ? 'active' : 'text-secondary' }}"
               style="{{ $tab === 'terjadwal' ? 'background: #2563eb; border-radius: 12px;' : 'border-radius: 12px;' }}">
                <i class="fas fa-clock me-1.5"></i> Terjadwal
                @if($totalTerjadwal > 0)
                <span class="badge ms-1.5 {{ $tab === 'terjadwal' ? 'bg-light text-dark' : 'bg-primary bg-opacity-10 text-primary' }}">{{ $totalTerjadwal }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pengumuman.index', array_merge(request()->except('tab', 'page'), ['tab' => 'arsip'])) }}"
               class="nav-link py-2 px-3 fw-bold {{ $tab === 'arsip' ? 'active' : 'text-secondary' }}"
               style="{{ $tab === 'arsip' ? 'background: #334155; border-radius: 12px;' : 'border-radius: 12px;' }}">
                <i class="fas fa-box-archive me-1.5"></i> Arsip &amp; Kedaluwarsa
                @if($totalArsip > 0)
                <span class="badge ms-1.5 {{ $tab === 'arsip' ? 'bg-light text-dark' : 'bg-secondary bg-opacity-10 text-secondary' }}">{{ $totalArsip }}</span>
                @endif
            </a>
        </li>
        @endif
    </ul>
</div>

<!-- ==================== C. PENGUMUMAN PENTING & PINNED (SECTION C HIGHLIGHTS) ==================== -->
@if($pinnedHighlights->count() > 0 && in_array($tab, ['aktif', 'penting']))
<div class="mb-4">
    <div class="d-flex align-items-center gap-2 mb-2">
        <i class="fas fa-thumbtack text-warning fs-5"></i>
        <h6 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 0.05em; font-size: 0.82rem; color: var(--nx-dark);">
            Pengumuman Prioritas Tersemat (Pinned &amp; Emergency)
        </h6>
    </div>

    <div class="row g-3">
        @foreach($pinnedHighlights as $ph)
            @php
                $isPhRead = $ph->isReadByUser(Auth::id());
                $phPb = $ph->prioritas_badge;
                $phKb = $ph->kategori_badge;
            @endphp
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card p-3 h-100 shadow-sm position-relative d-flex flex-column justify-between"
                     style="border-radius: 16px; border: 1px solid {{ $ph->prioritas === 'darurat' ? '#FEB2B2' : '#FDE68A' }} !important; border-left: 5px solid {{ $ph->prioritas === 'darurat' ? '#dc2626' : ($ph->is_pinned ? 'var(--nx-forest)' : '#d97706') }} !important; background: {{ $ph->prioritas === 'darurat' ? '#FFF5F5' : '#FFFFFF' }};">
                    <div>
                        <div class="d-flex align-items-center justify-between gap-2 mb-2">
                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                @if($ph->is_pinned)
                                <span class="badge rounded-pill px-2 py-1" style="background: var(--nx-mint); color: var(--nx-forest); font-size: 0.68rem; font-weight: 700;">
                                    <i class="fas fa-thumbtack me-0.5"></i> Disematkan
                                </span>
                                @endif
                                <span class="badge rounded-pill px-2 py-1" style="background: {{ $phPb['bg'] }}; color: {{ $phPb['color'] }}; font-size: 0.68rem; font-weight: 700;">
                                    <i class="fas {{ $phPb['icon'] }} me-0.5"></i> {{ $phPb['tag'] }}
                                </span>
                                <span class="badge bg-light text-secondary rounded-pill px-2 py-1" style="font-size: 0.68rem;">
                                    {{ $ph->kategori_label }}
                                </span>
                            </div>
                            @if(!$isPhRead)
                            <span class="badge bg-danger rounded-circle p-1" style="width: 10px; height: 10px;" title="Belum Dibaca"></span>
                            @endif
                        </div>

                        <a href="{{ route('pengumuman.show', $ph->id) }}" class="text-decoration-none d-block fw-bold mb-1.5" style="color: var(--nx-dark); font-size: 0.95rem; line-height: 1.35;">
                            {{ $ph->title }}
                        </a>

                        <p class="text-muted small mb-3" style="font-size: 0.78rem; line-height: 1.45; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ Str::limit(strip_tags($ph->content), 120) }}
                        </p>
                    </div>

                    <div class="pt-2 border-top d-flex align-items-center justify-content-between" style="border-color: var(--nx-border) !important;">
                        <span class="text-muted small" style="font-size: 0.72rem;">
                            <i class="fas fa-users me-1"></i> {{ $ph->target_teks }}
                        </span>
                        <a href="{{ route('pengumuman.show', $ph->id) }}" class="btn btn-sm btn-link p-0 fw-bold text-decoration-none" style="color: var(--nx-forest); font-size: 0.78rem;">
                            Buka Warta <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- ==================== FILTER & SEARCH BAR ==================== -->
<div class="card p-3 border-0 shadow-sm mb-4" style="border-radius: 16px; border: 1px solid var(--nx-border) !important;">
    <form action="{{ route('pengumuman.index') }}" method="GET" class="row g-2 align-items-center">
        <input type="hidden" name="tab" value="{{ $tab }}">

        <!-- Search Keyword -->
        <div class="col-lg-4 col-md-6 col-12">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0" style="border-radius: 12px 0 0 12px; border-color: var(--nx-border);">
                    <i class="fas fa-magnifying-glass text-muted small"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul warta, isi maklumat, nama pengurus..."
                       class="form-control border-start-0 ps-0" style="border-radius: 0 12px 12px 0; border-color: var(--nx-border); font-size: 0.85rem;">
            </div>
        </div>

        <!-- Kategori -->
        <div class="col-lg-3 col-md-6 col-12">
            <select name="kategori" class="form-select" style="border-radius: 12px; border-color: var(--nx-border); font-size: 0.85rem;" onchange="this.form.submit()">
                <option value="semua" {{ request('kategori') == 'semua' || !request('kategori') ? 'selected' : '' }}>Semua Kategori (7 Kategori)</option>
                <option value="umum" {{ request('kategori') == 'umum' ? 'selected' : '' }}>Umum &amp; Lingkungan</option>
                <option value="kegiatan" {{ request('kategori') == 'kegiatan' ? 'selected' : '' }}>Kegiatan &amp; Rapat</option>
                <option value="keuangan" {{ request('kategori') == 'keuangan' ? 'selected' : '' }}>Keuangan &amp; Iuran</option>
                <option value="keamanan" {{ request('kategori') == 'keamanan' ? 'selected' : '' }}>Keamanan &amp; Siskamling</option>
                <option value="kesehatan" {{ request('kategori') == 'kesehatan' ? 'selected' : '' }}>Kesehatan &amp; Posyandu</option>
                <option value="sosial" {{ request('kategori') == 'sosial' ? 'selected' : '' }}>Sosial &amp; Bansos</option>
                <option value="darurat" {{ request('kategori') == 'darurat' ? 'selected' : '' }}>Darurat &amp; Bencana</option>
            </select>
        </div>

        <!-- Prioritas -->
        <div class="col-lg-2 col-md-6 col-12">
            <select name="prioritas" class="form-select" style="border-radius: 12px; border-color: var(--nx-border); font-size: 0.85rem;" onchange="this.form.submit()">
                <option value="semua" {{ request('prioritas') == 'semua' || !request('prioritas') ? 'selected' : '' }}>Semua Prioritas</option>
                <option value="darurat" {{ request('prioritas') == 'darurat' ? 'selected' : '' }}>Darurat (Mendesak)</option>
                <option value="penting" {{ request('prioritas') == 'penting' ? 'selected' : '' }}>Penting (Perlu Tindakan)</option>
                <option value="kegiatan" {{ request('prioritas') == 'kegiatan' ? 'selected' : '' }}>Kegiatan Warga</option>
                <option value="informasi" {{ request('prioritas') == 'informasi' ? 'selected' : '' }}>Informasi Umum</option>
            </select>
        </div>

        <!-- Target RT & Tombol -->
        <div class="col-lg-3 col-md-6 col-12 d-flex align-items-center gap-2">
            @if($isAdminRw)
            <select name="target_rt" class="form-select flex-grow-1" style="border-radius: 12px; border-color: var(--nx-border); font-size: 0.85rem;" onchange="this.form.submit()">
                <option value="semua" {{ request('target_rt') == 'semua' || !request('target_rt') ? 'selected' : '' }}>Semua Target RT</option>
                <option value="all" {{ request('target_rt') == 'all' ? 'selected' : '' }}>Seluruh RW 018</option>
                @foreach($rtList as $rt)
                    <option value="{{ $rt }}" {{ request('target_rt') == $rt ? 'selected' : '' }}>Khusus RT {{ $rt }}</option>
                @endforeach
            </select>
            @endif

            <button type="submit" class="btn btn-primary fw-bold px-3 py-2 shrink-0" style="border-radius: 12px; background: var(--nx-forest); border-color: var(--nx-forest); font-size: 0.85rem;">
                <i class="fas fa-filter me-1"></i> Filter
            </button>

            @if(request()->anyFilled(['search', 'kategori', 'prioritas', 'target_rt']))
            <a href="{{ route('pengumuman.index', ['tab' => $tab]) }}" class="btn btn-outline-secondary px-2.5 py-2 shrink-0" style="border-radius: 12px;" title="Reset Filter">
                <i class="fas fa-rotate-left"></i>
            </a>
            @endif
        </div>
    </form>
</div>

<!-- ==================== D. DAFTAR PENGUMUMAN CARDS GRID ==================== -->
<div class="row g-4">
    @forelse($pengumumans as $p)
        @php
            $pb = $p->prioritas_badge;
            $kb = $p->kategori_badge;
            $sb = $p->status_badge;
            $isRead = $p->isReadByUser(Auth::id());
        @endphp
        <div class="col-lg-4 col-md-6 col-12">
            <div class="card h-100 p-4 border-0 shadow-sm d-flex flex-column justify-between card-hover position-relative"
                 style="border-radius: 18px; border: 1px solid {{ $p->prioritas === 'darurat' ? '#FEB2B2' : 'var(--nx-border)' }} !important; border-left: 5px solid {{ $p->prioritas === 'darurat' ? '#dc2626' : ($p->prioritas === 'penting' ? '#f59e0b' : ($p->prioritas === 'kegiatan' ? 'var(--nx-forest)' : '#0284c7')) }} !important; transition: transform 0.2s, box-shadow 0.2s;">
                
                <div>
                    <!-- Badges Header -->
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                            <!-- Kategori Badge -->
                            <span class="badge rounded-pill px-2.5 py-1"
                                  style="background: {{ $kb['bg'] }}; color: {{ $kb['color'] }}; font-size: 0.72rem; font-weight: 700;">
                                <i class="fas {{ $kb['icon'] }} me-1"></i>
                                <span>{{ $p->kategori_label }}</span>
                            </span>

                            <!-- Prioritas Badge -->
                            <span class="badge rounded-pill px-2.5 py-1"
                                  style="background: {{ $pb['bg'] }}; color: {{ $pb['color'] }}; border: 1px solid {{ $pb['border'] }}; font-size: 0.72rem; font-weight: 700;">
                                <i class="fas {{ $pb['icon'] }} me-1"></i>
                                <span>{{ $p->prioritas_label }}</span>
                            </span>

                            @if($p->is_pinned)
                            <span class="badge rounded-pill px-2 py-1" style="background: var(--nx-mint); color: var(--nx-forest); font-size: 0.72rem; font-weight: 700;">
                                <i class="fas fa-thumbtack"></i>
                            </span>
                            @endif
                        </div>

                        <!-- Read Status Indicator -->
                        <div>
                            @if($isWarga)
                                @if($isRead)
                                <span class="badge rounded-pill px-2.5 py-1" style="background: #E8F5E9; color: #2e7d32; font-size: 0.7rem; font-weight: 700;">
                                    <i class="fas fa-check-double me-1"></i> Dibaca
                                </span>
                                @else
                                <span class="badge rounded-pill px-2.5 py-1" style="background: #FFE4E6; color: #be123c; font-size: 0.7rem; font-weight: 700;">
                                    <i class="fas fa-envelope me-1"></i> Baru
                                </span>
                                @endif
                            @else
                                <span class="badge rounded-pill px-2.5 py-1"
                                      style="background: {{ $sb['bg'] }}; color: {{ $sb['color'] }}; font-size: 0.7rem; font-weight: 700;">
                                    <i class="fas {{ $sb['icon'] }} me-1"></i> {{ $sb['label'] }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Title -->
                    <a href="{{ route('pengumuman.show', $p->id) }}" class="text-decoration-none d-block fw-bold mb-2" style="color: var(--nx-dark); font-size: 1.05rem; line-height: 1.35;">
                        {{ $p->title }}
                    </a>

                    <!-- Metadata: Tanggal & Penerbit -->
                    <div class="d-flex align-items-center flex-wrap gap-2 text-muted small mb-3" style="font-size: 0.76rem;">
                        <span>
                            <i class="far fa-calendar me-1 text-muted"></i>
                            {{ $p->published_at ? $p->published_at->translatedFormat('d M Y') : $p->created_at->translatedFormat('d M Y') }}
                        </span>
                        <span>•</span>
                        <span>
                            <i class="fas fa-user-pen me-1 text-muted"></i>
                            {{ $p->creator ? $p->creator->name : 'Pengurus' }}
                        </span>
                        <span>•</span>
                        <span class="badge bg-light text-secondary rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                            <i class="fas fa-location-dot me-1 text-muted"></i> {{ $p->target_teks }}
                        </span>
                    </div>

                    <!-- Ringkasan Isi -->
                    <p class="text-muted small mb-3" style="font-size: 0.82rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ Str::limit(strip_tags($p->content), 140) }}
                    </p>

                    <!-- Feature Tags (Lampiran, RSVP, Kalender) -->
                    <div class="d-flex align-items-center gap-1.5 flex-wrap mb-3">
                        @if($p->lampiran_file)
                        <span class="badge bg-light text-secondary rounded-pill px-2 py-1 border" style="font-size: 0.68rem; font-weight: 600;">
                            <i class="fas fa-paperclip me-1 text-muted"></i> Lampiran Berkas
                        </span>
                        @endif

                        @if($p->requires_rsvp)
                        <span class="badge rounded-pill px-2 py-1 border" style="background: #EEF2FF; color: #4338CA; border-color: #C7D2FE !important; font-size: 0.68rem; font-weight: 700;">
                            <i class="fas fa-clipboard-question me-1"></i> Perlu RSVP
                        </span>
                        @endif

                        @if($p->is_kegiatan)
                        <span class="badge rounded-pill px-2 py-1 border" style="background: #ECFDF5; color: #047857; border-color: #A7F3D0 !important; font-size: 0.68rem; font-weight: 700;">
                            <i class="far fa-calendar-check me-1"></i> Agenda: {{ $p->kegiatan_tanggal ? $p->kegiatan_tanggal->format('d/m') : '' }}
                        </span>
                        @endif
                    </div>
                </div>

                <!-- Footer Card -->
                <div class="pt-3 border-top" style="border-color: var(--nx-border) !important;">
                    @if(!$isWarga)
                    <!-- Officer Read Tracker Progress Bar -->
                    @php
                        $stats = $p->stats_keterbacaan;
                    @endphp
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between small mb-1" style="font-size: 0.72rem;">
                            <span class="text-muted">Keterbacaan Warga:</span>
                            <span class="fw-bold" style="color: var(--nx-dark);">{{ $stats['percentage'] }}% ({{ $stats['total_read'] }}/{{ $stats['total_target'] }} KK)</span>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 999px; background: #e2e8f0;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $stats['percentage'] }}%; background: var(--nx-forest); border-radius: 999px;" aria-valuenow="{{ $stats['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    @endif

                    <div class="d-flex align-items-center justify-content-between">
                        <a href="{{ route('pengumuman.show', $p->id) }}" class="btn btn-sm btn-link p-0 fw-bold text-decoration-none" style="color: var(--nx-forest); font-size: 0.84rem;">
                            Baca Selengkapnya <i class="fas fa-arrow-right ms-1"></i>
                        </a>

                        @if(!$isWarga && ($isAdminRw || Auth::id() === $p->created_by))
                        <div class="d-flex align-items-center gap-1">
                            <a href="{{ route('pengumuman.edit', $p->id) }}" class="btn btn-sm btn-light p-1.5 text-warning" style="border-radius: 8px;" title="Edit Pengumuman">
                                <i class="fas fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('pengumuman.archive', $p->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-light p-1.5 text-secondary" style="border-radius: 8px;" title="{{ $p->is_archived ? 'Pulihkan dari Arsip' : 'Arsipkan' }}">
                                    <i class="fas {{ $p->is_archived ? 'fa-box-open' : 'fa-box-archive' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('pengumuman.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus warta pengumuman ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light p-1.5 text-danger" style="border-radius: 8px;" title="Hapus Pengumuman">
                                    <i class="far fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card p-5 text-center border-0 shadow-sm" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 64px; height: 64px; background: var(--nx-mint); color: var(--nx-forest); font-size: 1.5rem;">
                    <i class="fas fa-newspaper"></i>
                </div>
                <h5 class="fw-bold mb-1" style="color: var(--nx-dark);">Belum Ada Pengumuman Pada Kategori Ini</h5>
                <p class="text-muted small mb-3">Tidak ditemukan warta resmi yang sesuai dengan filter pencarian atau tab saat ini.</p>
                @if(request()->anyFilled(['search', 'kategori', 'prioritas', 'target_rt']))
                <div>
                    <a href="{{ route('pengumuman.index', ['tab' => $tab]) }}" class="btn btn-outline-secondary btn-sm fw-bold px-3 py-2" style="border-radius: 12px;">
                        <i class="fas fa-rotate-left me-1.5"></i> Reset Filter Pencarian
                    </a>
                </div>
                @endif
            </div>
        </div>
    @endforelse
</div>

<!-- ==================== PAGINATION ==================== -->
@if($pengumumans->hasPages())
<div class="mt-4 d-flex justify-content-center">
    {{ $pengumumans->links() }}
</div>
@endif

@endsection
