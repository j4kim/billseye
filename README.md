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

Navigate to http://localhost:6412/.

Log in using `admin@billseye.ch` / `admin`.

## Doppio

Set `DOPPIO_AUTH_TOKEN` in `.env` for PDF generation.

## Links

[Data model](https://excalidraw.com/#json=5w4xcHQVsYui-TH08XDgj,vsY3RXcjTTr69bMCO8T1xw)
