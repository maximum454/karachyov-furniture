<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
<?
if (!empty($arResult['ITEMS'])) {
    $templateLibrary = array('popup');
    $currencyList = '';
    if (!empty($arResult['CURRENCIES'])) {
        $templateLibrary[] = 'currency';
        $currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
    }
    $templateData = array(
        'TEMPLATE_THEME' => $this->GetFolder() . '/themes/' . $arParams['TEMPLATE_THEME'] . '/style.css',
        'TEMPLATE_CLASS' => 'bx_' . $arParams['TEMPLATE_THEME'],
        'TEMPLATE_LIBRARY' => $templateLibrary,
        'CURRENCIES' => $currencyList
    );
    unset($currencyList, $templateLibrary);

    $skuTemplate = array();
    if (!empty($arResult['SKU_PROPS'])) {
        foreach ($arResult['SKU_PROPS'] as $arProp) {
            $propId = $arProp['ID'];
            $skuTemplate[$propId] = array(
                'SCROLL' => array(
                    'START' => '',
                    'FINISH' => '',
                ),
                'FULL' => array(
                    'START' => '',
                    'FINISH' => '',
                ),
                'ITEMS' => array()
            );
            $templateRow = '';
            if ('TEXT' == $arProp['SHOW_MODE']) {
                $skuTemplate[$propId]['SCROLL']['START'] = '<div class="bx_item_detail_size full" id="#ITEM#_prop_' . $propId . '_cont">' .
                    '<span class="bx_item_section_name_gray">' . htmlspecialcharsbx($arProp['NAME']) . '</span>' .
                    '<div class="bx_size_scroller_container"><div class="bx_size"><ul id="#ITEM#_prop_' . $propId . '_list" style="width: #WIDTH#;">';;
                $skuTemplate[$propId]['SCROLL']['FINISH'] = '</ul></div>' .
                    '<div class="bx_slide_left" id="#ITEM#_prop_' . $propId . '_left" data-treevalue="' . $propId . '" style=""></div>' .
                    '<div class="bx_slide_right" id="#ITEM#_prop_' . $propId . '_right" data-treevalue="' . $propId . '" style=""></div>' .
                    '</div></div>';

                $skuTemplate[$propId]['FULL']['START'] = '<div class="bx_item_detail_size" id="#ITEM#_prop_' . $propId . '_cont">' .
                    '<span class="bx_item_section_name_gray">' . htmlspecialcharsbx($arProp['NAME']) . '</span>' .
                    '<div class="bx_size_scroller_container"><div class="bx_size"><ul id="#ITEM#_prop_' . $propId . '_list" style="width: #WIDTH#;">';;
                $skuTemplate[$propId]['FULL']['FINISH'] = '</ul></div>' .
                    '<div class="bx_slide_left" id="#ITEM#_prop_' . $propId . '_left" data-treevalue="' . $propId . '" style="display: none;"></div>' .
                    '<div class="bx_slide_right" id="#ITEM#_prop_' . $propId . '_right" data-treevalue="' . $propId . '" style="display: none;"></div>' .
                    '</div></div>';
                foreach ($arProp['VALUES'] as $value) {
                    $value['NAME'] = htmlspecialcharsbx($value['NAME']);
                    $skuTemplate[$propId]['ITEMS'][$value['ID']] = '<li data-treevalue="' . $propId . '_' . $value['ID'] .
                        '" data-onevalue="' . $value['ID'] . '" style="width: #WIDTH#;" title="' . $value['NAME'] . '"><i></i><span class="cnt">' . $value['NAME'] . '</span></li>';
                }
                unset($value);
            } elseif ('PICT' == $arProp['SHOW_MODE']) {
                $skuTemplate[$propId]['SCROLL']['START'] = '<div class="bx_item_detail_scu full" id="#ITEM#_prop_' . $propId . '_cont">' .
                    '<span class="bx_item_section_name_gray">' . htmlspecialcharsbx($arProp['NAME']) . '</span>' .
                    '<div class="bx_scu_scroller_container"><div class="bx_scu"><ul id="#ITEM#_prop_' . $propId . '_list" style="width: #WIDTH#;">';
                $skuTemplate[$propId]['SCROLL']['FINISH'] = '</ul></div>' .
                    '<div class="bx_slide_left" id="#ITEM#_prop_' . $propId . '_left" data-treevalue="' . $propId . '" style=""></div>' .
                    '<div class="bx_slide_right" id="#ITEM#_prop_' . $propId . '_right" data-treevalue="' . $propId . '" style=""></div>' .
                    '</div></div>';

                $skuTemplate[$propId]['FULL']['START'] = '<div class="bx_item_detail_scu" id="#ITEM#_prop_' . $propId . '_cont">' .
                    '<span class="bx_item_section_name_gray">' . htmlspecialcharsbx($arProp['NAME']) . '</span>' .
                    '<div class="bx_scu_scroller_container"><div class="bx_scu"><ul id="#ITEM#_prop_' . $propId . '_list" style="width: #WIDTH#;">';
                $skuTemplate[$propId]['FULL']['FINISH'] = '</ul></div>' .
                    '<div class="bx_slide_left" id="#ITEM#_prop_' . $propId . '_left" data-treevalue="' . $propId . '" style="display: none;"></div>' .
                    '<div class="bx_slide_right" id="#ITEM#_prop_' . $propId . '_right" data-treevalue="' . $propId . '" style="display: none;"></div>' .
                    '</div></div>';
                foreach ($arProp['VALUES'] as $value) {
                    $value['NAME'] = htmlspecialcharsbx($value['NAME']);
                    $skuTemplate[$propId]['ITEMS'][$value['ID']] = '<li data-treevalue="' . $propId . '_' . $value['ID'] .
                        '" data-onevalue="' . $value['ID'] . '" style="width: #WIDTH#; padding-top: #WIDTH#;"><i title="' . $value['NAME'] . '"></i>' .
                        '<span class="cnt"><span class="cnt_item" style="background-image:url(\'' . $value['PICT']['SRC'] . '\');" title="' . $value['NAME'] . '"></span></span></li>';
                }
                unset($value);
            }
        }
        unset($templateRow, $arProp);
    }

    if ($arParams["DISPLAY_TOP_PAGER"]) {
        ?><?= $arResult["NAV_STRING"]; ?><?
    }

    $strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
    $strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
    $arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));

    if ($arParams['HIDE_SECTION_DESCRIPTION'] !== 'Y') { ?>
        <div class="bx-section-desc <?= $templateData['TEMPLATE_CLASS']; ?>">
            <p class="bx-section-desc-post"><?= $arResult["DESCRIPTION"] ?></p>
        </div>
    <? } ?>
    <div class="b-catalog__inner">
        <div class="container-fluid">
            <div class="row">
                <? foreach ($arResult['ITEMS'] as $key => $arItem) {
                    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
                    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
                    $strMainID = $this->GetEditAreaId($arItem['ID']);

                    $arItemIDs = array(
                        'ID' => $strMainID,
                        'PICT' => $strMainID . '_pict',
                        'SECOND_PICT' => $strMainID . '_secondpict',
                        'STICKER_ID' => $strMainID . '_sticker',
                        'SECOND_STICKER_ID' => $strMainID . '_secondsticker',
                        'QUANTITY' => $strMainID . '_quantity',
                        'QUANTITY_DOWN' => $strMainID . '_quant_down',
                        'QUANTITY_UP' => $strMainID . '_quant_up',
                        'QUANTITY_MEASURE' => $strMainID . '_quant_measure',
                        'BUY_LINK' => $strMainID . '_buy_link',
                        'BASKET_ACTIONS' => $strMainID . '_basket_actions',
                        'NOT_AVAILABLE_MESS' => $strMainID . '_not_avail',
                        'SUBSCRIBE_LINK' => $strMainID . '_subscribe',
                        'COMPARE_LINK' => $strMainID . '_compare_link',

                        'PRICE' => $strMainID . '_price',
                        'DSC_PERC' => $strMainID . '_dsc_perc',
                        'SECOND_DSC_PERC' => $strMainID . '_second_dsc_perc',
                        'PROP_DIV' => $strMainID . '_sku_tree',
                        'PROP' => $strMainID . '_prop_',
                        'DISPLAY_PROP_DIV' => $strMainID . '_sku_prop',
                        'BASKET_PROP_DIV' => $strMainID . '_basket_prop',
                    );

                    $strObName = 'ob' . preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);

                    $productTitle = (
                    isset($arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] != ''
                        ? $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
                        : $arItem['NAME']
                    );
                    $imgTitle = (
                    isset($arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']) && $arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'] != ''
                        ? $arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']
                        : $arItem['NAME']
                    );

                    $minPrice = false;
                    if (isset($arItem['MIN_PRICE']) || isset($arItem['RATIO_PRICE'])) $minPrice = (isset($arItem['RATIO_PRICE']) ? $arItem['RATIO_PRICE'] : $arItem['MIN_PRICE']);?>
                    <?
                    $price = $arItem['DISPLAY_PROPERTIES']['PRICE']['VALUE'];
                    $discount = $arItem['DISPLAY_PROPERTIES']['DISCOUNT']['VALUE'];
                    $discount_2 = $arItem['DISPLAY_PROPERTIES']['DISCOUNT_2']['VALUE'];
                    $new = $arItem['DISPLAY_PROPERTIES']['NEW']['VALUE'];
                    ?>
                    <div class="col-md-6 col-lg-4 col-sm-6 no-pad">
                        <a href="<?= $arItem['DETAIL_PAGE_URL']; ?>" title="<?=$productTitle;?>" class="b-catalog__item" id="<?= $strMainID; ?>">
                            <div class="b-catalog__img">
                                <?if(!empty($arItem['PREVIEW_PICTURE']['SRC'])):?>
                                    <img src="<?= $arItem['PREVIEW_PICTURE']['SRC']; ?>" alt="<?= $imgTitle; ?>">
                                <?else:?>
                                    <img src="<?=$componentPath?>/images/news01.jpg" alt="<?= $imgTitle; ?>">
                                <?endif;?>
                                <div class="b-catalog__property">
                                    <?if(!empty($discount_2)):?>
                                        <div class="b-catalog__discount"><?=$discount_2?>%</div>
                                    <?endif;?>
                                    <?if(!empty($new== 'Да')):?>
                                        <div class="b-catalog__discount">New</div>
                                    <?endif;?>
                                </div>
                                <?if($arItem['DISPLAY_PROPERTIES']['COUNTRY']['VALUE_ENUM_ID']== '99'):?>
                                    <div class="b-catalog__label"><img src="<?=$templateFolder?>/img/3.png" alt=""></div>
                                <?elseif($arItem['DISPLAY_PROPERTIES']['COUNTRY']['VALUE_ENUM_ID']== '6'):?>
                                    <div class="b-catalog__label"><img src="<?=$templateFolder?>/img/2.png" alt=""></div>
                                <?elseif($arItem['DISPLAY_PROPERTIES']['COUNTRY']['VALUE_ENUM_ID']== '7'):?>
                                    <div class="b-catalog__label"><img src="<?=$templateFolder?>/img/11.png" alt=""></div>
                                <?elseif($arItem['DISPLAY_PROPERTIES']['COUNTRY']['VALUE_ENUM_ID']== '8'):?>
                                    <div class="b-catalog__label"><img src="<?=$templateFolder?>/img/5.png" alt=""></div>
                                <?elseif($arItem['DISPLAY_PROPERTIES']['COUNTRY']['VALUE_ENUM_ID']== '9'):?>
                                    <div class="b-catalog__label"><img src="<?=$templateFolder?>/img/3.png" alt=""></div>
                                <?elseif($arItem['DISPLAY_PROPERTIES']['COUNTRY']['VALUE_ENUM_ID']== '10'):?>
                                    <div class="b-catalog__label"><img src="<?=$templateFolder?>/img/8.png" alt=""></div>
                                <?elseif($arItem['DISPLAY_PROPERTIES']['COUNTRY']['VALUE_ENUM_ID']== '31'):?>
                                    <div class="b-catalog__label"><img src="<?=$templateFolder?>/img/9.png" alt=""></div>
                                <?elseif($arItem['DISPLAY_PROPERTIES']['COUNTRY']['VALUE_ENUM_ID']== '98'):?>
                                    <div class="b-catalog__label"><img src="<?=$templateFolder?>/img/3.png" alt=""></div>
                                <?elseif($arItem['DISPLAY_PROPERTIES']['COUNTRY']['VALUE_ENUM_ID']== '99'):?>
                                    <div class="b-catalog__label"><img src="<?=$templateFolder?>/img/3.png" alt=""></div>
                                <?elseif($arItem['DISPLAY_PROPERTIES']['COUNTRY']['VALUE_ENUM_ID']== '100'):?>
                                    <div class="b-catalog__label"><img src="<?=$templateFolder?>/img/6.png" alt=""></div>
                                <?elseif($arItem['DISPLAY_PROPERTIES']['COUNTRY']['VALUE_ENUM_ID']== '101'):?>
                                    <div class="b-catalog__label"><img src="<?=$templateFolder?>/img/7.png" alt=""></div>
                                <?elseif($arItem['DISPLAY_PROPERTIES']['COUNTRY']['VALUE_ENUM_ID']== '102'):?>
                                    <div class="b-catalog__label"><img src="<?=$templateFolder?>/img/10.png" alt=""></div>
                                <?elseif($arItem['DISPLAY_PROPERTIES']['COUNTRY']['VALUE_ENUM_ID']== '103'):?>
                                    <div class="b-catalog__label"><img src="<?=$templateFolder?>/img/12.png" alt=""></div>
                                <?endif?>
                            </div>
                            <div class="b-catalog__txt">
                                <div class="b-catalog__name" style="display: block;"><?=TruncateText($productTitle, 50);?></div>
                                <div class="b-catalog__size"><?=$arItem['DISPLAY_PROPERTIES']['SIZE']['VALUE']?></div>
                                <div class="b-catalog__city"><?=$arItem['DISPLAY_PROPERTIES']['COUNTRY']['VALUE']?></div>
                                <?if(!empty($discount_2)):?>

                                    <div class="b-catalog__price" id="<?= $arItemIDs['PRICE']; ?>"><s><?=$price?></s> <?= $price-(($price * $discount_2)/100)?> тенге/м2</div>
                                <?else:?>
                                    <div class="b-catalog__price" id="<?= $arItemIDs['PRICE']; ?>"><?=$arItem['DISPLAY_PROPERTIES']['PRICE']['VALUE']?> тенге/м2</div>
                                <?endif;?>

                            </div>
                        </a>
                    </div>
                <?}?>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        BX.message({
            BTN_MESSAGE_BASKET_REDIRECT: '<?=  GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_BASKET_REDIRECT'); ?>',
            BASKET_URL: '<?=  $arParams["BASKET_URL"]; ?>',
            ADD_TO_BASKET_OK: '<?=  GetMessageJS('ADD_TO_BASKET_OK'); ?>',
            TITLE_ERROR: '<?=  GetMessageJS('CT_BCS_CATALOG_TITLE_ERROR') ?>',
            TITLE_BASKET_PROPS: '<?=  GetMessageJS('CT_BCS_CATALOG_TITLE_BASKET_PROPS') ?>',
            TITLE_SUCCESSFUL: '<?=  GetMessageJS('ADD_TO_BASKET_OK'); ?>',
            BASKET_UNKNOWN_ERROR: '<?=  GetMessageJS('CT_BCS_CATALOG_BASKET_UNKNOWN_ERROR') ?>',
            BTN_MESSAGE_SEND_PROPS: '<?=  GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_SEND_PROPS'); ?>',
            BTN_MESSAGE_CLOSE: '<?=  GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_CLOSE') ?>',
            BTN_MESSAGE_CLOSE_POPUP: '<?=  GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_CLOSE_POPUP'); ?>',
            COMPARE_MESSAGE_OK: '<?=  GetMessageJS('CT_BCS_CATALOG_MESS_COMPARE_OK') ?>',
            COMPARE_UNKNOWN_ERROR: '<?=  GetMessageJS('CT_BCS_CATALOG_MESS_COMPARE_UNKNOWN_ERROR') ?>',
            COMPARE_TITLE: '<?=  GetMessageJS('CT_BCS_CATALOG_MESS_COMPARE_TITLE') ?>',
            BTN_MESSAGE_COMPARE_REDIRECT: '<?=  GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_COMPARE_REDIRECT') ?>',
            SITE_ID: '<?=  SITE_ID; ?>'
        });
    </script>
    <?
    if ($arParams["DISPLAY_BOTTOM_PAGER"]) {
        ?><?= $arResult["NAV_STRING"]; ?><?
    }
}