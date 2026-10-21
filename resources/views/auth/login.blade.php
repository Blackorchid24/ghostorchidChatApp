<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link
        rel="icon"
        type="image/png"
        sizes="32x32"
        href="{{ asset('images/ghostorchid-logo.png') }}?v=2"
    >

    <link
        rel="shortcut icon"
        type="image/png"
        href="{{ asset('images/ghostorchid-logo.png') }}"
    >

    <title>Sign in · GhostOrchid</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="auth-body">

    <div class="auth-glow glow-one"></div>
    <div class="auth-glow glow-two"></div>

    <main class="auth-layout">

        <section class="auth-brand-panel">

            <a
                href="{{ url('/') }}"
                class="brand brand-large"
                aria-label="GHOSTORCHID Home"
            >
                <span class="brand-mark">
                    <i class="fa-solid fa-spa"></i>
                </span>

                <span class="brand-text">
                    <strong>GHOSTORCHID</strong>
                    <small>PRIVATE MESSAGING</small>
                </span>
            </a>

            <div class="auth-hero">

                <span class="hero-kicker">
                    <i class="fa-solid fa-sparkles"></i>
                    YOUR PRIVATE SPACE
                </span>

                <h1>
                    Conversations<br>
                    with <em>presence.</em>
                </h1>

                <p>
                    A quieter corner of the internet. Connect with your people
                    in a space built for focus, clarity, and meaningful conversations.
                </p>

                <div
                    class="hero-orchid"
                    aria-hidden="true"
                >
                    <i class="fa-solid fa-spa"></i>
                    <span></span>
                    <span></span>
                    <span></span>
                </div>

            </div>

            <div class="auth-foot">
                © {{ date('Y') }} GHOSTORCHID. MADE FOR REAL CONNECTIONS.
            </div>

        </section>


        <section class="auth-form-panel">

            <form
                class="auth-card"
                method="POST"
                action="{{ route('login.store') }}"
            >

                @csrf

                <div class="auth-form-header">

                    <span class="form-kicker">
                        WELCOME BACK
                    </span>

                    <h2>
                        Sign in to your space
                    </h2>

                    <p class="muted">
                        Enter your details to continue.
                    </p>

                </div>


                @if ($errors->any())
                    <div
                        class="flash-error"
                        role="alert"
                    >
                        <i class="fa-solid fa-circle-exclamation"></i>

                        <div>
                            {{ $errors->first() }}
                        </div>
                    </div>
                @endif


                <div class="form-group">

                    <label for="email">
                        Email address
                    </label>

                    <div class="field-wrap">

                        <i class="fa-regular fa-envelope field-icon"></i>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="you@example.com"
                            autocomplete="email"
                            maxlength="255"
                            required
                            autofocus
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <div class="field-wrap">

                        <i class="fa-solid fa-lock field-icon"></i>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="reveal-password"
                            data-password="password"
                            aria-label="Show password"
                            title="Show password"
                        >
                            <i class="fa-regular fa-eye"></i>
                        </button>

                    </div>

                </div>


                <div class="form-row">

                    <label
                        class="check-label"
                        for="remember"
                    >
                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                            value="1"
                        >

                        <span>
                            Remember me
                        </span>
                    </label>

                    <span class="muted tiny">
                        Keep your account secure
                    </span>

                </div>


                <button
                    class="primary-button full-button"
                    type="submit"
                >
                    <span>Sign in</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>


                <div class="divider-label">

                    <span></span>

                    <small>OR CONTINUE WITH</small>

                    <span></span>

                </div>


                <a
                    href="{{ route('google.redirect') }}"
                    class="google-button"
                >
                    <i class="fa-brands fa-google"></i>

                    <span>
                        Continue with Google
                    </span>
                </a>


                <p class="auth-switch">
                    New to GhostOrchid?

                    <a href="{{ route('register') }}">
                        Create an account
                    </a>
                </p>

            </form>

        </section>

    </main>

    <script>
        document.querySelectorAll('.reveal-password').forEach(button => {
            button.addEventListener('click', function () {
                const input = document.getElementById(this.dataset.password);
                const icon = this.querySelector('i');

                if (!input) return;

                const showing = input.type === 'password';

                input.type = showing ? 'text' : 'password';

                icon.classList.toggle('fa-eye', !showing);
                icon.classList.toggle('fa-eye-slash', showing);

                this.setAttribute(
                    'aria-label',
                    showing ? 'Hide password' : 'Show password'
                );

                this.setAttribute(
                    'title',
                    showing ? 'Hide password' : 'Show password'
                );
            });
        });
    </script>

</body>

</html>
