# FlowTimeUp ⏱️

> Modern time tracking application built with Laravel and Vue.js. Track your time across tasks and projects with an intuitive, beautiful interface.

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.4-4FC08D?style=flat&logo=vue.js&logoColor=white)](https://vuejs.org)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.7-3178C6?style=flat&logo=typescript&logoColor=white)](https://www.typescriptlang.org)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.2-38B2AC?style=flat&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Screenshots](#screenshots)
- [Installation](#installation)
- [Project Structure](#project-structure)
- [Key Features Explained](#key-features-explained)
- [Performance Optimizations](#performance-optimizations)
- [Security](#security)
- [License](#license)

## 🎯 Overview

FlowTimeUp is a full-stack time tracking application designed to help individuals and teams track time spent on tasks and projects efficiently. Built with modern web technologies, it provides a seamless single-page application experience using Inertia.js, combining the power of Laravel's backend with Vue.js's reactive frontend.

### Why FlowTimeUp?

- ⚡ **Fast & Responsive** - Optimized build with code splitting and lazy loading
- 🎨 **Modern UI/UX** - Beautiful, intuitive interface built with Tailwind CSS
- 🔒 **Secure** - reCAPTCHA v3 protection, email verification, and secure authentication
- 📊 **Analytics** - Detailed reports and time tracking insights
- 🚀 **Production Ready** - Optimized for performance with caching and code splitting

## ✨ Features

### Core Functionality
- ⏱️ **Precise Time Tracking** - Track time down to the second with start/stop functionality
- 📝 **Task Management** - Create, edit, and organize tasks with priorities and due dates
- 📁 **Project Organization** - Group tasks by projects with custom colors
- 🏷️ **Tag System** - Categorize tasks with flexible tagging
- 📅 **Calendar View** - Visualize tasks and time sessions in calendar format
- 📊 **Reports & Export** - Generate detailed reports and export data as CSV
- 📋 **Task Templates** - Create reusable task templates for faster workflow

### User Experience
- 🎨 **Modern Design** - Clean, responsive interface that works on all devices
- 🔄 **Real-time Updates** - Instant UI updates without page refreshes
- 📱 **Mobile Friendly** - Fully responsive design optimized for mobile devices
- ⚡ **Fast Navigation** - Prefetching and optimized routing for instant page transitions
- 🎯 **Intuitive Dashboard** - Overview of today's time, weekly stats, and recent tasks

### Security & Authentication
- 🔐 **Secure Authentication** - Laravel Sanctum for API authentication
- ✉️ **Email Verification** - Built-in email verification system
- 🛡️ **reCAPTCHA v3** - Bot protection on registration
- 🔒 **Password Reset** - Secure password reset functionality

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 11.x
- **PHP**: 8.2+
- **Database**: MySQL/PostgreSQL/SQLite
- **Authentication**: Laravel Sanctum
- **API**: RESTful API with Inertia.js
- **Email**: Laravel Mail with custom templates
- **Validation**: Laravel Form Requests & DTOs

### Frontend
- **Framework**: Vue.js 3.4 (Composition API)
- **Language**: TypeScript 5.7
- **Build Tool**: Vite 6.0
- **UI Framework**: Tailwind CSS 3.2
- **State Management**: Pinia
- **Routing**: Inertia.js (SPA-like experience)
- **Icons**: Lucide Vue Next
- **Components**: Radix Vue, shadcn-vue
- **Date Handling**: Day.js

### Development Tools
- **Code Quality**: Laravel Pint, Prettier
- **Testing**: Pest PHP
- **Package Manager**: Composer, npm
- **Version Control**: Git

### Third-Party Services
- **reCAPTCHA v3**: Google reCAPTCHA for bot protection
- **Google Analytics**: Optional analytics integration
- **Email**: Configurable mail drivers (SMTP, Resend, etc.)

## 📸 Screenshots

> _Add screenshots of your application here_

## 🚀 Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- MySQL/PostgreSQL/SQLite

### Step 1: Clone the repository

```bash
git clone https://github.com/yourusername/flowtimeup.git
cd flowtimeup
```

### Step 2: Install PHP dependencies

```bash
composer install
```

### Step 3: Install Node dependencies

```bash
npm install
```

### Step 4: Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

Configure your `.env` file with database credentials and other settings:

```env
APP_NAME=FlowTimeUp
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=flowtimeup
DB_USERNAME=root
DB_PASSWORD=

# reCAPTCHA v3 (optional)
RECAPTCHA_SITE_KEY=your_site_key
RECAPTCHA_SECRET_KEY=your_secret_key

# Google Analytics (optional)
GOOGLE_ANALYTICS_ID=G-XXXXXXXXXX
```

### Step 5: Database setup

```bash
php artisan migrate
php artisan db:seed  # Optional: seed with sample data
```

### Step 6: Build assets

For development:
```bash
npm run dev
```

For production:
```bash
npm run build
```

### Step 7: Start the server

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

### Quick Start (Development)

Use the included dev script to run everything at once:

```bash
composer run dev
```

This will start:
- Laravel development server
- Queue worker
- Vite dev server

## 📁 Project Structure

```
flowtimeup/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Application controllers
│   │   └── Requests/          # Form request validation
│   ├── Models/                # Eloquent models
│   ├── DTOs/                  # Data Transfer Objects
│   └── Resources/             # API resources
├── database/
│   ├── migrations/            # Database migrations
│   └── seeders/               # Database seeders
├── resources/
│   ├── js/
│   │   ├── Components/        # Vue components
│   │   ├── Layouts/           # Layout components
│   │   ├── Pages/              # Inertia pages
│   │   ├── stores/             # Pinia stores
│   │   └── app.ts              # Application entry point
│   ├── css/                   # Stylesheets
│   └── views/                 # Blade templates
├── routes/
│   ├── web.php                # Web routes
│   └── auth.php               # Authentication routes
├── config/                    # Configuration files
└── public/                    # Public assets
```

## 🔑 Key Features Explained

### Time Tracking System
- **Precise Tracking**: Tracks time with millisecond precision
- **Session Management**: Automatically saves time sessions
- **Resume Functionality**: Continue tracking from where you left off
- **Time Aggregation**: Calculates total time per task, project, and day

### Task Management
- **CRUD Operations**: Full create, read, update, delete functionality
- **Priority Levels**: Low, medium, high priority system
- **Due Dates**: Set and track task deadlines
- **Bulk Operations**: Select and delete multiple tasks at once
- **Filtering & Sorting**: Filter by project, priority, completion status

### Project Organization
- **Color Coding**: Custom colors for visual organization
- **Project Statistics**: Track time spent per project
- **Task Grouping**: Organize related tasks under projects

### Reporting System
- **Time Reports**: Daily, weekly, monthly time breakdowns
- **Project Reports**: Time spent per project analysis
- **CSV Export**: Export data for external analysis
- **Visual Charts**: (Future feature) Charts and graphs

## ⚡ Performance Optimizations

### Frontend Optimizations
- **Code Splitting**: Automatic chunk splitting for vendor libraries
- **Lazy Loading**: Components loaded on demand
- **Prefetching**: Inertia.js prefetching for instant navigation
- **Asset Optimization**: Minified and compressed production builds
- **Tree Shaking**: Unused code elimination

### Backend Optimizations
- **Eager Loading**: Optimized database queries with eager loading
- **Caching**: Configuration and route caching
- **Query Optimization**: Efficient database queries
- **API Resources**: Transformed API responses

### Build Optimizations
- **Vite Configuration**: Optimized build settings
- **ESBuild Minification**: Fast JavaScript minification
- **CSS Optimization**: Purged unused CSS in production

## 🔒 Security

- **CSRF Protection**: Laravel's built-in CSRF protection
- **XSS Prevention**: Vue.js automatic escaping
- **SQL Injection Prevention**: Eloquent ORM protection
- **Authentication**: Secure password hashing (bcrypt)
- **reCAPTCHA v3**: Bot protection on registration
- **Email Verification**: Required email verification
- **Rate Limiting**: API rate limiting protection

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👨‍💻 Author

**Your Name**
- GitHub: [@yourusername](https://github.com/yourusername)
- LinkedIn: [Your LinkedIn](https://linkedin.com/in/yourprofile)
- Portfolio: [Your Portfolio](https://yourportfolio.com)

## 🙏 Acknowledgments

- Built with [Laravel](https://laravel.com)
- UI components from [Radix Vue](https://www.radix-vue.com/) and [shadcn-vue](https://www.shadcn-vue.com/)
- Icons by [Lucide](https://lucide.dev/)

---

⭐ If you find this project helpful, please consider giving it a star!
