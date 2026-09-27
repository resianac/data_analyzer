# Sources

Сервис `Sources` отвечает за получение данных из внешних источников и их передачу в бизнес-модули приложения.

Сейчас модуль `Catalog` работает с четырьмя источниками:

| Источник | Категории | Количество jobs |
|---|---|---:|
| Enter | `TV`, `FRIDGE` | 2 |
| Cactus | `TV` | 1 |
| Maximum | `TV` | 1 |
| Ultra | `TV`, `FRIDGE` | 2 |

Итого при полном очередном запуске создаётся 6 jobs.

`Bomba`, `Marketplace999` и `RabotaMd` остаются клиентами, но не зарегистрированы в `Catalog`. Команда каталога их не запускает и не считает каталоговыми источниками.

## Главный принцип архитектуры

Зависимости направлены в одну сторону:

```text
Module -> Client -> Shared
```

- `Clients` знают, как общаться с конкретным внешним сайтом и преобразовать его ответ.
- `Modules` определяют бизнес-сценарий: какие клиенты объединить, какие категории запустить, как сохранить результат и как организовать очередь.
- `Shared` содержит нейтральные технические компоненты без привязки к конкретному бизнес-модулю.

Клиент не должен импортировать классы из `Modules`. Благодаря этому один клиент можно использовать в другом модуле или независимо от остальных источников.

## Структура

```text
Sources/
├── Clients/
│   ├── BaseClient.php
│   ├── Enter/
│   ├── Cactus/
│   ├── Maximum/
│   ├── Ultra/
│   ├── Bomba/
│   ├── Marketplace999/
│   └── RabotaMd/
│
├── Modules/
│   └── Catalog/
│       ├── Application/
│       │   ├── Import/
│       │   └── Run/
│       ├── Contracts/
│       ├── Domain/
│       │   └── Results/
│       └── Infrastructure/
│           ├── Persistence/
│           ├── Providers/
│           └── Queue/
│
└── Shared/
    ├── Configuration/
    ├── Contracts/
    ├── Data/
    ├── Enums/
    ├── Factories/
    ├── Formatting/
    ├── Normalization/
    └── Transport/
```

## Clients

Каждая директория внутри `Clients` является вертикальным срезом одного внешнего источника.

Например, `Clients/Enter` содержит:

- `EnterClient` — формирует адрес запроса, вызывает transport и возвращает страницу результата;
- `EnterConfig` — URL, timeout, HTTP headers, задержки, шаг пагинации и отслеживаемые метрики;
- `Enums/EnterSearchParam` — поддерживаемые категории и их URL-фрагменты;
- `Filters/Variables` — CSS selectors для HTML-парсинга;
- `Data/EnterData` — DTO ответа Enter;
- `Normalizers` — формирование общего `match_id`;
- `Adapters` — преобразование DTO источника в общий `EntityData`.

Клиент не управляет очередью, не запускает другие источники и не сохраняет сущности в базу.

### BaseClient

`BaseClient` хранит transport, конфигурацию, имя и тип источника.

Конфигурация определяется по соглашению об именах:

```text
name = enter
config = App\Services\Sources\Clients\Enter\EnterConfig
```

Transport получает найденную конфигурацию через `setTransport()`.

### Результат страницы

Активные каталоговые клиенты возвращают `Shared\Data\PageResult`:

```php
new PageResult(
    entities: $entities,
    hasNextPage: true,
);
```

Это отделяет получение страницы от решения, куда и как сохранять данные.

## Shared

`Shared` содержит компоненты, которыми могут пользоваться любые клиенты и модули.

### Transport

- `HtmlTransport` выполняет HTTP GET и извлекает данные из HTML по CSS selectors.
- `GraphQLTransport` отправляет GraphQL-запрос и проверяет HTTP-, JSON- и GraphQL-ошибки.
- `BaseTransport` и `TransportInterface` задают общий контракт и подключают конфигурацию.

Transport ничего не знает о Catalog, категориях и сохранении сущностей.

### VariableFactory

`VariableFactory` находит класс selectors по типу источника и фильтру.

Пример для Enter:

```text
SourceClientType::ENTER + EntityFilter::ENTER_ENTITY
    -> Clients\Enter\Filters\Variables\EnterEntityVariables
```

Метод `byItems()` найденного класса возвращает selectors для списка товаров, полей товара и кнопки следующей страницы.

### Общие DTO и enum

В `Shared/Data` находятся нейтральные результаты и атрибуты, включая `PageResult` и `ProductAttributes`. В `Shared/Enums` находятся общие типы источников, фильтров и метрик.

## Модуль Catalog

Catalog объединяет только Enter, Cactus, Maximum и Ultra.

### Domain

`CatalogSource` является закрытым списком источников, доступных команде:

```php
enum CatalogSource: string
{
    case ENTER = 'enter';
    case CACTUS = 'cactus';
    case MAXIMUM = 'maximum';
    case ULTRA = 'ultra';
}
```

`CategoryRunResult` хранит источник, категорию, время выполнения и исключение категории. `SourceRunResult` агрегирует результаты всех категорий источника.

### CatalogProvider

Provider является адаптером между Catalog и конкретным клиентом. Он сообщает:

- какой источник представляет;
- какие категории поддерживает;
- как выполнить импорт одной категории сейчас;
- какую job создать для отложенного запуска.

Например, `EnterCatalogProvider` возвращает `EnterSearchParam::cases()`, синхронно запускает `ImportEnterCatalogCategory` или создаёт `SearchEnterCategoryJob`.

Проверка типа категории выполняется внутри provider. Передать категорию другого источника нельзя: будет выброшен `InvalidArgumentException`.

### Регистрация providers

`CatalogSourcesServiceProvider` регистрирует:

- реализацию `CatalogEntitySink`;
- singleton `CatalogProviderRegistry`;
- четыре активных provider.

`CatalogProviderRegistry` индексирует providers по значению `CatalogSource`. Координатор получает provider из реестра и не содержит `match` по конкретным классам источников.

### Импорт категории

Общий сценарий находится в `BaseCatalogCategoryImportAction`:

```text
1. Создать конкретный client.
2. Запросить страницу, начиная с page = 1.
3. Получить PageResult.
4. Передать entities в CatalogEntitySink.
5. Если есть следующая страница — выдержать задержку из config.
6. Увеличить page на config.limit и повторить.
```

Конкретный класс импорта, например `ImportEnterCatalogCategory`, определяет только:

- enum категории;
- `EntityFilter`;
- нужный client и transport.

### Сохранение

`EloquentCatalogEntitySink` является инфраструктурной реализацией `CatalogEntitySink`.

Он передаёт коллекцию в Laravel Pipeline и использует:

- `StoreEntitiesPipe`;
- `EntityMasterRepository` с текущей категорией;
- `MetricTracker` с `metric_fields` из конфигурации источника.

Таким образом, получение данных и их сохранение разделены контрактом. Для тестов или другого хранилища можно подставить другую реализацию `CatalogEntitySink`.

## Запуск из консоли

Основная команда:

```bash
php artisan sources:catalog:run
```

Без списка источников запускаются все четыре источника в порядке `CatalogSource::cases()`.

### Выбор источников

```bash
php artisan sources:catalog:run enter
php artisan sources:catalog:run enter ultra
php artisan sources:catalog:run cactus maximum
```

Повторяющиеся аргументы удаляются. Регистр не важен. Неизвестный источник завершает команду с кодом `INVALID` до запуска импорта.

Допустимые значения:

```text
enter, cactus, maximum, ultra
```

## Синхронный режим

Синхронный режим используется по умолчанию:

```bash
php artisan sources:catalog:run --mode=sync
```

Путь выполнения:

```text
RunCatalogSourcesCommand
    -> CatalogRunCoordinator::runSync()
        -> CatalogProviderRegistry::get()
            -> CatalogSourceRunner::run()
                -> CatalogProvider::import()
                    -> Import{Source}CatalogCategory::handle()
                        -> Client::search()
                            -> Transport::call()
                        -> CatalogEntitySink::store()
```

Источники и категории выполняются последовательно.

Ошибка одной категории перехватывается `CatalogSourceRunner`. После неё runner продолжает остальные категории и источники. По завершении:

- команда возвращает `SUCCESS`, если успешны все категории;
- команда возвращает `FAILURE`, если хотя бы одна категория завершилась ошибкой.

### Консольный вывод

`ConsoleCatalogRunObserver` получает события начала источника и завершения категории:

```text
[1/4] Enter
  ✓ TV               completed     18.4s
  ✓ FRIDGE           completed     22.1s

[2/4] Cactus
  ✓ TV               completed     14.7s

[3/4] Maximum
  ✗ TV               failed         2.3s  HTTP 503
```

Application-слой не зависит от консоли. При запуске не из команды можно не передавать observer — будет использован `NullCatalogRunObserver`.

## Запуск через очередь

```bash
php artisan sources:catalog:run --mode=queue
```

Можно выбрать connection и имя очереди:

```bash
php artisan sources:catalog:run enter ultra --mode=queue --connection=sources --queue=sources
```

`CatalogRunCoordinator::dispatchBatch()` создаёт отдельную job на каждую категорию выбранных источников и объединяет их в один Laravel batch.

Путь выполнения одной job:

```text
Search{Source}CategoryJob
    -> BaseCatalogSourceJob::handle()
        -> Import{Source}CatalogCategory::handle()
            -> тот же импорт, что используется в sync-режиме
```

Sync и queue не содержат две разные реализации парсинга. Они сходятся в одном классе импорта категории.

### Параметры jobs

`BaseCatalogSourceJob` задаёт общие правила:

| Параметр | Значение |
|---|---:|
| Попытки | 3 |
| Timeout | 900 секунд |
| Backoff | 60 и 300 секунд |
| Fail on timeout | да |

Batch использует `allowFailures()`: окончательное падение одной job не отменяет jobs других категорий.

Если batch отменён, job завершится до запуска импорта.

Каждая job получает tags:

```text
catalog-source:enter
category:TV
run:<run-id>
```

### Worker

Для стандартной конфигурации:

```bash
php artisan queue:work sources --queue=sources --timeout=900 --tries=3
```

Connection `sources` использует database driver. Основные настройки окружения:

```dotenv
# Необязательно: отдельное подключение БД для очереди Sources
SOURCES_DB_QUEUE_CONNECTION=mysql
SOURCES_QUEUE=sources
SOURCES_QUEUE_RETRY_AFTER=1200
```

Таблицы `jobs`, `job_batches` и `failed_jobs` создаются стандартной миграцией jobs.

Важно: успешное завершение команды в queue-режиме означает, что batch создан. Фактический результат импорта появляется после обработки jobs worker-ом.

## Run ID и логирование

Каждый запуск команды получает ULID `runId`. Он передаётся координатору, batch и каждой job и позволяет собрать события одного запуска.

Основной канал:

```php
Log::channel('sources.run')
```

Логи записываются daily-драйвером в:

```text
storage/logs/sources/runs-YYYY-MM-DD.log
```

По умолчанию файлы хранятся 14 дней. Настройки:

```dotenv
SOURCES_LOG_DAYS=14
SOURCES_LOG_LEVEL=info
```

Для sync фиксируются:

- начало и завершение общего запуска;
- результат каждого источника;
- результат каждой категории;
- `duration_ms` и исключение.

Для queue фиксируются:

- создание batch;
- начало, успешное завершение и ошибка каждой попытки job;
- окончательное падение job;
- завершение batch и количество failed/pending jobs.

## Как добавить новый источник в Catalog

### 1. Создать или подготовить client

```text
Clients/NewSource/
├── NewSourceClient.php
├── NewSourceConfig.php
├── Adapters/
├── Data/
├── Enums/NewSourceSearchParam.php
├── Filters/Variables/
└── Normalizers/
```

Client должен возвращать `PageResult` и не импортировать классы Catalog.

### 2. Добавить источник в CatalogSource

```php
case NEW_SOURCE = 'new-source';
```

### 3. Создать provider infrastructure

```text
Modules/Catalog/Infrastructure/Providers/NewSource/
├── NewSourceCatalogProvider.php
├── ImportNewSourceCatalogCategory.php
└── Jobs/SearchNewSourceCategoryJob.php
```

Provider должен реализовать `CatalogProvider`, импорт — расширить `BaseCatalogCategoryImportAction`, job — расширить `BaseCatalogSourceJob`.

В job значение источника возвращается из enum:

```php
protected function source(): string
{
    return CatalogSource::NEW_SOURCE->value;
}
```

### 4. Зарегистрировать provider

Добавить provider в `CatalogSourcesServiceProvider`:

```php
$app->make(NewSourceCatalogProvider::class),
```

После регистрации команда автоматически получит категории provider и сможет создать для них jobs.

### 5. Добавить тесты

Минимально необходимо проверить:

- количество jobs и их классы;
- запуск выбранного источника;
- отображение категорий и ошибок;
- пагинацию и преобразование ответа client-а.

## Как добавить другую группу источников

Если клиенты относятся не к каталогу, новый сценарий создаётся отдельным модулем:

```text
Modules/
├── Catalog/
├── Jobs/
└── SomeIndependentWorkflow/
```

Новый модуль должен иметь собственные enum, contracts, coordinator, providers, jobs и команду. Не следует добавлять не-каталоговый источник в `CatalogSource` только ради общего запуска.

Один client может использоваться несколькими модулями, если каждый модуль предоставляет собственный provider-адаптер. Независимый client может вызываться напрямую и вообще не иметь provider.

## Проверки

Тесты Sources:

```bash
php artisan test tests/Feature/Sources tests/Unit/Services/Sources
```

Полный набор тестов:

```bash
php artisan test
```

Проверка форматирования:

```bash
vendor/bin/pint --test
```
