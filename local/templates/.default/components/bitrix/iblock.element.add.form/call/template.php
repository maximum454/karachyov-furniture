<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
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
$this->setFrameMode(false);

if (!empty($arResult["ERRORS"])):?>
	<?ShowError(implode("<br />", $arResult["ERRORS"]))?>
<?endif;
if (strlen($arResult["MESSAGE"]) > 0):?>
	<?ShowNote($arResult["MESSAGE"])?>
<?endif?>
<form class="b-form-popup" name="iblock_add" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
	<?=bitrix_sessid_post()?>
	<?if ($arParams["MAX_FILE_SIZE"] > 0):?><input type="hidden" name="MAX_FILE_SIZE" value="<?=$arParams["MAX_FILE_SIZE"]?>" /><?endif?>
	<div class="arcticmodal-close">закрыть</div>
    <div class="b-form-popup__head">ОБРАТНАЯ СВЯЗЬ</div>
    <div class="b-form-popup__item">
        <input type="text" name="PROPERTY[NAME][0]" class="input-style" placeholder="<?=GetMessage("IBLOCK_FORM_NAME")?>">
    </div>
    <div class="b-form-popup__item">
        <input type="text" name="PROPERTY[5][0]" class="input-style" placeholder="<?=GetMessage("IBLOCK_FORM_PHONE")?>">
    </div>
    <input type="submit" class="b-btn b-btn_popup" name="iblock_submit" value="<?=GetMessage("IBLOCK_FORM_SUBMIT")?>" />
</form>