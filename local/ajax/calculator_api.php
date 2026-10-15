<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Loader;

// Очищаем буфер вывода
while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/json; charset=utf-8');

if (!Loader::includeModule('iblock')) {
    echo json_encode(array('success' => false, 'error' => 'Модуль iblock не установлен'));
    exit();
}

$action = isset($_REQUEST['action']) ? trim((string)$_REQUEST['action']) : '';

/**
 * Достает цену за метр из свойств коллекции (ИБ 48).
 * Поддерживает и PRICE, и PRICE_PER_METER (в разных инсталляциях свойство может называться по-разному).
 */
function fabricPriceFromProps($props) {
    if (isset($props['PRICE']['VALUE']) && (float)$props['PRICE']['VALUE'] > 0) {
        return (float)$props['PRICE']['VALUE'];
    }
    if (isset($props['PRICE_PER_METER']['VALUE']) && (float)$props['PRICE_PER_METER']['VALUE'] > 0) {
        return (float)$props['PRICE_PER_METER']['VALUE'];
    }
    return 0;
}

/**
 * Резолв цены ткани по ID.
 * 1) Пробуем как элемент коллекции (ИБ 48) — так шлет канбан.
 * 2) Если не нашли/цена 0 — пробуем как цвет-SKU (ИБ 51): по CSTAR_LINK находим коллекцию (ИБ 48).
 *    Так устроена витрина: data-fabric-id у свотчей — это ID цвета ИБ 51.
 */
function getFabricPricePerMeter($fabricId) {
    $fabricId = (int)$fabricId;
    if ($fabricId <= 0) {
        return 0;
    }

    $res = CIBlockElement::GetList(
        array(),
        array('ID' => $fabricId, 'IBLOCK_ID' => 48, 'ACTIVE' => 'Y'),
        false,
        false,
        array('ID')
    );
    if ($ob = $res->GetNextElement()) {
        $price = fabricPriceFromProps($ob->GetProperties());
        if ($price > 0) {
            return $price;
        }
    }

    $res = CIBlockElement::GetList(
        array(),
        array('ID' => $fabricId, 'IBLOCK_ID' => 51, 'ACTIVE' => 'Y'),
        false,
        false,
        array('ID', 'PROPERTY_CSTAR_LINK')
    );
    if ($ob = $res->GetNextElement()) {
        $fields = $ob->GetFields();
        $colId = (int)$fields['PROPERTY_CSTAR_LINK_VALUE'];
        if ($colId > 0) {
            $res = CIBlockElement::GetList(
                array(),
                array('ID' => $colId, 'IBLOCK_ID' => 48, 'ACTIVE' => 'Y'),
                false,
                false,
                array('ID')
            );
            if ($ob = $res->GetNextElement()) {
                return fabricPriceFromProps($ob->GetProperties());
            }
        }
    }

    return 0;
}

// 1. Получение списка спецификаций (ИБ 49) и тканей (ИБ 48) для интерфейса
if ($action === 'get_initial_data') {
    $specifications = array();
    $fabrics = array();

    // Запрос спецификаций моделей (IBLOCK_ID = 49)
    $resSpec = CIBlockElement::GetList(
        array('SORT' => 'ASC', 'NAME' => 'ASC'),
        array('IBLOCK_ID' => 49, 'ACTIVE' => 'Y'),
        false,
        false,
        array('ID', 'NAME')
    );

    while ($ob = $resSpec->GetNextElement()) {
        $fields = $ob->GetFields();
        $props = $ob->GetProperties();

        // 1. Чертежи / карты раскроя (CUTTING_MAPS)
        $files = array();
        $cuttingMapsVal = isset($props['CUTTING_MAPS']['VALUE']) ? $props['CUTTING_MAPS']['VALUE'] : null;
        if (!empty($cuttingMapsVal)) {
            $fileIds = is_array($cuttingMapsVal) ? $cuttingMapsVal : array($cuttingMapsVal);
            foreach ($fileIds as $fId) {
                if (is_numeric($fId) && (int)$fId > 0) {
                    $filePath = CFile::GetPath($fId);
                    $fileArr = CFile::GetFileArray($fId);
                    if ($filePath) {
                        $files[] = array(
                            'id' => (int)$fId,
                            'src' => $filePath,
                            'name' => isset($fileArr['ORIGINAL_NAME']) ? $fileArr['ORIGINAL_NAME'] : 'Чертеж'
                        );
                    }
                }
            }
        }

        // 2. Список материалов (MATERIALS_LIST)
        $cleanMaterials = array();
        $materialsVal = isset($props['MATERIALS_LIST']['VALUE']) ? $props['MATERIALS_LIST']['VALUE'] : array();
        if (!is_array($materialsVal)) {
            $materialsVal = !empty($materialsVal) ? array($materialsVal) : array();
        }

        foreach ($materialsVal as $mat) {
            if (is_array($mat)) {
                if (isset($mat['TEXT']) && is_string($mat['TEXT']) && trim($mat['TEXT']) !== '') {
                    $cleanMaterials[] = trim($mat['TEXT']);
                } elseif (isset($mat['VALUE']) && (is_string($mat['VALUE']) || is_numeric($mat['VALUE']))) {
                    $cleanMaterials[] = trim((string)$mat['VALUE']);
                }
            } elseif (is_string($mat) || is_numeric($mat)) {
                $val = trim((string)$mat);
                if ($val !== '') {
                    $cleanMaterials[] = $val;
                }
            }
        }

        // 3. Размер (SIZE)
        $rawSize = isset($props['SIZE']['VALUE']) ? $props['SIZE']['VALUE'] : '';
        $sizeStr = is_array($rawSize) ? implode(', ', array_filter($rawSize)) : (string)$rawSize;

        $specifications[] = array(
                    'id' => (int)$fields['ID'],
                    'title' => (string)$fields['NAME'],
                    'size' => $sizeStr,
                    'priceMultiplier' => (float)(isset($props['PRICE_MULTIPLIER']['VALUE']) ? $props['PRICE_MULTIPLIER']['VALUE'] : 0),
                    'baseCost' => (float)(isset($props['BASE_COST']['VALUE']) ? $props['BASE_COST']['VALUE'] : 0),
                    'mechanismCost' => (float)(isset($props['MECHANISM_COST']['VALUE']) ? $props['MECHANISM_COST']['VALUE'] : 0),
                    'fabricConsumption' => (float)(isset($props['FABRIC_CONSUMPTION']['VALUE']) ? $props['FABRIC_CONSUMPTION']['VALUE'] : 0),
                    'materialsList' => array_values($cleanMaterials),
                    'files' => $files
                );
            }

    // Запрос коллекций тканей (IBLOCK_ID = 48)
    $resFabrics = CIBlockElement::GetList(
        array('SORT' => 'ASC', 'NAME' => 'ASC'),
        array('IBLOCK_ID' => 48, 'ACTIVE' => 'Y'),
        false,
        false,
        array('ID', 'NAME')
    );

    while ($ob = $resFabrics->GetNextElement()) {
        $fields = $ob->GetFields();
        $props = $ob->GetProperties();

        // Проверяем цену за метр (PRICE или PRICE_PER_METER)
                $price = fabricPriceFromProps($props);

        $fabrics[] = array(
            'id' => (int)$fields['ID'],
            'name' => (string)$fields['NAME'],
            'pricePerMeter' => $price
        );
    }

    echo json_encode(array(
        'success' => true,
        'specifications' => $specifications,
        'fabrics' => $fabrics
    ), JSON_UNESCAPED_UNICODE);
    exit();
}

// 2. Динамический расчет себестоимости и рекомендуемой цены
if ($action === 'calculate') {
    $specId = isset($_REQUEST['spec_id']) ? (int)$_REQUEST['spec_id'] : 0;
    $fabricId = isset($_REQUEST['fabric_id']) ? (int)$_REQUEST['fabric_id'] : 0;
    $hasMechanism = (isset($_REQUEST['has_mechanism']) && ($_REQUEST['has_mechanism'] === 'true' || $_REQUEST['has_mechanism'] === '1'));
    $marginCoeff = isset($_REQUEST['margin_coeff']) ? (float)$_REQUEST['margin_coeff'] : 0;

    $baseCost = 0;
    $fabricConsumption = 0;
    $mechanismCost = 0;
    $pricePerMeter = 0;
    $priceMultiplier = 0;

    // Считываем спецификацию (ИБ 49)
    if ($specId > 0) {
        $res = CIBlockElement::GetList(array(), array('ID' => $specId, 'IBLOCK_ID' => 49), false, false, array('ID'));
        if ($ob = $res->GetNextElement()) {
            $props = $ob->GetProperties();
            $baseCost = (float)(isset($props['BASE_COST']['VALUE']) ? $props['BASE_COST']['VALUE'] : 0);
            $fabricConsumption = (float)(isset($props['FABRIC_CONSUMPTION']['VALUE']) ? $props['FABRIC_CONSUMPTION']['VALUE'] : 0);
            $mechanismCost = (float)(isset($props['MECHANISM_COST']['VALUE']) ? $props['MECHANISM_COST']['VALUE'] : 0);
            $priceMultiplier = (float)(isset($props['PRICE_MULTIPLIER']['VALUE']) ? $props['PRICE_MULTIPLIER']['VALUE'] : 0);
        }
    }

    // Считываем цену ткани: ИБ 48 (коллекция) или ИБ 51 (цвет-SKU) через CSTAR_LINK
    if ($fabricId > 0) {
        $pricePerMeter = getFabricPricePerMeter($fabricId);
    }

    // Расчет себестоимости: База + (Расход ткани * Цена ткани за метр) + (Механизм, если выбран)
    $fabricTotalCost = $fabricConsumption * $pricePerMeter;
    $mechanismTotalCost = $hasMechanism ? $mechanismCost : 0;

    $costPrice = $baseCost + $fabricTotalCost + $mechanismTotalCost;

    // Коэффициент наценки: приоритет у PRICE_MULTIPLIER из спецификации, затем переданный, затем 1.8
    $finalCoeff = $priceMultiplier > 0 ? $priceMultiplier : ($marginCoeff > 0 ? $marginCoeff : 1.8);
    // Розничная цена по ТЗ: ceil(Себестоимость * Коэффициент)
    $recommendedPrice = (int)ceil($costPrice * $finalCoeff);

    echo json_encode(array(
        'success' => true,
        'cost_price' => round($costPrice, 2),
        'recommended_price' => $recommendedPrice,
        'details' => array(
            'base_cost' => $baseCost,
            'fabric_cost' => $fabricTotalCost,
            'mechanism_cost' => $mechanismTotalCost,
            'fabric_consumption' => $fabricConsumption,
            'price_per_meter' => $pricePerMeter,
            'price_multiplier' => $finalCoeff
        )
    ), JSON_UNESCAPED_UNICODE);
    exit();
}

echo json_encode(array('success' => false, 'error' => 'Неизвестный action'));
exit();