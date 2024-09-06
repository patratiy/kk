# СЕРВИС АНАЛИТИКА (ERP мойсклад)

### установка миграций

```bash
php artisan migrate
```

### .ENV

настройка env окружения, открыть env.example, и скопировать три последние переменные в .env, заполнить их нужными данными

`TIME_OUT_BETWEEN_REQUEST` - время в мсек между запросами для заказов и бандлов, `MOY_SKLAD_TOKEN`, `BASE_URL_MOY_SKLAD`, `NAME_SYNC_ENTITY_FOR_FULL_LOAD` - в ней через запятую указываются синхр. сущности, которые нужно синхронить целиком, при первой заливке это каталог и бандлы

### настройка CRON

для функционирования shedule команд прописать на сервере в crontab команду

```bash
* * * * * cd /path_to_your_project_on_server && php artisan schedule:run >> /dev/null 2>&1
```

### запуск синхр. вручную

через artisan возможно запускать команды для синхронизации принудительно

```bash
php artisan sync:moysklad orders
```

## кэширование конфикурации

после того как env переменные были заполнены, конфигурацию можно закэшировать

```bash
php artisan config:cache
```

для внесения изменений, удалить кэш, и выполнить операцию выше

```bash
php artisan config:clear
```





