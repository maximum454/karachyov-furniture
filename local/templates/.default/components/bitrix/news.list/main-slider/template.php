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
<div class="b-slider js-slider">
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="b-slider__item" style="background-image: url('<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>')" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
        <div class="b-slider__caption">
            <div class="b-slider__head"><?echo $arItem["NAME"]?></div>
            <div class="b-slider__title"><?=$arItem["DISPLAY_PROPERTIES"][TITLE][VALUE]?></div>
            <a href="/catalog/" class="b-btn b-btn_slider-catalog">СМОТРЕТЬ КАТАЛОГ</a> <a href="#consult" class="b-btn b-btn_slider-consult popap_box" data-body="call">КОНСУЛЬТАЦИЯ</a>
        </div>
    </div>
<?endforeach;?>
</div>
