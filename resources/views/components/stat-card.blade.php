@props([
    'title' => '',
    'value' => '0',
    'sub' => null,
    'icon' => 'fas fa-chart-line',
    'theme' => 'forest',
    'valColor' => null,
])

@php
    $themeMap = [
        'forest'  => ['card' => 'nx-stat-card--forest',  'icon' => 'nx-stat-icon--forest'],
        'success' => ['card' => 'nx-stat-card--success', 'icon' => 'nx-stat-icon--success'],
        'warning' => ['card' => 'nx-stat-card--warning', 'icon' => 'nx-stat-icon--warning'],
        'danger'  => ['card' => 'nx-stat-card--danger',  'icon' => 'nx-stat-icon--danger'],
        'primary' => ['card' => 'nx-stat-card--primary', 'icon' => 'nx-stat-icon--primary'],
        'indigo'  => ['card' => 'nx-stat-card--indigo',  'icon' => 'nx-stat-icon--indigo'],
    ];

    $classes = $themeMap[$theme] ?? $themeMap['forest'];
@endphp

<div {{ $attributes->merge(['class' => 'nx-stat-card ' . $classes['card']]) }}>
    <div class="nx-stat-content">
        <span class="nx-stat-label">{!! $title !!}</span>
        <h3 class="nx-stat-value" @if($valColor) style="color: {{ $valColor }} !important;" @endif>
            {{ $value }}
        </h3>
        @if($sub)
            <small class="nx-stat-sub">{!! $sub !!}</small>
        @endif
    </div>
    @if($icon)
        <div class="nx-stat-icon {{ $classes['icon'] }}">
            <i class="{{ $icon }}"></i>
        </div>
    @endif
</div>
