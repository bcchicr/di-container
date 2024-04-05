# Dependency Injection Container

Простой контейнер зависимостей, соответствующий стандарту PSR-11. Реализует автосвязывание и кэширование.

- [Требования](#Требования)
- [Установка](#Установка)
- [Использование](#Использование)

## Требования

- PHP 8.2 или более поздняя версия

## Установка

Установка через Composer:

```shell script
composer require bcchicr/di-container
```

## Использование

### Создание контейнера:

```php
use Bcchicr\Container\Container;

$container = new Container();
```

### Регистрация зависимостей:

```php
/**
 * Зарегистрировать вызываемое значение в качестве определения зависимости
 *
 * @param string $id
 * @param callable $callback
 * @return void
 */
$container->register($id, function(ContainerInterface $container) {
    return 'dependency';
});

/**
 * Зарегистрировать зависимость непосредственно
 *
 * @param string $id
 * @param mixed $instance
 * @return void
 */
$container->instance($id, 'instance');
```

### Проверка на возможность получения зависимости из контейнера:

```php
/**
 * Возвращает 'true` если зависимость с указанным ID может быть получена из контейнера
 *
 * @param string $id
 * @return bool
 */
$container->has($id);
```

### Получение зависимости из контейнера:

```php
/**
 * Возвращает зависимость с указанным ID
 *
 * @param string $id
 * @return mixed
 * @throws Bcchicr\Container\Exception\NotFoundException Если ID неизвестен контейнеру
 * @throws Bcchicr\Container\Exception\ContainerGetException Если не получается получить зависимость из определения
 */
$container->get($id);
```

### Два последовательных вызова `get()` с одинаковым ID возвращают тот же объект:

```php
$dep1 = $container->get($id);
$dep2 = $container->get($id);

$dep1 === $dep2 // true
```

### Реализовано автосвязывание объекта по имени класса:

```php
$dep1 = $container->get(SomeClass::class);

```

Оно возможно, если:

- Конструктор получаемого объекта не содержит параметров
- Параметры конструктора получаемого объекта имеют значения по умолчанию
- Параметры конструктора являются объектами, доступными для получения из контейнера (В т.ч. через автосвязывание)
