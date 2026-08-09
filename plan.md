# Единый план расширения учебного фреймворка (Аудит, Анализ и Имплементация)

Этот документ объединяет результаты аудита кодовой базы, архитектурный анализ пробелов фреймворка и детальный пошаговый план разработки на 5-7 дней. 

*Статус на 9 августа 2026 года: проведен аудит коммитов от 09.08 (`08d6320`). Студент имплементировал HTTP-клиент (HttpClient), TelegramService, событие PostPendingModerationEvent и слушатель SendTelegramModerationRequest. В процессе аудита выявлена 1 фатальная ошибка вызова конструктора и 3 бага в TelegramService.*

---

## 1. Результаты аудита кодовой базы (Текущие проблемы)

Перед внедрением новых фич необходимо устранить критические баги и архитектурные несоответствия, которые сейчас присутствуют в коде:

### А. Критические ошибки (сломают запуск или логику автозагрузки)
1. **Отсутствие зависимости `phpdotenv` и файла `.env`** `[Частично обойдено]`:
   * **Что сделано**: Студент добавил проверку `if (!file_exists($path)) { return; }` в `Config::load()`. Теперь приложение не падает, если файла `.env` нет (используются дефолтные значения).
   * **Проблема**: В `composer.json` по-прежнему нет зависимости `vlucas/phpdotenv`. Если создать файл `.env` в корне, автозагрузчик не найдет класс `Dotenv\Dotenv` и приложение упадет.
   * **Решение**: Выполнить `composer require vlucas/phpdotenv` и создать реальный файл `.env` для гибкой настройки БД.
2. **Баг авторегистрации в контейнере (`App/Container/Container.php`)** `[ИСПРАВЛЕНО]`:
   * **Исправлено в коммите c0dad41**: Скобка цикла `foreach` в `registerDirectory()` перенесена. Теперь при сканировании директорий в контейнере корректно регистрируются все класс-файлы.
3. **Фатальная ошибка `ArgumentCountError` в контроллерах (`PostsController`, `LoginController`, `RegisterController`)** `[КРИТИЧЕСКАЯ ОШИБКА]`:
   * **Проблема**: В `Controller.php` добавлен 4-й обязательный параметр `protected EventDispatcherInterface $dispatcher`. При этом `PostsController`, `LoginController` и `RegisterController` продолжают вызывать родительский конструктор с 3 параметрами `parent::__construct($request, $view, $session)`.
   * **Результат**: При открытии главной страницы постов или авторизации выбросится неперехватываемое исключение `ArgumentCountError` и запуск упадет.
   * **Решение**: Передать `$dispatcher` в `parent::__construct` во всех дочерних контроллерах (как это сделано в `LogoutController`).

### Б. Архитектурные несоответствия (Service Locator вместо DI)
1. **Контроллеры принимают конкретные зависимости** `[ИСПРАВЛЕНО]`:
   * `LoginController`, `PostsController` и `RegisterController` теперь используют Constructor Injection: принимают `Request`, `View`, `Session`, `EventDispatcherInterface` и сервисы в конструкторах. Базовый `Controller` также рефакторирован.
2. **Перенос middleware на контейнер** `[ИСПРАВЛЕНО]`:
   * В `MiddlewareDispatcher` посредники теперь разрешаются из контейнера через `$this->container->get($middlewareClass)`. Это позволило избавиться от оператора `new`.
3. **Использование Service Locator в Middleware** `[ИСПРАВЛЕНО]`:
   * **Исправлено в коммите c0dad41**: В `AuthMiddleware`, `GuestMiddleware` и `LoggerMiddleware` добавлен конструктор `__construct(private Session $session)`. Контейнер автоматически внедряет экземпляр `Session` при их создании.
4. **Использование оператора `new` вместо разрешения через DI-контейнер** `[ИСПРАВЛЕНО]`:
   * **Исправлено в коммите c0dad41**: 
     * В `EventDispatcher` слушатели разрешаются через `$this->container->get($listenerClass)`.
     * В `AbstractModel` свойство `QueryBuilder` внедряется через конструктор (`__construct(protected QueryBuilder $builder)`). Модели `Posts`, `Tags`, `Users` передают зависимость в родительский конструктор.

### В. Хардкод параметров и связей
1. **Зашитые пути**: В `View.php` дефолтный путь шаблонов `/var/www/html/templates` прописан жестко в объявлении конструктора `__construct(private string $templatePath = '/var/www/html/templates')`.
2. **Динамические Middleware** `[ИСПРАВЛЕНО]`:
   * Логика назначения Middleware перенесена из `Router.php` в контроллеры с помощью свойств и метода `getMiddlewareConfig()`. В роутере настроено динамическое чтение правил из контроллера и слияние с глобальным `LoggerMiddleware`.

### Г. Новые замечания (по результатам последних коммитов)
1. **Критическое несовпадение типов событий и слушателей в `EventDispatcher`** `[ИСПРАВЛЕНО]`:
   * В `Application::registerEvents()` подписка слушателей `LogPostCreatedListener` и `LogUserRegisteredListener` переведена на `ModelSavedEvent::class`.
2. **Использование Service Locator в методах моделей (`Users.php`, `Posts.php`, `Categories.php`)** `[ЗАМЕЧАНИЕ]`:
   * В `Users::getFollowers()`, `Posts::getUser()`, `Posts::getCategory()`, `Categories::getPosts()` и `Posts::getPostsByUserId()` по-прежнему используется обращение к глобальному синглтону:
     ```php
     $container = Application::getInstance()->getContainer();
     $user = $container->get(Users::class);
     ```
   * **Решение**: Избавиться от Service Locator, передавая зависимости/фабрики или используя ключевое слово `new`.
3. **Некорректная интерполяция строки в `TelegramService::sendPostForModeration()`** `[ОШИБКА]`:
   * На строке 37 вызов `mb_substr` попал внутрь двойных кавычек строки (`"<b>Содержание:</b>\n . mb_substr..."`), из-за чего в Telegram отправляется код PHP.
   * На строках 43 и 47 в `callback_data` написано `"approve_post:{post_id}"` вместо `"approve_post:{$postId}"`.
   * **Решение**: Исправить склейку строк и подставить переменную `$postId`.
4. **Отсутствие ключа `telegram` в `Config::$config`** `[ОШИБКА]`:
   * `TelegramService` запрашивает `$this->config->get('telegram.token')`, но в `Config.php` этот ключ не объявлен. Метод возвращает `null`, и запросы отправляются на некорректный URL `https://api.telegram.org/bot`.
   * **Решение**: Добавить настройки `telegram` в `Config::$config`.
5. **Неверный тип аргумента в `TelegramService::answerCallbackQuery()`** `[ОШИБКА]`:
   * Параметр `$callbackQueryId` объявлен как `array`, хотя Telegram передает `string`. Вызов приведет к `TypeError`.
   * **Решение**: Изменить тип на `string $callbackQueryId`.
6. **Несоответствие условия и текста ошибки в `RegisterUserDTO.php`** `[ЗАМЕЧАНИЕ]`:
   * На строке 17 проверяется `if(strlen($this->password) < 6)`, а текст исключения утверждает `'пароль должен быть минимум 3 символа'`.
7. **Использование `strlen` вместо `mb_strlen` для валидации кириллических строк в DTO** `[ЗАМЕЧАНИЕ]`:
   * В `CreatePostDTO` (`strlen($this->title) < 3`) и `RegisterUserDTO` (`strlen($this->username) < 3`) однобайтовая функция `strlen` считает количество байтов, а не символов UTF-8.

---

## 2. Обзор целевой архитектуры фреймворка
Проект расширяется до структуры с разделением на Web-слой и CLI-слой:

```
[Клиент (Браузер)]
       │
       ▼
[pub/index.php (HTTP-вход)] ──► [Application] (Контейнер)
                                      │
                                      ▼
                               [Post/CommentService]
                                      ▲
                                      │
[bin/telegram-poll.php (CLI)] ────────┘ (Инициализирует Application,
       │                                 запускает бесконечный цикл)
       ▼
[Telegram Bot API] (Запросы getUpdates / Long Polling)
```

---

## 3. Пошаговый план имплементации (5-7 дней)

### День 1: Исправление ядра фреймворка, переход на настоящий DI и Config
* **Шаги**:
  1. `[ ]` **Установка Dotenv**: Выполнить установку `composer require vlucas/phpdotenv`.
  2. `[x]` **Исправление `Container.php`**: Перенести привязку классов внутрь цикла `foreach`.
  3. `[x]` **Рефакторинг `Application.php`**: Убран жесткий `bind` для контроллеров.
  4. `[x]` **Рефакторинг контроллеров**: Контроллеры принимают зависимости через конструктор.
  5. `[x]` **Рефакторинг Middleware**: Разрешение посредников через контейнер.
  6. `[x]` **Рефакторинг моделей**: Внедрение `QueryBuilder` через аргументы.
  7. `[ ]` **Конфигурация View**: Получать путь к шаблонам из `Config::get('app.templates_path')`.

### День 2: Event/Observer и DI в слушателях
* **Шаги**:
  1. `[x]` **Рефакторинг `EventDispatcher`**: Разрешение слушателей через DI-контейнер.
  2. `[x]` **Интеграция событий в модели**: Вызовы `ModelSavingEvent` и `ModelSavedEvent` в `AbstractModel::save()`.
  3. `[x]` **Проверка работы**: События логируются.

### День 3: Динамические Middleware и Декларативный роутинг
* **Шаги**:
  1. `[x]` **Динамические Middleware**: Использование `getMiddlewareConfig()`.
  2. `[x]` **LoggerMiddleware**: Логирование запросов в `logs.json`.

### День 4: Новые сущности в БД (Миграции и связи)
* **Шаги**:
  1. `[x]` **Роли пользователей**: Поле `role` в `users`.
  2. `[x]` **Категории постов**: Таблица `categories` и поле `category_id`.
  3. `[x]` **Комментарии с рекурсивной вложенностью**: Таблица `comments` с `parent_id` и `status`.
  4. `[x]` **Подписки**: Таблица `subscriptions`.
  5. `[x]` **Создание моделей**: Классы `Categories`, `Comments`, `Subscriptions`.

### День 5: Внедрение Service Layer и DTO
* **Шаги**:
  1. `[x]` **Создание DTO**: Классы `CreatePostDTO`, `LoginUserDTO`, `RegisterUserDTO`.
  2. `[x]` **UserService**: Сервис авторизации, регистрации и подписок.
  3. `[x]` **PostService**: Сервис постов, категорий и тегов.
  4. `[x]` **CommentService**: Сервис комментариев и дерево `getCommentTree()`.
  5. `[x]` **Перевод контроллеров**: Контроллеры переведены на сервисы.

### День 6: Внешние API и CLI Long Polling (Двусторонняя Telegram-модерация)
* **Шаги**:
  1. `[x]` **HTTP-клиент**: Создан класс `App/Core/HttpClient.php` на cURL.
  2. `[x]` **Слушатель отправки на модерацию**:
     * Создано событие `PostPendingModerationEvent`.
     * Создан слушатель `SendTelegramModerationRequest` и зарегистрирован в `Application.php`.
     * Добавлен `TelegramService.php` для взаимодействия с Telegram API.
  3. `[ ]` **Консольный скрипт модерации (`bin/telegram-poll.php`)**:
     * Скрипт инициализирует DI-контейнер и запускает бесконечный цикл `while (true)`.
     * Делает запросы к API Telegram `getUpdates` с параметром `offset`.
     * При получении нажатия кнопки меняет статус в БД и обновляет сообщение в Telegram.

### День 7: Роли, Панель модерации, Рекурсивный рендеринг и UI
* **Шаги**:
  1. `[ ]` **RoleMiddleware**: Проверка прав `admin`/`moderator`.
  2. `[ ]` **AdminController**: Контроллер модерации комментариев.
  3. `[ ]` **Рекурсивное отображение комментариев**: Рендеринг дерева комментариев в шаблонах.
  4. `[ ]` **UI/CSS**: Стили для веток комментариев.

---

## 4. План проверки и верификации (Verification Plan)

### Автоматические проверки (через CLI-скрипты в Docker)
* Запуск скриптов проверки контейнера и подписок событий.

### Ручное тестирование сценариев
1. **Исправление `parent::__construct`**: Передать `$dispatcher` во все контроллеры, проверить открытие `/posts`, `/login`, `/register`.
2. **Проверка отправки поста в Telegram**:
   * Создать новый пост на сайте.
   * Убедиться, что в Telegram приходит сообщение с кнопками "Одобрить" и "Отклонить".
3. **Проверка работы демона Telegram (CLI Long Polling)**:
   * Запустить скрипт `docker compose exec app php bin/telegram-poll.php`.
   * Нажать кнопку в Telegram и убедиться, что статус в БД обновился.
