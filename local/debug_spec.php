<?php
// ============================================================
// ВРЕМЕННЫЙ диагностический скрипт. Только чтение, ничего не меняет.
// Выводит структуру инфоблоков каталога/спецификаций/тканей,
// чтобы понять, почему на карточке товара не выводятся размеры и опции.
// ПОСЛЕ ДИАГНОСТИКИ ФАЙЛ НУЖНО УДАЛИТЬ С СЕРВЕРА!
// ============================================================
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("BX_NO_ACCELERATOR_RESET", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

header('Content-Type: text/plain; charset=utf-8');

echo "PHP: " . PHP_VERSION . "\n";
echo "Bitrix: " . (defined('SM_VERSION') ? SM_VERSION : '?') . "\n\n";

function dumpProp($p) {
    $line = "  [{$p['ID']}] CODE={$p['CODE']} | NAME={$p['NAME']} | TYPE={$p['PROPERTY_TYPE']}";
    if ($p['USER_TYPE']) $line .= "/{$p['USER_TYPE']}";
    $line .= " | MULTIPLE={$p['MULTIPLE']}";
    echo $line . "\n";
    if ($p['PROPERTY_TYPE'] === 'L') {
        echo "      enum:\n";
        $rsEnum = CIBlockPropertyEnum::GetList(['SORT' => 'ASC'], ['IBLOCK_ID' => $p['IBLOCK_ID'], 'PROPERTY_ID' => $p['ID']]);
        while ($e = $rsEnum->Fetch()) {
            echo "        - ID={$e['ID']} VALUE=\"{$e['VALUE']}\" XML_ID=\"{$e['XML_ID']}\"\n";
        }
    }
}

// 1. Инфоблоки 46-51 (каталог, спецификации, материалы, ткани, SKU, заявки)
echo "=== ИНФОБЛОКИ (ID 46-51) ===\n";
$rsIb = CIBlock::GetList(['SORT' => 'ASC'], ['ID' => [46, 47, 48, 49, 50, 51]]);
while ($ib = $rsIb->Fetch()) {
    echo "[{$ib['ID']}] {$ib['NAME']} | CODE=\"{$ib['CODE']}\"\n";
}
echo "\n";

// 2. Свойства ИБ 49 (спецификации) — ключ: XML_ID у SPEC_TYPE
echo "=== СВОЙСТВА ИБ 49 (спецификации) ===\n";
$rsProps = CIBlockProperty::GetList(['SORT' => 'ASC'], ['IBLOCK_ID' => 49]);
while ($p = $rsProps->Fetch()) {
    dumpProp($p);
}
echo "\n";

// 3. Элементы ИБ 49 — спецификации: тип, размер, материалы
echo "=== СПЕЦИФИКАЦИИ (ИБ 49) ===\n";
$rsEl = CIBlockElement::GetList(['SORT' => 'ASC', 'NAME' => 'ASC'], ['IBLOCK_ID' => 49, 'ACTIVE' => 'Y'], false, false, ['ID', 'NAME']);
while ($ob = $rsEl->GetNextElement()) {
    $f = $ob->GetFields();
    $pr = $ob->GetProperties();
    $specType = is_array($pr['SPEC_TYPE']) ? ($pr['SPEC_TYPE']['VALUE'] . ' | XML_ID=' . $pr['SPEC_TYPE']['VALUE_XML_ID']) : '(нет)';
    $size = is_array($pr['SIZE']) ? ($pr['SIZE']['VALUE'] . ' | XML_ID=' . $pr['SIZE']['VALUE_XML_ID']) : '(нет)';
    echo "[{$f['ID']}] \"{$f['NAME']}\" | SPEC_TYPE: {$specType} | SIZE: {$size}\n";
}
echo "\n";

// 4. Ищем ИБ каталога: у которого есть свойство SPECIFICATIONS, и выводим товары с привязкой
echo "=== КАТАЛОГ ТОВАРОВ (ИБ со свойством SPECIFICATIONS) ===\n";
$rsIbAll = CIBlock::GetList(['SORT' => 'ASC']);
while ($ib = $rsIbAll->Fetch()) {
    $hasSpec = false;
    $rsProps = CIBlockProperty::GetList([], ['IBLOCK_ID' => $ib['ID']]);
    while ($p = $rsProps->Fetch()) {
        if ($p['CODE'] === 'SPECIFICATIONS' || $p['CODE'] === 'FABRICS') {
            $hasSpec = true;
            echo "[{$ib['ID']}] {$ib['NAME']} | CODE=\"{$ib['CODE']}\" | свойство: {$p['CODE']} (TYPE={$p['PROPERTY_TYPE']})\n";
        }
    }
    if ($hasSpec) {
        $rsEl = CIBlockElement::GetList(['SORT' => 'ASC'], ['IBLOCK_ID' => $ib['ID'], 'ACTIVE' => 'Y'], false, false, ['ID', 'NAME']);
        while ($ob = $rsEl->GetNextElement()) {
            $f = $ob->GetFields();
            $pr = $ob->GetProperties();
            $linkVal = isset($pr['SPECIFICATIONS']['VALUE']) ? $pr['SPECIFICATIONS']['VALUE'] : null;
            $linkTxt = '';
            if (is_array($linkVal)) {
                foreach ($linkVal as $v) {
                    $linkTxt .= (strlen($linkTxt) ? ', ' : '') . $v;
                }
            } elseif (!empty($linkVal)) {
                $linkTxt = $linkVal;
            }
            $fabVal = isset($pr['FABRICS']['VALUE']) ? $pr['FABRICS']['VALUE'] : null;
            $fabTxt = is_array($fabVal) ? implode(', ', $fabVal) : (string)$fabVal;
            echo "  Товар [{$f['ID']}] \"{$f['NAME']}\" | SPECIFICATIONS=[{$linkTxt}] | FABRICS=[{$fabTxt}]\n";
        }
        echo "\n";
    }
}
echo "ГОТОВО\n";