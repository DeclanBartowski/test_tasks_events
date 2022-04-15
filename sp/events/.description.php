<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = [
    'NAME' => Loc::getMessage('PROJECT_NAME_EVENT'),
    'DESCRIPTION' => Loc::getMessage('PROJECT_NAME_DESCRIPTION_EVENT'),
    'SORT' => 10,
    "COMPLEX" => "N",
    'PATH' => [
        'ID' => 'SP',
        'NAME' => Loc::getMessage('PROJECT_NS_EVENT'),
        'SORT' => 10,
    ]
];