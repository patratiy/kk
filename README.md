# СЕРВИС АНАЛИТИКА (ERP мойсклад)

### Заполнить актуальные подключения к БД в .env

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=maxmoll
DB_USERNAME=maxmoll
DB_PASSWORD='_X#675:w88F8'
```

### установка миграций

```bash
php artisan migrate
```

### .ENV

настройка env окружения, открыть env.example, и скопировать три последние переменные в .env, заполнить их нужными данными

`TIME_OUT_BETWEEN_REQUEST` - время в мсек между запросами для заказов и бандлов

`MOY_SKLAD_TOKEN` - токен авторизации для ERP МойСклад 

`BASE_URL_MOY_SKLAD` - базовый url для ERP МойСклад 

`NAME_SYNC_ENTITY_FOR_FULL_LOAD` - через запятую указываются синхр. сущности, которые нужно синхронить целиком, при первой заливке это каталог и бандлы

`DAYS_INTERVAL_UPDATE` - временной отрезок от настоящего момент времени в днях, для выполнения обновления сущностей МойСклад

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





