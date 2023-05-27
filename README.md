## kpo-HW4: Система обработки заказов ресторана
Шубников Андрей Кириллович, БПИ216 ([akshubnikov@edu.hse.ru](mailto:akshubnikov@edu.hse.ru))

### Установка
- ```composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev```
- ```yarn install```
- ```cp .env.example .env``` [заполнить конфиг]
- ```php artisan key:generate```
- ```php artisan jwt:secret```
- ```php artisan migrate```
- ```sail up``` [используется docker-контейнер]
- ```sail artisan schedule:run``` [запускам все job, нужно поставить запуск команды раз в минуту в crontab]

### Информация
- Старался писать подробный код, если есть какие-то сложные моменты, то я оставлял комментарии в коде. 
- Сделал [postman](https://github.com/shbov/hse-java-hw4/blob/master/postman.json) для удобства, там все понятно (создаем аккаунт, входим в него и получаем токен, дальше работаем с ним)
- Есть таблица `sessions` для удобства отслеживания токенов (в проде ее не должно быть, чисто для дебага)
- Все данные валидируются, в случае ошибок показываются ошибки и соответствующие коды
- ```password_hash``` -> ```password```
- Наличие блюда отслеживается с помощью observer'a (если при изменении количества оно становится <=0, то ```is_available``` = false)
- Чуть-чуть изменил [миграции](https://github.com/shbov/hse-java-hw4/tree/master/database/migrations), чтобы они соответствовали фреймворку 
- Можно было сделать еще лучше: вынести бизнес-логику в сервисы, сделать отдельный класс для работы с сообщениями, но тут кода мало, так что это было бы сильно дольше. 

### Архитектура
Выбрал фреймворк Laravel (MVC-архитектура), т.к. mvc идеально подходит для нашей задачи: есть сущности, контроллеры (view нет, но опустим это)

### Валидация данных
- Данные валидируются с помощью [Request'ов](https://github.com/shbov/hse-java-hw4/tree/master/app/Http/Requests), в [Controller'ы](https://github.com/shbov/hse-java-hw4/tree/master/app/Http/Controllers) попадаем, если все данные корректны.
- Для корректной работы приложения с разными доступами использую Middleware (```role: ...```, ```auth:api```)

### Имитация работы
Сделал отдельный [Job](https://github.com/shbov/hse-java-hw4/blob/master/app/Jobs/ServeOrder.php), который имитирует работу шефа: берет заказ из пулла, что-то с ним делает (с 33% шансом отменяет, иначе его завершает). Сообщения пишутся в [логи](https://github.com/shbov/hse-java-hw4/tree/master/storage/logs), статусы заказов изменяются соответственно.
