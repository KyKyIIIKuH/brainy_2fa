# 2FA (Google Authenticator) for BrainyCP

Функционал простой, позволяет хоть как-то защитить страницу авторизации.

# Установка:
1) Скачать zip архив с репозитория https://github.com/KyKyIIIKuH/brainy_2fa (Скриншот)
2) Распаковать в папку панели /etc/brainy (можно исключить файлы LICENSE и README.md)
3) советую поменять название файла auth.php на другое
[*]в файле /etc/brainy/auth_2FA.php, указываем название файла которое указали в 3 шаге
```html
$file_auth_brainy = "auth_brainy.php";
```
4) В файле /etc/brainy/tpl/basic/auth/auth.tpl
Найти:
```html
<form class="auth-form" action="auth.php" method="post">
```
Заменить на это:
```html
<form class="auth-form" action="auth_2FA.php" method="post">
```

5) Открыть панель https://your_ip:8000/index.php?do=2fa (your_ip - ip адрес вашей панели)
6) Создаем новый QR код

Также можно добавить ссылку в боковое меню (код вставлять в файл /etc/brainy/tpl/basic/index.tpl):
После строки 159, 313
```html
<a href="?do=2FA">2FA</a>
```

# Ограничения
- Для работы модуля необходима установленная версия php 7.2.
