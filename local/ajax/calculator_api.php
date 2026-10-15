<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Loader;

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
 * Резолв цены ткани по ID (ИБ 48 или ИБ 51 через CSTAR_LINK/FABRIC_COLLECTION).
 */
function getFabricPricePerMeter($fabricId) {
    $fabricId = (int)$fabricId;
    if ($fabricId <= 0) return 0;

    // Пробуем как коллекцию (ИБ 48)
    $res = CIBlockElement::GetList(
        array(), array('ID' => $fabricId, 'IBLOCK_ID' => 48, 'ACTIVE' => 'Y'),
        false, false,
        array('ID', 'NAME', 'PROPERTY_PRICE', 'PROPERTY_PRICE_PER_METER')
    );
    if ($ar = $res->Fetch()) {
        $price = (float)$ar['PROPERTY_PRICE_VALUE'];
        if ($price > 0) return $price;
        $price = (float)$ar['PROPERTY_PRICE_PER_METER_VALUE'];
        if ($price > 0) return $price;
    }

    // Пробуем как цвет-SKU (ИБ 51) → коллекция (ИБ 48)
    $res = CIBlockElement::GetList(
        array(), array('ID' => $fabricId, 'IBLOCK_ID' => 51, 'ACTIVE' => 'Y'),
        false, false,
        array('ID', 'PROPERTY_CSTAR_LINK', 'PROPERTY_FABRIC_COLLECTION')
    );
    if ($ar = $res->Fetch()) {
        $colId = (int)$ar['PROPERTY_CSTAR_LINK_VALUE'];
        if ($colId <= 0) $colId = (int)$ar['PROPERTY_FABRIC_COLLECTION_VALUE'];
        if ($colId <= 0) {
            // Fallback через GetProperty
            $rs = CIBlockElement::GetProperty(51, $fabricId, array(), array('CODE' => 'CSTAR_LINK'));
            if ($p = $rs->Fetch()) $colId = (int)$p['VALUE'];
            if ($colId <= 0) {
                $rs = CIBlockElement::GetProperty(51, $fabricId, array(), array('CODE' => 'FABRIC_COLLECTION'));
                if ($p = $rs->Fetch()) $colId = (int)$p['VALUE'];
            }
        }
        if ($colId > 0) {
            $res = CIBlockElement::GetList(
                array(), array('ID' => $colId, 'IBLOCK_ID' => 48, 'ACTIVE' => 'Y'),
                false, false,
                array('ID', 'NAME', 'PROPERTY_PRICE', 'PROPERTY_PRICE_PER_METER')
            );
            if ($ar = $res->Fetch()) {
                $price = (float)$ar['PROPERTY_PRICE_VALUE'];
                if ($price > 0) return $price;
                $price = (float)$ar['PROPERTY_PRICE_PER_METER_VALUE'];
                if ($price > 0) return $price;
            }
        }
    }
    return 0;
}

/**
 * Читает ID материалов из спецификации (поддержка множественного MATERIALS_REF)
 */
function getMaterialRefs($iblockId, $elementId) {
    $ids = array();
    $rs = CIBlockElement::GetProperty($iblockId, $elementId, array(), array('CODE' => 'MATERIALS_REF'));
    while ($prop = $rs->Fetch()) {
        $val = $prop['VALUE'];
        if (is_array($val)) {
            foreach ($val as $v) { if (is_numeric($v)) $ids[] = (int)$v; }
        } elseif (is_numeric($val)) {
            $ids[] = (int)$val;
        }
    }
    return $ids;
}

/**
 * Читает количество материалов из спецификации (MATERIALS_QTY)
 */
function getMaterialQtys($iblockId, $elementId) {
    $qtys = array();
    $rs = CIBlockElement::GetProperty($iblockId, $elementId, array(), array('CODE' => 'MATERIALS_QTY'));
    while ($prop = $rs->Fetch()) {
        $val = $prop['VALUE'];
        if (is_array($val)) {
            foreach ($val as $v) { $qtys[] = trim((string)$v); }
        } elseif (!empty($val)) {
            $qtys[] = trim((string)$val);
        }
    }
    return $qtys;
}

/**
 * Считает стоимость материалов спецификации через GetProperty
 */
function calcMaterialsCostGetProp($iblockId, $specId) {
    $total = 0.0;
    $materialsRefs = getMaterialRefs($iblockId, $specId);
    $materialsQtys = getMaterialQtys($iblockId, $specId);
    if (empty($materialsRefs)) return 0;

    foreach ($materialsRefs as $i => $matId) {
        $matId = (int)$matId;
        if ($matId <= 0) continue;

        $qty = (float)(isset($materialsQtys[$i]) ? $materialsQtys[$i] : 0);
        if ($qty <= 0) continue;

        $resMat = CIBlockElement::GetList(
            array(),
            array('ID' => $matId, 'IBLOCK_ID' => 50, 'ACTIVE' => 'Y'),
            false, false,
            array('ID', 'NAME', 'PROPERTY_PRICE')
        );
        if ($arMat = $resMat->Fetch()) {
            $price = (float)$arMat['PROPERTY_PRICE_VALUE'];
            if ($price > 0) {
                $total += ceil($price * $qty);
            }
        }
    }
    return $total;
}

// === ACTION: get_initial_data — список спецификаций и тканей (для канбана)
if ($action === 'get_initial_data') {
    $specifications = array();
    $fabrics = array();

    // Спецификации (ИБ 49) через Fetch
        $resSpec = CIBlockElement::GetList(
            array('SORT' => 'ASC', 'NAME' => 'ASC'),
            array('IBLOCK_ID' => 49, 'ACTIVE' => 'Y'),
            false, false,
            array('ID', 'NAME', 'PROPERTY_SPEC_TYPE', 'PROPERTY_SIZE', 'PROPERTY_MODEL_ID', 'PROPERTY_PRICE_MULTIPLIER', 'PROPERTY_FABRIC_CONSUMPTION', 'PROPERTY_MATERIALS_REF', 'PROPERTY_MATERIALS_QTY', 'PROPERTY_CUTTING_MAPS')
        );
    while ($ar = $resSpec->Fetch()) {
        $files = array();
        $cuttingVal = $ar['PROPERTY_CUTTING_MAPS_VALUE'];
        if (!empty($cuttingVal)) {
            $fileIds = is_array($cuttingVal) ? $cuttingVal : array($cuttingVal);
            foreach ($fileIds as $fId) {
                if (is_numeric($fId) && (int)$fId > 0) {
                    $fp = CFile::GetPath($fId);
                    $fa = CFile::GetFileArray($fId);
                    if ($fp) $files[] = array('id' => (int)$fId, 'src' => $fp, 'name' => !empty($fa['ORIGINAL_NAME']) ? $fa['ORIGINAL_NAME'] : 'Чертеж');
                }
            }
        }

        // Материалы для списка закупки
                $st = '';
                $stXml2 = $ar['PROPERTY_SPEC_TYPE_VALUE_XML_ID'];
                $stVal2 = $ar['PROPERTY_SPEC_TYPE_VALUE'];
                if (!empty($stXml2)) $st = $stXml2;
                elseif (!empty($stVal2)) $st = $stVal2;
                $mats = array();
        $matIds = $ar['PROPERTY_MATERIALS_REF_VALUE'];
        $matQtys = $ar['PROPERTY_MATERIALS_QTY_VALUE'];
        if (!empty($matIds)) {
            $idArr = is_array($matIds) ? $matIds : array($matIds);
            $qtyArr = !empty($matQtys) ? (is_array($matQtys) ? $matQtys : array($matQtys)) : array();
            foreach ($idArr as $i => $mid) {
                $mid = (int)$mid;
                if ($mid <= 0) continue;
                $qty = (float)(isset($qtyArr[$i]) ? $qtyArr[$i] : 0);
                // Читаем название материала
                $rm = CIBlockElement::GetList(array(), array('ID' => $mid, 'IBLOCK_ID' => 50), false, false, array('ID', 'NAME'));
                if ($am = $rm->Fetch()) $mats[] = $am['NAME'] . ' x' . $qty;
            }
        }

        $specifications[] = array(
                            'id' => (int)$ar['ID'],
                            'title' => $ar['NAME'],
                            'size' => !empty($ar['PROPERTY_SIZE_VALUE']) ? $ar['PROPERTY_SIZE_VALUE'] : '',
                            'model_id' => (int)$ar['PROPERTY_MODEL_ID_VALUE'],
                            'spec_type' => $st,
                            'is_option' => ($st === 'OPTION' || $st === 'Опция') ? true : false,
                    'priceMultiplier' => (float)$ar['PROPERTY_PRICE_MULTIPLIER_VALUE'],
                    'fabricConsumption' => (float)$ar['PROPERTY_FABRIC_CONSUMPTION_VALUE'],
                    'materialsList' => $mats,
                    'files' => $files,
                );
            }

            // Модели (ИБ 46)
            $models = array();
            $resMod = CIBlockElement::GetList(
                array('SORT' => 'ASC', 'NAME' => 'ASC'),
                array('IBLOCK_ID' => 46, 'ACTIVE' => 'Y'),
                false, false,
                array('ID', 'NAME')
            );
            while ($arMod = $resMod->Fetch()) {
                $models[] = array('id' => (int)$arMod['ID'], 'name' => $arMod['NAME']);
            }

            // Ткани (ИБ 48)
    $resFab = CIBlockElement::GetList(
        array('SORT' => 'ASC', 'NAME' => 'ASC'),
        array('IBLOCK_ID' => 48, 'ACTIVE' => 'Y'),
        false, false,
        array('ID', 'NAME', 'PROPERTY_PRICE_PER_METER')
    );
    while ($ar = $resFab->Fetch()) {
        $fabrics[] = array(
            'id' => (int)$ar['ID'],
            'name' => $ar['NAME'],
            'pricePerMeter' => (float)$ar['PROPERTY_PRICE_PER_METER_VALUE'],
        );
    }

    echo json_encode(array(
        'success' => true,
        'specifications' => $specifications,
        'fabrics' => $fabrics
    ), JSON_UNESCAPED_UNICODE);
    exit();
}

// === ACTION: calculate ===
if ($action === 'calculate') {
    $specId     = isset($_REQUEST['spec_id']) ? (int)$_REQUEST['spec_id'] : 0;
    $fabricId   = isset($_REQUEST['fabric_id']) ? (int)$_REQUEST['fabric_id'] : 0;
    $optionIds  = isset($_REQUEST['option_ids']) ? $_REQUEST['option_ids'] : '';
    $marginCoeff = isset($_REQUEST['margin_coeff']) ? (float)$_REQUEST['margin_coeff'] : 0;

    // Парсим option_ids
    $optIdArr = array();
    if (!empty($optionIds)) {
        if (is_array($optionIds)) {
            foreach ($optionIds as $oid) $optIdArr[] = (int)$oid;
        } else {
            foreach (explode(',', (string)$optionIds) as $part) {
                $part = trim($part);
                if (is_numeric($part)) $optIdArr[] = (int)$part;
            }
        }
    }

    $materialsCost = 0.0;
    $fabricConsumption = 0.0;
    $priceMultiplier = 0.0;

    // Собираем все spec_id для расчёта: базовая + опции
    $allSpecIds = array($specId);
    foreach ($optIdArr as $oid) {
        if ($oid > 0 && $oid !== $specId) $allSpecIds[] = $oid;
    }

    foreach ($allSpecIds as $sid) {
        if ($sid <= 0) continue;

        if ($sid === $specId) {
            // Базовая спецификация: материалы через GetProperty
            $materialsCost += calcMaterialsCostGetProp(49, $sid);

            // FABRIC_CONSUMPTION и PRICE_MULTIPLIER через Fetch
            $res = CIBlockElement::GetList(
                array(), array('ID' => $sid, 'IBLOCK_ID' => 49, 'ACTIVE' => 'Y'),
                false, false,
                array('ID', 'PROPERTY_FABRIC_CONSUMPTION', 'PROPERTY_PRICE_MULTIPLIER')
            );
            if ($ar = $res->Fetch()) {
                $fc = (float)$ar['PROPERTY_FABRIC_CONSUMPTION_VALUE'];
                if ($fc > 0) $fabricConsumption = $fc;

                $pm = (float)$ar['PROPERTY_PRICE_MULTIPLIER_VALUE'];
                if ($pm > 0) $priceMultiplier = $pm;
            }
        } else {
            // Опция: материалы через GetProperty
            $materialsCost += calcMaterialsCostGetProp(49, $sid);
        }
    }

    $pricePerMeter = 0;
    if ($fabricId > 0) {
        $pricePerMeter = getFabricPricePerMeter($fabricId);
    }
    $fabricTotalCost = $fabricConsumption * $pricePerMeter;

    $costPrice = $materialsCost + $fabricTotalCost;

    $finalCoeff = $priceMultiplier > 0 ? $priceMultiplier : ($marginCoeff > 0 ? $marginCoeff : 1.8);
    $recommendedPrice = (int)ceil($costPrice * $finalCoeff);

    echo json_encode(array(
        'success' => true,
        'cost_price' => round($costPrice, 2),
        'recommended_price' => $recommendedPrice,
        'details' => array(
            'materials_cost' => $materialsCost,
            'fabric_cost' => $fabricTotalCost,
            'fabric_consumption' => $fabricConsumption,
            'price_per_meter' => $pricePerMeter,
            'price_multiplier' => $finalCoeff,
            'option_ids' => $optIdArr,
        )
    ), JSON_UNESCAPED_UNICODE);
    exit();
}

echo json_encode(array('success' => false, 'error' => 'Неизвестный action'));
exit();