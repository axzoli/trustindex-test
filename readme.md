# Company Review

A simple Symfony application for collecting and reviewing company feedback. Users can add reviews, browse them, and inspect per-company statistics and trends.

## Requirements

- PHP 8.2+
- Composer
- Symfony CLI
- MySQL or SQLite

## Installation

```bash
composer install
```

## Run the application

```bash
symfony serve
```

Then open: http://localhost:8000

## Database setup

Create the database and run migrations:

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

Load sample data:

```bash
php bin/console doctrine:fixtures:load --no-interaction
```

## Testing

Run the test suite:

```bash
php bin/phpunit
```

## Work log

- Added fixtures for sample reviews
- Implemented review listing and search
- Added company statistics page
- Added detail page with charts for rating trends and review volume
- Added functional and unit tests