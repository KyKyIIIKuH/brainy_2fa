# 2FA (Google Authenticator) for BrainyCP 1.08

Функционал простой, позволяет хоть как-то защитить страницу авторизации.

на странице ввода 2FA кода, будет Ваш логотип (не переживайте)  ;) 
![alt tag](https://ploader.ru/brainy_2fa/photo/auth.png "Страница ввода 2FA кода")​

# Установка:
1) Скачать zip архив
2) Распаковать в папку панели /etc/brainy (можно исключить файлы LICENSE и README.md)
3) В файле /etc/brainy/tpl/basic/auth/auth.tpl

Найти:
```html
<form class="auth-form" action="auth.php" method="post">
```
Заменить на это:
```html
<form class="auth-form" action="auth_2FA.php" method="post">
```

4) Открыть панель https://your_ip:8000/index.php?do=2FA (your_ip - ip адрес вашей панели)
5) Создаем новый QR код
6) Запускаем приложение Google Authenticator или Authy, сканируем QR Code

Также можно добавить ссылку в боковое меню (код вставлять в файл /etc/brainy/tpl/basic/index.tpl):

После строки 162, 316
```html
<a href="?do=2FA">2FA</a>
```
# Восстановление ключа (если был потерян)
Открываем файл /etc/brainy/data/2FA/users.dat
- Находим нужного пользователя и смотрим объект secret и отдаем этот код пользователю
- Чтобы отключить 2FA пользователю, удаляем пользователя из файла

# Ограничения
- Для работы модуля необходима установленная версия php 7.2.

# История изменений
30.12.2020
- добавил фокус поля ввода кода 2FA

12.12.2020
- исправлена ошибка при авторизации через 2FA
