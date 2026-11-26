@props([
    'status' => 'aktif',
    'label' => null,
    'icon' => null,
])

@php
    $raw = strtolower(trim((string)$status));
    
    // Status color mapping per NexaNest standard specifications:
    // Hijau: Disetujui, Selesai, Aktif, Terverifikasi, Lunas
    // Kuning: Menunggu, Pending, Perlu Ditinjau, Akan Datang, Belum Bayar
    // Merah: Ditolak, Bermasalah, Terlambat, Dibatalkan, Rusak, Perlu Perawatan
    // Biru: Diproses, Digunakan, Berlangsung, Sedang Digunakan, Informasi, Perbaikan, Perlu Perbaikan
    // Abu-abu: Draft, Nonaktif, Belum Tersedia, Arsip
    
    $colorClass = match(true) {
        in_array($raw, ['disetujui', 'selesai', 'aktif', 'terverifikasi', 'lunas', 'sukses', 'normal', 'tersedia', 'baik', 'diterima', 'aman']) => 'nx-badge-success',
        in_array($raw, ['menunggu', 'pending', 'pending_rt', 'pending_rw', 'perlu_ditinjau', 'menunggu_review', 'akan_datang', 'menunggu_konfirmasi', 'menunggu_pengembalian', 'peringatan', 'tunggakan', 'belum_lunas']) => 'nx-badge-warning',
        in_array($raw, ['ditolak', 'bermasalah', 'terlambat', 'dibatalkan', 'rusak', 'batal', 'lewat_batas', 'bahaya', 'darurat', 'tidak_tersedia']) => 'nx-badge-danger',
        in_array($raw, ['diproses', 'digunakan', 'sedang_digunakan', 'berlangsung', 'informasi', 'perbaikan', 'perlu_perbaikan', 'investigasi', 'tindak_lanjut']) => 'nx-badge-info',
        default => 'nx-badge-secondary',
    };

    $defaultIcons = [
        'nx-badge-success' => 'fas fa-check-circle',
        'nx-badge-warning' => 'fas fa-hourglass-half',
        'nx-badge-danger' => 'fas fa-times-circle',
        'nx-badge-info' => 'fas fa-spinner fa-spin-pulse',
        'nx-badge-secondary' => 'fas fa-circle-dot',
    ];

    $resolvedIcon = $icon ?? ($defaultIcons[$colorClass] ?? 'fas fa-circle');

    // Auto-formatted label
    $resolvedLabel = $label ?? ucwords(str_replace(['_', '-'], ' ', $status));
@endphp

<span {{ $attributes->merge(['class' => 'nx-status-badge ' . $colorClass]) }}>
    @if($resolvedIcon)
        <i class="{{ $resolvedIcon }} me-1" style="font-size: 0.8em;"></i>
    @endif
    <span>{{ $resolvedLabel }}</span>
</span>
