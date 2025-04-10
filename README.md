# Тестовое задание на позицию php разработчика

## Запуск приложения
После клонирования репозитория, в папке проекта:

    docker compose up -d
    docker exec -it api_app bash
    composer install

Создание .env файла по примеру .env.example

    php artisan migrate

### Функционал приложения:

- Создание задачи - POST http://localhost:8888/api/tasks
- Получение списка задач - GET http://localhost:8888/api/tasks
- Получение конкретной задачи - GET http://localhost:8888/api/tasks{id}
- Обновление задачи - PATCH http://localhost:8888/api/tasks{id}
- Удаление задачи - DELETE http://localhost:8888/api/tasks{id}  
- Swagger документация - http://localhost:8888/api/documentation#/
  
### Дополнительные возможности:

- Заполнение БД тестовыми данными:


    php artisan DB:seed

- Запуск функциональных тестов:


    php artisan test
