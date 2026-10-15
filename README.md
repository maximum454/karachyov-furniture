# karachyov-furniture — Калькулятор и CRM мебельного производства

Backend-driven калькулятор себестоимости и розничной цены для мебельного интернет-магазина (мягкая мебель, кровати) на 1C-Bitrix.

**Домен:** https://karachyov.webtm.ru

## Возможности

- **Калькулятор цен** (`calculator_api.php`) — расчёт себестоимости по спецификациям модели: материалы (ИБ 50) + ткань (ИБ 48/51) + механизм
- **Kanban CRM** (`kanban_api.php`) — управление заказами по статусам (NEW → AGREE → PREPAY → PRODUCTION → DELIVERY → DONE)
- **Заявки из WhatsApp** (`save_lead.php`) — сохранение заказа с карточки товара
- **Витрина** (`template.php` + JS) — выбор размера, опций, ткани; кнопка заказа в WhatsApp

## Инфоблоки

| ID | CODE | Описание |
|----|------|----------|
| 46 | `CATALOG` | Товары (модели кроватей) |
| 47 | `lead` | Заявки / Лиды (Kanban) |
| 48 | `FURNITURE_FABRICS` | Коллекции тканей |
| 49 | `FURNITURE_SPECIFICATIONS` | Спецификации моделей (размеры, опции, материалы) |
| 50 | `FURNITURE_MATERIALS` | Справочник материалов |
| 51 | `SKU-FABRIC` | Цвета тканей (SKU) |

## Файловая структура

| Файл | Назначение |
|------|------------|
| `local/ajax/calculator_api.php` | JSON API: расчёт цены по spec_id, fabric_id, has_mechanism |
| `local/ajax/kanban_api.php` | JSON API: управление статусами заказов |
| `local/ajax/save_lead.php` | Создание лида из заказа |
| `local/php_interface/init.php` | Обработчики событий Bitrix |
| `admin-kanban/index.php` | Kanban-доска (Vue через CDN) |
| `local/templates/karachyov/` | Шаблон сайта: header, footer, компоненты |

## Разработка

Локально: WSL
Сервер: Timeweb, SSH
PHP 5.6