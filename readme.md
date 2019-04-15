#Currency Converter

## Project setup
```
docker-compose run --no-deps --rm devapp composer install
cp .env.example .env
docker-compose run --no-deps --rm devapp php artisan key:generate
```

### Run server
```
docker-composer up -d
```

### Run tests
```
docker-compose run --no-deps --rm devapp vendor/bin/phpunit
```

## API

##### POST /api/conversion
Request Body:
```
{
    "from": "USD",
    "to": "EUR",
    "amount": 150.50
}
```
Response Body:
```
{
    "total": 133.2858
}
```
