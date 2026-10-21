
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b0712">
    <meta name="description" content="Secure administrator login for GhostOrchid Control Center.">

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/ghostorchid-logo.png') }}?v=2">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/ghostorchid-logo.png') }}">

    <title>Administrator Login | GhostOrchid</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        color-scheme: dark;
        --admin-bg: #0b0712;
        --admin-panel: transparent;
        --admin-border: transparent;
        --admin-text: #f8f5ff;
        --admin-muted: #a69ab8;
        --admin-purple: #a879ff;
        --admin-pink: #e2a5ff;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }

    html {
        min-height: 100%;
        scroll-behavior: smooth;
        -webkit-text-size-adjust: 100%;
    }

    body.auth-body.admin-auth {
        position: relative;
        isolation: isolate;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 100vh;
        min-height: 100dvh;
        margin: 0;
        padding: 36px 20px;
        overflow-x: hidden;
        color: var(--admin-text);
        background:
            radial-gradient(ellipse at 12% 10%, rgba(128, 68, 196, .19), transparent 38%),
            radial-gradient(ellipse at 90% 90%, rgba(92, 45, 155, .17), transparent 38%),
            linear-gradient(145deg, #09060e, #100a19 50%, #08060d);
        font-family: 'Plus Jakarta Sans', sans-serif;
        -webkit-font-smoothing: antialiased;
        text-rendering: optimizeLegibility;
    }

    a {
        color: inherit;
        text-decoration: none;
    }

    button,
    input {
        font: inherit;
    }

    button,
    a,
    input {
        -webkit-tap-highlight-color: transparent;
    }

    button {
        touch-action: manipulation;
    }

    .auth-background {
        position: fixed;
        z-index: -1;
        inset: 0;
        overflow: hidden;
        pointer-events: none;
        background: transparent;
    }

    .auth-grid {
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255, 255, 255, .014) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, .014) 1px, transparent 1px);
        background-size: 46px 46px;
        mask-image: linear-gradient(to bottom, #000, transparent 90%);
        -webkit-mask-image: linear-gradient(to bottom, #000, transparent 90%);
    }

    .auth-stars {
        position: absolute;
        inset: 0;
        opacity: .32;
        background-image:
            radial-gradient(circle at 17% 26%, rgba(226, 194, 255, .8) 0 1px, transparent 1.5px),
            radial-gradient(circle at 78% 19%, rgba(226, 194, 255, .7) 0 1px, transparent 1.5px),
            radial-gradient(circle at 86% 68%, rgba(226, 194, 255, .6) 0 1px, transparent 1.5px),
            radial-gradient(circle at 23% 81%, rgba(226, 194, 255, .6) 0 1px, transparent 1.5px),
            radial-gradient(circle at 66% 87%, rgba(226, 194, 255, .5) 0 1px, transparent 1.5px);
    }

    .auth-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(90px);
        opacity: .2;
        animation: orb-float 13s ease-in-out infinite alternate;
        will-change: transform;
    }

    .auth-orb-one {
        top: -190px;
        left: -120px;
        width: 420px;
        height: 420px;
        background: #9143eb;
    }

    .auth-orb-two {
        right: -140px;
        bottom: -200px;
        width: 460px;
        height: 460px;
        background: #7228c5;
        animation-delay: -4s;
    }

    .auth-orb-three {
        top: 35%;
        left: 48%;
        width: 220px;
        height: 220px;
        background: #b75be8;
        opacity: .07;
        animation-delay: -7s;
    }

    .admin-login-card {
        position: relative;
        isolation: isolate;
        display: flex;
        flex-direction: column;
        width: min(100%, 465px);
        min-width: 0;
        gap: 0;
        margin: auto;
        padding: 42px 40px 28px;
        overflow: hidden;
        border: none !important;
        border-radius: 0;
        outline: none;
        background: transparent !important;
        box-shadow: none !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        animation: card-appear .65s ease both;
    }

    .admin-card-glow {
        position: absolute;
        z-index: -1;
        top: -150px;
        left: 50%;
        width: 320px;
        height: 260px;
        border-radius: 50%;
        background: rgba(150, 86, 225, .13);
        filter: blur(70px);
        transform: translateX(-50%);
        pointer-events: none;
    }

    .brand-center {
        display: inline-flex;
        align-items: center;
        align-self: center;
        justify-content: center;
        gap: 13px;
        max-width: 100%;
        margin-bottom: 34px;
    }

    .brand-mark {
        display: grid;
        width: 49px;
        height: 49px;
        flex: 0 0 49px;
        place-items: center;
        border: none !important;
        border-radius: 15px;
        color: #e5c9ff;
        background: rgba(173, 116, 255, .10);
        box-shadow: none;
        font-size: 21px;
    }

    .brand-copy {
        min-width: 0;
    }

    .brand-copy strong,
    .brand-copy small {
        display: block;
        overflow-wrap: anywhere;
    }

    .brand-copy strong {
        color: #fbf8ff;
        font-size: 15px;
        font-weight: 800;
        letter-spacing: 2.2px;
    }

    .brand-copy small {
        margin-top: 5px;
        color: #ad9abb;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 2.5px;
    }

    .form-kicker {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        align-self: center;
        max-width: 100%;
        margin-bottom: 17px;
        padding: 9px 13px;
        border: none !important;
        border-radius: 100px;
        color: #d1b1ff;
        background: rgba(164, 106, 255, .055);
        font-size: 9px;
        font-weight: 800;
        letter-spacing: 1.8px;
        text-align: center;
    }

    .status-dot {
        position: relative;
        width: 7px;
        height: 7px;
        flex: 0 0 7px;
        border-radius: 50%;
        background: #c99aff;
        box-shadow: 0 0 12px rgba(201, 154, 255, .8);
    }

    .status-dot::after {
        position: absolute;
        inset: -4px;
        border: 0;
        border-radius: 50%;
        content: "";
        box-shadow: 0 0 0 1px rgba(201, 154, 255, .25);
        animation: status-pulse 2s ease-out infinite;
    }

    .admin-heading {
        margin-bottom: 28px;
        text-align: center;
    }

    .admin-heading h1 {
        margin: 0;
        color: #faf7ff;
        font-size: clamp(27px, 5vw, 33px);
        font-weight: 800;
        line-height: 1.3;
        letter-spacing: -1.4px;
        overflow-wrap: anywhere;
    }

    .admin-heading h1 span {
        color: #c49aff;
    }

    .admin-heading p {
        max-width: 320px;
        margin: 12px auto 0;
        color: var(--admin-muted);
        font-size: 11px;
        line-height: 1.9;
    }

    .flash-error {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        margin: 0 0 22px;
        padding: 12px 0;
        border: none !important;
        border-radius: 0;
        color: #ffb9c8;
        background: transparent !important;
        font-size: 11px;
        line-height: 1.7;
        overflow-wrap: anywhere;
    }

    .flash-error-icon {
        display: grid;
        flex: 0 0 18px;
        place-items: center;
        padding-top: 1px;
        font-size: 14px;
    }

    .admin-login-form {
        display: flex;
        flex-direction: column;
        width: 100%;
        min-width: 0;
        gap: 21px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        min-width: 0;
        gap: 10px;
    }

    .label-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .form-label {
        display: block;
        color: #e8e0f4;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.6;
    }

    .field-wrap {
        display: flex;
        align-items: center;
        width: 100%;
        min-width: 0;
        min-height: 54px;
        border: none !important;
        border-radius: 12px;
        background: rgba(255, 255, 255, .035) !important;
        box-shadow: inset 0 0 0 1px rgba(190, 166, 220, .045);
        transition: background .2s ease, box-shadow .2s ease;
    }

    .field-wrap:focus-within {
        border: none !important;
        background: rgba(153, 98, 223, .075) !important;
        box-shadow:
            inset 0 0 0 1px rgba(177, 124, 255, .12),
            0 0 22px rgba(151, 91, 226, .09);
    }

    .field-icon-wrap {
        display: grid;
        width: 48px;
        height: 52px;
        flex: 0 0 48px;
        place-items: center;
    }

    .field-icon {
        color: #a995c2;
        font-size: 14px;
    }

    .field-wrap input {
        display: block;
        width: 100%;
        min-width: 0;
        height: 52px;
        margin: 0;
        padding: 0 12px 0 0;
        border: 0 !important;
        outline: none;
        color: #f8f4ff;
        background: transparent !important;
        font-size: 12px;
        font-weight: 500;
        box-shadow: none !important;
        appearance: none;
        -webkit-appearance: none;
    }

    .field-wrap input::placeholder {
        color: #877a97;
        opacity: 1;
    }

    .field-wrap input:-webkit-autofill,
    .field-wrap input:-webkit-autofill:hover,
    .field-wrap input:-webkit-autofill:focus {
        -webkit-text-fill-color: #f8f4ff;
        caret-color: #f8f4ff;
        transition: background-color 9999s ease-in-out 0s;
        box-shadow: 0 0 0 1000px #100a19 inset !important;
    }

    .password-toggle {
        display: grid;
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        place-items: center;
        margin-right: 4px;
        padding: 0;
        border: none !important;
        border-radius: 10px;
        color: #a995bd;
        background: transparent !important;
        cursor: pointer;
        transition: color .2s ease, background .2s ease;
    }

    .password-toggle:hover,
    .password-toggle:focus-visible {
        color: #e0c5ff;
        background: rgba(174, 120, 255, .09) !important;
        outline: none;
    }

    .primary-button {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 54px;
        margin-top: 4px;
        padding: 0 17px;
        border: none !important;
        border-radius: 12px;
        color: #fff;
        background: linear-gradient(110deg, #8050d4, #a36be8 54%, #8c5cdb);
        box-shadow: 0 8px 26px rgba(115, 65, 190, .22);
        cursor: pointer;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .primary-button-content {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        gap: 13px;
        font-size: 12px;
        font-weight: 800;
    }

    .primary-button-icon {
        display: grid;
        width: 27px;
        height: 27px;
        flex: 0 0 27px;
        place-items: center;
        border: none !important;
        border-radius: 9px;
        background: rgba(255, 255, 255, .12);
        transition: transform .2s ease, background .2s ease;
    }

    .primary-button-icon i {
        font-size: 10px;
    }

    .primary-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 13px 32px rgba(115, 65, 190, .32);
    }

    .primary-button:hover .primary-button-icon {
        transform: translateX(3px);
        background: rgba(255, 255, 255, .18);
    }

    .primary-button:active {
        transform: translateY(0);
    }

    .primary-button:focus-visible {
        outline: 2px solid #d4b7ff;
        outline-offset: 4px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        align-self: center;
        gap: 10px;
        width: fit-content;
        max-width: 100%;
        margin: 25px auto 24px;
        color: #b6a6ca;
        font-size: 11px;
        font-weight: 600;
        text-align: center;
        transition: color .2s ease;
    }

    .back-link:hover {
        color: #e4ceff;
    }

    .back-link-icon {
        display: grid;
        width: 27px;
        height: 27px;
        flex: 0 0 27px;
        place-items: center;
        border: none !important;
        border-radius: 9px;
        background: rgba(255, 255, 255, .035);
        transition: transform .2s ease, background .2s ease;
    }

    .back-link:hover .back-link-icon {
        transform: translateX(-3px);
        background: rgba(174, 120, 255, .1);
    }

    .back-link-icon i {
        font-size: 10px;
    }

    .security-note {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        gap: 9px;
        padding-top: 17px;
        border-top: none !important;
        color: #9588a7;
        font-size: 9px;
        font-weight: 600;
        line-height: 1.7;
    }

    .security-icon {
        display: grid;
        width: 25px;
        height: 25px;
        flex: 0 0 25px;
        place-items: center;
        border: none !important;
        border-radius: 8px;
        color: #c1a0ef;
        background: rgba(151, 91, 226, .07);
        font-size: 11px;
    }

    .security-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-left: auto;
        padding: 5px 8px;
        border: none !important;
        border-radius: 7px;
        color: #8cdbb0;
        background: rgba(58, 174, 111, .06);
        font-size: 9px;
        white-space: nowrap;
    }

    .security-status i {
        font-size: 6px;
    }

    .admin-login-card a:focus-visible,
    .admin-login-card button:focus-visible {
        outline-offset: 4px;
    }

    @keyframes orb-float {
        from {
            transform: translate3d(0, 0, 0) scale(1);
        }

        to {
            transform: translate3d(25px, 20px, 0) scale(1.08);
        }
    }

    @keyframes card-appear {
        from {
            opacity: 0;
            transform: translateY(12px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes status-pulse {
        0% {
            transform: scale(.8);
            opacity: .8;
        }

        100% {
            transform: scale(1.8);
            opacity: 0;
        }
    }

    @media (max-width: 768px) {
        body.auth-body.admin-auth {
            padding: 28px 18px;
        }

        .admin-login-card {
            width: min(100%, 440px);
            padding: 34px 28px 24px;
        }

        .brand-center {
            margin-bottom: 29px;
        }

        .admin-heading {
            margin-bottom: 25px;
        }

        .admin-login-form {
            gap: 19px;
        }
    }

    @media (max-width: 600px) {
        body.auth-body.admin-auth {
            align-items: center;
            padding: 22px 16px;
        }

        .auth-grid {
            background-size: 34px 34px;
        }

        .auth-orb {
            filter: blur(65px);
            opacity: .16;
        }

        .auth-orb-one {
            top: -130px;
            left: -150px;
            width: 300px;
            height: 300px;
        }

        .auth-orb-two {
            right: -160px;
            bottom: -140px;
            width: 320px;
            height: 320px;
        }

        .auth-orb-three {
            width: 150px;
            height: 150px;
        }

        .admin-login-card {
            width: 100%;
            max-width: 440px;
            padding: 30px 24px 22px;
            border: none !important;
            border-radius: 0;
            background: transparent !important;
            box-shadow: none !important;
        }

        .brand-center {
            gap: 11px;
            margin-bottom: 27px;
        }

        .brand-mark {
            width: 43px;
            height: 43px;
            flex-basis: 43px;
            border-radius: 12px;
            font-size: 18px;
        }

        .brand-copy strong {
            font-size: 13px;
            letter-spacing: 1.8px;
        }

        .brand-copy small {
            font-size: 8px;
            letter-spacing: 2px;
        }

        .form-kicker {
            gap: 8px;
            margin-bottom: 14px;
            padding: 8px 11px;
            font-size: 8px;
            letter-spacing: 1.5px;
        }

        .admin-heading {
            margin-bottom: 24px;
        }

        .admin-heading h1 {
            font-size: clamp(25px, 7vw, 31px);
            letter-spacing: -1px;
        }

        .admin-heading p {
            max-width: 300px;
            margin-top: 10px;
            font-size: 11px;
            line-height: 1.8;
        }

        .flash-error {
            margin-bottom: 18px;
            font-size: 11px;
        }

        .admin-login-form {
            gap: 18px;
        }

        .form-group {
            gap: 9px;
        }

        .form-label {
            font-size: 11px;
        }

        .field-wrap {
            min-height: 52px;
        }

        .field-icon-wrap {
            width: 43px;
            height: 50px;
            flex-basis: 43px;
        }

        .field-wrap input {
            height: 50px;
            font-size: 12px;
        }

        .password-toggle {
            width: 42px;
            height: 42px;
            flex-basis: 42px;
        }

        .primary-button {
            min-height: 52px;
        }

        .back-link {
            margin: 22px auto 20px;
            font-size: 11px;
        }

        .security-note {
            justify-content: center;
            gap: 8px;
            padding-top: 14px;
            font-size: 9px;
            text-align: center;
        }

        .security-status {
            margin-left: 0;
        }
    }

    @media (max-width: 380px) {
        body.auth-body.admin-auth {
            align-items: center;
            padding: 16px 12px;
        }

        .admin-login-card {
            padding: 26px 16px 20px;
        }

        .brand-center {
            gap: 10px;
            margin-bottom: 24px;
        }

        .brand-mark {
            width: 39px;
            height: 39px;
            flex-basis: 39px;
            font-size: 17px;
        }

        .brand-copy strong {
            font-size: 11px;
            letter-spacing: 1.5px;
        }

        .brand-copy small {
            font-size: 7px;
            letter-spacing: 1.5px;
        }

        .form-kicker {
            font-size: 7px;
            letter-spacing: 1.2px;
        }

        .admin-heading {
            margin-bottom: 21px;
        }

        .admin-heading h1 {
            font-size: 25px;
        }

        .admin-heading p {
            font-size: 10px;
        }

        .form-label {
            font-size: 10px;
        }

        .field-wrap,
        .field-wrap input {
            min-height: 49px;
            height: 49px;
        }

        .field-icon-wrap {
            width: 38px;
            height: 47px;
            flex-basis: 38px;
        }

        .field-wrap input {
            font-size: 11px;
        }

        .password-toggle {
            width: 36px;
            height: 40px;
            flex-basis: 36px;
        }

        .primary-button {
            min-height: 49px;
            padding: 0 12px;
        }

        .primary-button-content {
            gap: 10px;
            font-size: 11px;
        }

        .back-link {
            gap: 8px;
            margin-top: 19px;
            margin-bottom: 17px;
            font-size: 10px;
        }

        .security-note {
            gap: 7px;
            font-size: 8px;
        }

        .security-icon {
            width: 22px;
            height: 22px;
            flex-basis: 22px;
        }

        .security-status {
            padding: 4px 6px;
            font-size: 8px;
        }
    }

    @media (max-height: 700px) {
        body.auth-body.admin-auth {
            align-items: flex-start;
            padding-top: 18px;
            padding-bottom: 18px;
        }

        .admin-login-card {
            margin-top: auto;
            margin-bottom: auto;
            padding-top: 24px;
            padding-bottom: 20px;
        }

        .brand-center {
            margin-bottom: 21px;
        }

        .admin-heading {
            margin-bottom: 20px;
        }

        .admin-login-form {
            gap: 15px;
        }

        .back-link {
            margin-top: 18px;
            margin-bottom: 16px;
        }

        .security-note {
            padding-top: 12px;
        }
    }

    @media (max-height: 500px) and (max-width: 900px) {
        body.auth-body.admin-auth {
            align-items: flex-start;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        .admin-login-card {
            max-width: 430px;
            padding-top: 18px;
            padding-bottom: 16px;
        }

        .brand-center {
            margin-bottom: 16px;
        }

        .brand-mark {
            width: 36px;
            height: 36px;
            flex-basis: 36px;
        }

        .admin-heading {
            margin-bottom: 16px;
        }

        .admin-heading p {
            margin-top: 6px;
        }

        .admin-login-form {
            gap: 12px;
        }

        .field-wrap,
        .field-wrap input {
            min-height: 44px;
            height: 44px;
        }

        .field-icon-wrap {
            height: 44px;
        }

        .back-link {
            margin-top: 14px;
            margin-bottom: 12px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        *,
        *::before,
        *::after {
            scroll-behavior: auto !important;
            animation-duration: .01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: .01ms !important;
            will-change: auto !important;
        }
    }
</style>

</head>

<body class="auth-body admin-auth">

    <div class="auth-background" aria-hidden="true">
        <div class="auth-orb auth-orb-one"></div>
        <div class="auth-orb auth-orb-two"></div>
        <div class="auth-orb auth-orb-three"></div>
        <div class="auth-grid"></div>
        <div class="auth-stars"></div>
    </div>

    <main class="admin-login-card">

        <div class="admin-card-glow" aria-hidden="true"></div>

        <a class="brand-center" href="{{ url('/') }}" aria-label="GhostOrchid home">
            <span class="brand-mark">
                <i class="fa-solid fa-spa" aria-hidden="true"></i>
            </span>

            <span class="brand-copy">
                <strong>GHOSTORCHID</strong>
                <small>CONTROL CENTER</small>
            </span>
        </a>

        <div class="form-kicker">
            <span class="status-dot" aria-hidden="true"></span>
            <span>RESTRICTED ACCESS</span>
        </div>

        <div class="admin-heading">
            <h1>Administrator<span>.</span></h1>
        </div>

        @if ($errors->any())
            <div class="flash-error" role="alert" aria-live="polite">
                <span class="flash-error-icon">
                    <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                </span>

                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.store') }}" class="admin-login-form">
            @csrf

            <div class="form-group">
                <label class="form-label" for="username">
                    Admin username
                </label>

                <div class="field-wrap">
                    <span class="field-icon-wrap">
                        <i class="fa-regular fa-user field-icon" aria-hidden="true"></i>
                    </span>

                    <input
                        id="username"
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        placeholder="Enter your username"
                        autocomplete="username"
                        autocapitalize="none"
                        spellcheck="false"
                        maxlength="255"
                        required
                        autofocus
                    >
                </div>
            </div>

            <div class="form-group">
                <div class="label-row">
                    <label class="form-label" for="password">Password</label>
                </div>

                <div class="field-wrap">
                    <span class="field-icon-wrap">
                        <i class="fa-solid fa-lock field-icon" aria-hidden="true"></i>
                    </span>

                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required
                    >

                    <button
                        class="password-toggle"
                        type="button"
                        id="togglePassword"
                        aria-label="Show password"
                        aria-controls="password"
                        aria-pressed="false"
                    >
                        <i class="fa-regular fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <button class="primary-button" type="submit">
                <span class="primary-button-content">
                    <span>Access dashboard</span>

                    <span class="primary-button-icon">
                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </span>
                </span>
            </button>
        </form>

        <a class="back-link" href="{{ route('login') }}">
            <span class="back-link-icon">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
            </span>

            <span>Return to client login</span>
        </a>

        <div class="security-note">
            <span class="security-icon">
                <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
            </span>

            <span>Protected administrator access</span>

            <span class="security-status">
                <i class="fa-solid fa-circle" aria-hidden="true"></i>
                Secure
            </span>
        </div>

    </main>

    <script>
        (() => {
            const toggle = document.getElementById('togglePassword');
            const password = document.getElementById('password');

            if (!toggle || !password) {
                return;
            }

            toggle.addEventListener('click', () => {
                const showPassword = password.type === 'password';

                password.type = showPassword ? 'text' : 'password';

                toggle.setAttribute('aria-pressed', String(showPassword));
                toggle.setAttribute(
                    'aria-label',
                    showPassword ? 'Hide password' : 'Show password'
                );

                const icon = toggle.querySelector('i');

                if (icon) {
                    icon.classList.toggle('fa-eye', !showPassword);
                    icon.classList.toggle('fa-eye-slash', showPassword);
                }
            });
        })();
    </script>

</body>

</html>
