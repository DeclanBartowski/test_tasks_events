<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var array $arResult */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */

/** @var CBitrixComponent $component */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
?>
<? if ($arResult['IS_AJAX'] != 'Y'){ ?>
    <h1><?= Loc::getMessage('SP_TMPL_TITLE') ?></h1>
    <div><?= Loc::getMessage('SP_TMPL_SET_CITY') ?></div>
    <? if ($arResult['CITIES']) { ?>
    <select name="cities" id="cities">
        <option value="all" selected><?= Loc::getMessage('SP_TMPL_SET_CITY_ALL') ?></option>
        <? foreach ($arResult['CITIES'] as $arCity) { ?>
            <option value="<?= $arCity['ID'] ?>"><?= $arCity['NAME'] ?></option>
        <? } ?>
    </select>
<? } ?>
    <div class="block-content" id="ajax-content">
        <? } ?>
        <? foreach ($arResult['CITIES'] as $arCity) { ?>
            <div class="city-block">
                <h2><?= $arCity['NAME'] ?></h2>
                <? if ($arCity['EVENTS']) { ?>
                    <ul>
                        <? foreach ($arCity["EVENTS"] as $arEvent) { ?>
                            <li><?= $arEvent['NAME'] ?></li>
                            <? if ($arEvent['MEMBERS']) { ?>
                                <div><?= Loc::getMessage('SP_TMPL_MEMBERS') ?></div>
                                <ul>
                                    <? foreach ($arEvent['MEMBERS'] as $arMember) { ?>
                                        <li><?= $arMember['NAME'] ?></li>
                                    <? } ?>
                                </ul>
                            <? } ?>
                        <? } ?>
                    </ul>
                <? } else { ?>
                    <div><?= Loc::getMessage('SP_TMPL_NO_EVENTS') ?></div>
                <? } ?>
            </div>
        <? } ?>
        <? if ($arResult['IS_AJAX'] != 'Y'){ ?>
    </div>
    <script type="text/javascript">
        var params = <?=\Bitrix\Main\Web\Json::encode(['signedParameters' => $this->getComponent()->getSignedParameters()])?>;
    </script>
<? } ?>