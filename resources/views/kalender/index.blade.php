@extends('layouts.app')

@section('title', 'Kalender Kegiatan & Agenda RW 018 — NexaNest')

@section('content')
<!-- A. HEADER HALAMAN & NAVIGASI BULAN/TAHUN -->
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-calendar-days me-1.5"></i> Pusat Jadwal Lingkungan Terpadu
        </span>
        <h1 class="nx-page-title">Kalender Kegiatan &amp; Agenda RW 018</h1>
        <p class="nx-page-subtitle">
            Pusat informasi jadwal terpadu seluruh kegiatan lingkungan: Musyawarah RW/RT, Siskamling Ronda Malam, Reservasi Fasum, dan Posyandu.
        </p>
    </div>

    <!-- Navigasi Cepat Periode Kalender -->
    <div class="d-flex flex-wrap align-items-center gap-2">
        <!-- Tombol Hari Ini -->
        <a href="{{ route('kalender.index', ['bulan' => date('m'), 'tahun' => date('Y'), 'kategori' => $filterKategori, 'rt_number' => $filterRt]) }}"
           class="btn btn-sm btn-outline-primary px-3 py-2 fw-bold" style="border-radius: 10px; height: 38px; display: inline-flex; align-items: center;">
            <i class="fas fa-calendar-day me-1.5"></i>Hari Ini
        </a>

        <!-- Tombol Prev Month -->
        <a href="{{ route('kalender.index', ['bulan' => $prevMonth, 'tahun' => $prevYear, 'kategori' => $filterKategori, 'rt_number' => $filterRt, 'search' => $search]) }}"
           class="btn btn-sm btn-outline-secondary px-2.5 py-2" style="border-radius: 10px; height: 38px; display: inline-flex; align-items: center;" title="Bulan Sebelumnya">
            <i class="fas fa-chevron-left"></i>
        </a>

        <!-- Form Dropdown Bulan & Tahun -->
        <form method="GET" action="{{ route('kalender.index') }}" class="d-flex align-items-center gap-1.5" id="formPeriodeKalender">
            <input type="hidden" name="kategori" value="{{ $filterKategori }}">
            <input type="hidden" name="rt_number" value="{{ $filterRt }}">
            @if($search)
                <input type="hidden" name="search" value="{{ $search }}">
            @endif

            <select name="bulan" class="form-select form-select-sm fw-bold" style="border-radius: 10px; height: 38px; min-width: 130px;" onchange="this.form.submit()">
                @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $k => $n)
                    <option value="{{ $k }}" {{ $bulan == $k ? 'selected' : '' }}>{{ $n }}</option>
                @endforeach
            </select>

            <select name="tahun" class="form-select form-select-sm fw-bold" style="border-radius: 10px; height: 38px; width: 95px;" onchange="this.form.submit()">
                @foreach(range(date('Y') + 1, date('Y') - 4) as $y)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>

        <!-- Tombol Next Month -->
        <a href="{{ route('kalender.index', ['bulan' => $nextMonth, 'tahun' => $nextYear, 'kategori' => $filterKategori, 'rt_number' => $filterRt, 'search' => $search]) }}"
           class="btn btn-sm btn-outline-secondary px-2.5 py-2" style="border-radius: 10px; height: 38px; display: inline-flex; align-items: center;" title="Bulan Berikutnya">
            <i class="fas fa-chevron-right"></i>
        </a>
    </div>
</div>

<!-- B. RINGKASAN KEGIATAN (3 DATABASE-DRIVEN STAT CARDS STANDAR NEXANEST) -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <x-stat-card 
            title="Total Agenda Bulan Ini" 
            value="{{ $totalBulanIni }}" 
            sub="{{ \Carbon\Carbon::createFromDate($tahun, (int)$bulan, 1)->translatedFormat('F Y') }}" 
            icon="fas fa-calendar-check" 
            theme="forest" 
        />
    </div>
    <div class="col-md-4">
        <x-stat-card 
            title="Agenda Hari Ini" 
            value="{{ $totalHariIni }}" 
            sub="{{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}" 
            icon="fas fa-bell" 
            theme="primary" 
            valColor="#1F567A" 
        />
    </div>
    <div class="col-md-4">
        <x-stat-card 
            title="Agenda Mendatang" 
            value="{{ $totalMendatang }}" 
            sub="Kegiatan terjadwal berikutnya" 
            icon="fas fa-clock" 
            theme="warning" 
            valColor="#D97706" 
        />
    </div>
</div>

<!-- C. FILTER KEGIATAN TERPADU (KATEGORI, WILAYAH, PENCARIAN) -->
<div class="card nx-filter-card mb-4 shadow-xs" style="border-radius: 14px; border: 1px solid var(--nx-border);">
    <div class="p-3">
        <form method="GET" action="{{ route('kalender.index') }}" class="row g-2 align-items-center">
            <input type="hidden" name="bulan" value="{{ $bulan }}">
            <input type="hidden" name="tahun" value="{{ $tahun }}">

            <!-- Kategori Chips / Pill Filter -->
            <div class="col-xl-6 col-lg-12">
                <div class="d-flex flex-wrap gap-1.5 align-items-center">
                    <a href="{{ route('kalender.index', ['bulan' => $bulan, 'tahun' => $tahun, 'rt_number' => $filterRt, 'search' => $search]) }}"
                       class="btn btn-sm {{ empty($filterKategori) ? 'btn-primary' : 'btn-outline-secondary' }}" style="border-radius: 20px; font-size: 0.76rem; font-weight: 700;">
                        Semua Kategori
                    </a>
                    <a href="{{ route('kalender.index', ['bulan' => $bulan, 'tahun' => $tahun, 'kategori' => 'rapat', 'rt_number' => $filterRt, 'search' => $search]) }}"
                       class="btn btn-sm {{ $filterKategori == 'rapat' ? 'btn-success' : 'btn-outline-secondary' }}" style="border-radius: 20px; font-size: 0.76rem; font-weight: 700;">
                        <i class="fas fa-handshake me-1" style="color: #285943;"></i>Rapat &amp; Musyawarah
                    </a>
                    <a href="{{ route('kalender.index', ['bulan' => $bulan, 'tahun' => $tahun, 'kategori' => 'ronda', 'rt_number' => $filterRt, 'search' => $search]) }}"
                       class="btn btn-sm {{ $filterKategori == 'ronda' ? 'btn-primary' : 'btn-outline-secondary' }}" style="border-radius: 20px; font-size: 0.76rem; font-weight: 700;">
                        <i class="fas fa-shield-halved me-1" style="color: #1E4B6D;"></i>Ronda Malam
                    </a>
                    <a href="{{ route('kalender.index', ['bulan' => $bulan, 'tahun' => $tahun, 'kategori' => 'fasilitas', 'rt_number' => $filterRt, 'search' => $search]) }}"
                       class="btn btn-sm {{ $filterKategori == 'fasilitas' ? 'btn-warning text-dark' : 'btn-outline-secondary' }}" style="border-radius: 20px; font-size: 0.76rem; font-weight: 700;">
                        <i class="fas fa-building me-1" style="color: #854D0E;"></i>Peminjaman Fasilitas
                    </a>
                    <a href="{{ route('kalender.index', ['bulan' => $bulan, 'tahun' => $tahun, 'kategori' => 'posyandu', 'rt_number' => $filterRt, 'search' => $search]) }}"
                       class="btn btn-sm {{ $filterKategori == 'posyandu' ? 'btn-danger' : 'btn-outline-secondary' }}" style="border-radius: 20px; font-size: 0.76rem; font-weight: 700;">
                        <i class="fas fa-heart me-1" style="color: #E11D48;"></i>Posyandu
                    </a>
                </div>
            </div>

            <!-- Filter Wilayah RT -->
            <div class="col-xl-3 col-md-6 col-12">
                <select name="rt_number" class="form-select form-select-sm" style="border-radius: 10px; height: 38px;" onchange="this.form.submit()">
                    <option value="">-- Semua Wilayah RT / RW --</option>
                    @foreach($rtList as $rt)
                        <option value="{{ $rt }}" {{ $filterRt == $rt ? 'selected' : '' }}>RT {{ $rt }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Search Keyword Input -->
            <div class="col-xl-3 col-md-6 col-12">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 10px 0 0 10px;">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Cari nama kegiatan, lokasi..." value="{{ $search }}" style="height: 38px;">
                    <button type="submit" class="btn btn-primary btn-sm px-2.5" style="border-radius: 0 10px 10px 0; height: 38px;">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                    @if(!empty($filterKategori) || !empty($filterRt) || !empty($search))
                        <a href="{{ route('kalender.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-outline-secondary btn-sm ms-1" style="border-radius: 10px; height: 38px; display: flex; align-items: center;" title="Reset Filter">
                            <i class="fas fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- LEGENDA WARNA KATEGORI RESMI NEXANEST -->
<div class="d-flex flex-wrap align-items-center justify-content-between p-2.5 mb-3 rounded-3" style="background: #ffffff; border: 1px solid var(--nx-border); font-size: 0.78rem;">
    <div class="d-flex flex-wrap align-items-center gap-3">
        <span class="fw-bold text-dark"><i class="fas fa-tags me-1 text-secondary"></i>Legenda Kategori:</span>
        <div class="d-flex align-items-center gap-1.5">
            <span class="d-inline-block rounded-circle" style="width: 10px; height: 10px; background: #285943;"></span>
            <span class="text-dark">Rapat &amp; Musyawarah</span>
        </div>
        <div class="d-flex align-items-center gap-1.5">
            <span class="d-inline-block rounded-circle" style="width: 10px; height: 10px; background: #1E4B6D;"></span>
            <span class="text-dark">Ronda Malam</span>
        </div>
        <div class="d-flex align-items-center gap-1.5">
            <span class="d-inline-block rounded-circle" style="width: 10px; height: 10px; background: #D97706;"></span>
            <span class="text-dark">Peminjaman Fasilitas</span>
        </div>
        <div class="d-flex align-items-center gap-1.5">
            <span class="d-inline-block rounded-circle" style="width: 10px; height: 10px; background: #E11D48;"></span>
            <span class="text-dark">Posyandu</span>
        </div>
    </div>
    <div class="text-muted small mt-1 mt-md-0">
        <i class="fas fa-mouse-pointer me-1"></i>Klik tanggal atau kegiatan untuk melihat detail lengkap.
    </div>
</div>

<div class="row g-4">
    <!-- D. KALENDER UTAMA: GRID 7 KOLOM PROPORSIONAL -->
    <div class="col-xl-8 col-lg-7">
        <div class="card p-3.5 shadow-xs h-100" style="border-radius: 14px; border: 1px solid var(--nx-border); background: #ffffff;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.1rem;">
                    <i class="fas fa-calendar-alt text-success me-2"></i>{{ \Carbon\Carbon::createFromDate($tahun, (int)$bulan, 1)->translatedFormat('F Y') }}
                </h5>
                <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700; font-size: 0.75rem;">
                    {{ $totalBulanIni }} Kegiatan Aktif
                </span>
            </div>

            <!-- TABEL KALENDER 7 KOLOM (SENIN - MINGGU) -->
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle mb-0 nx-calendar-table" style="table-layout: fixed; border-color: var(--nx-border) !important;">
                    <thead style="background: #F7F9F7;">
                        <tr class="small font-monospace" style="font-size: 0.8rem; font-weight: 700;">
                            <th class="py-2.5 text-dark" style="width: 14.28%;">Sen</th>
                            <th class="py-2.5 text-dark" style="width: 14.28%;">Sel</th>
                            <th class="py-2.5 text-dark" style="width: 14.28%;">Rab</th>
                            <th class="py-2.5 text-dark" style="width: 14.28%;">Kam</th>
                            <th class="py-2.5 text-dark" style="width: 14.28%;">Jum</th>
                            <th class="py-2.5 text-dark" style="width: 14.28%;">Sab</th>
                            <th class="py-2.5 text-danger" style="width: 14.28%;">Min</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($calendarWeeks as $week)
                            <tr>
                                @foreach($week as $day)
                                    @php
                                        $isToday = $day['isToday'];
                                        $isCurrentMonth = $day['isCurrentMonth'];
                                        $dayEvents = $day['events'];
                                        $eventCount = $day['eventCount'];
                                    @endphp
                                    <td class="nx-cal-cell {{ !$isCurrentMonth ? 'nx-cal-dimmed' : '' }} {{ $isToday ? 'nx-cal-today' : '' }}"
                                        data-date="{{ $day['date'] }}"
                                        data-formatted-date="{{ $day['carbon']->translatedFormat('l, d F Y') }}"
                                        style="height: 105px; vertical-align: top; padding: 6px; position: relative; background: {{ $isToday ? 'rgba(40, 89, 67, 0.05)' : (!$isCurrentMonth ? '#FAFBF9' : '#ffffff') }}; {{ $isToday ? 'border: 2px solid var(--nx-forest) !important;' : '' }}; cursor: pointer;"
                                        onclick="openDateSummary('{{ $day['date'] }}', '{{ $day['carbon']->translatedFormat('l, d F Y') }}')">
                                        
                                        <!-- Header Tanggal -->
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="nx-cal-day-num {{ $day['isSunday'] ? 'text-danger' : ($isCurrentMonth ? 'text-dark' : 'text-muted') }} {{ $isToday ? 'badge rounded-circle bg-success text-white' : 'fw-bold' }}"
                                                  style="font-size: 0.8rem; {{ $isToday ? 'width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; background: var(--nx-forest) !important;' : '' }}">
                                                {{ $day['dayNumber'] }}
                                            </span>
                                            @if($isToday)
                                                <span class="badge rounded-pill px-1.5 py-0.5" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-size: 0.6rem; font-weight: 700;">Hari Ini</span>
                                            @elseif($eventCount > 0)
                                                <span class="badge rounded-circle p-1" style="background: var(--nx-forest); width: 6px; height: 6px;" title="{{ $eventCount }} Kegiatan"></span>
                                            @endif
                                        </div>

                                        <!-- Container Event Chips -->
                                        @if($eventCount > 0)
                                            <div class="d-flex flex-column gap-1 text-start">
                                                @foreach($dayEvents->take(2) as $ev)
                                                    <div class="nx-event-chip text-truncate px-1.5 py-0.5 rounded"
                                                         style="background: {{ $ev['category_bg'] }}; color: {{ $ev['category_color'] }}; border-left: 3px solid {{ $ev['category_color'] }}; font-size: 0.68rem; font-weight: 700; line-height: 1.3;"
                                                         onclick="event.stopPropagation(); showEventDetail({{ json_encode($ev) }})"
                                                         title="{{ $ev['title'] }} ({{ $ev['time'] }})">
                                                        <span class="text-truncate d-block">{{ $ev['title'] }}</span>
                                                    </div>
                                                @endforeach

                                                @if($eventCount > 2)
                                                    <div class="text-start">
                                                        <span class="badge rounded-pill px-1.5 py-0.5" style="background: #E5EBE6; color: var(--nx-dark); font-size: 0.62rem; font-weight: 700;">
                                                            +{{ $eventCount - 2 }} agenda lagi
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- E. DAFTAR AGENDA KEGIATAN (PANEL LIST TERSTRUKTUR & LEGA) -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow-xs h-100" style="border-radius: 14px; border: 1px solid var(--nx-border); background: #ffffff;">
            <div class="p-3.5 border-bottom d-flex justify-content-between align-items-center" style="border-color: var(--nx-border) !important;">
                <div>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">
                        <i class="fas fa-list-check text-primary me-2"></i>Daftar Agenda Kegiatan
                    </h5>
                    <small class="text-muted">Urutan jadwal terdekat &amp; mendatang.</small>
                </div>
                <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700; font-size: 0.72rem;">
                    {{ count($sortedEvents) }} Agenda
                </span>
            </div>

            <!-- List Container: Lega, Spacing Teratur, Tanpa Cramped Scrollbar -->
            <div class="p-3" style="max-height: 650px; overflow-y: auto;">
                @forelse($sortedEvents as $ev)
                    <div class="card p-3 mb-3 shadow-xs border transition-card" style="border-radius: 12px; border-color: var(--nx-border) !important; background: {{ $ev['is_cancelled'] ? '#FEF2F2' : '#ffffff' }}; border-left: 4px solid {{ $ev['category_color'] }} !important;">
                        <!-- Top Meta: Kategori & Tanggal -->
                        <div class="d-flex justify-content-between align-items-center mb-1.5">
                            <span class="badge rounded-pill px-2 py-0.5" style="background: {{ $ev['category_bg'] }}; color: {{ $ev['category_color'] }}; font-size: 0.68rem; font-weight: 700;">
                                {{ $ev['category_label'] }}
                            </span>
                            <span class="small fw-bold text-dark" style="font-size: 0.75rem;">
                                <i class="fas fa-calendar-day me-1 text-success"></i>{{ \Carbon\Carbon::parse($ev['date'])->translatedFormat('d M Y') }}
                            </span>
                        </div>

                        <!-- Judul Kegiatan -->
                        <h6 class="fw-bold text-dark mb-1.5" style="font-size: 0.9rem; line-height: 1.4;">
                            <a href="javascript:void(0)" onclick="showEventDetail({{ json_encode($ev) }})" class="text-decoration-none text-dark hover-forest">
                                {{ $ev['title'] }}
                            </a>
                        </h6>

                        <!-- Jam, Lokasi, Wilayah -->
                        <div class="small text-muted mb-2" style="font-size: 0.76rem; line-height: 1.6;">
                            <div class="d-flex align-items-center mb-0.5">
                                <i class="fas fa-clock me-1.5 text-primary" style="width: 14px;"></i>
                                <span class="fw-semibold text-dark">{{ $ev['time'] }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-0.5">
                                <i class="fas fa-location-dot me-1.5 text-danger" style="width: 14px;"></i>
                                <span class="text-truncate">{{ $ev['location'] }} ({{ $ev['rt_number'] }})</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-tie me-1.5 text-secondary" style="width: 14px;"></i>
                                <span class="text-truncate">{{ $ev['organizer'] }}</span>
                            </div>
                        </div>

                        <!-- Footer Item: Status & Aksi -->
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="border-color: var(--nx-border) !important;">
                            <div>
                                @if($ev['is_cancelled'])
                                    <span class="badge rounded-pill px-2 py-0.5" style="background: var(--nx-danger-soft); color: var(--nx-danger); font-size: 0.68rem; font-weight: 700;">
                                        <i class="fas fa-ban me-1"></i>Dibatalkan
                                    </span>
                                @elseif($ev['status'] == 'selesai')
                                    <span class="badge rounded-pill px-2 py-0.5" style="background: var(--nx-mint); color: var(--nx-forest); font-size: 0.68rem; font-weight: 700;">
                                        <i class="fas fa-check-circle me-1"></i>Selesai
                                    </span>
                                @elseif($ev['status'] == 'berlangsung')
                                    <span class="badge rounded-pill px-2 py-0.5" style="background: var(--nx-powder); color: #1e4b6d; font-size: 0.68rem; font-weight: 700;">
                                        <i class="fas fa-satellite-dish me-1"></i>Berlangsung
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-2 py-0.5" style="background: var(--nx-warning-soft); color: var(--nx-warning); font-size: 0.68rem; font-weight: 700;">
                                        <i class="fas fa-clock me-1"></i>Terjadwal
                                    </span>
                                @endif
                            </div>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-0.5" style="border-radius: 6px; font-size: 0.72rem; font-weight: 600;" onclick="showEventDetail({{ json_encode($ev) }})">
                                    <i class="fas fa-eye me-1"></i>Detail
                                </button>
                                <a href="{{ $ev['link'] }}" class="btn btn-sm btn-outline-primary px-2 py-0.5" style="border-radius: 6px; font-size: 0.72rem; font-weight: 600;">
                                    Buka &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width: 54px; height: 54px; background: var(--nx-mint-soft); color: var(--nx-forest);">
                            <i class="fas fa-calendar-xmark fa-lg"></i>
                        </span>
                        <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem;">Tidak Ada Agenda</h6>
                        <p class="small text-muted mb-0" style="max-width: 260px; margin: 0 auto; font-size: 0.78rem;">
                            Tidak ditemukan kegiatan pada periode atau filter yang dipilih.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 1: DETAIL LENGKAP KEGIATAN INTERAKTIF -->
<!-- ========================================== -->
<div class="modal fade" id="modalDetailAgenda" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.14);">
            <div class="modal-header px-4 pt-4 pb-2 border-0">
                <div>
                    <span id="modalEventCategoryBadge" class="badge rounded-pill mb-1.5 px-2.5 py-1" style="font-size: 0.72rem; font-weight: 700;">
                        Kategori
                    </span>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="modalEventTitle" style="font-size: 1.15rem; line-height: 1.4;">
                        Judul Kegiatan
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 py-3">
                <!-- Status Banner -->
                <div class="d-flex justify-content-between align-items-center p-2.5 rounded-3 mb-3" id="modalEventStatusBanner" style="background: #F7F9F7; border: 1px solid var(--nx-border);">
                    <span class="text-muted small fw-semibold">Status Pelaksanaan:</span>
                    <span id="modalEventStatusBadge" class="badge rounded-pill px-2.5 py-1 font-weight-bold">Status</span>
                </div>

                <!-- Informasi Waktu & Lokasi -->
                <div class="row g-2 mb-3">
                    <div class="col-12">
                        <div class="p-2.5 rounded-3 bg-light border small text-dark">
                            <span class="text-muted d-block" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600;">Waktu Pelaksanaan</span>
                            <div class="fw-bold mt-0.5" id="modalEventTime">Tanggal &amp; Waktu</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-2.5 rounded-3 bg-light border small text-dark">
                            <span class="text-muted d-block" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600;">Lokasi &amp; Wilayah</span>
                            <div class="fw-bold mt-0.5" id="modalEventLocation">Lokasi</div>
                        </div>
                    </div>
                </div>

                <!-- Penyelenggara & Sasaran -->
                <ul class="list-unstyled mb-3 small" style="line-height: 1.8;">
                    <li class="pb-1 border-bottom">
                        <span class="text-muted">Penyelenggara:</span>
                        <strong class="text-dark float-end" id="modalEventOrganizer">-</strong>
                    </li>
                    <li class="py-1 border-bottom">
                        <span class="text-muted">Sasaran / Undangan:</span>
                        <strong class="text-dark float-end" id="modalEventAudience">-</strong>
                    </li>
                    <li class="pt-1">
                        <span class="text-muted">Sumber Data Terpadu:</span>
                        <span class="badge rounded-pill bg-light text-dark float-end" id="modalEventSource">-</span>
                    </li>
                </ul>

                <!-- Deskripsi Kegiatan -->
                <div class="mb-0">
                    <span class="text-muted d-block small mb-1" style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Keterangan &amp; Deskripsi</span>
                    <div class="p-3 rounded-3 border text-dark" id="modalEventDescription" style="background: #F7F9F7; font-size: 0.85rem; line-height: 1.6; white-space: pre-line;">
                        Deskripsi kegiatan
                    </div>
                </div>
            </div>

            <div class="modal-footer px-4 pb-4 pt-2 border-0 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 10px;">Tutup</button>
                <a href="#" id="modalEventDirectLink" class="btn btn-primary px-3.5" style="border-radius: 10px; font-weight: 700;">
                    <i class="fas fa-arrow-up-right-from-square me-1.5"></i>Buka Halaman Terkait
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 2: RINGKASAN AGENDA HARIAN (KLIK TANGGAL) -->
<!-- ========================================== -->
<div class="modal fade" id="modalAgendaHarian" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.14);">
            <div class="modal-header px-4 pt-4 pb-2 border-0">
                <div>
                    <span class="badge rounded-pill mb-1.5 px-2.5 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-size: 0.72rem; font-weight: 700;">
                        <i class="fas fa-calendar-day me-1"></i>Agenda Tanggal Terpilih
                    </span>
                    <h5 class="modal-title fw-bold text-dark" id="modalHarianDateTitle" style="font-size: 1.15rem;">
                        Senin, 01 Januari 2026
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 py-3">
                <div id="modalHarianEventsContainer" class="d-flex flex-column gap-2.5">
                    <!-- Dynamic Event Items Rendered by JS -->
                </div>
            </div>

            <div class="modal-footer px-4 pb-4 pt-2 border-0">
                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 10px;">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Styling Tambahan Kalender Responsif */
.nx-calendar-table {
    border-collapse: collapse;
}
.nx-cal-cell:hover {
    background: rgba(40, 89, 67, 0.04) !important;
    transition: background 0.15s ease-in-out;
}
.nx-cal-dimmed {
    opacity: 0.38;
}
.nx-cal-dimmed .nx-event-chip {
    opacity: 0.7;
}
.nx-event-chip:hover {
    filter: brightness(0.95);
    transform: translateY(-1px);
    transition: all 0.1s ease;
}
.transition-card:hover {
    border-color: var(--nx-forest) !important;
    transform: translateY(-2px);
    transition: all 0.2s ease-in-out;
}
</style>

<script>
// Data Events dari PHP ke JavaScript untuk modal interaktif
const allEventsGrouped = @json($eventsByDate);

function showEventDetail(ev) {
    document.getElementById('modalEventTitle').textContent = ev.title;
    
    // Category badge styling
    const catBadge = document.getElementById('modalEventCategoryBadge');
    catBadge.textContent = ev.category_label;
    catBadge.style.background = ev.category_bg;
    catBadge.style.color = ev.category_color;
    catBadge.style.border = `1px solid ${ev.category_border}`;

    // Status badge
    const statusBadge = document.getElementById('modalEventStatusBadge');
    statusBadge.textContent = ev.status_label || ev.status;
    if (ev.is_cancelled) {
        statusBadge.className = 'badge rounded-pill px-2.5 py-1 bg-danger text-white';
    } else if (ev.status === 'selesai') {
        statusBadge.className = 'badge rounded-pill px-2.5 py-1 bg-success text-white';
    } else if (ev.status === 'berlangsung') {
        statusBadge.className = 'badge rounded-pill px-2.5 py-1 bg-primary text-white';
    } else {
        statusBadge.className = 'badge rounded-pill px-2.5 py-1 bg-warning text-dark';
    }

    document.getElementById('modalEventTime').innerHTML = `<i class="fas fa-calendar-day text-success me-1.5"></i>${ev.date} · <i class="fas fa-clock text-primary ms-1 me-1.5"></i>${ev.time}`;
    document.getElementById('modalEventLocation').innerHTML = `<i class="fas fa-location-dot text-danger me-1.5"></i>${ev.location} (${ev.rt_number})`;
    document.getElementById('modalEventOrganizer').textContent = ev.organizer || '-';
    document.getElementById('modalEventAudience').textContent = ev.target_audience || '-';
    document.getElementById('modalEventSource').textContent = ev.source_name || '-';
    document.getElementById('modalEventDescription').textContent = ev.description || 'Tidak ada deskripsi tambahan.';

    const directLink = document.getElementById('modalEventDirectLink');
    if (ev.link) {
        directLink.href = ev.link;
        directLink.classList.remove('d-none');
    } else {
        directLink.classList.add('d-none');
    }

    const modal = new bootstrap.Modal(document.getElementById('modalDetailAgenda'));
    modal.show();
}

function openDateSummary(dateStr, formattedDate) {
    const container = document.getElementById('modalHarianEventsContainer');
    document.getElementById('modalHarianDateTitle').textContent = formattedDate;

    const events = allEventsGrouped[dateStr] || [];

    if (events.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4 text-muted">
                <i class="fas fa-calendar-xmark fa-2x mb-2 text-muted" style="opacity: 0.35;"></i>
                <p class="mb-0 small">Tidak ada agenda atau kegiatan yang dijadwalkan pada tanggal ini.</p>
            </div>
        `;
    } else {
        let html = '';
        events.forEach(ev => {
            html += `
                <div class="card p-3 shadow-xs border" style="border-radius: 12px; border-left: 4px solid ${ev.category_color} !important; background: ${ev.is_cancelled ? '#FEF2F2' : '#ffffff'};">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="badge rounded-pill px-2 py-0.5" style="background: ${ev.category_bg}; color: ${ev.category_color}; font-size: 0.7rem; font-weight: 700;">
                            ${ev.category_label}
                        </span>
                        <span class="badge rounded-pill px-2 py-0.5" style="background: #E5EBE6; color: var(--nx-dark); font-size: 0.7rem;">
                            ${ev.status_label || ev.status}
                        </span>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">${ev.title}</h6>
                    <div class="small text-muted mb-2" style="font-size: 0.78rem;">
                        <div><i class="fas fa-clock text-primary me-1.5"></i>${ev.time}</div>
                        <div><i class="fas fa-location-dot text-danger me-1.5"></i>${ev.location} (${ev.rt_number})</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <small class="text-muted" style="font-size: 0.72rem;">Penyelenggara: ${ev.organizer}</small>
                        <a href="${ev.link}" class="btn btn-sm btn-outline-primary px-2.5 py-1" style="border-radius: 6px; font-size: 0.75rem; font-weight: 600;">
                            Buka Detail &rarr;
                        </a>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    const modal = new bootstrap.Modal(document.getElementById('modalAgendaHarian'));
    modal.show();
}
</script>
@endsection
