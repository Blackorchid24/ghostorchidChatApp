<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/ghostorchid-logo.png') }}?v=2">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/ghostorchid-logo.png') }}">
    <title>Create account — GhostOrchid</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="auth-body">
    <div class="auth-glow glow-one"></div>
    <div class="auth-glow glow-two"></div>
    <main class="auth-layout">

        {{-- Brand / Hero Panel --}}
        <section class="auth-brand-panel">

            <a href="{{ url('/') }}" class="brand brand-large" aria-label="GHOSTORCHID Home">
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
                    A SPACE OF YOUR OWN
                </span>

                <h1>
                    Make room<br>
                    for <em>connection.</em>
                </h1>

                <p>
                    Bring your conversations together in a calm, thoughtful space
                    designed around the people who matter.
                </p>

                <div class="hero-orchid" aria-hidden="true">
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


        {{-- Registration Form --}}
        <section class="auth-form-panel">

            <form class="auth-card" method="POST" action="{{ route('register.store') }}" novalidate>

                @csrf

                <span class="form-kicker">
                    GET STARTED
                </span>

                <h2>Create your account</h2>

                <p class="muted">
                    Your own space is a few steps away.
                </p>


                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="flash-error" role="alert">
                        <i class="fa-solid fa-circle-exclamation"></i>

                        <div>
                            {{ $errors->first() }}
                        </div>
                    </div>
                @endif


                {{-- Full Name --}}
                <div class="form-group">

                    <label for="name">
                        Full name
                    </label>

                    <div class="field-wrap">
                        <i class="fa-regular fa-user"></i>

                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                            placeholder="Your name" autocomplete="name" maxlength="255" required autofocus>
                    </div>

                </div>


                {{-- Email --}}
                <div class="form-group">

                    <label for="email">
                        Email address
                    </label>

                    <div class="field-wrap">
                        <i class="fa-regular fa-envelope"></i>

                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            placeholder="you@example.com" autocomplete="email" maxlength="255" required>
                    </div>

                </div>


                {{-- Password --}}
                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <div class="field-wrap">

                        <i class="fa-solid fa-lock"></i>

                        <input type="password" id="password" name="password" placeholder="At least 8 characters"
                            autocomplete="new-password" minlength="8" required>

                        <button type="button" class="reveal-password" data-password="password"
                            aria-label="Show password" title="Show password">
                            <i class="fa-regular fa-eye"></i>
                        </button>

                    </div>

                </div>


                {{-- Confirm Password --}}
                <div class="form-group">

                    <label for="password_confirmation">
                        Confirm password
                    </label>

                    <div class="field-wrap">

                        <i class="fa-solid fa-shield-halved"></i>

                        <input type="password" id="password_confirmation" name="password_confirmation"
                            placeholder="Repeat your password" autocomplete="new-password" minlength="8" required>

                        <button type="button" class="reveal-password" data-password="password_confirmation"
                            aria-label="Show password" title="Show password">
                            <i class="fa-regular fa-eye"></i>
                        </button>

                    </div>

                </div>


                {{-- Register Button --}}
                <button class="primary-button full-button" type="submit">
                    <span>Create account</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>


                {{-- Divider --}}
                <div class="divider-label">
                    <span></span>
                    <small>OR</small>
                    <span></span>
                </div>


                {{-- Google Registration --}}
                <a href="{{ route('google.redirect') }}" class="google-button">
                    <i class="fa-brands fa-google"></i>
                    <span>Sign up with Google</span>
                </a>


                {{-- Login Link --}}
                <p class="auth-switch">
                    Already have an account?
                    <a href="{{ route('login') }}">
                        Sign in
                    </a>
                </p>

            </form>

        </section>

    </main>
    <script>
        document.querySelectorAll('.reveal-password').forEach(button => {
            button.addEventListener('click', function() {
                const input = document.getElementById(this.dataset.password);
                const icon = this.querySelector('i');

                if (!input) return;

                const isPassword = input.type === 'password';

                input.type = isPassword ? 'text' : 'password';

                icon.classList.toggle('fa-eye', !isPassword);
                icon.classList.toggle('fa-eye-slash', isPassword);

                this.setAttribute(
                    'aria-label',
                    isPassword ? 'Hide password' : 'Show password'
                );

                this.setAttribute(
                    'title',
                    isPassword ? 'Hide password' : 'Show password'
                );
            });
        });
    </script>
</body>

</html>
