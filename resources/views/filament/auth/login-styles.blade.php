<style>
    /* ══════════════════════════════════════════════════════════════ */
    /* 1. OVERRIDE FILAMENT DEFAULT — Buka lebar card login            */
    /* ══════════════════════════════════════════════════════════════ */
    .fi-simple-layout .fi-simple-main {
        max-width: none !important;
        width: 100% !important;
        padding: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        border: none !important;
        border-radius: 0 !important;
    }

    .fi-simple-layout .fi-simple-main-ctn {
        max-width: none !important;
        width: 100% !important;
        padding: 0 !important;
    }

    /* ⭐ TAMBAHAN: sembunyikan header & footer default Filament */
    /*    Supaya brandName "Admin Asy-Syams" & footer bawaan tidak muncul */
    .fi-simple-layout .fi-simple-header,
    .fi-simple-layout .fi-simple-footer {
        display: none !important;
    }

    /* ══════════════════════════════════════════════════════════════ */
    /* 2. LAYOUT WRAPPER                                               */
    /* ══════════════════════════════════════════════════════════════ */
    .fi-simple-layout {
        background: #f5f5f0 !important;
        padding: 24px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-height: 100vh !important;
    }

    .yptq-login-wrap {
        width: 100% !important;
        max-width: 1100px !important;
        margin: 0 auto !important;
        animation: yptqCardEnter 800ms cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    @keyframes yptqCardEnter {
        from { opacity: 0; transform: translateY(24px) scale(0.98); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .yptq-login-card {
        display: grid;
        grid-template-columns: 1fr;
        background: #ffffff;
        border-radius: 28px;
        overflow: hidden;
        box-shadow:
            0 1px 3px rgba(0, 0, 0, 0.04),
            0 12px 32px rgba(6, 78, 59, 0.08),
            0 32px 64px rgba(6, 78, 59, 0.06);
    }

    @media (min-width: 1024px) {
        .yptq-login-card { grid-template-columns: 1.1fr 1fr; }
    }

    /* ══════════════════════════════════════════════════════════════ */
    /* 3. LEFT PANEL — Branding & Dekorasi                             */
    /* ══════════════════════════════════════════════════════════════ */
    .yptq-left {
        position: relative;
        background: linear-gradient(145deg, #0a3d2e 0%, #062921 100%);
        padding: 48px 44px;
        overflow: hidden;
        color: #ffffff;
        display: none;
    }

    @media (min-width: 1024px) {
        .yptq-left {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 640px;
        }
    }

    .yptq-pattern {
        position: absolute; inset: 0;
        width: 100%; height: 100%;
        opacity: 0.6; pointer-events: none;
    }

    .yptq-book {
        position: absolute;
        bottom: 40px; right: -40px;
        width: 320px; height: 180px;
        opacity: 0.9; pointer-events: none;
    }

    .yptq-left-content {
        position: relative; z-index: 1;
        display: flex; flex-direction: column; height: 100%;
    }

    .yptq-brand { display: flex; align-items: center; gap: 16px; margin-bottom: 56px; }

    .yptq-brand-logo {
        width: 64px; height: 64px;
        border-radius: 50%; background: #ffffff;
        padding: 4px; object-fit: contain;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .yptq-brand-name {
        font-family: ui-serif, Georgia, serif;
        font-weight: 700; font-size: 1.25rem;
        letter-spacing: 0.02em; color: #ffffff;
    }

    .yptq-brand-tag {
        font-size: 0.8125rem;
        color: rgba(255, 255, 255, 0.65);
        margin-top: 2px; letter-spacing: 0.01em;
    }

    .yptq-tagline {
        font-size: 0.6875rem; font-weight: 700;
        letter-spacing: 0.22em; text-transform: uppercase;
        color: #d4af37; margin-bottom: 20px;
    }

    .yptq-hero-title {
        font-family: ui-serif, Georgia, serif;
        font-size: 2.75rem; font-weight: 700;
        line-height: 1.12; letter-spacing: -0.02em;
        color: #ffffff; margin: 0 0 24px 0;
    }

    .yptq-hero-title em { font-style: italic; color: #e6c94d; font-weight: 700; }

    .yptq-hero-desc {
        font-size: 1rem; line-height: 1.65;
        color: rgba(255, 255, 255, 0.78);
        max-width: 380px; margin: 0 0 32px 0;
    }

    .yptq-footer-text {
        font-size: 0.875rem; line-height: 1.65;
        color: rgba(255, 255, 255, 0.6);
        max-width: 320px; padding-top: 20px;
        border-top: 1px solid rgba(212, 175, 55, 0.3);
        margin-top: auto;
    }

    /* ══════════════════════════════════════════════════════════════ */
    /* 4. RIGHT PANEL — Form Login                                     */
    /* ══════════════════════════════════════════════════════════════ */
    .yptq-right {
        padding: 48px 44px;
        display: flex; flex-direction: column; justify-content: center;
    }

    @media (max-width: 640px) {
        .yptq-right { padding: 36px 28px; }
    }

    .yptq-back-link {
        display: inline-flex; align-items: center; gap: 8px;
        font-size: 0.875rem; font-weight: 600;
        color: #15803d; text-decoration: none;
        margin-bottom: 36px;
        transition: all 250ms cubic-bezier(0.16, 1, 0.3, 1);
        align-self: flex-start;
    }

    .yptq-back-link:hover { color: #166534; transform: translateX(-3px); }

    .yptq-portal-label {
        font-size: 0.6875rem; font-weight: 700;
        letter-spacing: 0.22em; text-transform: uppercase;
        color: #d97706; margin-bottom: 12px;
    }

    .yptq-welcome {
        font-family: ui-serif, Georgia, serif;
        font-size: 2rem; font-weight: 700;
        color: #064e3b; letter-spacing: -0.02em;
        line-height: 1.15; margin: 0 0 8px 0;
    }

    .yptq-subtitle {
        font-size: 0.9375rem; color: #6b7280;
        margin: 0 0 36px 0; line-height: 1.5;
    }

    /* ══════════════════════════════════════════════════════════════ */
    /* 5. FORM — Field Wrapper & Label                                 */
    /* ══════════════════════════════════════════════════════════════ */
    .yptq-form { display: flex; flex-direction: column; gap: 4px; }

    .yptq-form .fi-fo-component-ctn,
    .yptq-form .fi-fo-field-wrp,
    .yptq-form .fi-fo-field-wrp-field,
    .yptq-form .fi-fo-field-wrp-label-wrap {
        width: 100% !important;
        max-width: 100% !important;
    }

    .yptq-form .fi-fo-field-wrp { margin-bottom: 20px !important; }

    .yptq-form .fi-fo-field-wrp label,
    .yptq-form .fi-fo-field-wrp-label {
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        color: #1f2937 !important;
        margin-bottom: 8px !important;
        letter-spacing: 0 !important;
    }

    .yptq-form .fi-fo-field-wrp label .fi-fo-field-wrp-requirement-indicator,
    .yptq-form .fi-fo-field-wrp-label .fi-fo-field-wrp-requirement-indicator {
        color: #ef4444 !important;
    }

    /* ⭐ TAMBAHAN: Style pesan error validasi */
    .yptq-form .fi-fo-field-wrp-error-message,
    .yptq-form .fi-fo-field-wrp-error-message * {
        font-size: 0.8125rem !important;
        color: #dc2626 !important;
        margin-top: 6px !important;
        font-weight: 500 !important;
    }

    .yptq-form .fi-fo-field-wrp-hint {
        font-size: 0.8125rem !important;
        color: #9ca3af !important;
        margin-top: 4px !important;
    }

    /* ══════════════════════════════════════════════════════════════ */
    /* 6. INPUT WRAPPER — Border, Background, Focus Ring               */
    /* ══════════════════════════════════════════════════════════════ */
    .yptq-form .fi-input-wrp {
        position: relative !important;
        display: flex !important;
        align-items: center !important;
        border-radius: 10px !important;
        border: 1.5px solid #e5e7eb !important;
        background: #ffffff !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02) !important;
        transition: all 200ms cubic-bezier(0.16, 1, 0.3, 1) !important;
        overflow: hidden !important;
        min-height: 48px !important;
    }

    .yptq-form .fi-input-wrp:hover { border-color: #d1d5db !important; }

    .yptq-form .fi-input-wrp:focus-within {
        border-color: #15803d !important;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.12) !important;
    }

    /* ⭐ TAMBAHAN: state error — border merah */
    .yptq-form .fi-input-wrp:has(.fi-fo-field-wrp-error) {
        border-color: #fca5a5 !important;
    }

    .yptq-form .fi-input-wrp:has(.fi-fo-field-wrp-error):focus-within {
        border-color: #dc2626 !important;
        box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.12) !important;
    }

    /* ══════════════════════════════════════════════════════════════ */
    /* 7. INPUT FIELD — Reset agresif biar bersih                      */
    /* ══════════════════════════════════════════════════════════════ */
    .yptq-form .fi-input,
    .yptq-form input.fi-input,
    .yptq-form .fi-input:focus,
    .yptq-form .fi-input:active,
    .yptq-form .fi-input:focus-visible,
    .yptq-form input.fi-input:focus,
    .yptq-form input.fi-input:focus-visible {
        padding: 12px 16px !important;
        font-size: 0.9375rem !important;
        color: #111827 !important;
        background: transparent !important;
        background-color: transparent !important;
        border: 0 !important;
        border-width: 0 !important;
        border-style: none !important;
        border-color: transparent !important;
        outline: 0 !important;
        outline-offset: 0 !important;
        outline-style: none !important;
        box-shadow: none !important;
        -webkit-box-shadow: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
        --tw-ring-shadow: 0 0 #0000 !important;
        --tw-ring-offset-shadow: 0 0 #0000 !important;
        --tw-ring-color: transparent !important;
        --tw-ring-inset: 0 0 #0000 !important;
        --tw-ring-offset-width: 0 !important;
        --tw-ring-offset-color: transparent !important;
        width: 100% !important;
        flex: 1 !important;
        min-width: 0 !important;
    }

    .yptq-form .fi-input::placeholder {
        color: #9ca3af !important; opacity: 1 !important;
    }

    /* ⭐ TAMBAHAN: reset Chrome autofill background kuning */
    .yptq-form .fi-input:-webkit-autofill,
    .yptq-form .fi-input:-webkit-autofill:hover,
    .yptq-form .fi-input:-webkit-autofill:focus {
        -webkit-text-fill-color: #111827 !important;
        -webkit-box-shadow: 0 0 0 1000px #ffffff inset !important;
        transition: background-color 5000s ease-in-out 0s !important;
    }

    /* ══════════════════════════════════════════════════════════════ */
    /* 8. PREFIX ICON — Ikon di kiri input                             */
    /* ══════════════════════════════════════════════════════════════ */
    .yptq-form .fi-input-wrp-prefix {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding-left: 14px !important;
        padding-right: 6px !important;
        color: #9ca3af !important;
        flex-shrink: 0 !important;
        width: 24px !important;
        min-width: 24px !important;
        max-width: 24px !important;
    }

    .yptq-form .fi-input-wrp-prefix svg {
        width: 18px !important; height: 18px !important;
        min-width: 18px !important; min-height: 18px !important;
        max-width: 18px !important; max-height: 18px !important;
        stroke-width: 1.75 !important;
        flex-shrink: 0 !important;
    }

    .yptq-form .fi-input-wrp:focus-within .fi-input-wrp-prefix {
        color: #15803d !important;
    }

    /* ══════════════════════════════════════════════════════════════ */
    /* 9. CUSTOM PASSWORD TOGGLE — Eye toggle                          */
    /* ══════════════════════════════════════════════════════════════ */
    /* Sembunyikan suffix bawaan Filament (kalau ada) */
    .yptq-form .fi-input-wrp-suffix { display: none !important; }

    .yptq-form .yptq-eye-toggle {
        position: absolute !important;
        right: 10px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        width: 34px !important; height: 34px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 !important;
        background: transparent !important;
        border: none !important;
        border-radius: 8px !important;
        color: #9ca3af !important;
        cursor: pointer !important;
        opacity: 0 !important;
        pointer-events: none !important;
        transition:
            opacity 200ms cubic-bezier(0.16, 1, 0.3, 1),
            background-color 200ms ease,
            color 200ms ease !important;
        z-index: 2 !important;
    }

    .yptq-form .yptq-eye-toggle.is-visible {
        opacity: 1 !important;
        pointer-events: auto !important;
    }

    .yptq-form .yptq-eye-toggle:hover {
        background: #f0fdf4 !important;
        color: #15803d !important;
    }

    .yptq-form .yptq-eye-toggle:active {
        transform: translateY(-50%) scale(0.92) !important;
    }

    .yptq-form .yptq-eye {
        width: 20px !important; height: 20px !important;
        max-width: 20px !important; max-height: 20px !important;
        stroke-width: 1.75 !important;
        pointer-events: none !important;
    }

    /* ⭐ TAMBAHAN: fallback padding kalau :has() tidak didukung */
    .yptq-form input.yptq-input-password,
    .yptq-form .fi-input-wrp:has(.yptq-input-password) .fi-input {
        padding-right: 50px !important;
    }

    /* Hide native browser password reveal (Edge/IE) */
    .yptq-form input[type="password"]::-ms-reveal,
    .yptq-form input[type="password"]::-ms-clear {
        display: none !important;
    }

    .yptq-form input[type="password"]::-webkit-credentials-auto-fill-button,
    .yptq-form input[type="password"]::-webkit-strong-password-auto-fill-button {
        display: none !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }

    /* ══════════════════════════════════════════════════════════════ */
    /* 10. CHECKBOX — Remember Me                                      */
    /* ══════════════════════════════════════════════════════════════ */
    .yptq-form .fi-fo-checkbox-list-option { padding: 0 !important; }

    .yptq-form .fi-fo-checkbox-list-option-label {
        font-size: 0.875rem !important;
        color: #4b5563 !important;
        font-weight: 500 !important;
        margin-left: 8px !important;
    }

    .yptq-form .fi-checkbox-input {
        border-radius: 6px !important;
        border: 1.5px solid #d1d5db !important;
        width: 18px !important; height: 18px !important;
        cursor: pointer !important;
    }

    .yptq-form .fi-checkbox-input:checked {
        background-color: #15803d !important;
        border-color: #15803d !important;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.12) !important;
    }

    /* ══════════════════════════════════════════════════════════════ */
    /* 11. SUBMIT BUTTON                                                */
    /* ══════════════════════════════════════════════════════════════ */
    .yptq-submit {
        display: flex; align-items: center; justify-content: center;
        gap: 10px; width: 100%;
        padding: 14px 24px; margin-top: 8px;
        font-size: 0.9375rem; font-weight: 700;
        color: #ffffff;
        background: linear-gradient(135deg, #15803d 0%, #0f5132 100%);
        border: none; border-radius: 10px;
        cursor: pointer;
        box-shadow: 0 1px 2px rgba(21, 128, 61, 0.15), 0 4px 12px rgba(21, 128, 61, 0.20);
        transition: all 300ms cubic-bezier(0.16, 1, 0.3, 1);
    }

    .yptq-submit:hover {
        background: linear-gradient(135deg, #166534 0%, #14532d 100%);
        transform: translateY(-2px);
        box-shadow: 0 2px 4px rgba(21, 128, 61, 0.15), 0 12px 24px rgba(21, 128, 61, 0.25);
    }

    .yptq-submit:active { transform: translateY(0) scale(0.98); }

    .yptq-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* ══════════════════════════════════════════════════════════════ */
    /* 12. FOOTER — Garis + Brand                                       */
    /* ══════════════════════════════════════════════════════════════ */
    .yptq-footer { display: flex; align-items: center; gap: 12px; margin-top: 36px; }

    .yptq-footer-line { flex: 1; height: 1px; background: #e5e7eb; }

    .yptq-footer-brand {
        font-size: 0.75rem; font-weight: 600;
        color: #9ca3af; letter-spacing: 0.05em;
        white-space: nowrap;
    }

    /* ══════════════════════════════════════════════════════════════ */
    /* 13. RESPONSIVE                                                   */
    /* ══════════════════════════════════════════════════════════════ */
    @media (max-width: 1023px) {
        .yptq-login-card { max-width: 460px; margin: 0 auto; }
    }

    /* ══════════════════════════════════════════════════════════════ */
    /* 14. ACCESSIBILITY — Respect prefers-reduced-motion               */
    /* ══════════════════════════════════════════════════════════════ */
    @media (prefers-reduced-motion: reduce) {
        .yptq-login-wrap,
        .yptq-submit,
        .yptq-back-link {
            animation: none !important;
            transition: none !important;
        }
    }
</style>