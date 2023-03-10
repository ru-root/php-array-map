## Data Structures array

[![https://img.shields.io/badge/PHP-8.0-blue](https://img.shields.io/badge/PHP-8.0-blue)](https://www.php.net/releases/8.0/en.php)
[![https://img.shields.io/badge/PHP_Pecl_DS-1.4.0-blue](https://img.shields.io/badge/PHP_Pecl_DS-1.4.0-blue)](https://pecl.php.net/package/ds)


- Оболочка для ассоциативных массивов
    - Wrapper for associative arrays

    - [Эффективные структуры данных для `PHP`, представленные как альтернативы для типа `array`](https://www.php.net/manual/en/book.ds.php)

____

## Requirements

- PHP `8.0` или выше
    - Расширение PHP Pecl DS
    - Классы структур
        - Ds\Map
        - Ds\Set

## Installation

- Скачайте эту библиотеку и подключите файл в вашем скрипте.
    - Download this library and require the necessary file directly in your script.


``` php
<?php

require __DIR__ . '/path/vendor/ArrayMap.php';
```

## Creation

- Создайте новый класс наследуемый от класса ArrayMap

``` php
<?php

require __DIR__ . '/path/vendor/ArrayMap.php';

class Config extends ArrayMap
{
}

class Users extends Config
{
}

class Page extends Config
{
}
```

## Usage

``` php
<?php

$users = (new Users)
    ->load(__DIR__ .'/users.php');
             $users->setDima(['email' => '']);

$page =
    (new Page)
        ->setConfig(
            (new Config)
                ->load(__DIR__ .'/config.php')
        )
        ->setUsers($users)
        ->setTitle('List users')
        ->setDescription('All users site')
        ->setTemplate(__DIR__ .'/template.php');

// Чистые данные в шаблон, без примесей path template и т. д.
echo $page->render($page->unsetTemplate());
```

## Доступ к данным

- Пример:

``` php
<?php

foreach ($users as $name => $value) {
    echo nl2br($name .': '.$value->email .PHP_EOL, FALSE);
}
```

- Вернёт:

```
Dima: dima@example.com
Vasya: vasya@example.com
```

- Пример: Получить или удалить

``` php
<?php

echo $users->getDima();       // Получить
echo $users->Dima;            // Получить
echo $page->users->getDima(); // Получить
echo $page->users->Dima;      // Получить
echo $users->unsetDima();     // Получить и удалить
```

- Или так

``` php
<?php

echo $users->Dima->email;   // Получить данные ключа email
unset($users->Dima->email); // Удалит ключ email
```

- Проанализировать построение данных:

``` php
<?php

var_dump($page);
```

## Available Runtimes
### PHP-FPM and traditional web servers
These runtimes are for PHP-FPM and the more traditional web servers one might use for local development.



