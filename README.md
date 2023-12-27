# Тестовые задания
Тестовые задания находятся в папках task1 и task2 соответственно
## Задание 1 (решенеие)
```sh
SELECT users.id AS ID, CONCAT(users.first_name, ' ', users.last_name) AS Name, books.author AS Author, GROUP_CONCAT(DISTINCT books.name SEPARATOR ', ') AS Books
FROM users
JOIN user_books ON users.id = user_books.user_id
JOIN books ON user_books.book_id = books.id
JOIN books b ON b.author = books.author
WHERE 
    TIMESTAMPDIFF(YEAR, users.birthday, CURDATE()) BETWEEN 7 AND 17
    AND TIMESTAMPDIFF(DAY, user_books.get_date, user_books.return_date) <= 14
GROUP BY users.id
HAVING COUNT(DISTINCT user_books.book_id) = 2
```

## Задание 2
### Описание
Задача написана на PHP 8.1 с использованием фреймворка Laravel
### Развертывание и тестирование
1. Запустите Docker-контейнер для тестирования
```sh
docker-compose up
```
либо
```sh
docker build -t app .
docker run -p 8000:8000 app
```
2. Приложение будет доступно по адресу http://localhost:8000/
3. Проверьте запросы любым удобным способом (каждый запрос должен быть авторизован с помощью заголовка "Authorization: Bearer 7ec82c9d-3908-4b00-a3e0-b5c52071fabd-58cc9dae250aa4-9cea6dd430a7")
