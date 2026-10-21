@extends('layouts.app')

@section('title', 'My Profile')
@section('heading', 'My Profile')

@section('content')
    <div class="profile-page">
        <section class="panel profile-card">

            <header class="panel-heading">
                <div>
                    <span class="section-kicker">👤 YOUR ACCOUNT</span>
                    <h3>⚙️ Profile Settings</h3>
                </div>

                <span class="panel-icon" aria-hidden="true">
                    <i class="fa-regular fa-user"></i>
                </span>
            </header>

            <p class="panel-description">
                💜 Keep your profile up to date so people can recognize you.
            </p>

            <div class="profile-preview">
                <img
                    src="{{ auth()->user()->avatar_url }}"
                    alt="{{ auth()->user()->name }}'s profile photo"
                    loading="lazy">

                <div class="profile-preview-info">
                    <h3>👋 {{ auth()->user()->name }}</h3>
                    <p>📧 {{ auth()->user()->email }}</p>

                    <span class="online-label">
                        <span aria-hidden="true"></span>
                        ACCOUNT ACTIVE
                    </span>
                </div>
            </div>

            @if (session('success'))
                <div class="flash-success" role="status">
                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="flash-error" role="alert">
                    <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>

                    <div>
                        <strong>⚠️ Please check the following:</strong>

                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('profile.update') }}"
                enctype="multipart/form-data"
                class="profile-form">

                @csrf
                @method('PUT')

                <div class="profile-form-group">
                    <label for="profileName">✏️ Display Name</label>

                    <input
                        id="profileName"
                        type="text"
                        name="name"
                        value="{{ old('name', auth()->user()->name) }}"
                        maxlength="100"
                        autocomplete="name"
                        placeholder="Enter your display name"
                        aria-describedby="profileNameError"
                        required>

                    @error('name')
                        <small class="field-error" id="profileNameError">
                            {{ $message }}
                        </small>
                    @enderror
                </div>

                <div class="profile-form-group">
                    <label for="avatarFile">📸 Profile Photo</label>

                    <input
                        id="avatarFile"
                        type="file"
                        name="avatar_file"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        aria-describedby="avatarHelp avatarFileError">

                    <small class="muted" id="avatarHelp">
                        🖼️ JPG, PNG, or WEBP. Maximum file size: 2 MB.
                    </small>

                    @error('avatar_file')
                        <small class="field-error" id="avatarFileError">
                            {{ $message }}
                        </small>
                    @enderror

                    <div
                        class="avatar-preview-container"
                        id="avatarPreviewContainer"
                        hidden>

                        <span class="muted">✨ New Photo Preview</span>

                        <img
                            id="avatarPreview"
                            src=""
                            alt="Preview of your selected profile photo">
                    </div>
                </div>

                <div class="profile-form-actions">
                    <button class="primary-button" type="submit">
                        <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                        Save Changes 💜
                    </button>
                </div>
            </form>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('avatarFile');
            const previewContainer = document.getElementById('avatarPreviewContainer');
            const previewImage = document.getElementById('avatarPreview');

            if (!fileInput || !previewContainer || !previewImage) {
                return;
            }

            const maxFileSize = 2 * 1024 * 1024;

            const allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            let previewUrl = null;

            function clearPreview() {
                if (previewUrl) {
                    URL.revokeObjectURL(previewUrl);
                    previewUrl = null;
                }

                previewImage.removeAttribute('src');
                previewContainer.hidden = true;
            }

            fileInput.addEventListener('change', function() {
                clearPreview();

                const file = this.files && this.files[0];

                if (!file) {
                    return;
                }

                if (!allowedTypes.includes(file.type)) {
                    this.value = '';
                    alert('Please choose a JPG, PNG, or WEBP image.');
                    return;
                }

                if (file.size > maxFileSize) {
                    this.value = '';
                    alert('Your profile photo must be 2 MB or smaller.');
                    return;
                }

                previewUrl = URL.createObjectURL(file);
                previewImage.src = previewUrl;
                previewContainer.hidden = false;
            });

            window.addEventListener('beforeunload', clearPreview);
        });
    </script>
@endsection