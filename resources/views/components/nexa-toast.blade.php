@php
    $currentUser = auth()->user();
    $userGenderTheme = $currentUser ? $currentUser->getToastTheme() : 'neutral';
@endphp

<!-- NEXANEST MODERN FLOATING TOAST SYSTEM -->
<div id="nexa-toast-container" class="nexa-toast-container" aria-live="polite" aria-atomic="true" data-default-theme="{{ $userGenderTheme }}"></div>

<style>
/* Container Floating Toast */
.nexa-toast-container {
    position: fixed;
    top: 24px;
    right: 24px;
    z-index: 999999;
    display: flex;
    flex-direction: column;
    gap: 12px;
    pointer-events: none;
    max-width: 420px;
    width: calc(100% - 48px);
}

@media (max-width: 640px) {
    .nexa-toast-container {
        top: 16px;
        left: 16px;
        right: 16px;
        width: calc(100% - 32px);
        max-width: none;
    }
}

/* Base Toast Item */
.nexa-toast-item {
    position: relative;
    pointer-events: auto;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 16px 18px 18px 16px;
    border-radius: 18px;
    border: 1px solid var(--nexa-t-border);
    background: var(--nexa-t-bg);
    box-shadow: 0 12px 30px -6px rgba(15, 23, 42, 0.08), 0 4px 12px -2px rgba(15, 23, 42, 0.04);
    backdrop-filter: blur(8px);
    overflow: hidden;
    animation: nexaToastSlideIn 0.38s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.nexa-toast-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 36px -6px rgba(15, 23, 42, 0.12), 0 6px 16px -2px rgba(15, 23, 42, 0.06);
}

.nexa-toast-item.nexa-toast-closing {
    animation: nexaToastSlideOut 0.32s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Icon Badge */
.nexa-toast-icon-wrap {
    flex-shrink: 0;
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: var(--nexa-t-badge-bg);
    color: var(--nexa-t-badge-color);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.05rem;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.6);
}

/* Body Content */
.nexa-toast-content {
    flex-grow: 1;
    min-width: 0;
}

.nexa-toast-title {
    font-size: 0.88rem;
    font-weight: 700;
    line-height: 1.35;
    margin-bottom: 2px;
    color: var(--nexa-t-title);
    letter-spacing: -0.01em;
}

.nexa-toast-message {
    font-size: 0.82rem;
    font-weight: 500;
    line-height: 1.45;
    color: var(--nexa-t-msg);
    word-break: break-word;
}

/* Close Button */
.nexa-toast-close {
    flex-shrink: 0;
    background: transparent;
    border: none;
    color: var(--nexa-t-msg);
    opacity: 0.55;
    font-size: 1rem;
    padding: 0;
    margin-left: 4px;
    cursor: pointer;
    line-height: 1;
    transition: opacity 0.15s ease, transform 0.15s ease;
}

.nexa-toast-close:hover {
    opacity: 0.95;
    transform: scale(1.15);
}

/* Progress Bar */
.nexa-toast-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    width: 100%;
    background: var(--nexa-t-progress);
    transform-origin: left center;
    animation: nexaToastProgressLinear var(--toast-duration, 4500ms) linear forwards;
}

.nexa-toast-item:hover .nexa-toast-progress {
    animation-play-state: paused !important;
}

/* ========================================================================= */
/* THEMES: FEMALE (PASTEL PINK), MALE (PASTEL BLUE), NEUTRAL (PASTEL GREEN) */
/* ========================================================================= */

/* 1. FEMALE THEME (PASTEL PINK) */
.nexa-toast-theme-female {
    --nexa-t-bg: #FFF5F7;
    --nexa-t-border: #FBCFE8;
    --nexa-t-badge-bg: #FCE7F3;
    --nexa-t-badge-color: #DB2777;
    --nexa-t-title: #831843;
    --nexa-t-msg: #9D174D;
    --nexa-t-progress: #EC4899;
}

/* 2. MALE THEME (PASTEL BLUE) */
.nexa-toast-theme-male {
    --nexa-t-bg: #F0F9FF;
    --nexa-t-border: #BAE6FD;
    --nexa-t-badge-bg: #E0F2FE;
    --nexa-t-badge-color: #0284C7;
    --nexa-t-title: #0C4A6E;
    --nexa-t-msg: #0369A1;
    --nexa-t-progress: #0284C7;
}

/* 3. NEUTRAL / GUEST / FALLBACK THEME (PASTEL GREEN NEXANEST) */
.nexa-toast-theme-neutral {
    --nexa-t-bg: #ECFDF5;
    --nexa-t-border: #A7F3D0;
    --nexa-t-badge-bg: #D1FAE5;
    --nexa-t-badge-color: #059669;
    --nexa-t-title: #064E3B;
    --nexa-t-msg: #047857;
    --nexa-t-progress: #10B981;
}

/* 4. WARNING THEME (PASTEL AMBER) */
.nexa-toast-theme-warning {
    --nexa-t-bg: #FFFBEB !important;
    --nexa-t-border: #FDE68A !important;
    --nexa-t-badge-bg: #FEF3C7 !important;
    --nexa-t-badge-color: #D97706 !important;
    --nexa-t-title: #78350F !important;
    --nexa-t-msg: #92400E !important;
    --nexa-t-progress: #F59E0B !important;
}

/* 5. ERROR THEME (PASTEL CORAL/ROSE) */
.nexa-toast-theme-error {
    --nexa-t-bg: #FFF1F2 !important;
    --nexa-t-border: #FECDD3 !important;
    --nexa-t-badge-bg: #FFE4E6 !important;
    --nexa-t-badge-color: #E11D48 !important;
    --nexa-t-title: #881337 !important;
    --nexa-t-msg: #9F1239 !important;
    --nexa-t-progress: #E11D48 !important;
}

/* Dark Mode Support for Modern Toast */
html.dark .nexa-toast-theme-female {
    --nexa-t-bg: #1E162B !important;
    --nexa-t-border: rgba(244, 114, 182, 0.4) !important;
    --nexa-t-badge-bg: rgba(236, 72, 153, 0.2) !important;
    --nexa-t-badge-color: #F472B6 !important;
    --nexa-t-title: #FDF2F8 !important;
    --nexa-t-msg: #F9A8D4 !important;
    --nexa-t-progress: #EC4899 !important;
}

html.dark .nexa-toast-theme-male {
    --nexa-t-bg: #0F172A !important;
    --nexa-t-border: rgba(56, 189, 248, 0.4) !important;
    --nexa-t-badge-bg: rgba(56, 189, 248, 0.2) !important;
    --nexa-t-badge-color: #38BDF8 !important;
    --nexa-t-title: #F0F9FF !important;
    --nexa-t-msg: #BAE6FD !important;
    --nexa-t-progress: #38BDF8 !important;
}

html.dark .nexa-toast-theme-neutral {
    --nexa-t-bg: #0F231D !important;
    --nexa-t-border: rgba(52, 211, 153, 0.4) !important;
    --nexa-t-badge-bg: rgba(16, 185, 129, 0.2) !important;
    --nexa-t-badge-color: #34D399 !important;
    --nexa-t-title: #ECFDF5 !important;
    --nexa-t-msg: #A7F3D0 !important;
    --nexa-t-progress: #10B981 !important;
}

html.dark .nexa-toast-theme-warning {
    --nexa-t-bg: #261B0B !important;
    --nexa-t-border: rgba(245, 158, 11, 0.4) !important;
    --nexa-t-badge-bg: rgba(245, 158, 11, 0.2) !important;
    --nexa-t-badge-color: #FBBF24 !important;
    --nexa-t-title: #FFFBEB !important;
    --nexa-t-msg: #FDE68A !important;
    --nexa-t-progress: #F59E0B !important;
}

html.dark .nexa-toast-theme-error {
    --nexa-t-bg: #291217 !important;
    --nexa-t-border: rgba(244, 63, 94, 0.4) !important;
    --nexa-t-badge-bg: rgba(244, 63, 94, 0.2) !important;
    --nexa-t-badge-color: #FB7185 !important;
    --nexa-t-title: #FFF1F2 !important;
    --nexa-t-msg: #FDA4AF !important;
    --nexa-t-progress: #F43F5E !important;
}

/* Keyframes */
@keyframes nexaToastSlideIn {
    0% {
        opacity: 0;
        transform: translateY(-8px) translateX(45px) scale(0.96);
    }
    100% {
        opacity: 1;
        transform: translateY(0) translateX(0) scale(1);
    }
}

@keyframes nexaToastSlideOut {
    0% {
        opacity: 1;
        transform: translateY(0) translateX(0) scale(1);
        max-height: 140px;
        margin-bottom: 12px;
    }
    100% {
        opacity: 0;
        transform: translateY(-6px) translateX(55px) scale(0.92);
        max-height: 0;
        margin-bottom: 0;
        padding-top: 0;
        padding-bottom: 0;
    }
}

@keyframes nexaToastProgressLinear {
    0% {
        transform: scaleX(1);
    }
    100% {
        transform: scaleX(0);
    }
}
</style>

<script>
(function() {
    const container = document.getElementById('nexa-toast-container');
    const defaultGenderTheme = container ? container.dataset.defaultTheme || 'neutral' : 'neutral';

    /**
     * Tampilkan Floating Toast Modern
     * @param {string} type - 'success', 'info', 'warning', 'error', 'status'
     * @param {string} message - Isi pesan notifikasi
     * @param {string|null} title - Judul notifikasi (opsional)
     * @param {string|null} customTheme - 'female', 'male', 'neutral' (opsional)
     */
    window.showNexaToast = function(type, message, title = null, customTheme = null) {
        if (!container || !message) return;

        type = (type || 'info').toLowerCase();
        let themeClass = '';
        let iconClass = 'fas fa-info-circle';
        let defaultTitle = 'Informasi';
        let duration = 4500;

        // Pilih tema warna
        if (type === 'error') {
            themeClass = 'nexa-toast-theme-error';
            iconClass = 'fas fa-circle-exclamation';
            defaultTitle = 'Pemberitahuan';
            duration = 6000;
        } else if (type === 'warning') {
            themeClass = 'nexa-toast-theme-warning';
            iconClass = 'fas fa-triangle-exclamation';
            defaultTitle = 'Perhatian';
            duration = 5000;
        } else {
            // Tipe success / info / status menggunakan tema pastel gender pengguna
            const activeTheme = customTheme || defaultGenderTheme;
            if (activeTheme === 'female') {
                themeClass = 'nexa-toast-theme-female';
                iconClass = type === 'success' ? 'fas fa-heart-circle-check' : 'fas fa-sparkles';
            } else if (activeTheme === 'male') {
                themeClass = 'nexa-toast-theme-male';
                iconClass = type === 'success' ? 'fas fa-circle-check' : 'fas fa-circle-info';
            } else {
                themeClass = 'nexa-toast-theme-neutral';
                iconClass = type === 'success' ? 'fas fa-circle-check' : 'fas fa-circle-info';
            }

            if (type === 'success' || type === 'status') {
                defaultTitle = 'Berhasil';
            }
        }

        const toastEl = document.createElement('div');
        toastEl.className = `nexa-toast-item ${themeClass}`;
        toastEl.style.setProperty('--toast-duration', `${duration}ms`);
        toastEl.setAttribute('role', 'alert');

        const finalTitle = title || defaultTitle;

        toastEl.innerHTML = `
            <div class="nexa-toast-icon-wrap">
                <i class="${iconClass}"></i>
            </div>
            <div class="nexa-toast-content">
                <div class="nexa-toast-title">${escapeHtml(finalTitle)}</div>
                <div class="nexa-toast-message">${escapeHtml(message)}</div>
            </div>
            <button type="button" class="nexa-toast-close" aria-label="Tutup">
                <i class="fas fa-times"></i>
            </button>
            <div class="nexa-toast-progress"></div>
        `;

        container.appendChild(toastEl);

        // Timer dismiss logic with Pause on Hover
        let remainingTime = duration;
        let startTime = Date.now();
        let timerId = null;

        const startDismissTimer = () => {
            startTime = Date.now();
            timerId = setTimeout(() => {
                closeToast(toastEl);
            }, remainingTime);
        };

        const pauseDismissTimer = () => {
            if (timerId) {
                clearTimeout(timerId);
                timerId = null;
                remainingTime -= (Date.now() - startTime);
                if (remainingTime < 300) remainingTime = 300;
            }
        };

        toastEl.addEventListener('mouseenter', pauseDismissTimer);
        toastEl.addEventListener('mouseleave', startDismissTimer);

        // Close button click
        const closeBtn = toastEl.querySelector('.nexa-toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (timerId) clearTimeout(timerId);
                closeToast(toastEl);
            });
        }

        startDismissTimer();
    };

    function closeToast(el) {
        if (!el || el.classList.contains('nexa-toast-closing')) return;
        el.classList.add('nexa-toast-closing');
        setTimeout(() => {
            if (el.parentNode) {
                el.parentNode.removeChild(el);
            }
        }, 320);
    }

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // Auto trigger flash messages on page load
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            window.showNexaToast('success', @json(session('success')), 'Sukses');
        @endif

        @if(session('status'))
            window.showNexaToast('status', @json(session('status')), 'Pemberitahuan');
        @endif

        @if(session('info'))
            window.showNexaToast('info', @json(session('info')), 'Informasi');
        @endif

        @if(session('warning'))
            window.showNexaToast('warning', @json(session('warning')), 'Peringatan');
        @endif

        @if(session('error'))
            window.showNexaToast('error', @json(session('error')), 'Terjadi Kesalahan');
        @endif

        @if(isset($errors) && $errors->any())
            @if(!session('error'))
                window.showNexaToast('error', 'Terdapat isian formulir yang belum sesuai. Silakan periksa kembali data Anda.', 'Validasi Formulir');
            @endif
        @endif
    });
})();
</script>
