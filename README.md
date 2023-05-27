### Заг

## Установка
- ```composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev```
- ```yarn install```
- ```cp .env.example .env``` [заполнить конфиг]
- ```php artisan key:generate```
- ```php artisan jwt:secret```
- ```php artisan migrate```
- ```sail up``` [используется docker-контейнер]
