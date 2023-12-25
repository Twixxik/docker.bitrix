## Инструкция по запуску Битрикса в докере
### Запуск
Выполните команду в терминале PhpStorm
```bash
bash bin/docker
```

### Разворачиваем бэкап
1. Клонируем репозиторий с проектом битрикса, нужна будет ссылка на репу
    ```bash
    bash bin/init
    ```

2. Разворачиваем БД. Нам нужен дамп базы. Используйте [bxdump](https://github.com/Twixxik/bxdump) и закиньте sql файлы из архива в `.volumes/mysql`, затем запустите команду
    ```bash
    bash bin/mysql
    ```

### Настраиваем PhpStorm

Рекомендует исключить следующие папки из индекса (Перейдите в "Settings -> Directories"):

```
/bitrix
/local
/images
/upload
/vendor
```

И добавьте эти папки в индекс для поиска по ним, перейдите в "Settings -> PHP -> Include Path".
```
/bitrix/modules
/bitrix/components
/bitrix/js
/bitrix/css
```

### Debug and Profiling

Существуют контейнеры `php-debug` и `php-profile`, определенные в конфигурации `docker-compose` с включенным `xdebug`.

Интерфейс Nginx настроен на прокси-запросы по заголовкам, указанным в службе `php-debug` и `php-profile`

1. Убедитесь что контейнеры `php-debug` и `php-profile` запущены (профили `debug` добавлен в переменную `COMPOSE_PROFILES` в файле `.env`)
2. Установите браузерное расширение [Xdebug Helper](https://www.jetbrains.com/help/phpstorm/browser-debugging-extensions.html). Смотрите [Полный гайд по расширению](https://www.jetbrains.com/help/phpstorm/debugging-with-phpstorm-ultimate-guide.html)
3. В настройках расширения укажите ключ `docker` во все триггеры
4. Включите нужный режим в расширении и наслаждайтесь!

### XHProf
Для профилирования используйте php-функцию `xhprof_start();` до профилируемого кода и `xhptof_stop();` после.

После выполнения кода на страницу будет выведено окно в нижнем левом углу экрана с ссылкой на результат.
