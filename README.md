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

echo $page->render($page->unsetTemplate());
```

## Доступ к данным

- Пример:

``` php
<?php

foreach ($users->page->users as $name => $value)
{
    echo nl2br(ucfirst($name) .': '.$value->email .PHP_EOL, FALSE);
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

$dima = $users->page->users->getDima(); // Получить
$users->page->users->unsetDima(); // удалить
```

- Или так

``` php
<?php

echo $users->page->users->dima->email;
unset($users->page->users->dima->email); // Удалит ключ email
```

- Проанализировать построение данных:

``` php
<?php

var_dump($users);
```

## Available Runtimes
### PHP-FPM and traditional web servers
These runtimes are for PHP-FPM and the more traditional web servers one might use for local development.

### PSR-7 and PSR-15
Use the popular PSR architecture.


