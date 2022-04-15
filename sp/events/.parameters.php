<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;

if (!Loader::includeModule('iblock')) {
    return;
}

$arIBlock = [];
$iblockFilter = (['ACTIVE' => 'Y']);
$rsIBlock = CIBlock::GetList(['SORT' => 'ASC'], $iblockFilter);
while ($arr = $rsIBlock->Fetch()) {
    $arIBlock[$arr['ID']] = '[' . $arr['ID'] . '] ' . $arr['NAME'];
}
unset($arr, $rsIBlock, $iblockFilter);

$arPrice = [];


$arComponentParameters = [
    "PARAMETERS" => [
        "IBLOCK_ID_EVENTS" => [
            "PARENT" => "BASE",
            "NAME" => GetMessage("IBLOCK_ID_EVENTS"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arIBlock,
        ],
        "IBLOCK_ID_CITIES" => [
            "PARENT" => "BASE",
            "NAME" => GetMessage("IBLOCK_ID_CITIES"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arIBlock,
        ],
        "IBLOCK_ID_MEMBERS" => [
            "PARENT" => "BASE",
            "NAME" => GetMessage("IBLOCK_ID_MEMBERS"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arIBlock,
        ]
    ],
];