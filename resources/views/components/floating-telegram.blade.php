@auth
@php
    $telegramUser = Auth::user();
    // Muncul hanya jika user adalah Warga, Admin RT, Admin RW, atau Super Admin yang sedang simulasi role
    $isSuperAdminRaw = $telegramUser->isSuperAdmin() && !$telegramUser->isRoleSwitched();
    $showTelegramBtn = !$isSuperAdminRaw && ($telegramUser->isWarga() || $telegramUser->isAdminRt() || $telegramUser->isAdminRw() || $telegramUser->isBendahara());
@endphp

@if($showTelegramBtn)
<style>
    .nx-floating-telegram {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 1040;
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2AABEE 0%, #229ED9 100%);
        color: #FFFFFF !important;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 16px rgba(34, 158, 217, 0.4), 0 2px 6px rgba(0, 0, 0, 0.1);
        text-decoration: none;
        transition: transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.22s ease, background 0.2s ease;
        border: 2px solid rgba(255, 255, 255, 0.25);
    }

    .nx-floating-telegram:hover {
        transform: translateY(-4px) scale(1.05);
        box-shadow: 0 8px 24px rgba(34, 158, 217, 0.55), 0 4px 10px rgba(0, 0, 0, 0.15);
        color: #FFFFFF !important;
    }

    .nx-floating-telegram:active {
        transform: scale(0.94);
    }

    .nx-floating-telegram svg,
    .nx-floating-telegram i {
        font-size: 24px;
        width: 24px;
        height: 24px;
        transition: transform 0.2s ease;
    }

    .nx-floating-telegram:hover svg,
    .nx-floating-telegram:hover i {
        transform: scale(1.08) rotate(-4deg);
    }

    /* Tooltip halus di desktop */
    .nx-floating-telegram .nx-telegram-tooltip {
        position: absolute;
        right: calc(100% + 12px);
        top: 50%;
        transform: translateY(-50%) translateX(6px);
        background: rgba(15, 23, 42, 0.92);
        color: #FFFFFF;
        font-size: 0.76rem;
        font-weight: 600;
        letter-spacing: -0.01em;
        padding: 6px 12px;
        border-radius: 8px;
        white-space: nowrap;
        pointer-events: none;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s ease, transform 0.2s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    .nx-floating-telegram:hover .nx-telegram-tooltip {
        opacity: 1;
        visibility: visible;
        transform: translateY(-50%) translateX(0);
    }

    /* Arrow tooltip */
    .nx-floating-telegram .nx-telegram-tooltip::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 100%;
        transform: translateY(-50%);
        border-width: 5px;
        border-style: solid;
        border-color: transparent transparent transparent rgba(15, 23, 42, 0.92);
    }

    /* Mobile Positioning: tepat di atas Bottom Navigation Bar */
    @media (max-width: 768px) {
        .nx-floating-telegram {
            bottom: 86px !important;
            right: 16px !important;
            width: 48px;
            height: 48px;
            box-shadow: 0 4px 14px rgba(34, 158, 217, 0.45);
        }

        .nx-floating-telegram svg,
        .nx-floating-telegram i {
            font-size: 21px;
            width: 21px;
            height: 21px;
        }

        /* Di mobile tooltip tidak perlu melayang menghalangi layar */
        .nx-floating-telegram .nx-telegram-tooltip {
            display: none !important;
        }
    }
</style>

<a href="https://t.me/noIrbiella" 
   target="_blank" 
   rel="noopener noreferrer" 
   class="nx-floating-telegram" 
   aria-label="Pengaduan via Telegram"
   title="Pengaduan via Telegram">
    <!-- SVG Resmi Telegram Clean & Sharp -->
    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.75-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/>
    </svg>
    <span class="nx-telegram-tooltip">Pengaduan via Telegram</span>
</a>
@endif
@endauth
