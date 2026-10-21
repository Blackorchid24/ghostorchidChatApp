
# GhostOrchid Chat

A modern real-time messaging web application built with Laravel, PHP, MySQL, and Laravel Reverb.

## About the Project

GhostOrchid Chat is designed to provide users with a clean, responsive interface for communication. This project aims to deliver a modern messaging experience with a focus on usability, security, and real-time interaction.

## Features

- User registration and authentication
- Real-time messaging with Laravel Reverb
- Private conversations
- User profile and avatar display
- Responsive chat interface
- Client management dashboard
- Administrative account controls

> Features listed here should match what is implemented in the current version.

## Screenshots

### Login Page

![GhostOrchid Login](screenshots/login.png)

### Chat Interface

![GhostOrchid Chat](screenshots/chat.png)

## Requirements

- PHP 8.2 or a compatible version
- Composer
- Node.js and npm
- MySQL
- XAMPP or another compatible local development environment

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/YOUR_USERNAME/ghostorchid-chat.git
cd ghostorchid-chat
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Configure the environment

```bash
cp .env.example .env
php artisan key:generate
```

On Windows, you can also copy `.env.example` to `.env` manually.

Configure your MySQL database credentials in `.env`.

### 4. Run database migrations

```bash
php artisan migrate
```

### 5. Start the application

Run each command in a separate terminal:

```bash
php artisan serve
```

```bash
npm run dev
```

For real-time messaging, start Laravel Reverb in another terminal:

```bash
php artisan reverb:start
```

Configure the broadcast settings in `.env` before starting Reverb.

## Usage

1. Open the local application in your browser.
2. Register an account or sign in.
3. Open the chat interface.
4. Select a user or conversation.
5. Send messages and verify real-time updates.

## Technology Stack

- Laravel
- PHP
- MySQL
- Laravel Reverb
- JavaScript
- HTML and CSS
- Vite

## License

Specify your chosen project license here.
