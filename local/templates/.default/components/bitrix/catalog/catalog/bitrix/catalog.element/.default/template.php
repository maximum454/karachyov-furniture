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

$strMainID = $this->GetEditAreaId($arResult['ID']);
$arItemIDs = array(
    'ID' => $strMainID,
    'PICT' => $strMainID . '_pict',
    'DISCOUNT_PICT_ID' => $strMainID . '_dsc_pict',
    'STICKER_ID' => $strMainID . '_sticker',
    'BIG_SLIDER_ID' => $strMainID . '_big_slider',
    'BIG_IMG_CONT_ID' => $strMainID . '_bigimg_cont',
    'SLIDER_CONT_ID' => $strMainID . '_slider_cont',
    'SLIDER_LIST' => $strMainID . '_slider_list',
    'SLIDER_LEFT' => $strMainID . '_slider_left',
    'SLIDER_RIGHT' => $strMainID . '_slider_right',
    'OLD_PRICE' => $strMainID . '_old_price',
    'PRICE' => $strMainID . '_price',
    'DISCOUNT_PRICE' => $strMainID . '_price_discount',
    'SLIDER_CONT_OF_ID' => $strMainID . '_slider_cont_',
    'SLIDER_LIST_OF_ID' => $strMainID . '_slider_list_',
    'SLIDER_LEFT_OF_ID' => $strMainID . '_slider_left_',
    'SLIDER_RIGHT_OF_ID' => $strMainID . '_slider_right_',
    'QUANTITY' => $strMainID . '_quantity',
    'QUANTITY_DOWN' => $strMainID . '_quant_down',
    'QUANTITY_UP' => $strMainID . '_quant_up',
    'QUANTITY_MEASURE' => $strMainID . '_quant_measure',
    'QUANTITY_LIMIT' => $strMainID . '_quant_limit',
    'BASIS_PRICE' => $strMainID . '_basis_price',
    'BUY_LINK' => $strMainID . '_buy_link',
    'ADD_BASKET_LINK' => $strMainID . '_add_basket_link',
    'BASKET_ACTIONS' => $strMainID . '_basket_actions',
    'NOT_AVAILABLE_MESS' => $strMainID . '_not_avail',
    'COMPARE_LINK' => $strMainID . '_compare_link',
    'PROP' => $strMainID . '_prop_',
    'PROP_DIV' => $strMainID . '_skudiv',
    'DISPLAY_PROP_DIV' => $strMainID . '_sku_prop',
    'OFFER_GROUP' => $strMainID . '_set_group_',
    'BASKET_PROP_DIV' => $strMainID . '_basket_prop',
    'SUBSCRIBE_LINK' => $strMainID . '_subscribe',
);
$strObName = 'ob' . preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);
$templateData['JS_OBJ'] = $strObName;

$strTitle = (
isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"] != ''
    ? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]
    : $arResult['NAME']
);
$strAlt = (
isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"] != ''
    ? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]
    : $arResult['NAME']
);
?>
<?reset($arResult['MORE_PHOTO']);
$arFirstPhoto = current($arResult['MORE_PHOTO']);
?>

<div class="b-card__slider">
    <div class="b-slider card__slider">
        <?if (!empty($arResult["PROPERTIES"]["SLIDER"]["VALUE"])):?>
            <?foreach($arResult["PROPERTIES"]["SLIDER"]["VALUE"] as $photo):?>
                <div class="b-slider__item" style="background-image: url(<?=CFile::GetPath($photo)?>)"></div>
            <?endforeach?>
            <?unset($photo); ?>
        <?endif?>

    </div>
    <?if(!empty($arResult['PREVIEW_TEXT'])):?>
        <div class="b-card__inner">
            <h2>Описание</h2>
            <div class="b-card__anons">
                <?=$arResult['PREVIEW_TEXT']?>
            </div>
        </div>
    <?endif;?>
</div>

<div class="b-card__info">
    <? if ('Y' == $arParams['DISPLAY_NAME']):?>
        <h2><?echo(isset($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] != '' ? $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] : $arResult["NAME"]); ?></h2>
    <?endif;?>
    <?
    $price = $arResult['DISPLAY_PROPERTIES']['PRICE']['VALUE'];
    $discount = $arResult['DISPLAY_PROPERTIES']['DISCOUNT']['VALUE'];
    $discount_2 = $arResult['DISPLAY_PROPERTIES']['DISCOUNT_2']['VALUE'];
    ?>
    <?if(!empty($discount_2)):?>
        <div class="card__price">
            цена: <span> <span id="lprice"><?= $price-(($price * $discount_2)/100)?></span> <? if ($arResult["DISPLAY_PROPERTIES"]['PRICE_FOR']){?>
                    <?echo $arResult['DISPLAY_PROPERTIES']['PRICE_FOR']['DISPLAY_VALUE'];?>
                <?}?></span>
        </div>
    <?else:?>
        <div class="card__price">
            цена: <span> <span id="lprice"><?=$price?></span>
                <? if ($arResult["DISPLAY_PROPERTIES"]['PRICE_FOR']){?>
                    <?echo $arResult['DISPLAY_PROPERTIES']['PRICE_FOR']['DISPLAY_VALUE'];?>
                <?}?>
            </span>
        </div>
    <?endif;?>





    <div class="card__calc">

        <?if(empty($arResult['DISPLAY_PROPERTIES']['LENGTH_BOARD']['VALUE'])):?>
            <div class="card__inner">
                <div data-trigger="spinner" id="spinner">
                    <a href="javascript:;" data-spin="down">-</a>
                    <input type="text" value="1" data-rule="quantity">
                    <a href="javascript:;" data-spin="up">+</a>
                </div>
            </div>
        <?else:?>
            <p>Введите метраж вашего помещения</p>
            <div class="card__inner">
                <form action="" class="form">
                    <div class="card__items">
                        <div class="card__item">
                            <label for="">Общая площадь комнаты(м)</label>
                            <input id="s-room" type="text"  value="">

                        </div>
                    </div>
                    <p>или</p>
                    <div class="card__items">
                        <div class="card__item">
                            <label for="">Длина комнаты(м)</label>
                            <input id="len-room" type="text"  value="<?=$arResult['DISPLAY_PROPERTIES']['LENGTH_ROOM']['VALUE']?>">
                        </div>
                        <div class="card__item">
                            <label for="">Ширина комнаты(м)</label>
                            <input id="width-room" type="text" value="<?=$arResult['DISPLAY_PROPERTIES']['WIDTH_ROOM']['VALUE']?>">
                        </div>
                    </div>
                    <div class="card__items">
                        <div class="card__item">
                            <label for="">Длина доски</label>
                            <input id="len-board" type="text"  disabled="disabled" value="<?=$arResult['DISPLAY_PROPERTIES']['LENGTH_BOARD']['VALUE']?>">
                        </div>
                        <div class="card__item">
                            <label for="">Ширина доски</label>
                            <input id="width-board" type="text" disabled="disabled" value="<?=$arResult['DISPLAY_PROPERTIES']['WIDTH_BOARD']['VALUE']?>">
                        </div>
                    </div>
                    <div class="card__items">
                        <div class="card__item">
                            <label for="">Число досок<br>в упаковке </label>
                            <input id="number-board" type="text"  disabled="disabled" value="<?=$arResult['DISPLAY_PROPERTIES']['NUMBER_BOARD']['VALUE']?>">
                        </div>
                        <div class="card__item">
                            <label for="">Число упаковок </label>
                            <input id="number-packaging" type="text"  disabled="disabled" value="">
                        </div>
                        <!--<div class="card__item">
                            <label for="">Укладка по</label>
                            <select class="card__select" name="" id="">
                                <option value="">Длине</option>
                                <option value="">Ширине</option>
                            </select>
                        </div>-->
                </form>
            </div>
        <?endif;?>

    </div>
    <div class="card__price card__price-error"><span></span></div>

    <?if(!empty($discount_2)):?>
        <div class="card__price card__price-itog">
            итого: <span><i id="spinner-value" data-price="<?=$price?>"> <?= $price-(($price * $discount_2)/100)?></i> тенге</span>
        </div>
    <?else:?>
        <div class="card__price card__price-itog">
            итого: <span><i id="spinner-value" data-price="<?=$price?>"> <?=$price?></i> тенге</span>
        </div>
    <?endif;?>



    <a href="#sales" class="b-btn b-btn_header popap_box" data-body="call">ХОЧУ КУПИТЬ</a>

    <? if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS']) : ?>
        <div class="b-card-char">
            <div class="title">ХАРАКТЕРИСТИКИ</div>
            <? if (!empty($arResult['DISPLAY_PROPERTIES'])): ?>
                <ul class="list char__list">
                    <?foreach ($arResult['DISPLAY_PROPERTIES'] as &$arOneProp): ?>
                        <?if(true):?>
                            <li>
                                <span class="ch-title"><?= $arOneProp['NAME']; ?></span>
                                <span class="ch-d"><i></i></span>
                                    <span class="ch-name"><? echo(
                                        is_array($arOneProp['DISPLAY_VALUE'])
                                            ? implode(' / ', $arOneProp['DISPLAY_VALUE'])
                                            : $arOneProp['DISPLAY_VALUE']
                                        ); ?></span>
                            </li>
                        <?endif;?>
                    <?endforeach;?>
                    <?unset($arOneProp); ?>
                </ul>
            <? endif; ?>
        </div>
    <?endif;?>
</div>



<? if (isset($arResult['OFFERS']) && !empty($arResult['OFFERS'])) {
    foreach ($arResult['JS_OFFERS'] as &$arOneJS) {
        if ($arOneJS['PRICE']['DISCOUNT_VALUE'] != $arOneJS['PRICE']['VALUE']) {
            $arOneJS['PRICE']['DISCOUNT_DIFF_PERCENT'] = -$arOneJS['PRICE']['DISCOUNT_DIFF_PERCENT'];
            $arOneJS['BASIS_PRICE']['DISCOUNT_DIFF_PERCENT'] = -$arOneJS['BASIS_PRICE']['DISCOUNT_DIFF_PERCENT'];
        }
        $strProps = '';
        if ($arResult['SHOW_OFFERS_PROPS']) {
            if (!empty($arOneJS['DISPLAY_PROPERTIES'])) {
                foreach ($arOneJS['DISPLAY_PROPERTIES'] as $arOneProp) {
                    $strProps .= '<dt>' . $arOneProp['NAME'] . '</dt><dd>' . (
                        is_array($arOneProp['VALUE'])
                            ? implode(' / ', $arOneProp['VALUE'])
                            : $arOneProp['VALUE']
                        ) . '</dd>';
                }
            }
        }
        $arOneJS['DISPLAY_PROPERTIES'] = $strProps;
    }
    if (isset($arOneJS))
        unset($arOneJS);
    $arJSParams = array(
        'CONFIG' => array(
            'USE_CATALOG' => $arResult['CATALOG'],
            'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
            'SHOW_PRICE' => true,
            'SHOW_DISCOUNT_PERCENT' => ($arParams['SHOW_DISCOUNT_PERCENT'] == 'Y'),
            'SHOW_OLD_PRICE' => ($arParams['SHOW_OLD_PRICE'] == 'Y'),
            'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
            'SHOW_SKU_PROPS' => $arResult['SHOW_OFFERS_PROPS'],
            'OFFER_GROUP' => $arResult['OFFER_GROUP'],
            'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE'],
            'SHOW_BASIS_PRICE' => ($arParams['SHOW_BASIS_PRICE'] == 'Y'),
            'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
            'SHOW_CLOSE_POPUP' => ($arParams['SHOW_CLOSE_POPUP'] == 'Y'),
            'USE_STICKERS' => true,
            'USE_SUBSCRIBE' => $showSubscribeBtn,
        ),
        'PRODUCT_TYPE' => $arResult['CATALOG_TYPE'],
        'VISUAL' => array(
            'ID' => $arItemIDs['ID'],
        ),
        'DEFAULT_PICTURE' => array(
            'PREVIEW_PICTURE' => $arResult['DEFAULT_PICTURE'],
            'DETAIL_PICTURE' => $arResult['DEFAULT_PICTURE']
        ),
        'PRODUCT' => array(
            'ID' => $arResult['ID'],
            'NAME' => $arResult['~NAME']
        ),
        'BASKET' => array(
            'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
            'BASKET_URL' => $arParams['BASKET_URL'],
            'SKU_PROPS' => $arResult['OFFERS_PROP_CODES'],
            'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
            'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
        ),
        'OFFERS' => $arResult['JS_OFFERS'],
        'OFFER_SELECTED' => $arResult['OFFERS_SELECTED'],
        'TREE_PROPS' => $arSkuProps
    );
    if ($arParams['DISPLAY_COMPARE']) {
        $arJSParams['COMPARE'] = array(
            'COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],
            'COMPARE_PATH' => $arParams['COMPARE_PATH']
        );
    }
} else {
    $emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
    if ('Y' == $arParams['ADD_PROPERTIES_TO_BASKET'] && !$emptyProductProperties) {
        ?>
        <div id="<? echo $arItemIDs['BASKET_PROP_DIV']; ?>" style="display: none;">
            <? if (!empty($arResult['PRODUCT_PROPERTIES_FILL'])) {
                foreach ($arResult['PRODUCT_PROPERTIES_FILL'] as $propID => $propInfo) {
                    ?>
                    <input type="hidden" name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]"
                           value="<? echo htmlspecialcharsbx($propInfo['ID']); ?>">
                    <?
                    if (isset($arResult['PRODUCT_PROPERTIES'][$propID]))
                        unset($arResult['PRODUCT_PROPERTIES'][$propID]);
                }
            }
            $emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
            if (!$emptyProductProperties) {?>
                <table>
                    <?foreach ($arResult['PRODUCT_PROPERTIES'] as $propID => $propInfo):?>
                        <tr>
                            <td><? echo $arResult['PROPERTIES'][$propID]['NAME']; ?></td>
                            <td>
                                <? if (
                                    'L' == $arResult['PROPERTIES'][$propID]['PROPERTY_TYPE']
                                    && 'C' == $arResult['PROPERTIES'][$propID]['LIST_TYPE']
                                ) {
                                    foreach ($propInfo['VALUES'] as $valueID => $value) {?>
                                        <label><input type="radio"
                                                      name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]"
                                                      value="<? echo $valueID; ?>" <? echo($valueID == $propInfo['SELECTED'] ? '"checked"' : ''); ?>><? echo $value; ?>
                                        </label><br>
                                    <?}
                                } else {?>
                                    <select name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]">
                                        <?foreach ($propInfo['VALUES'] as $valueID => $value) {?>
                                            <option value="<? echo $valueID; ?>" <? echo($valueID == $propInfo['SELECTED'] ? '"selected"' : ''); ?>><? echo $value; ?></option>
                                        <?}?>
                                    </select>
                                <?}?>
                            </td>
                        </tr>
                    <?endforeach;?>
                </table>

            <?}?>

        </div>
        <?
    }
    if ($arResult['MIN_PRICE']['DISCOUNT_VALUE'] != $arResult['MIN_PRICE']['VALUE']) {
        $arResult['MIN_PRICE']['DISCOUNT_DIFF_PERCENT'] = -$arResult['MIN_PRICE']['DISCOUNT_DIFF_PERCENT'];
        $arResult['MIN_BASIS_PRICE']['DISCOUNT_DIFF_PERCENT'] = -$arResult['MIN_BASIS_PRICE']['DISCOUNT_DIFF_PERCENT'];
    }
    $arJSParams = array(
        'CONFIG' => array(
            'USE_CATALOG' => $arResult['CATALOG'],
            'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
            'SHOW_PRICE' => (isset($arResult['MIN_PRICE']) && !empty($arResult['MIN_PRICE']) && is_array($arResult['MIN_PRICE'])),
            'SHOW_DISCOUNT_PERCENT' => ($arParams['SHOW_DISCOUNT_PERCENT'] == 'Y'),
            'SHOW_OLD_PRICE' => ($arParams['SHOW_OLD_PRICE'] == 'Y'),
            'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
            'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE'],
            'SHOW_BASIS_PRICE' => ($arParams['SHOW_BASIS_PRICE'] == 'Y'),
            'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
            'SHOW_CLOSE_POPUP' => ($arParams['SHOW_CLOSE_POPUP'] == 'Y'),
            'USE_STICKERS' => true,
            'USE_SUBSCRIBE' => $showSubscribeBtn,
        ),
        'VISUAL' => array(
            'ID' => $arItemIDs['ID'],
        ),
        'PRODUCT_TYPE' => $arResult['CATALOG_TYPE'],
        'PRODUCT' => array(
            'ID' => $arResult['ID'],
            'PICT' => $arFirstPhoto,
            'NAME' => $arResult['~NAME'],
            'SUBSCRIPTION' => true,
            'PRICE' => $arResult['MIN_PRICE'],
            'BASIS_PRICE' => $arResult['MIN_BASIS_PRICE'],
            'SLIDER_COUNT' => $arResult['MORE_PHOTO_COUNT'],
            'SLIDER' => $arResult['MORE_PHOTO'],
            'CAN_BUY' => $arResult['CAN_BUY'],
            'CHECK_QUANTITY' => $arResult['CHECK_QUANTITY'],
            'QUANTITY_FLOAT' => is_double($arResult['CATALOG_MEASURE_RATIO']),
            'MAX_QUANTITY' => $arResult['CATALOG_QUANTITY'],
            'STEP_QUANTITY' => $arResult['CATALOG_MEASURE_RATIO'],
        ),
        'BASKET' => array(
            'ADD_PROPS' => ($arParams['ADD_PROPERTIES_TO_BASKET'] == 'Y'),
            'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
            'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
            'EMPTY_PROPS' => $emptyProductProperties,
            'BASKET_URL' => $arParams['BASKET_URL'],
            'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
            'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
        )
    );
    if ($arParams['DISPLAY_COMPARE']) {
        $arJSParams['COMPARE'] = array(
            'COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],
            'COMPARE_PATH' => $arParams['COMPARE_PATH']
        );
    }
    unset($emptyProductProperties);
}
?>
<script type="text/javascript">
    var <? echo $strObName; ?> =
    new JCCatalogElement(<? echo CUtil::PhpToJSObject($arJSParams, false, true); ?>);
    BX.message({
        ECONOMY_INFO_MESSAGE: '<? echo GetMessageJS('CT_BCE_CATALOG_ECONOMY_INFO'); ?>',
        BASIS_PRICE_MESSAGE: '<? echo GetMessageJS('CT_BCE_CATALOG_MESS_BASIS_PRICE') ?>',
        TITLE_ERROR: '<? echo GetMessageJS('CT_BCE_CATALOG_TITLE_ERROR') ?>',
        TITLE_BASKET_PROPS: '<? echo GetMessageJS('CT_BCE_CATALOG_TITLE_BASKET_PROPS') ?>',
        BASKET_UNKNOWN_ERROR: '<? echo GetMessageJS('CT_BCE_CATALOG_BASKET_UNKNOWN_ERROR') ?>',
        BTN_SEND_PROPS: '<? echo GetMessageJS('CT_BCE_CATALOG_BTN_SEND_PROPS'); ?>',
        BTN_MESSAGE_BASKET_REDIRECT: '<? echo GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_BASKET_REDIRECT') ?>',
        BTN_MESSAGE_CLOSE: '<? echo GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE'); ?>',
        BTN_MESSAGE_CLOSE_POPUP: '<? echo GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE_POPUP'); ?>',
        TITLE_SUCCESSFUL: '<? echo GetMessageJS('CT_BCE_CATALOG_ADD_TO_BASKET_OK'); ?>',
        COMPARE_MESSAGE_OK: '<? echo GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_OK') ?>',
        COMPARE_UNKNOWN_ERROR: '<? echo GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_UNKNOWN_ERROR') ?>',
        COMPARE_TITLE: '<? echo GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_TITLE') ?>',
        BTN_MESSAGE_COMPARE_REDIRECT: '<? echo GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_COMPARE_REDIRECT') ?>',
        PRODUCT_GIFT_LABEL: '<? echo GetMessageJS('CT_BCE_CATALOG_PRODUCT_GIFT_LABEL') ?>',
        SITE_ID: '<? echo SITE_ID; ?>'
    });
</script>