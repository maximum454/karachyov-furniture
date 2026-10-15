<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Канбан Заказов (Производство Мебели)");
?>

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        [v-cloak] { display: none !important; }

        :root {
            --bg-primary: #f4f5f7;
            --card-bg: #ffffff;
            --text-main: #172b4d;
            --text-sub: #5e6c84;
            --accent: #0052cc;
            --accent-hover: #0065ff;
            --accent-green: #25d366;
            --warning-bg: #ffebe6;
            --warning-text: #de350b;
            --border-radius: 8px;
        }

        .workarea, #workarea, .container, .page-content {
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 10px !important;
        }

        .kanban-board__container {
            width: 100%;
            height: calc(100vh - 110px);
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            padding: 10px 0;
        }

        /* Верхняя панель действий */
        .kanban-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding: 0 10px;
            gap: 15px;
        }
        .kanban-title-group { display: flex; align-items: center; gap: 15px; }
        .kanban-header h1 { font-size: 20px; margin: 0; color: #091e42; font-weight: 700; }

        .kanban-controls { display: flex; align-items: center; gap: 12px; flex-grow: 1; max-width: 600px; }
        .search-input-wrapper { position: relative; width: 100%; }
        .search-input-wrapper i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #8993a4; }
        .search-input {
            width: 100%;
            padding: 8px 12px 8px 36px;
            border-radius: 6px;
            border: 1px solid #dfe1e6;
            background: #fff;
            font-size: 13px;
            box-sizing: border-box;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .search-input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 2px rgba(0,82,204,0.2); }

        /* Доска */
        .kanban-board {
            display: flex;
            gap: 14px;
            overflow-x: auto;
            overflow-y: hidden;
            padding: 4px 10px 15px 10px;
            align-items: stretch;
            flex-grow: 1;
            height: 100%;
        }

        .column {
            background: #ebecf0;
            border-radius: var(--border-radius);
            width: 310px;
            min-width: 310px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            height: 100%;
            box-sizing: border-box;
        }
        .column-header {
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #5e6c84;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }
        .badge {
            background: #dfe1e6;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
            color: #172b4d;
            font-weight: 600;
        }

        .cards-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            overflow-y: auto;
            flex-grow: 1;
            padding: 2px 4px 2px 2px;
        }

        .cards-list::-webkit-scrollbar, .kanban-board::-webkit-scrollbar { width: 6px; height: 8px; }
        .cards-list::-webkit-scrollbar-thumb, .kanban-board::-webkit-scrollbar-thumb { background: #c1c7d0; border-radius: 4px; }

        /* Карточка */
        .card {
            background: var(--card-bg);
            border-radius: 8px;
            padding: 12px 14px;
            box-shadow: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.2);
            cursor: grab;
            transition: transform 0.15s, box-shadow 0.15s;
            border-left: 4px solid var(--accent);
            position: relative;
            flex-shrink: 0;
        }
        .card:hover { box-shadow: 0 4px 8px rgba(9, 30, 66, 0.15); transform: translateY(-1px); }
        .card:active { cursor: grabbing; }

        /* Флаг задержки заказа */
        .card-warning-stagnant { border-left-color: #de350b !important; }

        .card-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
        .card-id { font-size: 11px; font-weight: 700; color: var(--accent); background: #e3f2fd; padding: 2px 6px; border-radius: 4px; }
        .card-date { font-size: 11px; color: #8993a4; }

        .stagnant-tag {
            font-size: 10px; font-weight: 700; background: var(--warning-bg); color: var(--warning-text);
            padding: 2px 6px; border-radius: 4px; display: inline-flex; align-items: center; gap: 3px;
        }

        .card-title { font-size: 14px; font-weight: 600; line-height: 1.3; margin-bottom: 8px; color: #091e42; }

        .card-tags { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 8px; }
        .tag { font-size: 11px; padding: 2px 6px; border-radius: 4px; background: #f4f5f7; color: #42526e; font-weight: 500; }
        .tag-fabric { background: #eae6ff; color: #403294; }
        .tag-size { background: #e3fcef; color: #006644; }

        .card-phone-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #25d366;
            font-weight: 600;
            text-decoration: none;
            margin-top: 4px;
            background: #e8f8ef;
            padding: 4px 8px;
            border-radius: 4px;
            width: fit-content;
        }
        .card-phone-btn:hover { background: #d2f3e1; }

        /* Модалки */
        .modal-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(9, 30, 66, 0.54);
            display: flex; justify-content: center; align-items: center;
            z-index: 1000;
            backdrop-filter: blur(2px);
        }
        .modal-content {
            background: #fff;
            border-radius: 12px;
            width: 580px;
            max-width: 92vw;
            max-height: 90vh;
            overflow-y: auto;
            padding: 24px;
            box-shadow: 0 12px 24px rgba(9, 30, 66, 0.25);
            position: relative;
        }
        .modal-close {
            position: absolute; top: 16px; right: 16px;
            background: #f4f5f7; border: none; font-size: 18px;
            width: 32px; height: 32px; border-radius: 50%;
            cursor: pointer; color: #5e6c84;
            display: flex; align-items: center; justify-content: center;
        }
        .modal-close:hover { background: #dfe1e6; color: #091e42; }

        .modal-header { margin-bottom: 18px; border-bottom: 1px solid #ebecf0; padding-bottom: 12px; }
        .modal-title { font-size: 18px; font-weight: 700; margin: 4px 0 0 0; color: #091e42; }

        .form-group { margin-bottom: 14px; }
        .form-label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #5e6c84; margin-bottom: 4px; }
        .form-control {
            width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #dfe1e6;
            font-size: 13px; color: #091e42; box-sizing: border-box;
        }
        .form-control:focus { outline: none; border-color: var(--accent); }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px; }

        .modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 20px; flex-wrap: wrap; }
        .btn { padding: 8px 14px; border-radius: 6px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
        .btn-primary { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: var(--accent-hover); }
        .btn-whatsapp { background: #25d366; color: #fff; text-decoration: none; }
        .btn-whatsapp:hover { background: #20bd5a; }
        .btn-secondary { background: #ebecf0; color: #172b4d; }
        .btn-secondary:hover { background: #dfe1e6; }

        /* Стиль для печатного бланка */
        @media print {
            body * { visibility: hidden; }
            #print-area, #print-area * { visibility: visible; }
            #print-area { position: absolute; left: 0; top: 0; width: 100%; padding: 20px; font-family: sans-serif; }
            .no-print { display: none !important; }
        }
    </style>

    <div id="app" v-cloak>
        <div class="kanban-board__container">

            <!-- Шапка и поиск -->
            <div class="kanban-header">
                <div class="kanban-title-group">
                    <h1>📋 Заказы (KARACHYOV)</h1>
                    <button class="btn btn-primary" @click="openCreateModal">
                        <i class="fa-solid fa-plus"></i> Новый заказ
                    </button>
                </div>

                <div class="kanban-controls">
                    <div class="search-input-wrapper">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text"
                               v-model="searchQuery"
                               class="search-input"
                               placeholder="Поиск по номеру (#12), имени, телефону, ткани, размеру...">
                    </div>
                </div>
            </div>

            <!-- Доска -->
            <div class="kanban-board">
                <div v-for="col in columns" :key="col.id" class="column">
                    <div class="column-header">
                        <span>{{ col.title }}</span>
                        <span class="badge">{{ getFilteredCardsByStatus(col.id).length }}</span>
                    </div>

                    <div class="cards-list"
                         @dragover.prevent
                         @drop="onDrop($event, col.id)">

                        <div v-for="card in getFilteredCardsByStatus(col.id)"
                             :key="card.id"
                             class="card"
                             :class="{ 'card-warning-stagnant': isCardStagnant(card) }"
                             draggable="true"
                             @dragstart="onDragStart($event, card)"
                             @click="openCardModal(card)">

                            <div class="card-top">
                                <span class="card-id">#{{ card.id }}</span>
                                <span v-if="isCardStagnant(card)" class="stagnant-tag" title="Без движения более 3 дней">
                                <i class="fa-solid fa-clock"></i> {{ getDaysStagnant(card) }}д
                            </span>
                                <span class="card-date">{{ card.date }}</span>
                            </div>

                            <div class="card-title">{{ card.name }}</div>

                            <!-- Теги -->
                            <div class="card-tags" v-if="card.size || card.fabric">
                                <span v-if="card.size" class="tag tag-size"><i class="fa-solid fa-ruler-combined"></i> {{ card.size }}</span>
                                <span v-if="card.fabric" class="tag tag-fabric"><i class="fa-solid fa-scroll"></i> {{ card.fabric }}</span>
                            </div>

                            <!-- Цена на карточке -->
                            <div class="card-price mt-2 fw-bold text-success" v-if="card.price_total">
                                {{ Number(card.price_total).toLocaleString() }} ₸
                            </div>

                            <!-- WhatsApp -->
                            <a v-if="card.phone"
                               :href="getWhatsAppLink(card.phone)"
                               target="_blank"
                               class="card-phone-btn mt-2"
                               @click.stop>
                                <i class="fa-brands fa-whatsapp"></i> {{ card.phone }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- МОДАЛЬНОЕ ОКНО ДЕТАЛЕЙ / РЕДАКТИРОВАНИЯ -->
        <div v-if="selectedCard" class="modal-overlay" @click.self="closeCardModal">
            <div class="modal-content" style="max-width: 650px;">
                <button class="modal-close" @click="closeCardModal">&times;</button>

                <div class="modal-header">
                    <span class="card-id">Заявка #{{ selectedCard.id }} от {{ selectedCard.date }}</span>
                    <h2 class="modal-title" v-if="!isEditing">{{ selectedCard.name }}</h2>
                    <input v-else type="text" v-model="editForm.name" class="form-control" style="font-weight: bold; margin-top: 6px;">
                </div>

                <!-- Статус -->
                <div class="form-group mb-3">
                    <label class="form-label">Текущий статус</label>
                    <select class="form-control"
                            :value="selectedCard.status"
                            @change="changeStatusFromModal($event.target.value)"
                            style="font-weight: 600;">
                        <option v-for="col in columns" :key="col.id" :value="col.id">
                            {{ col.title }}
                        </option>
                    </select>
                </div>

                <!-- БЛОК КАЛЬКУЛЯТОРА В МОДАЛЬНОМ ОКНЕ -->
                <div class="calculator-box p-3 rounded mb-3" style="background: #f1f5f9; border: 1px solid #cbd5e1;">
                    <h4 style="font-size: 15px; font-weight: 700; margin-bottom: 12px; color: #1e293b;">🧮 Расчет стоимости и материалов</h4>

                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small">Модель / Спецификация</label>
                            <select v-model="selectedSpecId" @change="onSpecChange" class="form-control form-control-sm" :disabled="!isEditing && selectedCard">
                                <option value="">-- Выберите модель --</option>
                                <option v-for="spec in specifications" :key="spec.id" :value="spec.id">
                                    {{ spec.title }} ({{ spec.size }})
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Ткань</label>
                            <select v-model="selectedFabricId" @change="onFabricChange" class="form-control form-control-sm" :disabled="!isEditing && selectedCard">
                                <option value="">-- Выберите ткань --</option>
                                <option v-for="fab in fabrics" :key="fab.id" :value="fab.id">
                                    {{ fab.name }} ({{ fab.pricePerMeter }} ₸/м)
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-check mb-2">
                        <input type="checkbox" id="modalMechCheck" v-model="hasMechanism" class="form-check-input" :disabled="!isEditing && selectedCard">
                        <label for="modalMechCheck" class="form-check-label small fw-semibold">Подъемный механизм (ПМ)</label>
                    </div>

                    <!-- Финансовая сводка -->
                    <div class="row text-center bg-white p-2 rounded border my-2" v-if="selectedSpec">
                        <div class="col-6 border-end">
                            <span class="text-muted small d-block">Себестоимость:</span>
                            <span class="fw-bold text-danger">{{ calculatedCostPrice.toLocaleString() }} ₸</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted small d-block">Реком. цена (x1.8):</span>
                            <span class="fw-bold text-success">{{ recommendedRetailPrice.toLocaleString() }} ₸</span>
                        </div>
                    </div>

                    <div class="form-group my-2">
                        <label class="form-label small">Цена для клиента (₸):</label>
                        <input type="number" v-model="customPriceTotal" :placeholder="recommendedRetailPrice" class="form-control form-control-sm fw-bold" :readonly="!isEditing && selectedCard">
                    </div>

                    <!-- Динамический список закупки -->
                    <div v-if="shoppingList.length > 0" class="mt-2 pt-2 border-top">
                        <span class="form-label small fw-bold d-block">📦 Бланк закупки материалов:</span>
                        <ul class="mb-0 ps-3 small text-secondary">
                            <li v-for="(item, idx) in shoppingList" :key="idx">{{ item }}</li>
                        </ul>
                    </div>

                    <!-- Чертежи и карты раскроя -->
                    <div v-if="selectedSpec && selectedSpec.files && selectedSpec.files.length > 0" class="mt-2 pt-2 border-top">
                        <span class="form-label small fw-bold d-block">📐 Карта раскроя / Чертежи:</span>
                        <div class="d-flex flex-wrap gap-2 mt-1">
                            <a v-for="file in selectedSpec.files" :key="file.id" :href="file.src" target="_blank" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 12px;">
                                <i class="fa-solid fa-file-pdf"></i> {{ file.name }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Режим ПРОСМОТРА -->
                <template v-if="!isEditing">
                    <div class="info-grid mb-3">
                        <div>
                            <span class="form-label">Телефон / WhatsApp</span>
                            <strong>{{ selectedCard.phone || 'Не указан' }}</strong>
                        </div>
                        <div>
                            <span class="form-label">Размер</span>
                            <strong>{{ selectedCard.size || 'Стандарт' }}</strong>
                        </div>
                        <div>
                            <span class="form-label">Ткань / Цвет</span>
                            <strong>{{ selectedCard.fabric || 'Не выбрано' }}</strong>
                        </div>
                    </div>

                    <div class="form-group mb-2" v-if="selectedCard.address">
                        <span class="form-label">Адрес доставки</span>
                        <div>📍 {{ selectedCard.address }}</div>
                    </div>

                    <div class="form-group mb-3" v-if="selectedCard.text" style="background: #fafbfc; padding: 10px; border-radius: 6px;">
                        <span class="form-label">Комментарий / Детали</span>
                        <div style="white-space: pre-wrap; font-size: 13px;">{{ selectedCard.text }}</div>
                    </div>
                </template>

                <!-- Режим РЕДАКТИРОВАНИЯ -->
                <template v-else>
                    <div class="info-grid mb-2">
                        <div class="form-group">
                            <label class="form-label">Телефон</label>
                            <input type="text" v-model="editForm.phone" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Размер</label>
                            <input type="text" v-model="editForm.size" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Ткань / Цвет</label>
                            <input type="text" v-model="editForm.fabric" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Адрес доставки</label>
                            <input type="text" v-model="editForm.address" class="form-control">
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Комментарий / Пожелания</label>
                        <textarea v-model="editForm.text" class="form-control" rows="3"></textarea>
                    </div>
                </template>

                <!-- Кнопки действий -->
                <div class="modal-actions">
                    <template v-if="!isEditing">
                        <button class="btn btn-secondary" @click="startEditing">
                            <i class="fa-solid fa-pen"></i> Редактировать
                        </button>
                        <button class="btn btn-secondary" @click="printWorkOrder(selectedCard)">
                            <i class="fa-solid fa-print"></i> Бланк в цех
                        </button>
                        <button class="btn btn-secondary" @click="copyOrderSummary(selectedCard)">
                            <i class="fa-regular fa-copy"></i> Скопировать
                        </button>
                        <a v-if="selectedCard.phone"
                           :href="getWhatsAppLink(selectedCard.phone)"
                           target="_blank"
                           class="btn btn-whatsapp">
                            <i class="fa-brands fa-whatsapp"></i> Чат
                        </a>
                    </template>
                    <template v-else>
                        <button class="btn btn-secondary" @click="isEditing = false">Отмена</button>
                        <button class="btn btn-primary" @click="saveEditing">Сохранить изменения</button>
                    </template>
                </div>
            </div>
        </div>

        <!-- МОДАЛЬНОЕ ОКНО СОЗДАНИЯ ЗАКАЗА -->
        <div v-if="showCreateModal" class="modal-overlay" @click.self="showCreateModal = false">
            <div class="modal-content" style="max-width: 650px;">
                <button class="modal-close" @click="showCreateModal = false">&times;</button>

                <div class="modal-header">
                    <h2 class="modal-title">➕ Создать новый заказ</h2>
                </div>

                <div class="form-group mb-2">
                    <label class="form-label">Наименование / Модель *</label>
                    <input type="text" v-model="createForm.name" class="form-control" placeholder="Например: Кровать 'Рим'">
                </div>

                <!-- КАЛЬКУЛЯТОР В ОКНЕ СОЗДАНИЯ -->
                <div class="calculator-box p-3 rounded mb-3" style="background: #f1f5f9; border: 1px solid #cbd5e1;">
                    <h4 style="font-size: 15px; font-weight: 700; margin-bottom: 12px; color: #1e293b;">🧮 Выбор спецификации и расчет</h4>

                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small">Модель / Спецификация</label>
                            <select v-model="selectedSpecId" @change="onSpecChange" class="form-control form-control-sm">
                                <option value="">-- Выберите модель --</option>
                                <option v-for="spec in specifications" :key="spec.id" :value="spec.id">
                                    {{ spec.title }} ({{ spec.size }})
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Ткань</label>
                            <select v-model="selectedFabricId" @change="onFabricChange" class="form-control form-control-sm">
                                <option value="">-- Выберите ткань --</option>
                                <option v-for="fab in fabrics" :key="fab.id" :value="fab.id">
                                    {{ fab.name }} ({{ fab.pricePerMeter }} ₸/м)
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-check mb-2">
                        <input type="checkbox" id="createMechCheck" v-model="hasMechanism" class="form-check-input">
                        <label for="createMechCheck" class="form-check-label small fw-semibold">Подъемный механизм (ПМ)</label>
                    </div>

                    <!-- Сводка калькулятора -->
                    <div class="row text-center bg-white p-2 rounded border my-2" v-if="selectedSpec">
                        <div class="col-6 border-end">
                            <span class="text-muted small d-block">Себестоимость:</span>
                            <span class="fw-bold text-danger">{{ calculatedCostPrice.toLocaleString() }} ₸</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted small d-block">Реком. цена (x1.8):</span>
                            <span class="fw-bold text-success">{{ recommendedRetailPrice.toLocaleString() }} ₸</span>
                        </div>
                    </div>

                    <div class="form-group my-2">
                        <label class="form-label small">Цена для клиента (₸):</label>
                        <input type="number" v-model="customPriceTotal" :placeholder="recommendedRetailPrice" class="form-control form-control-sm fw-bold">
                    </div>

                    <!-- Список материалов -->
                    <div v-if="shoppingList.length > 0" class="mt-2 pt-2 border-top">
                        <span class="form-label small fw-bold d-block">📦 Бланк закупки материалов:</span>
                        <ul class="mb-0 ps-3 small text-secondary">
                            <li v-for="(item, idx) in shoppingList" :key="idx">{{ item }}</li>
                        </ul>
                    </div>
                </div>

                <div class="info-grid mb-2">
                    <div class="form-group">
                        <label class="form-label">Телефон клиента</label>
                        <input type="text" v-model="createForm.phone" class="form-control" placeholder="+7 (707) ...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Начальный статус</label>
                        <select v-model="createForm.status" class="form-control">
                            <option v-for="col in columns" :key="col.id" :value="col.id">{{ col.title }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Размер</label>
                        <input type="text" v-model="createForm.size" class="form-control" placeholder="180х200...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ткань / Категория</label>
                        <input type="text" v-model="createForm.fabric" class="form-control" placeholder="Велюр...">
                    </div>
                </div>

                <div class="form-group mb-2">
                    <label class="form-label">Адрес доставки</label>
                    <input type="text" v-model="createForm.address" class="form-control">
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Детали / Комментарий</label>
                    <textarea v-model="createForm.text" class="form-control" rows="2"></textarea>
                </div>

                <div class="modal-actions">
                    <button class="btn btn-secondary" @click="showCreateModal = false">Отмена</button>
                    <button class="btn btn-primary" @click="submitCreateForm">Создать заказ</button>
                </div>
            </div>
        </div>

        <!-- Область печати Наряда для Цеха -->
        <div id="print-area" v-if="printCardData" class="no-print-screen">
            <h2 style="text-align: center; margin-bottom: 5px;">БЛАНК-НАРЯД НА ПРОИЗВОДСТВО #{{ printCardData.id }}</h2>
            <p style="text-align: center; margin-top: 0; color: #555;">Бренд: KARACHYOV Furniture | Дата: {{ printCardData.date }}</p>
            <hr style="margin: 15px 0;">
            <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <tr>
                    <td style="width: 30%; font-weight: bold;">Модель / Изделие:</td>
                    <td>{{ printCardData.name }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Габариты / Размер:</td>
                    <td>{{ printCardData.size || 'Стандарт' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Обивочная ткань / Цвет:</td>
                    <td>{{ printCardData.fabric || 'Не указана' }}</td>
                </tr>
                <tr v-if="shoppingList.length > 0">
                    <td style="font-weight: bold; vertical-align: top;">Комплектация / Материалы:</td>
                    <td>
                        <ul style="margin: 0; padding-left: 18px;">
                            <li v-for="(item, idx) in shoppingList" :key="idx">{{ item }}</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Техническое задание:</td>
                    <td style="white-space: pre-wrap;">{{ printCardData.text || 'Без особых примечаний' }}</td>
                </tr>
            </table>

            <div style="margin-top: 30px; display: flex; justify-content: space-between; font-size: 13px;">
                <div>Мастер цеха: _______________</div>
                <div>ОТК / Мастер сборки: _______________</div>
            </div>
        </div>

    </div>

    <script>
        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    columns: [
                        { id: 'NEW', title: 'Новый лид' },
                        { id: 'AGREE', title: 'Согласование' },
                        { id: 'PREPAY', title: 'Предоплата' },
                        { id: 'PRODUCTION', title: 'В производстве' },
                        { id: 'DELIVERY', title: 'Доставка' },
                        { id: 'DONE', title: 'Завершен' }
                    ],
                    cards: [],
                    searchQuery: '',
                    draggedCard: null,
                    selectedCard: null,
                    isEditing: false,
                    editForm: {},
                    showCreateModal: false,
                    createForm: {
                        name: '', phone: '', size: '', fabric: '', address: '', text: '', status: 'NEW'
                    },
                    printCardData: null,

                    // Справочники для калькулятора
                    specifications: [],
                    fabrics: [],

                    // Переменные калькулятора в модальном окне
                    selectedSpecId: '',
                    selectedFabricId: '',
                    hasMechanism: false,
                    customPriceTotal: null // Для хранения ручной цены
                }
            },
            mounted() {
                this.fetchCards();
                this.fetchCalculatorData();
            },
            computed: {
                // Текущая выбранная спецификация
                selectedSpec() {
                    return this.specifications.find(s => s.id == this.selectedSpecId) || null;
                },
                // Текущая выбранная ткань
                selectedFabric() {
                    return this.fabrics.find(f => f.id == this.selectedFabricId) || null;
                },

                // 1. Расчет себестоимости
                calculatedCostPrice() {
                    if (!this.selectedSpec) return 0;

                    const baseCost = Number(this.selectedSpec.baseCost) || 0;
                    const fabricPrice = this.selectedFabric ? (Number(this.selectedFabric.pricePerMeter) || 0) : 0;
                    const fabricCost = (Number(this.selectedSpec.fabricConsumption) || 0) * fabricPrice;
                    const mechanismCost = this.hasMechanism ? (Number(this.selectedSpec.mechanismCost) || 0) : 0;

                    return baseCost + fabricCost + mechanismCost;
                },

                // Коэффициент наценки: из спецификации (PRICE_MULTIPLIER), либо 1.8 по умолчанию
                selectedMultiplier() {
                    const m = this.selectedSpec ? Number(this.selectedSpec.priceMultiplier) : 0;
                    return m > 0 ? m : 1.8;
                },

                // 2. Рекомендованная розничная цена (ceil(Себестоимость * Коэффициент))
                recommendedRetailPrice() {
                    return Math.ceil(this.calculatedCostPrice * this.selectedMultiplier);
                },

                // Итоговая цена (ручная или рекомендованная)
                finalPriceTotal() {
                    if (this.customPriceTotal !== null && this.customPriceTotal !== '') {
                        return Number(this.customPriceTotal);
                    }
                    return this.recommendedRetailPrice;
                },

                // 3. Динамический список закупки под текущий заказ
                shoppingList() {
                    if (!this.selectedSpec) return [];

                    let list = [...(this.selectedSpec.materialsList || [])];

                    if (this.selectedFabric) {
                        list.unshift(`Ткань ${this.selectedFabric.name} — ${this.selectedSpec.fabricConsumption} п.м.`);
                    }

                    if (this.hasMechanism) {
                        list.push('Комплект подъемного механизма + газлифты — 1 компл.');
                    }

                    return list;
                }
            },
            watch: {
                // Автоматически синхронизируем расчеты с формой создания/редактирования
                calculatedCostPrice(val) {
                    if (this.isEditing) {
                        this.editForm.cost_price = val;
                    } else {
                        this.createForm.cost_price = val;
                    }
                },
                finalPriceTotal(val) {
                    if (this.isEditing) {
                        this.editForm.price_total = val;
                    } else {
                        this.createForm.price_total = val;
                    }
                }
            },
            methods: {
                fetchCards() {
                    fetch('/local/ajax/kanban_api.php?action=list')
                        .then(res => res.json())
                        .then(res => {
                            if (res.status === 'ok') {
                                this.cards = res.data;
                            }
                        })
                        .catch(err => console.error('Ошибка загрузки заявок:', err));
                },

                // Загрузка спецификаций и тканей с сервера
                fetchCalculatorData() {
                    fetch('/local/ajax/calculator_api.php?action=get_initial_data')
                        .then(res => res.json())
                        .then(res => {
                            if (res.success) {
                                this.specifications = res.specifications || [];
                                this.fabrics = res.fabrics || [];
                            }
                        })
                        .catch(err => console.error('Ошибка загрузки данных калькулятора:', err));
                },

                // При выборе спецификации автоматически проставляем размер в форму
                onSpecChange() {
                    if (this.selectedSpec) {
                        const sizeText = this.selectedSpec.size ? `${this.selectedSpec.title} (${this.selectedSpec.size})` : this.selectedSpec.title;
                        if (this.isEditing) {
                            this.editForm.size = sizeText;
                            this.editForm.spec_id = this.selectedSpec.id;
                        } else {
                            this.createForm.size = sizeText;
                            this.createForm.spec_id = this.selectedSpec.id;
                        }
                    }
                    this.customPriceTotal = null; // Сбрасываем ручную цену при смене модели
                },

                // При выборе ткани проставляем название ткани в форму
                onFabricChange() {
                    if (this.selectedFabric) {
                        if (this.isEditing) {
                            this.editForm.fabric = this.selectedFabric.name;
                            this.editForm.fabric_id = this.selectedFabric.id;
                        } else {
                            this.createForm.fabric = this.selectedFabric.name;
                            this.createForm.fabric_id = this.selectedFabric.id;
                        }
                    }
                    this.customPriceTotal = null;
                },

                getFilteredCardsByStatus(status) {
                    return this.cards.filter(c => {
                        if (c.status !== status) return false;
                        if (!this.searchQuery.trim()) return true;

                        const q = this.searchQuery.toLowerCase().trim();
                        return (
                            c.id.toString().includes(q) ||
                            (c.name && c.name.toLowerCase().includes(q)) ||
                            (c.phone && c.phone.toLowerCase().includes(q)) ||
                            (c.fabric && c.fabric.toLowerCase().includes(q)) ||
                            (c.size && c.size.toLowerCase().includes(q)) ||
                            (c.address && c.address.toLowerCase().includes(q))
                        );
                    });
                },

                getWhatsAppLink(phone) {
                    if (!phone) return '#';
                    const cleanPhone = phone.replace(/[^0-9]/g, '');
                    return `https://wa.me/${cleanPhone}`;
                },

                // Зависание карточки (> 3 дней без движения)
                isCardStagnant(card) {
                    if (card.status === 'DONE') return false;
                    const threeDaysInSec = 3 * 24 * 60 * 60;
                    const now = Math.floor(Date.now() / 1000);
                    return (now - card.updated_timestamp) > threeDaysInSec;
                },
                getDaysStagnant(card) {
                    const now = Math.floor(Date.now() / 1000);
                    const diffSec = now - card.updated_timestamp;
                    return Math.floor(diffSec / (24 * 3600));
                },

                openCardModal(card) {
                    this.selectedCard = card;
                    this.isEditing = false;

                    // Заполняем калькулятор сохраненными значениями из карточки
                    this.selectedSpecId = card.spec_id || '';
                    this.selectedFabricId = card.fabric_id || '';
                    this.hasMechanism = !!card.has_mechanism;
                    this.customPriceTotal = card.price_total ? Number(card.price_total) : null;
                },
                closeCardModal() {
                    this.selectedCard = null;
                    this.isEditing = false;
                },

                startEditing() {
                    this.editForm = { ...this.selectedCard };
                    this.isEditing = true;
                },
                saveEditing() {
                    const formData = new FormData();
                    formData.append('action', 'update');
                    formData.append('id', this.editForm.id);
                    formData.append('name', this.editForm.name);
                    formData.append('phone', this.editForm.phone);
                    formData.append('size', this.editForm.size);
                    formData.append('fabric', this.editForm.fabric);
                    formData.append('address', this.editForm.address);
                    formData.append('text', this.editForm.text);
                    formData.append('spec_id', this.selectedSpecId);
                    formData.append('fabric_id', this.selectedFabricId);
                    formData.append('has_mechanism', this.hasMechanism ? 'Y' : 'N');
                    formData.append('cost_price', this.calculatedCostPrice);
                    formData.append('price_total', this.finalPriceTotal);

                    fetch('/local/ajax/kanban_api.php', { method: 'POST', body: formData })
                        .then(res => res.json())
                        .then(res => {
                            if (res.status === 'ok') {
                                Object.assign(this.selectedCard, this.editForm);
                                this.selectedCard.cost_price = this.calculatedCostPrice;
                                this.selectedCard.price_total = this.finalPriceTotal;
                                this.selectedCard.has_mechanism = this.hasMechanism ? 'Y' : 'N';
                                this.selectedCard.updated_timestamp = Math.floor(Date.now() / 1000);
                                this.isEditing = false;
                            } else {
                                alert('Ошибка сохранения: ' + (res.message || ''));
                            }
                        });
                },

                openCreateModal() {
                    this.createForm = {
                        name: '', phone: '', size: '', fabric: '', address: '', text: '', status: 'NEW'
                    };
                    this.selectedSpecId = '';
                    this.selectedFabricId = '';
                    this.hasMechanism = false;
                    this.customPriceTotal = null;
                    this.showCreateModal = true;
                },
                submitCreateForm() {
                    if (!this.createForm.name.trim()) {
                        alert('Укажите наименование или модель заказа!');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('action', 'create');
                    for (let key in this.createForm) {
                        formData.append(key, this.createForm[key]);
                    }
                    formData.append('spec_id', this.selectedSpecId);
                    formData.append('fabric_id', this.selectedFabricId);
                    formData.append('has_mechanism', this.hasMechanism ? 'Y' : 'N');
                    formData.append('cost_price', this.calculatedCostPrice);
                    formData.append('price_total', this.finalPriceTotal);

                    fetch('/local/ajax/kanban_api.php', { method: 'POST', body: formData })
                        .then(res => res.json())
                        .then(res => {
                            if (res.status === 'ok') {
                                this.showCreateModal = false;
                                this.fetchCards();
                            } else {
                                alert('Ошибка создания: ' + (res.message || ''));
                            }
                        });
                },

                changeStatusFromModal(newStatus) {
                    if (!this.selectedCard) return;
                    const card = this.selectedCard;
                    const oldStatus = card.status;
                    card.status = newStatus;
                    this.updateStatusOnServer(card, oldStatus);
                },
                onDragStart(event, card) {
                    this.draggedCard = card;
                    event.dataTransfer.effectAllowed = 'move';
                },
                onDrop(event, targetStatus) {
                    if (!this.draggedCard || this.draggedCard.status === targetStatus) return;
                    const oldStatus = this.draggedCard.status;
                    this.draggedCard.status = targetStatus;
                    this.updateStatusOnServer(this.draggedCard, oldStatus);
                    this.draggedCard = null;
                },
                updateStatusOnServer(card, oldStatus) {
                    const formData = new FormData();
                    formData.append('action', 'update_status');
                    formData.append('id', card.id);
                    formData.append('status', card.status);

                    fetch('/local/ajax/kanban_api.php', { method: 'POST', body: formData })
                        .then(res => res.json())
                        .then(res => {
                            if (res.status !== 'ok') {
                                card.status = oldStatus;
                                alert('Ошибка сохранения статуса: ' + (res.message || ''));
                            } else {
                                card.updated_timestamp = Math.floor(Date.now() / 1000);
                            }
                        })
                        .catch(() => { card.status = oldStatus; });
                },

                copyOrderSummary(card) {
                    const summary = `Заказ #${card.id}
Товар: ${card.name}
Размер: ${card.size || 'Не указан'}
Ткань: ${card.fabric || 'Не указана'}
Адрес: ${card.address || 'Не указан'}
Тел: ${card.phone || 'Не указан'}
Цена: ${card.price_total ? card.price_total + ' ₸' : 'Не указана'}`;

                    navigator.clipboard.writeText(summary).then(() => {
                        alert('Данные заказа скопированы!');
                    });
                },

                printWorkOrder(card) {
                    this.printCardData = card;
                    this.$nextTick(() => {
                        window.print();
                    });
                }
            }
        }).mount('#app');
    </script>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>