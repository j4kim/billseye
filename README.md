# Bill's Eye

## Set up

```
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
```

## Run

```
composer run dev
```

Navigate to http://localhost:6412/admin.

Log in using `admin@billseye.ch` / `admin`.

## Doppio

Set `DOPPIO_AUTH_TOKEN` in `.env` for PDF generation.

## Links

[Data model](https://excalidraw.com/#json=tYUg0MC5Tl_PfWEJw8fM0,2phQXxSpIToew71RWJEy7A)
