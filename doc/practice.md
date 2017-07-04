# Практические советы

## Вынесите создание Container в отдельный фаил.

При использование composer обычно в самом начале подключается vendor/autoload.php от composer для загрузки классов.

Я рекомендую создать файлик app/mydi.php который бы возвращал объект с **ContainerInterface** в места где его вызывают и 
подключать этот фаил сразу после автозагрузчика composer.

Благодаря этому файлу все настройки будут в одном месте, их легко и конфигурировать и переиспользовать.

## Используйте ::class

Используйте в качестве имен контейнеров статическое свойства ::class нужного вам класса или интерфейса.

## Установите плагин PHP-DI

Плагин [PHP-DI plugin](https://plugins.jetbrains.com/plugin/7694-php-di-plugin) позволяет автоматически делать 
автокомплит, если например используете ::class, это очень удобно!!!

## Недостающий функционал можно реализовать с помощью своих провайдеров!

Я постарался сделать все необходимое и поместить в библиотеку, но иногда может потребоваться то чего нет, например 
конфигурация в yml, или ini, или что то еще, вы всегда это можете 
[реализовать с помощью своего провайдера](customProvider.md).