Downloads
=========

Вспомогательный модуль по отправке файлов для Eresus CMS.

Класс Downloads_File
--------------------

Обёртка для файла, отпарвляемого бразуеру. Основная задача класса — отправка правильных заголовков.

Пример:

```php
<?php
$file = new Downloads_File();
$file->setContents('foo');
$file->setFilename('example.txt');
$file->setContentType('text/plain');
$file->setCharset('utf-8');
$file->send();
```
