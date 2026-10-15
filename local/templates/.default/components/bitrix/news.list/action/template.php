<div class="e-news-out">
<?foreach($arResult["ITEMS"] as $arItem):?>
    <?
    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
    ?>
     <div class="e-news" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
        <div class="row">
            <div class="col-sm-4">
            <?if(!empty($arItem["PREVIEW_PICTURE"]["SRC"])):?>
                <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>" class="img-responsive">
            <?else:?>
                <img src="<?=$templateFolder?>/news01.jpg" alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>" class="img-responsive">
            <?endif;?>
            </div>
            <div class="col-sm-8">
                <div class="b-news-info">
                    <div class="news-date-el"><?echo $arItem["DISPLAY_ACTIVE_FROM"]?></div>
                    <h3><?echo $arItem["NAME"]?></h3>
                    <p><?echo $arItem["PREVIEW_TEXT"];?></p>
                    <a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn-styler">Подробнее</a>
                </div>
            </div>
        </div>
    </div>
<?endforeach;?>
</div>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
<?=$arResult["NAV_STRING"]?>
<?endif;?>