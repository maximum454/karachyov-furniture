<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @var string $strElementEdit */
/** @var string $strElementDelete */
/** @var array $arElementDeleteParams */
/** @var array $skuTemplate */
/** @var array $templateData */
global $APPLICATION;
?>

<div class="b-catalog__inner">
    <div class="container-fluid">
        <div class="row">
            <? foreach ($arResult['ITEMS'] as $key => $arItem):
                if($arItem[DISPLAY_PROPERTIES][DISCOUNT][VALUE] == 'Да'):
                    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
                    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
                    $strMainID = $this->GetEditAreaId($arItem['ID']);

                    $arItemIDs = array(
                        'ID' => $strMainID,
                        'PICT' => $strMainID . '_pict',
                        'SECOND_PICT' => $strMainID . '_secondpict',
                        'MAIN_PROPS' => $strMainID . '_main_props',

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
                        'BASKET_PROP_DIV' => $strMainID . '_basket_prop'
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
                    if (isset($arItem['MIN_PRICE']) || isset($arItem['RATIO_PRICE']))
                        $minPrice = (isset($arItem['RATIO_PRICE']) ? $arItem['RATIO_PRICE'] : $arItem['MIN_PRICE']);
                    ?>

                    <div class="col-md-6 col-lg-4 col-sm-6 no-pad">
                        <div class="b-catalog__item" id="<?= $strMainID; ?>">
                            <div class="b-catalog__img">
                                <img src="<?= $arItem['PREVIEW_PICTURE']['SRC']; ?>" alt="<?= $imgTitle; ?>">
                            </div>
                            <div class="b-catalog__txt">
                                <a href="<?= $arItem['DETAIL_PAGE_URL']; ?>" title="<?= $productTitle; ?>" class="b-catalog__name" style="display: block;"><?= $productTitle; ?></a>
                                <div class="b-catalog__size"><?=$arItem['DISPLAY_PROPERTIES']['SIZE']['VALUE']?></div>
                                <div class="b-catalog__city"><?=$arItem['DISPLAY_PROPERTIES']['COUNTRY']['VALUE']?></div>
                                <div class="b-catalog__price" id="<?= $arItemIDs['PRICE']; ?>"><?=$arItem['DISPLAY_PROPERTIES']['PRICE']['VALUE']?> 
                                    <? if ($arItem['DISPLAY_PROPERTIES']['PRICE_FOR']['VALUE']){?>
                                        <?echo $arItem['DISPLAY_PROPERTIES']['PRICE_FOR']['VALUE'];?>
                                    <?}?></div>
                            </div>
                        </div>
                    </div>
                <?endif;?>
            <?endforeach;?>
            <?unset($arItem);?>
        </div>
    </div>
</div>