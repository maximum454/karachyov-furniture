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
<!-- 3. ADVANTAGES (ПРЕИМУЩЕСТВА) -->
<section class="advantages" id="advantages">
    <div class="advantages__container">
        <h2 class="visually-hidden">Наши преимущества</h2>
        <ul class="advantages__list">
            <?foreach($arResult["ITEMS"] as $arItem):?>
                <?
                $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                ?>
                <li class="advantages__item feature-card"  id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                    <div class="feature-card__icon">
                        <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"
                             width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>"
                             height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>"
                             alt="<?=htmlspecialcharsbx($arItem["PREVIEW_PICTURE"]["ALT"])?>"
                             title="<?=htmlspecialcharsbx($arItem["PREVIEW_PICTURE"]["TITLE"])?>"
                             aria-hidden="true"
                        >
                    </div>
                    <h3 class="feature-card__title"><?=htmlspecialcharsbx($arItem["NAME"])?></h3>
                    <p class="feature-card__text"> <?=htmlspecialcharsbx($arItem["PREVIEW_TEXT"]);?></p>
                </li>
            <?endforeach;?>
        </ul>
    </div>
</section>





