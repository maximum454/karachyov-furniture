<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("BX_BUFFER_USED", true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;

while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/json; charset=utf-8');

if (!Loader::includeModule("iblock")) {
    echo json_encode(['status' => 'error', 'message' => 'Module iblock missing']);
    die();
}

$iblockId = 47;
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : 'list');

/**
 * Гарантирует наличие свойств калькулятора в инфоблоке заявок (47).
 * Вызывается при каждом запросе: если свойства еще не созданы (например, после выгрузки файлов на сервер),
 * они создаются автоматически. Проверка дешевая (по коду), повторно не создает.
 */
function ensureCalculatorProperties($iblockId) {
    $codes = [
        'SPEC_ID'       => 'ID спецификации (калькулятор)',
        'FABRIC_ID'     => 'ID ткани (калькулятор)',
        'HAS_MECHANISM' => 'Подъемный механизм (Y/N)',
        'COST_PRICE'    => 'Себестоимость (калькулятор)',
        'PRICE_TOTAL'   => 'Цена для клиента (калькулятор)',
    ];

    foreach ($codes as $code => $name) {
        $exists = false;
        $rsProp = CIBlockProperty::GetList([], ['IBLOCK_ID' => $iblockId, 'CODE' => $code]);
        if ($rsProp->Fetch()) {
            $exists = true;
        }
        if (!$exists) {
            $prop = new CIBlockProperty;
            $prop->Add([
                'IBLOCK_ID'    => $iblockId,
                'NAME'         => $name,
                'ACTIVE'       => 'Y',
                'SORT'         => 500,
                'CODE'         => $code,
                'PROPERTY_TYPE'=> 'S',
                'MULTIPLE'     => 'N',
            ]);
        }
    }
}

try {
    ensureCalculatorProperties($iblockId);

    // --- ACTION 1: Список заявок ---
    if ($action === 'list') {
        $arLeads = [];

        $rsElements = CIBlockElement::GetList(
            ["ID" => "DESC"],
            ["IBLOCK_ID" => $iblockId, "ACTIVE" => "Y"],
            false,
            false,
            ["ID", "NAME", "PREVIEW_TEXT", "DATE_ACTIVE_FROM", "DATE_CREATE", "TIMESTAMP_X"]
        );

        while ($ob = $rsElements->GetNextElement()) {
            $fields = $ob->GetFields();
            $props = $ob->GetProperties();

            // 1. Статус (STATUS)
            $statusXmlId = 'NEW';
            if (is_array($props) && isset($props['STATUS']) && is_array($props['STATUS'])) {
                if (!empty($props['STATUS']['VALUE_XML_ID'])) {
                    $statusXmlId = $props['STATUS']['VALUE_XML_ID'];
                }
            }

            // 2. Привязанный товар (PRODUCT)
            $productId = 0;
            $productName = '';
            if (is_array($props) && isset($props['PRODUCT']) && is_array($props['PRODUCT'])) {
                $productId = intval($props['PRODUCT']['VALUE']);
            }

            if ($productId > 0) {
                $rsProduct = CIBlockElement::GetByID($productId);
                if ($arProduct = $rsProduct->GetNext()) {
                    $productName = $arProduct['NAME'];
                }
            }

            // 3. Форматирование даты
            $rawDate = !empty($fields['DATE_ACTIVE_FROM']) ? $fields['DATE_ACTIVE_FROM'] : $fields['DATE_CREATE'];
            $formattedDate = '';
            if (!empty($rawDate)) {
                if (function_exists('MakeTimeStamp')) {
                    $timestamp = MakeTimeStamp($rawDate);
                    $formattedDate = $timestamp ? date('d.m H:i', $timestamp) : $rawDate;
                } else {
                    $formattedDate = $rawDate;
                }
            }

            // 3a. Время последнего изменения — для бейджа "завис" на фронте (unix timestamp)
            $updatedTimestamp = 0;
            if (!empty($fields['TIMESTAMP_X'])) {
                $ts = MakeTimeStamp($fields['TIMESTAMP_X']);
                $updatedTimestamp = $ts ? $ts : 0;
            }

            // 4. Доп. свойства (Phone, Size, Fabric, Options, Address)
            $phone   = (is_array($props) && isset($props['PHONE']['VALUE']))   ? $props['PHONE']['VALUE']   : '';
            $size    = (is_array($props) && isset($props['SIZE']['VALUE']))    ? $props['SIZE']['VALUE']    : '';
            $fabric  = (is_array($props) && isset($props['FABRIC']['VALUE']))  ? $props['FABRIC']['VALUE']  : '';
            $options = (is_array($props) && isset($props['OPTIONS']['VALUE'])) ? $props['OPTIONS']['VALUE'] : '';
            $address = (is_array($props) && isset($props['ADDRESS']['VALUE'])) ? $props['ADDRESS']['VALUE'] : '';

            // 4a. Поля калькулятора
            $specId        = (is_array($props) && isset($props['SPEC_ID']['VALUE']))       ? trim((string)$props['SPEC_ID']['VALUE'])       : '';
            $fabricId      = (is_array($props) && isset($props['FABRIC_ID']['VALUE']))     ? trim((string)$props['FABRIC_ID']['VALUE'])     : '';
            $hasMechanism  = (is_array($props) && isset($props['HAS_MECHANISM']['VALUE'])) ? ($props['HAS_MECHANISM']['VALUE'] === 'Y')     : false;
            $costPrice     = (is_array($props) && isset($props['COST_PRICE']['VALUE']))    ? (float)$props['COST_PRICE']['VALUE']           : 0;
            $priceTotal    = (is_array($props) && isset($props['PRICE_TOTAL']['VALUE']))   ? (float)$props['PRICE_TOTAL']['VALUE']          : 0;

            $arLeads[] = [
                'id'           => $fields['ID'],
                'name'         => $fields['NAME'],
                'text'         => $fields['PREVIEW_TEXT'],
                'date'         => $formattedDate,
                'status'       => $statusXmlId,
                'product_id'   => $productId,
                'product_name' => $productName,
                'phone'        => is_array($phone) ? implode(', ', $phone) : $phone,
                'size'         => is_array($size) ? implode(', ', $size) : $size,
                'fabric'       => is_array($fabric) ? implode(', ', $fabric) : $fabric,
                'options'      => is_array($options) ? implode(', ', $options) : $options,
                'address'      => is_array($address) ? implode(', ', $address) : $address,
                'spec_id'          => $specId,
                'fabric_id'        => $fabricId,
                'has_mechanism'    => $hasMechanism,
                'cost_price'       => $costPrice,
                'price_total'      => $priceTotal,
                'updated_timestamp'=> $updatedTimestamp,
            ];
        }

        echo json_encode(['status' => 'ok', 'data' => $arLeads], JSON_UNESCAPED_UNICODE);
        die();
    }

    // --- ACTION 2: Обновление статуса (drag-and-drop в канбане) ---
    if ($action === 'update_status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $leadId = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $newStatus = isset($_POST['status']) ? htmlspecialcharsbx($_POST['status']) : '';

        if ($leadId > 0 && !empty($newStatus)) {
            $propertyEnums = CIBlockPropertyEnum::GetList([], [
                "IBLOCK_ID" => $iblockId,
                "CODE"      => "STATUS",
                "XML_ID"    => $newStatus
            ]);

            if ($enum = $propertyEnums->GetNext()) {
                CIBlockElement::SetPropertyValuesEx($leadId, $iblockId, ['STATUS' => $enum['ID']]);

                // Сбрасываем кэш инфоблока, чтобы GetList сразу отдал новый статус
                CIBlock::ClearIBlockTagCache($iblockId);

                echo json_encode(['status' => 'ok']);
                die();
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Не найден вариант статуса с XML_ID: ' . $newStatus
                ], JSON_UNESCAPED_UNICODE);
                die();
            }
        }

        echo json_encode(['status' => 'error', 'message' => 'Invalid leadId or status']);
        die();
    }

    // --- ACTION 3: Создание нового заказа вручную из канбана ---
    if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $name    = !empty($_POST['name']) ? trim($_POST['name']) : 'Новый заказ';
        $phone   = !empty($_POST['phone']) ? trim($_POST['phone']) : '';
        $size    = !empty($_POST['size']) ? trim($_POST['size']) : '';
        $fabric  = !empty($_POST['fabric']) ? trim($_POST['fabric']) : '';
        $options = !empty($_POST['options']) ? trim($_POST['options']) : '';
        $address = !empty($_POST['address']) ? trim($_POST['address']) : '';
        $text    = !empty($_POST['text']) ? trim($_POST['text']) : '';
        $status  = !empty($_POST['status']) ? trim($_POST['status']) : 'NEW';

        $statusEnumId = false;
        $propertyEnums = CIBlockPropertyEnum::GetList([], ["IBLOCK_ID" => $iblockId, "CODE" => "STATUS", "XML_ID" => $status]);
        if ($enum = $propertyEnums->GetNext()) {
            $statusEnumId = $enum['ID'];
        }

        $el = new CIBlockElement;
        $arFields = [
            "IBLOCK_ID"        => $iblockId,
            "NAME"             => $name,
            "ACTIVE"           => "Y",
            "PREVIEW_TEXT"     => $text,
            "DATE_ACTIVE_FROM" => ConvertTimeStamp(time(), "FULL"),
            "PROPERTY_VALUES"  => [
                "PHONE"   => $phone,
                "SIZE"    => $size,
                "FABRIC"  => $fabric,
                "OPTIONS" => $options,
                "ADDRESS" => $address,
                "SPEC_ID"       => isset($_POST['spec_id']) ? trim((string)$_POST['spec_id']) : '',
                "FABRIC_ID"     => isset($_POST['fabric_id']) ? trim((string)$_POST['fabric_id']) : '',
                "HAS_MECHANISM" => (isset($_POST['has_mechanism']) && $_POST['has_mechanism'] === 'Y') ? 'Y' : 'N',
                "COST_PRICE"    => isset($_POST['cost_price']) ? (float)$_POST['cost_price'] : 0,
                "PRICE_TOTAL"   => isset($_POST['price_total']) ? (float)$_POST['price_total'] : 0,
            ]
        ];

        if ($statusEnumId) {
            $arFields["PROPERTY_VALUES"]["STATUS"] = $statusEnumId;
        }

        if ($newId = $el->Add($arFields)) {
            CIBlock::ClearIBlockTagCache($iblockId);
            echo json_encode(['status' => 'ok', 'id' => $newId], JSON_UNESCAPED_UNICODE);
            die();
        } else {
            echo json_encode(['status' => 'error', 'message' => $el->LAST_ERROR], JSON_UNESCAPED_UNICODE);
            die();
        }
    }

    // --- ACTION 4: Сохранение редактирования заказа ---
    if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $leadId  = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $name    = !empty($_POST['name']) ? trim($_POST['name']) : '';
        $phone   = !empty($_POST['phone']) ? trim($_POST['phone']) : '';
        $size    = !empty($_POST['size']) ? trim($_POST['size']) : '';
        $fabric  = !empty($_POST['fabric']) ? trim($_POST['fabric']) : '';
        $options = !empty($_POST['options']) ? trim($_POST['options']) : '';
        $address = !empty($_POST['address']) ? trim($_POST['address']) : '';
        $text    = isset($_POST['text']) ? trim($_POST['text']) : '';

        if ($leadId > 0) {
            $el = new CIBlockElement;

            $arFields = [
                "PREVIEW_TEXT" => $text
            ];
            if (!empty($name)) {
                $arFields["NAME"] = $name;
            }

            $el->Update($leadId, $arFields);

            // Обновляем свойства
            CIBlockElement::SetPropertyValuesEx($leadId, $iblockId, [
                "PHONE"   => $phone,
                "SIZE"    => $size,
                "FABRIC"  => $fabric,
                "OPTIONS" => $options,
                "ADDRESS" => $address,
                "SPEC_ID"       => isset($_POST['spec_id']) ? trim((string)$_POST['spec_id']) : '',
                "FABRIC_ID"     => isset($_POST['fabric_id']) ? trim((string)$_POST['fabric_id']) : '',
                "HAS_MECHANISM" => (isset($_POST['has_mechanism']) && $_POST['has_mechanism'] === 'Y') ? 'Y' : 'N',
                "COST_PRICE"    => isset($_POST['cost_price']) ? (float)$_POST['cost_price'] : 0,
                "PRICE_TOTAL"   => isset($_POST['price_total']) ? (float)$_POST['price_total'] : 0,
            ]);

            CIBlock::ClearIBlockTagCache($iblockId);

            echo json_encode(['status' => 'ok'], JSON_UNESCAPED_UNICODE);
            die();
        }

        echo json_encode(['status' => 'error', 'message' => 'Не указан ID заказа']);
        die();
    }

} catch (Throwable $e) {
    while (ob_get_level()) { ob_end_clean(); }
    echo json_encode([
        'status' => 'exception',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    die();
}