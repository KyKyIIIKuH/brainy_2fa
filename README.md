# 2FA (Google Authenticator) for BrainyCP

Функционал простой, позволяет хоть как-то защитить страницу авторизации.

[b]Установка:[/b]
1) Скачать zip архив с репозитория https://github.com/KyKyIIIKuH/brainy_2fa (Скриншот)
2) Распаковать в папку панели /etc/brainy (можно исключить файлы LICENSE и README.md)
3) Открыть панель https://your_ip:8000/index.php?do=2fa (your_ip - ip адрес вашей панели)
4) При первом запуске создаем новый QR код

Также можно добавить ссылку в боковое меню (код вставлять в файл /etc/brainy/tpl/basic/index.tpl):
После строки 159, 313
[code]<a href="?do=2FA">2FA</a>[/code]

Ограничения
- Для работы модуля необходима установленная версия php 7.2.
