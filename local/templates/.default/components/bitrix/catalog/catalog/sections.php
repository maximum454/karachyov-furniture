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
$this->addExternalCss("/bitrix/css/main/bootstrap.css");
?>
<div class="container">
	<main>
	<div class="row">
		<div class="col-lg-3 col-md-4">
			<?$APPLICATION->IncludeComponent(
				"bitrix:catalog.section.list",
				"",
				array(
					"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
					"COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
					"TOP_DEPTH" => $arParams["SECTION_TOP_DEPTH"],
					"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
					"VIEW_MODE" => $arParams["SECTIONS_VIEW_MODE"],
					"SHOW_PARENT_NAME" => $arParams["SECTIONS_SHOW_PARENT_NAME"],
					"HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
					"ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : '')
				),
				$component,
				array("HIDE_ICONS" => "Y")
			);?>

			<aside class="b-aside-left">
				<nav class="b-nav-catalog">
					<a href="/catalog/discount/" class="b-nav-catalog__item">Скидки</a>
					<a href="/catalog/novelty/" class="b-nav-catalog__item">Новинки</a>
					<a href="/catalog/popular/" class="b-nav-catalog__item">Популярные</a>
				</nav>
			</aside>
		</div>
		<div class="col-lg-9 col-md-8">
			<div class="b-content">
			<?if($arParams["USE_COMPARE"]=="Y"){?>
				<?$APPLICATION->IncludeComponent(
					"bitrix:catalog.compare.list",
					"",
					array(
						"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"NAME" => $arParams["COMPARE_NAME"],
						"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
						"COMPARE_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
						"ACTION_VARIABLE" => (!empty($arParams["ACTION_VARIABLE"]) ? $arParams["ACTION_VARIABLE"] : "action")."_ccl",
						"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
						'POSITION_FIXED' => isset($arParams['COMPARE_POSITION_FIXED']) ? $arParams['COMPARE_POSITION_FIXED'] : '',
						'POSITION' => isset($arParams['COMPARE_POSITION']) ? $arParams['COMPARE_POSITION'] : ''
					),
					$component,
					array("HIDE_ICONS" => "Y")
				);?>
			<?}?>
			
				<?if($arParams["SHOW_TOP_ELEMENTS"]!="N"){
					if (isset($arParams['USE_COMMON_SETTINGS_BASKET_POPUP']) && $arParams['USE_COMMON_SETTINGS_BASKET_POPUP'] == 'Y'){
						$basketAction = (isset($arParams['COMMON_ADD_TO_BASKET_ACTION']) ? $arParams['COMMON_ADD_TO_BASKET_ACTION'] : '');
					}else{
						$basketAction = (isset($arParams['TOP_ADD_TO_BASKET_ACTION']) ? $arParams['TOP_ADD_TO_BASKET_ACTION'] : '');
					}?>

					<?
					GLOBAL $arrFilterDiscount;
					GLOBAL $arrFilterNew;
					 $DiscountID = 75;
					 $NewID = 77;
					  $arrFilterDiscount = array(
					  'PROPERTY_23' => $DiscountID,
					);
					  $arrFilterNew = array(
					  'PROPERTY_24' => $NewID,
					);

					  
					?>
					<section class="b-catalog">
					<div class="b-catalog__head">СКИДКА</div>
					<?$APPLICATION->IncludeComponent(
						"bitrix:catalog.top",
						"",
						array(
							"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["IBLOCK_ID"],
							"FILTER_NAME" => "arrFilterDiscount",
							"ELEMENT_SORT_FIELD" => $arParams["TOP_ELEMENT_SORT_FIELD"],
							"ELEMENT_SORT_ORDER" => $arParams["TOP_ELEMENT_SORT_ORDER"],
							"ELEMENT_SORT_FIELD2" => $arParams["TOP_ELEMENT_SORT_FIELD2"],
							"ELEMENT_SORT_ORDER2" => $arParams["TOP_ELEMENT_SORT_ORDER2"],
							"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
							"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
							"BASKET_URL" => $arParams["BASKET_URL"],
							"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
							"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
							"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
							"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
							"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
							"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
							"ELEMENT_COUNT" => $arParams["TOP_ELEMENT_COUNT"],
							"LINE_ELEMENT_COUNT" => $arParams["TOP_LINE_ELEMENT_COUNT"],
							"PROPERTY_CODE" => $arParams["TOP_PROPERTY_CODE"],
							"PRICE_CODE" => $arParams["PRICE_CODE"],
							"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
							"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
							"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
							"PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
							"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
							"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
							"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
							"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
							"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
							"OFFERS_FIELD_CODE" => $arParams["TOP_OFFERS_FIELD_CODE"],
							"OFFERS_PROPERTY_CODE" => $arParams["TOP_OFFERS_PROPERTY_CODE"],
							"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
							"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
							"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
							"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
							"OFFERS_LIMIT" => $arParams["TOP_OFFERS_LIMIT"],
							'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
							'CURRENCY_ID' => $arParams['CURRENCY_ID'],
							'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
							'VIEW_MODE' => (isset($arParams['TOP_VIEW_MODE']) ? $arParams['TOP_VIEW_MODE'] : ''),
							'ROTATE_TIMER' => (isset($arParams['TOP_ROTATE_TIMER']) ? $arParams['TOP_ROTATE_TIMER'] : ''),
							'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
							'LABEL_PROP' => $arParams['LABEL_PROP'],
							'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
							'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

							'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
							'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
							'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
							'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
							'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
							'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
							'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
							'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
							'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
							'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],
							'ADD_TO_BASKET_ACTION' => $basketAction,
							'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
							'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare']
						),
						$component
					);?>
					</section>
					<section class="b-catalog">
					<div class="b-catalog__head">НОВИНКИ</div>
					<?$APPLICATION->IncludeComponent(
						"bitrix:catalog.top",
						"",
						array(
							"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["IBLOCK_ID"],
							"FILTER_NAME" => "arrFilterNew",
							"ELEMENT_SORT_FIELD" => $arParams["TOP_ELEMENT_SORT_FIELD"],
							"ELEMENT_SORT_ORDER" => $arParams["TOP_ELEMENT_SORT_ORDER"],
							"ELEMENT_SORT_FIELD2" => $arParams["TOP_ELEMENT_SORT_FIELD2"],
							"ELEMENT_SORT_ORDER2" => $arParams["TOP_ELEMENT_SORT_ORDER2"],
							"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
							"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
							"BASKET_URL" => $arParams["BASKET_URL"],
							"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
							"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
							"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
							"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
							"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
							"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
							"ELEMENT_COUNT" => $arParams["TOP_ELEMENT_COUNT"],
							"LINE_ELEMENT_COUNT" => $arParams["TOP_LINE_ELEMENT_COUNT"],
							"PROPERTY_CODE" => $arParams["TOP_PROPERTY_CODE"],
							"PRICE_CODE" => $arParams["PRICE_CODE"],
							"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
							"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
							"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
							"PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
							"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
							"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
							"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
							"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
							"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
							"OFFERS_FIELD_CODE" => $arParams["TOP_OFFERS_FIELD_CODE"],
							"OFFERS_PROPERTY_CODE" => $arParams["TOP_OFFERS_PROPERTY_CODE"],
							"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
							"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
							"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
							"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
							"OFFERS_LIMIT" => $arParams["TOP_OFFERS_LIMIT"],
							'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
							'CURRENCY_ID' => $arParams['CURRENCY_ID'],
							'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
							'VIEW_MODE' => (isset($arParams['TOP_VIEW_MODE']) ? $arParams['TOP_VIEW_MODE'] : ''),
							'ROTATE_TIMER' => (isset($arParams['TOP_ROTATE_TIMER']) ? $arParams['TOP_ROTATE_TIMER'] : ''),
							'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
							'LABEL_PROP' => $arParams['LABEL_PROP'],
							'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
							'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

							'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
							'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
							'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
							'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
							'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
							'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
							'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
							'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
							'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
							'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],
							'ADD_TO_BASKET_ACTION' => $basketAction,
							'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
							'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare']
						),
						$component
					);?>
					</section>

					<section class="b-catalog">
					<div class="b-catalog__head">САМЫЕ ПОПУЛЯРНЫЕ</div>
					<?$APPLICATION->IncludeComponent(
						"bitrix:catalog.top",
						"",
						array(
							"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["IBLOCK_ID"],
							"FILTER_NAME" => "arrFilterNew",
							"ELEMENT_SORT_FIELD" => $arParams["TOP_ELEMENT_SORT_FIELD"],
							"ELEMENT_SORT_ORDER" => $arParams["TOP_ELEMENT_SORT_ORDER"],
							"ELEMENT_SORT_FIELD2" => $arParams["TOP_ELEMENT_SORT_FIELD2"],
							"ELEMENT_SORT_ORDER2" => $arParams["TOP_ELEMENT_SORT_ORDER2"],
							"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
							"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
							"BASKET_URL" => $arParams["BASKET_URL"],
							"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
							"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
							"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
							"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
							"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
							"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
							"ELEMENT_COUNT" => $arParams["TOP_ELEMENT_COUNT"],
							"LINE_ELEMENT_COUNT" => $arParams["TOP_LINE_ELEMENT_COUNT"],
							"PROPERTY_CODE" => $arParams["TOP_PROPERTY_CODE"],
							"PRICE_CODE" => $arParams["PRICE_CODE"],
							"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
							"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
							"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
							"PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
							"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
							"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
							"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
							"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
							"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
							"OFFERS_FIELD_CODE" => $arParams["TOP_OFFERS_FIELD_CODE"],
							"OFFERS_PROPERTY_CODE" => $arParams["TOP_OFFERS_PROPERTY_CODE"],
							"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
							"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
							"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
							"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
							"OFFERS_LIMIT" => $arParams["TOP_OFFERS_LIMIT"],
							'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
							'CURRENCY_ID' => $arParams['CURRENCY_ID'],
							'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
							'VIEW_MODE' => (isset($arParams['TOP_VIEW_MODE']) ? $arParams['TOP_VIEW_MODE'] : ''),
							'ROTATE_TIMER' => (isset($arParams['TOP_ROTATE_TIMER']) ? $arParams['TOP_ROTATE_TIMER'] : ''),
							'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
							'LABEL_PROP' => $arParams['LABEL_PROP'],
							'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
							'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

							'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
							'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
							'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
							'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
							'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
							'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
							'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
							'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
							'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
							'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],
							'ADD_TO_BASKET_ACTION' => $basketAction,
							'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
							'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare']
						),
						$component
					);?>
					</section>
					<?unset($basketAction);?>
				<?}?>
			</section>
			</div>
		</div>
	</div>
	</main>
</div>