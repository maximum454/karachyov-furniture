<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>
<div class="mb-menu">
	<span></span>
	<span></span>
	<span></span>
</div>
<nav class="b-nav <?if(!empty($arResult[0][PARAMS])): echo"b-nav_footer"; endif;?>">
<?foreach($arResult as $arItem):
	if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
		continue;?>
	<?if($arItem["SELECTED"]):?>
		<a class="b-nav__item selected" href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
	<?else:?>
		<a class="b-nav__item" href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
	<?endif?>
<?endforeach?>
 </nav>
<div class="stone-b clearfix"></div>
<?endif?>