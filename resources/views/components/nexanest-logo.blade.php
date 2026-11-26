@props([
    'link' => true,
    'href' => null,
    'size' => 'md', // sm, md, lg
    'class' => '',
    'hideTextOnCollapse' => true,
])

@php
    $targetUrl = $href ?? (auth()->check() ? route('dashboard') : url('/'));
    
    // Scale parameters
    $iconSize = match($size) {
        'sm' => 32,
        'lg' => 48,
        default => 40,
    };
    
    $titleSize = match($size) {
        'sm' => '1.08rem',
        'lg' => '1.45rem',
        default => '1.24rem',
    };
    
    $subtitleSize = match($size) {
        'sm' => '0.68rem',
        'lg' => '0.80rem',
        default => '0.73rem',
    };
@endphp

@if($link)
<a href="{{ $targetUrl }}" class="nexanest-brand-logo d-inline-flex align-items-center gap-2 text-decoration-none {{ $class }}" aria-label="NexaNest Brand Logo">
@else
<div class="nexanest-brand-logo d-inline-flex align-items-center gap-2 {{ $class }}">
@endif
    <!-- Sharp Vector Icon: Rumah Atap Pelana + Cerobong Kanan + Pintu Lengkung + 2 Daun Hijau Segar -->
    <svg width="{{ $iconSize }}" height="{{ $iconSize }}" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg" class="nexanest-icon-svg" style="flex-shrink: 0; display: inline-block;">
        <!-- Cerobong Asap di Sisi Kanan Atap -->
        <path class="nx-logo-house" d="M29 11.5V7C29 6.44772 29.4477 6 30 6H33C33.5523 6 34 6.44772 34 7V16" stroke="#1B4332" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
        
        <!-- Outline Rumah Beratap Pelana dengan Ujung Membulat (Forest Green #1B4332) -->
        <path class="nx-logo-house" d="M7 21L22 8L37 21V36C37 37.1046 36.1046 38 35 38H11C9.89543 38 9 37.1046 9 36V22.5" stroke="#1B4332" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"/>
        
        <!-- Pintu Lengkung Melengkung di Tengah Bawah -->
        <path class="nx-logo-house" d="M19 38V29C19 27.3431 20.3431 26 22 26C23.6569 26 25 27.3431 25 29V38" stroke="#1B4332" stroke-width="2.6" stroke-linecap="round"/>
        
        <!-- Daun 1 (Utama - Hijau Tua Segar #40916C) Tumbuh Miring ke Kiri Atas -->
        <path class="nx-logo-leaf-1" d="M10 38C10 38 4 33 3 23C8.2 23 13 28 10 38Z" fill="#40916C" stroke="#1B4332" stroke-width="1.8" stroke-linejoin="round"/>
        <path class="nx-logo-leaf-vein" d="M10 38C7.5 32 6 27 3 23" stroke="#DCEBDD" stroke-width="1.2" stroke-linecap="round"/>
        
        <!-- Daun 2 (Sekunder - Hijau Mint Segar #52B788) Tumbuh Miring Keluar -->
        <path class="nx-logo-leaf-2" d="M9 37C9 37 3 35.2 1 30.5C5.5 29 9 32.5 9 37Z" fill="#52B788" stroke="#1B4332" stroke-width="1.5" stroke-linejoin="round"/>
        <path class="nx-logo-leaf-vein" d="M9 37C6.5 34 4 32.5 1 30.5" stroke="#DCEBDD" stroke-width="1" stroke-linecap="round"/>
    </svg>

    <!-- Tipografi Brand di Samping Kanan Ikon -->
    <div class="{{ $hideTextOnCollapse ? 'nx-brand-text' : '' }}" style="text-align: left;">
        <div class="nx-brand-title fw-bold" style="font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; font-size: {{ $titleSize }}; font-weight: 800; color: #1B4332; line-height: 1.08; letter-spacing: -0.025em; margin: 0;">NexaNest</div>
        <div class="nx-brand-sub fw-semibold" style="font-size: {{ $subtitleSize }}; font-weight: 600; color: #64748B; line-height: 1.2; letter-spacing: 0.01em; margin-top: 1px;">Smart &amp; Green Community</div>
    </div>
@if($link)
</a>
@else
</div>
@endif
