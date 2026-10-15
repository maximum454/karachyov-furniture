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

$iblockId = 47; // Инфоблок "Заявки / Лиды"

// Гарантируем наличие свойств калькулятора в инфоблоке заявок
foreach (['SPEC_ID' => 'ID спецификации', 'FABRIC_ID' => 'ID ткани', 'COST_PRICE' => 'Себестоимость', 'PRICE_TOTAL' => 'Цена для клиента'] as $code => $name) {
    $rsProp = CIBlockProperty::GetList([], ["IBLOCK_ID" => $iblockId, "CODE" => $code]);
    if (!$rsProp->Fetch()) {
        $prop = new CIBlockProperty;
        $prop->Add([
            'IBLOCK_ID'     => $iblockId,
            'NAME'          => $name,
            'ACTIVE'        => 'Y',
            'SORT'          => 500,
            'CODE'          => $code,
            'PROPERTY_TYPE' => 'S',
            'MULTIPLE'      => 'N',
        ]);
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["product_id"])) {
    $productId   = intval($_POST["product_id"]);
    $productName = !empty($_POST["product_name"]) ? htmlspecialcharsbx($_POST["product_name"]) : 'Кровать';
    $productSize = !empty($_POST["product_size"]) ? htmlspecialcharsbx($_POST["product_size"]) : '';
    $productFabric = !empty($_POST["product_fabric"]) ? htmlspecialcharsbx($_POST["product_fabric"]) : '';
    $productOptions = !empty($_POST["product_options"]) ? htmlspecialcharsbx($_POST["product_options"]) : '';

    // Поля калькулятора (передаются с витрины)
    $specId     = isset($_POST["spec_id"]) ? trim((string)$_POST["spec_id"]) : '';
    $fabricId   = isset($_POST["fabric_id"]) ? trim((string)$_POST["fabric_id"]) : '';
    $costPrice  = isset($_POST["cost_price"]) ? (float)$_POST["cost_price"] : 0;
    $priceTotal = isset($_POST["price_total"]) ? (float)$_POST["price_total"] : 0;

    // Получаем ID статуса "NEW"
    $statusEnumId = false;
    $propertyEnums = CIBlockPropertyEnum::GetList([], [
        "IBLOCK_ID" => $iblockId,
        "CODE"      => "STATUS",
        "XML_ID"    => "NEW"
    ]);
    if ($enum = $propertyEnums->GetNext()) {
        $statusEnumId = $enum['ID'];
    }

    // Формируем детальное описание заказа для менеджера
    $descriptionParts = [
        "Товар: " . $productName . " (ID: " . $productId . ")"
    ];
    if (!empty($productSize)) {
        $descriptionParts[] = "Размер: " . $productSize;
    }
    if (!empty($productFabric)) {
        $descriptionParts[] = "Ткань: " . $productFabric;
    }
    if (!empty($productOptions)) {
        $descriptionParts[] = "Комплектация и опции: " . $productOptions;
    }
    $previewText = implode("\n", $descriptionParts);

    $el = new CIBlockElement;

    // Свойства элемента
    $propValues = [
        "PRODUCT" => $productId,
        "SIZE"    => $productSize,
        "FABRIC"  => $productFabric,
        "OPTIONS" => $productOptions,
    ];

    // Поля калькулятора (опционально)
    if (!empty($specId)) {
        $propValues["SPEC_ID"] = $specId;
    }
    if (!empty($fabricId)) {
        $propValues["FABRIC_ID"] = $fabricId;
    }
    if ($costPrice > 0) {
        $propValues["COST_PRICE"] = $costPrice;
    }
    if ($priceTotal > 0) {
        $propValues["PRICE_TOTAL"] = $priceTotal;
    }

    if ($statusEnumId) {
        $propValues["STATUS"] = $statusEnumId;
    }

    $fields = [
        "IBLOCK_ID"       => $iblockId,
        "NAME"            => "Заявка из WA #" . $productId,
        "ACTIVE"          => "Y",
        "DATE_ACTIVE_FROM"=> ConvertTimeStamp(time(), "FULL"),
        "PREVIEW_TEXT"    => $previewText,
        "PROPERTY_VALUES" => $propValues
    ];

    if ($leadId = $el->Add($fields)) {
        // Обновляем название элемента, добавляя реальный № заявки
        $el->Update($leadId, [
            "NAME" => "Заявка №" . $leadId . " — " . $productName
        ]);

        echo json_encode(['status' => 'ok', 'lead_id' => $leadId]);
    } else {
        echo json_encode(['status' => 'error', 'message' => $el->LAST_ERROR]);
    }
    die();
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
die();