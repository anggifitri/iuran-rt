@props([
    'title' => null,
    'subtitle' => null,
    'total' => null,
    'badge' => null,
    'headerAction' => null,
    'pagination' => null,
    'tableId' => null,
    'hover' => true,
    'striped' => false,
])

<div {{ $attributes->merge(['class' => 'card nx-table-card overflow-hidden border-0 shadow-xs mb-4']) }}>
    @if($title || $headerAction || $total !== null || $badge)
        <div class="card-header bg-white p-3.5 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if($title)
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 0.96rem;">
                            {{ $title }}
                        </h5>
                    @endif
                    @if($badge)
                        <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-size: 0.72rem; font-weight: 700;">
                            {{ $badge }}
                        </span>
                    @elseif($total !== null)
                        <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-size: 0.72rem; font-weight: 700;">
                            {{ number_format($total) }} Data Tercatat
                        </span>
                    @endif
                </div>
                @if($subtitle)
                    <small class="text-muted d-block mt-0.5" style="font-size: 0.75rem;">{{ $subtitle }}</small>
                @endif
            </div>

            @if($headerAction)
                <div class="d-flex align-items-center gap-2">
                    {{ $headerAction }}
                </div>
            @endif
        </div>
    @endif

    <div class="table-responsive nx-table-responsive">
        <table @if($tableId) id="{{ $tableId }}" @endif class="table {{ $hover ? 'table-hover' : '' }} {{ $striped ? 'table-striped' : '' }} align-middle nx-table mb-0">
            {{ $slot }}
        </table>
    </div>

    @if($pagination)
        <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            {{ $pagination }}
        </div>
    @endif
</div>
