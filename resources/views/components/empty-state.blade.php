@props([
    'icon' => 'fas fa-inbox',
    'title' => 'Belum Ada Data Tersedia',
    'message' => 'Data untuk kriteria atau halaman ini belum tersedia saat ini.',
    'actionUrl' => null,
    'actionLabel' => null,
    'actionIcon' => 'fas fa-plus',
])

<div {{ $attributes->merge(['class' => 'nx-empty-state text-center py-5 px-3']) }}>
    <div class="nx-empty-icon mb-3">
        <i class="{{ $icon }}"></i>
    </div>
    <h5 class="fw-bold mb-1 text-dark" style="font-size: 1.05rem;">{{ $title }}</h5>
    <p class="text-muted small mb-3 mx-auto" style="max-width: 460px; line-height: 1.5;">
        {{ $message }}
    </p>
    @if($actionUrl && $actionLabel)
        <div>
            <a href="{{ $actionUrl }}" class="btn btn-primary btn-sm px-3.5 py-2 fw-bold" style="border-radius: 10px;">
                @if($actionIcon)<i class="{{ $actionIcon }} me-1.5"></i>@endif{{ $actionLabel }}
            </a>
        </div>
    @endif
    {{ $slot }}
</div>
