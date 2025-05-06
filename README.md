# Bill's Eye

## Set up

```
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
```

## Create admin user

```
php artisan orchid:admin
```

## Run

```
composer run dev
```

## Links

[Data model](https://excalidraw.com/#json=wn0qeST1bgySaW-QlSFWE,eaTjHU1zXbdKaQt6k4cKVg)
