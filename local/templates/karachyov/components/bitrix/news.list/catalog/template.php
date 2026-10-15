<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<!-- 4. CATALOG -->
<section class="catalog" id="catalog">
    <div class="catalog__container">
        <h2 class="catalog__title section-title">Каталог моделей</h2>
        <ul class="catalog__grid">
            <?foreach($arResult["ITEMS"] as $arItem):?>
                <?
                $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                ?>
                <?php
                $priceRaw = $arItem["PROPERTIES"]["PRICE"]["VALUE"];
                $priceFormatted = !empty($priceRaw) ? number_format((float)$priceRaw, 0, '', ' ') : '0';

                $phone = "77477747047"; // Ваш номер WhatsApp
                $itemName = $arItem["NAME"];
                $itemId = $arItem["ID"];

                // 1. Берём ID спецификаций из LINK_ELEMENT_VALUE (только те, что привязаны к товару через SPECIFICATIONS)
                                $arSpecList = [];
                                $arBaseSizes = [];
                                $arOptionSpecs = [];
                                $arSpecIds = [];
                                $arSeenSizeKeys = [];
                                $firstSize = '';
                                $hasOptions = false;
                                $specIds = [];

                                if (!empty($arItem["DISPLAY_PROPERTIES"]["SPECIFICATIONS"]["LINK_ELEMENT_VALUE"])) {
                                    foreach ($arItem["DISPLAY_PROPERTIES"]["SPECIFICATIONS"]["LINK_ELEMENT_VALUE"] as $specId => $arSpec) {
                                        $specIds[] = $specId;
                                    }
                                }

                                if (!empty($specIds)) {
                                    $dbSpecs = CIBlockElement::GetList(
                                        ["SORT" => "ASC", "ID" => "ASC"],
                                        ["IBLOCK_ID" => 49, "ID" => $specIds, "ACTIVE" => "Y"],
                                        false,
                                        false,
                                        ["ID", "NAME", "PROPERTY_SPEC_TYPE", "PROPERTY_SIZE"]
                                    );
                                    while ($arSpec = $dbSpecs->Fetch()) {
                                        if (isset($arSpecIds[$arSpec["ID"]])) continue;
                                        $arSpecIds[$arSpec["ID"]] = true;
                                        $arSpecList[] = $arSpec;

                                        $specTypeXmlId = !empty($arSpec["PROPERTY_SPEC_TYPE_VALUE_XML_ID"]) ? $arSpec["PROPERTY_SPEC_TYPE_VALUE_XML_ID"] : (!empty($arSpec["PROPERTY_SPEC_TYPE_VALUE"]) ? $arSpec["PROPERTY_SPEC_TYPE_VALUE"] : '');

                                        if ($specTypeXmlId === 'OPTION' || $specTypeXmlId === 'Опция') {
                                            $arOptionSpecs[] = $arSpec;
                                            $hasOptions = true;
                                        } else {
                                            $sizeEnum = !empty($arSpec["PROPERTY_SIZE_VALUE"]) ? $arSpec["PROPERTY_SIZE_VALUE"] : '';
                                            // Дедуплицируем размеры: если такой SIZE уже есть — пропускаем
                                            if (!empty($sizeEnum) && isset($arSeenSizeKeys[$sizeEnum])) continue;
                                            if (!empty($sizeEnum)) $arSeenSizeKeys[$sizeEnum] = true;
                                            $arBaseSizes[] = $arSpec;
                                            if (empty($firstSize)) {
                                                $firstSize = !empty($sizeEnum) ? $sizeEnum : $arSpec["NAME"];
                                            }
                                        }
                                    }
                                }

                if (empty($firstSize)) {
                    $firstSize = '160x200';
                }
                ?>
                <!-- 2. Выборка тканей (ИБ 48 — Ткани, ИБ 51 — Цвета SKU) -->
                <?php
                $arItemFabrics = [];

                // Получаем привязанные к товару коллекции из свойства FABRICS (ИБ 48)
                if (!empty($arItem["PROPERTIES"]["FABRICS"]["VALUE"])) {
                    $fabricCollectionIds = is_array($arItem["PROPERTIES"]["FABRICS"]["VALUE"])
                        ? $arItem["PROPERTIES"]["FABRICS"]["VALUE"]
                        : [$arItem["PROPERTIES"]["FABRICS"]["VALUE"]];

                    // Ограничиваем до 5 коллекций для витрины
                    $fabricCollectionIds = array_slice($fabricCollectionIds, 0, 5);

                    foreach ($fabricCollectionIds as $colId) {
                        if (empty($colId)) continue;

                        // Достаем данные коллекции из ИБ 48
                        $dbCollection = CIBlockElement::GetList(
                            [],
                            ["IBLOCK_ID" => 48, "ID" => $colId, "ACTIVE" => "Y"],
                            false,
                            false,
                            ["ID", "NAME", "PROPERTY_MANUFACTURER", "PROPERTY_FABRIC_TYPE", "PROPERTY_MARTINDALE", "PROPERTY_PRICE"]
                        );

                        if ($arCol = $dbCollection->Fetch()) {
                            $colName = $arCol["NAME"];
                            $manufacturer = $arCol["PROPERTY_MANUFACTURER_VALUE"];
                            $fabricType = $arCol["PROPERTY_FABRIC_TYPE_VALUE"];
                            $martindale = $arCol["PROPERTY_MARTINDALE_VALUE"];
                            $extraPrice = (float)$arCol["PROPERTY_PRICE_VALUE"];

                            // Ищем цвет в ИБ 51 для этой коллекции
                            // Сначала: ищем с галочкой IS_MAIN_PREVIEW=Y
                            $arColor = false;
                            $dbColorMain = CIBlockElement::GetList(
                                ["SORT" => "ASC", "ID" => "DESC"],
                                ["IBLOCK_ID" => 51, "PROPERTY_FABRIC_COLLECTION" => $colId, "ACTIVE" => "Y"],
                                false,
                                ["nTopCount" => 5],
                                ["ID", "NAME", "PREVIEW_PICTURE", "PROPERTY_COLOR_CODE", "PROPERTY_COLOR_NAME", "PROPERTY_COLOR_HEX", "PROPERTY_IS_MAIN_PREVIEW"]
                            );
                            // Собираем все цвета коллекции, main первый
                            $arFoundColors = array();
                            while ($arColorItem = $dbColorMain->Fetch()) {
                                $isMain = !empty($arColorItem["PROPERTY_IS_MAIN_PREVIEW_VALUE"]) ? true : false;
                                if ($isMain) {
                                    // Main — вставляем в начало
                                    array_unshift($arFoundColors, $arColorItem);
                                } else {
                                    $arFoundColors[] = $arColorItem;
                                }
                            }
                            // Берем первый (main или первый попавшийся)
                            $arColor = !empty($arFoundColors) ? $arFoundColors[0] : false;

                            if ($arColor) {
                                $colorCode = $arColor["PROPERTY_COLOR_CODE_VALUE"];
                                $colorName = $arColor["PROPERTY_COLOR_NAME_VALUE"];
                                $colorHex = !empty($arColor["PROPERTY_COLOR_HEX_VALUE"]) ? $arColor["PROPERTY_COLOR_HEX_VALUE"] : '#cccccc';
                                $imgSrc = CFile::GetPath($arColor["PREVIEW_PICTURE"]);

                                // Формируем полное отображаемое название
                                $fullTitleParts = [];
                                if (!empty($manufacturer)) $fullTitleParts[] = $manufacturer;
                                $fullTitleParts[] = $colName;
                                if (!empty($colorCode)) $fullTitleParts[] = $colorCode;
                                if (!empty($colorName)) $fullTitleParts[] = $colorName;

                                $fullFabricName = implode(" ", $fullTitleParts);

                                // Доп. свойства для подсказки (тип и циклы)
                                $subInfoParts = [];
                                if (!empty($fabricType)) $subInfoParts[] = $fabricType;
                                if (!empty($martindale)) $subInfoParts[] = $martindale;
                                $subInfo = implode(", ", $subInfoParts);

                                // Текст доплаты (если есть)
                                $priceText = $extraPrice > 0 ? " (+" . number_format($extraPrice, 0, '', ' ') . " ₸)" : "";

                                $arItemFabrics[] = [
                                    'ID' => $arColor["ID"],
                                    'COL_ID' => $colId,
                                    'NAME' => $fullFabricName . $priceText,
                                    'SHORT_NAME' => $colName . ($colorCode ? " " . $colorCode : ""),
                                    'SUB_INFO' => $subInfo,
                                    'SRC' => $imgSrc,
                                    'HEX' => $colorHex,
                                    'EXTRA_PRICE' => $extraPrice
                                ];
                            }
                        }
                    }
                }

                $sizeText = " (размер {$firstSize})";
                $firstFabricText = !empty($arItemFabrics) ? ", ткань: " . $arItemFabrics[0]['NAME'] : "";
                $waText = "№{$itemId} Здравствуйте! Меня интересует кровать {$itemName}{$sizeText}{$firstFabricText}. Подскажите по срокам и наличию.";
                $waUrl = "https://wa.me/{$phone}?text=" . rawurlencode($waText);
                ?>

                <!-- Карточка -->
                <article class="catalog__item product-card" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                    <div class="product-card__image-wrapper">
                        <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"
                             width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>"
                             height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>"
                             alt="<?=htmlspecialcharsbx($arItem["PREVIEW_PICTURE"]["ALT"])?>"
                             title="<?=htmlspecialcharsbx($arItem["PREVIEW_PICTURE"]["TITLE"])?>"
                             class="product-card__image"
                        >
                    </div>
                    <div class="product-card__body">
                        <h3 class="product-card__title"><?=htmlspecialcharsbx($arItem["NAME"])?></h3>
                        <p class="product-card__description"><?=htmlspecialcharsbx($arItem["PREVIEW_TEXT"]);?></p>

                        <!-- БЛОК РАЗМЕРОВ -->
                        <div class="product-card__spec-block">
                            <span class="product-card__label">Размер:</span>
                            <div class="product-card__sizes js-size-group">
                                <?if(!empty($arBaseSizes)):?>
                                    <?$isFirst = true;?>
                                    <?foreach($arBaseSizes as $arSpec):?>
                                        <?
                                        $sizeEnum = !empty($arSpec["PROPERTY_SIZE_VALUE"]) ? $arSpec["PROPERTY_SIZE_VALUE"] : '';
                                        $cleanSize = !empty($sizeEnum) ? $sizeEnum : $arSpec["NAME"];
                                        $specId = $arSpec["ID"];
                                        ?>
                                        <span class="product-card__size js-size-btn <?=$isFirst ? 'product-card__size--active' : ''?>"
                                              data-spec-id="<?=$specId?>">
                                <?=htmlspecialcharsbx($cleanSize)?>
                            </span>
                                        <?$isFirst = false;?>
                                    <?endforeach;?>
                                <?else:?>
                                    <span class="product-card__size product-card__size--active">160x200</span>
                                <?endif;?>
                            </div>
                        </div>

                        <!-- БЛОК ОПЦИЙ -->
                        <?if($hasOptions):?>
                            <div class="product-card__spec-block">
                                <span class="product-card__label">Комплектация и опции:</span>
                                <div class="product-card__options js-options-group">
                                    <?foreach($arOptionSpecs as $arSpec):?>
                                                                            <?
                                                                            $cleanOptionName = trim(preg_replace('/\s*\([^)]*\)/u', '', $arSpec["NAME"]));
                                                                            $specId = $arSpec["ID"];
                                                                        ?>
                                        <label class="product-card__option-item">
                                            <input type="checkbox"
                                                   class="product-card__option-checkbox js-option-btn"
                                                   data-spec-id="<?=$specId?>"
                                                   data-option-name="<?=htmlspecialcharsbx($cleanOptionName)?>">
                                            <span class="product-card__option-text"><?=htmlspecialcharsbx($cleanOptionName)?></span>
                                        </label>
                                    <?endforeach;?>
                                </div>
                            </div>
                        <?endif;?>

                        <!-- БЛОК ТКАНИ -->
                        <div class="product-card__spec-block">
                            <div class="product-card__label-group">
                                <span class="product-card__label">Ткань обивки:</span>
                                <span class="product-card__selected-fabric js-selected-fabric-text">
                        <?=!empty($arItemFabrics) ? htmlspecialcharsbx($arItemFabrics[0]['NAME']) : 'Выбирается при заказе'?>
                    </span>
                            </div>
                            <div class="product-card__fabrics js-fabric-group">
                                <?if(!empty($arItemFabrics)):?>
                                    <?$isFirstFabric = true;?>
                                    <?foreach($arItemFabrics as $fabric):?>
                                        <button type="button"
                                                class="product-card__fabric-btn js-fabric-btn <?=$isFirstFabric ? 'product-card__fabric-btn--active' : ''?>"
                                                data-fabric-id="<?=$fabric['ID']?>"
                                                data-fabric-name="<?=htmlspecialcharsbx($fabric['NAME'])?>"
                                                title="<?=htmlspecialcharsbx($fabric['NAME'])?><?=$fabric['SUB_INFO'] ? ' ('.$fabric['SUB_INFO'].')' : ''?>">
                                            <?if(!empty($fabric['SRC'])):?>
                                                <img src="<?=$fabric['SRC']?>"
                                                     alt="<?=htmlspecialcharsbx($fabric['NAME'])?>"
                                                     class="product-card__fabric-img">
                                            <?else:?>
                                                <span class="product-card__fabric-hex" style="background-color: <?=$fabric['HEX']?>;"></span>
                                            <?endif;?>
                                        </button>
                                        <?$isFirstFabric = false;?>
                                    <?endforeach;?>
                                <?else:?>
                                    <span class="product-card__fabric-text">Велюр / Букле / Шенилл</span>
                                <?endif;?>

                                <!-- Плашка +50 тканей -->
                                <button type="button"
                                        class="product-card__fabric-more js-fabric-more-btn"
                                        title="Посмотреть все доступные ткани и цвета">
                                    +50 тканей
                                </button>
                            </div>
                        </div>

                        <div class="product-card__footer">
                            <div class="product-card__price">
                                <span class="product-card__price-label">от</span>
                                <span class="product-card__price-value"><?= $priceFormatted ?> ₸</span>
                            </div>
                            <!-- Кнопка WhatsApp -->
                            <a href="<?=$waUrl?>"
                               class="product-card__btn btn btn--whatsapp js-wa-btn"
                               target="_blank"
                               rel="noopener noreferrer"
                               data-id="<?=$arItem['ID']?>"
                               data-phone="<?=$phone?>"
                               data-name="<?=htmlspecialcharsbx($arItem['NAME'])?>">
                                Обсудить в WhatsApp
                            </a>
                        </div>
                    </div>
                </article>
            <?endforeach;?>
        </ul>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Функция пересчёта цены для карточки
        function updatePrice(card) {
            var specId = '';
            var fabricId = '';
            var optionIds = [];

            var activeSize = card.querySelector('.js-size-btn.product-card__size--active');
            if (activeSize) specId = activeSize.getAttribute('data-spec-id');

            var activeFabric = card.querySelector('.js-fabric-btn.product-card__fabric-btn--active');
            if (activeFabric) fabricId = activeFabric.getAttribute('data-fabric-id');

            card.querySelectorAll('.js-option-btn:checked').forEach(function(opt) {
                var oid = opt.getAttribute('data-spec-id');
                if (oid) optionIds.push(oid);
            });

            if (!specId) return;

            console.log('updatePrice spec=' + specId + ' fab=' + fabricId + ' opts=' + optionIds.join(','));

            var url = '/local/ajax/calculator_api.php?action=calculate&spec_id=' + encodeURIComponent(specId)
                + '&fabric_id=' + encodeURIComponent(fabricId ? fabricId : '0');
            if (optionIds.length > 0) {
                url += '&option_ids=' + optionIds.join(',');
            }

            var priceEl = card.querySelector('.product-card__price-value');
            if (!priceEl) return;

            var originalText = priceEl.innerText;
            priceEl.innerText = '...';

            fetch(url)
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data && data.success && data.recommended_price !== undefined) {
                        var formatted = data.recommended_price.toString().replace(/(\d)(?=(\d{3})+$)/g, '$1 ');
                        priceEl.innerText = formatted + ' ₸';
                    } else {
                        priceEl.innerText = originalText;
                    }
                })
                .catch(function() {
                    priceEl.innerText = originalText;
                });
        }

        // 1. Переключение размера
        document.querySelectorAll('.js-size-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var parentGroup = this.closest('.js-size-group');
                parentGroup.querySelectorAll('.js-size-btn').forEach(function(el) {
                    el.classList.remove('product-card__size--active');
                });
                this.classList.add('product-card__size--active');
                updatePrice(this.closest('.product-card'));
            });
        });

        // 2. Переключение ткани
        document.querySelectorAll('.js-fabric-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var specBlock = this.closest('.product-card__spec-block');

                // Снимаем активность со всех тканей в карточке
                specBlock.querySelectorAll('.js-fabric-btn').forEach(function(el) {
                    el.classList.remove('product-card__fabric-btn--active');
                });

                // Активируем кликнутую ткань
                this.classList.add('product-card__fabric-btn--active');

                // Обновляем плашку с именем выбранной ткани
                var fabricName = this.getAttribute('data-fabric-name');
                var labelText = specBlock.querySelector('.js-selected-fabric-text');
                if (labelText && fabricName) {
                    labelText.innerText = fabricName;
                }

                updatePrice(this.closest('.product-card'));
            });
        });

        // 3. Переключение опций (чекбоксы) — пересчёт цены
        document.querySelectorAll('.js-option-btn').forEach(function(opt) {
            opt.addEventListener('change', function() {
                updatePrice(this.closest('.product-card'));
            });
        });

        // 4. Обработка клика по "+50 тканей"
        document.querySelectorAll('.js-fabric-more-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var card = this.closest('.product-card');
                var waBtn = card ? card.querySelector('.js-wa-btn') : null;
                if (waBtn) {
                    var productId = waBtn.getAttribute('data-id');
                    var productName = waBtn.getAttribute('data-name');
                    var phone = waBtn.getAttribute('data-phone');

                    var waText = 'Здравствуйте! Меня интересует кровать ' + productName + ' (№' + productId + '). Хочу посмотреть весь каталог тканей и цветов (более 50 вариантов).';
                    window.open('https://wa.me/' + phone + '?text=' + encodeURIComponent(waText), '_blank');
                }
            });
        });

        // 4. Отправка в WhatsApp при клике по основной кнопке
        document.querySelectorAll('.js-wa-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var self = this;
                if (self.classList.contains('is-loading')) return;

                var card = self.closest('.product-card');
                var productId = self.getAttribute('data-id');
                var productName = self.getAttribute('data-name');
                var phone = self.getAttribute('data-phone');

                // Активный размер
                var activeSizeEl = card ? card.querySelector('.js-size-btn.product-card__size--active') : null;
                var selectedSize = activeSizeEl ? activeSizeEl.innerText.trim() : '';
                var sizeText = selectedSize ? ' (размер ' + selectedSize + ')' : '';

                // Выбранная ткань
                var activeFabricEl = card ? card.querySelector('.js-fabric-btn.product-card__fabric-btn--active') : null;
                var selectedFabric = activeFabricEl ? activeFabricEl.getAttribute('data-fabric-name') : '';
                var fabricText = selectedFabric ? ', ткань: ' + selectedFabric : '';

                // Отмеченные опции
                var selectedOptions = [];
                if (card) {
                    card.querySelectorAll('.js-option-btn:checked').forEach(function(optInput) {
                        var optName = optInput.getAttribute('data-option-name') || optInput.nextElementSibling.innerText.trim();
                        selectedOptions.push(optName);
                    });
                }
                var optionsText = selectedOptions.length > 0 ? ', опции: ' + selectedOptions.join(', ') : '';

                self.classList.add('is-loading');
                var originalText = self.innerText;
                self.innerText = 'Создаем заявку...';

                var formData = new FormData();
                formData.append('product_id', productId);
                formData.append('product_name', productName);
                formData.append('product_size', selectedSize);
                formData.append('product_fabric', selectedFabric);
                formData.append('product_options', selectedOptions.join(', '));
                formData.append('spec_id', activeSizeEl ? activeSizeEl.getAttribute('data-spec-id') : '');
                formData.append('fabric_id', activeFabricEl ? activeFabricEl.getAttribute('data-fabric-id') : '');

                fetch('/local/ajax/save_lead.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(function(response) { return response.json(); })
                    .then(function(data) {
                        var leadPrefix = (data.status === 'ok' && data.lead_id) ? '№' + data.lead_id + ' ' : '';
                        var waText = leadPrefix + 'Здравствуйте! Меня интересует кровать ' + productName + sizeText + fabricText + optionsText + '. Подскажите по срокам и наличию.';
                        var waUrl = 'https://wa.me/' + phone + '?text=' + encodeURIComponent(waText);

                        self.classList.remove('is-loading');
                        self.innerText = originalText;
                        window.location.href = waUrl;
                    })
                    .catch(function(error) {
                        self.classList.remove('is-loading');
                        self.innerText = originalText;
                        var waText = 'Здравствуйте! Меня интересует кровать ' + productName + sizeText + fabricText + optionsText + '.';
                        window.location.href = 'https://wa.me/' + phone + '?text=' + encodeURIComponent(waText);
                    });
            });
        });

        // Инициализация: пересчитать цену для всех карточек при загрузке
        document.querySelectorAll('.product-card').forEach(function(card) {
            updatePrice(card);
        });
    });
</script>
<style>
    .js-wa-btn {
        touch-action: manipulation; /* Отключает задержку клика и лишние жесты */
        -webkit-tap-highlight-color: transparent;
    }
    .product-card__size {
        cursor: pointer;
        user-select: none;
        /* остальные твои стили */
    }
    /* ==========================================================================
   Блок выбора тканей обивки (Product Card Fabrics)
   ========================================================================== */

    /* Верхняя строка с заголовком и наименованием выбранной ткани */
    .product-card__label-group {
        display: flex;
        align-items: baseline;
        gap: 6px;
        margin-bottom: 8px;
        flex-wrap: wrap;
    }

    /* Выбранная ткань (текст подсказки справа от label) */
    .product-card__selected-fabric {
        font-size: 13px;
        font-weight: 500;
        color: #4a5568;
        line-height: 1.2;
    }

    /* Контейнер для кнопок-свотчей и плашки "+50 тканей" */
    .product-card__fabrics {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    /* Элемент кнопки свотча ткани */
    .product-card__fabric-btn {
        position: relative;
        width: 38px;
        height: 38px;
        padding: 2px;
        border: 2px solid transparent;
        border-radius: 50%; /* Круглая форма (измените на 8px, если нужны квадраты) */
        background: transparent;
        cursor: pointer;
        outline: none;
        transition: border-color 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
        box-sizing: border-box;
    }

    .product-card__fabric-btn:hover {
        transform: translateY(-2px);
        border-color: #cbd5e0;
    }

    /* Активное состояние (выбранная ткань) */
    .product-card__fabric-btn--active {
        border-color: #2b6cb0; /* Акцентный цвет выделения */
        box-shadow: 0 0 0 1px #2b6cb0;
    }

    /* Картинка текстуры/свотча внутри кнопки */
    .product-card__fabric-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        display: block;
    }

    /* Заливка цветом (HEX-плашка, если нет картинки) */
    .product-card__fabric-hex {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        display: block;
        box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.1);
    }

    /* Кнопка «+50 тканей» */
    .product-card__fabric-more {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 38px;
        padding: 0 12px;
        border: 1px dashed #cbd5e1;
        border-radius: 20px;
        background-color: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        white-space: nowrap;
        outline: none;
    }

    .product-card__fabric-more:hover {
        background-color: #edf2f7;
        border-color: #94a3b8;
        color: #1e293b;
    }

    .product-card__fabric-more:active {
        transform: scale(0.98);
    }

    /* Фолбэк-текст, если ткани в админке не привязаны */
    .product-card__fabric-text {
        font-size: 13px;
        color: #a0aec0;
        font-style: italic;
    }

    /* Адаптив под мобильные устройства */
    @media (max-width: 480px) {
        .product-card__fabric-btn {
            width: 34px;
            height: 34px;
        }

        .product-card__fabric-more {
            height: 34px;
            padding: 0 10px;
            font-size: 11px;
        }

        .product-card__selected-fabric {
            font-size: 12px;
        }
    }
    /* ==========================================================================
   Блок опций и комплектации (Product Card Options)
   ========================================================================== */

    /* Общий блок спецификаций/опций */
    .product-card__spec-block {
        margin-bottom: 16px;
    }

    .product-card__spec-block:last-child {
        margin-bottom: 0;
    }

    /* Заголовок блока */
    .product-card__label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 8px;
    }

    /* Контейнер для чекбоксов опций */
    .product-card__options {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    /* Элемент-обертка чекбокса (label) */
    .product-card__option-item {
        display: inline-flex;
        align-items: center;
        position: relative;
        cursor: pointer;
        user-select: none;
    }

    /* Прячем дефолтный браузерный чекбокс */
    .product-card__option-checkbox {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        margin: 0;
        pointer-events: none;
    }

    /* Текстовая плашка-кнопка */
    .product-card__option-text {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        font-size: 13px;
        line-height: 1.2;
        font-weight: 500;
        color: #4a5568;
        background-color: #f7fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    /* Галочка-иконка слева от текста (создаем через кастомный кастомный псевдоэлемент ::before) */
    .product-card__option-text::before {
        content: "";
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 1.5px solid #cbd5e0;
        border-radius: 4px;
        background-color: #ffffff;
        transition: all 0.2s ease;
        box-sizing: border-box;
        flex-shrink: 0;
    }

    /* Ховер неотмеченного состояния */
    .product-card__option-item:hover .product-card__option-text {
        border-color: #cbd5e0;
        background-color: #edf2f7;
        color: #2d3748;
    }

    .product-card__option-item:hover .product-card__option-text::before {
        border-color: #a0aec0;
    }

    /* Активное/отмеченное состояние чекбокса */
    .product-card__option-checkbox:checked + .product-card__option-text {
        background-color: #ebf8ff;
        border-color: #3182ce;
        color: #2b6cb0;
        font-weight: 600;
    }

    /* Иконка галочки при выборе */
    .product-card__option-checkbox:checked + .product-card__option-text::before {
        background-color: #3182ce;
        border-color: #3182ce;
        /* Рисуем белую галочку с помощью SVG-фона */
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='3.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'%3E%3C/polyline%3E%3C/svg%3E");
        background-size: 10px 10px;
        background-position: center;
        background-repeat: no-repeat;
    }

    /* Фокус с клавиатуры (для доступности) */
    .product-card__option-checkbox:focus-visible + .product-card__option-text {
        box-shadow: 0 0 0 2px rgba(49, 130, 206, 0.4);
    }

    /* Адаптив под мобильные устройства */
    @media (max-width: 480px) {
        .product-card__option-text {
            padding: 5px 10px;
            font-size: 12px;
        }

        .product-card__option-text::before {
            width: 12px;
            height: 12px;
        }
    }
</style>