# GhostOrchid Chat App

A modern real-time messaging web application built with Laravel, PHP, MySQL, Laravel Reverb, and Google OAuth 2.0.

## About the Project

GhostOrchid Chat is a modern web-based messaging application designed to provide users with a clean, responsive, and intuitive communication experience. The project focuses on real-time messaging, secure authentication, user management, and a professional interface.

Users can register using the application's authentication system or sign in with their Google accounts through Google OAuth 2.0.

## Features

- User registration and authentication
- **Google Sign-In using Google OAuth 2.0**
- Google account authentication through Laravel Socialite
- Real-time messaging powered by Laravel Reverb
- Private conversations between users
- User profile and avatar display
- Responsive chat interface
- Client management dashboard
- Administrative account controls
- Modern, dark-themed user interface

> Some features may depend on the current application configuration and implementation.

## Screenshots

Screenshots help users understand the application's interface and features.

### Login Page

![GhostOrchid Login](screenshots/login.png)

### Google Sign-In

![GhostOrchid Google Sign-In](screenshots/google-signin.png)

### Chat Interface

![GhostOrchid Chat](screenshots/chat.png)

Place your actual screenshots in the `screenshots` directory using the filenames listed above.

## Requirements

- PHP 8.2 or a compatible version
- Composer
- Node.js and npm
- MySQL
- XAMPP or another compatible local development environment
- Google Cloud project with OAuth 2.0 credentials for Google Sign-In
- Laravel Reverb configuration for real-time messaging

## Installation

```bash
git clone https://github.com/YOUR_USERNAME/ghostorchid-chat.git
cd ghostorchid-chat
```

### 2. Install Dependencies

Install the PHP and JavaScript dependencies.

```bash
composer install
npm install
```

### 3. Configure the Environment

Create your local environment configuration file.

```bash
cp .env.example .env
php artisan key:generate
```

On Windows, you can copy `.env.example` to `.env` manually.

Configure your MySQL database credentials in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ghostorchid_chat
DB_USERNAME=root
DB_PASSWORD=
```

Create the corresponding database in MySQL before running the migrations. Adjust the database name and credentials to match your local configuration.

### 4. Configure Google OAuth 2.0

GhostOrchid Chat supports Google Sign-In using Google OAuth 2.0.

1. Visit the [Google Cloud Console](https://console.cloud.google.com/).
2. Create or select a Google Cloud project.
3. Configure the OAuth consent screen.
4. Create an OAuth 2.0 Client ID and select **Web application**.
5. Configure the authorized redirect URI to match your Laravel callback route.
6. Add your Google OAuth credentials to `.env`.

Example environment variables:

```env
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

Ensure the callback URL matches the URI configured in Google Cloud Console and the routes defined in your application.

If Laravel Socialite is not yet installed, install it using:

```bash
composer require laravel/socialite
```

After updating your environment configuration, clear the cached configuration:

```bash
php artisan config:clear
```

**Security:** Never publish your real Google Client Secret or commit your `.env` file to GitHub. Use placeholder values in `.env.example`.

### 5. Run Database Migrations

```bash
php artisan migrate
```

If your application requires seed data, run the appropriate database seeder according to your project configuration.

### 6. Start the Application

Open separate terminals in the project directory.

**Terminal 1 — Laravel development server**

```bash
php artisan serve
```

**Terminal 2 — Vite development server**

```bash
npm run dev
```

**Terminal 3 — Laravel Reverb**

```bash
php artisan reverb:start
```

Configure the broadcasting and Reverb environment variables before starting the Reverb server.

If your application uses queued broadcasting jobs, start a queue worker when required:

```bash
php artisan queue:work
```

Open the local application at `http://127.0.0.1:8000`.

## Usage

1. Open GhostOrchid Chat in your browser.
2. Register an account or sign in with an existing account.
3. Alternatively, select **Sign in with Google** if Google OAuth is configured.
4. Open the chat interface.
5. Select a user or conversation.
6. Send messages and verify that new messages appear in real time.
7. Administrators can use the administrative dashboard to manage client accounts, according to their assigned permissions.

## Google OAuth 2.0

Google OAuth 2.0 allows users to authenticate through their Google accounts without creating a separate application password.

The integration requires:

- A Google Cloud project
- OAuth 2.0 Client ID and Client Secret
- An authorized redirect URI
- Laravel Socialite and the corresponding application routes
- Secure server-side handling of OAuth credentials

For local development, use the callback URL configured for your local server. A deployed application must use its actual domain and a matching HTTPS redirect URI.

## Technology Stack

- **Backend:** Laravel and PHP
- **Database:** MySQL
- **Authentication:** Laravel authentication and Google OAuth 2.0
- **OAuth Integration:** Laravel Socialite
- **Real-Time Communication:** Laravel Reverb
- **Frontend:** HTML, CSS, and JavaScript
- **Asset Bundling:** Vite
- **Local Development:** XAMPP

## Security Notes

- Never commit `.env` or other files containing real credentials.
- Keep Google OAuth Client Secrets private.
- Configure the correct OAuth redirect URIs.
- Use strong authentication and appropriate authorization checks.
- Do not publish screenshots containing passwords, access tokens, private messages, or personal user information.

## License

Specify your chosen project license here. If you have not selected a license, review the available open-source licenses before publishing one.

## Author

Developed as the GhostOrchid Chat project.
