# FlowTimeUp

Modern time tracking application built with Laravel and Vue.js.

## Tech Stack

- **Backend:** Laravel 11, PHP 8.2+
- **Frontend:** Vue.js 3, TypeScript, Tailwind CSS
- **Database:** MySQL/PostgreSQL/SQLite

## Features

- Time tracking with start/stop timer
- Manual time entry
- Projects & tags organization
- Billable vs non-billable hours
- Recurring tasks
- Calendar view
- Reports & CSV export
- Dark mode
- Mobile responsive

## Installation

```bash
# Clone repository
git clone https://github.com/your-repo/flowtimeup.git
cd flowtimeup

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate

# Build assets
npm run build

# Start server
php artisan serve
```

## Development

```bash
npm run dev
```

## License

MIT
