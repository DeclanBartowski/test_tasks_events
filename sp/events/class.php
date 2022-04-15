<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loader::includeModule('iblock');

/**
 * Class EventsList
 */
class EventsList extends \CBitrixComponent implements Controllerable
{
    public function onIncludeComponentLang()
    {
        parent::onIncludeComponentLang();
        $this->includeComponentLang(basename(__FILE__));
    }

    /**
     * @return array
     */
    public function configureActions()
    {
        return [
            'getCity' => [
                'prefilters' => [],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function listKeysSignedParameters()
    {
        return [
            'IBLOCK_ID_CITIES',
            'IBLOCK_ID_EVENTS',
            'IBLOCK_ID_MEMBERS',
        ];
    }

    /**
     * @param string $cityID
     * @return string
     */
    public function getCityAction(string $cityID): string
    {
        $this->getSignedParameters();
        $id = ($cityID == 'all') ? '' : $cityID;
        $result = $this->getResult($id);

        return $this->getHtmlResult($result);
    }

    /**
     * @param array $data
     * @return string
     */
    private function getHtmlResult(array $data): string
    {
        ob_start();
        $this->arResult['CITIES'] = $data;
        $this->arResult['IS_AJAX'] = 'Y';
        $this->includeComponentTemplate();
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    /**
     * @param string $cityID
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     */
    private function getCitiesByFilter(string $cityID = ''): array
    {
        $result = [];
        if ($this->arParams['IBLOCK_ID_CITIES']) {
            $arSelect = ["ID", "NAME", 'IBLOCK_ID'];
            $arFilter = ['IBLOCK_ID' => $this->arParams['IBLOCK_ID_CITIES'], "ACTIVE" => "Y"];
            if (!empty($cityID)) {
                $arFilter['=ID'] = $cityID;
            }
            $res = ElementTable::getList([
                'order' => ['SORT' => 'DESC'],
                'select' => $arSelect,
                'filter' => $arFilter,
            ]);
            while ($ob = $res->fetch()) {
                $result[$ob['ID']] = $ob;
            }
        }
        return $result;
    }

    /**
     * @param array $filter
     * @return array
     */
    private function getEventsByFilter(array $filter = []): array
    {
        $result = [];
        if ($this->arParams['IBLOCK_ID_EVENTS']) {
            $arSelect = ["ID", "NAME", 'IBLOCK_ID'];
            $arFilter = ['IBLOCK_ID' => $this->arParams['IBLOCK_ID_EVENTS'], "ACTIVE" => "Y"];
            if (!empty($filter)) {
                $arFilter = array_merge($arFilter, $filter);
            }
            $res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
            while ($ob = $res->GetNextElement()) {
                $fields = $ob->GetFields();
                $fields['PROPERTIES']['MEMBERS'] = $ob->GetProperty('MEMBERS');
                $fields['PROPERTIES']['CITIES'] = $ob->GetProperty('CITIES');
                $result[$fields['ID']] = $fields;
            }
        }
        return $result;
    }

    /**
     * @param array $membersID
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     */
    private function getMembersByFilter(array $membersID = []): array
    {
        $result = [];
        if ($this->arParams['IBLOCK_ID_MEMBERS']) {
            $arSelect = ["ID", "NAME", 'IBLOCK_ID'];
            $arFilter = ['IBLOCK_ID' => $this->arParams['IBLOCK_ID_MEMBERS'], "ACTIVE" => "Y"];
            if (!empty($membersID)) {
                $arFilter['ID'] = $membersID;
            }
            $res = ElementTable::getList([
                'order' => ['SORT' => 'DESC'],
                'select' => $arSelect,
                'filter' => $arFilter,
            ]);
            while ($ob = $res->Fetch()) {
                $result[$ob['ID']] = $ob;
            }
        }
        return $result;
    }

    /**
     * @param string $cityID
     * @return array
     */
    public function getResult(string $cityID = ''): array
    {
        $currentDateStart = date('Y-m-d 00:00:00');
        $currentDateEnd = date('Y-m-d 23:59:59');
        $cities = $this->getCitiesByFilter($cityID);
        if ($cities) {
            $events = $this->getEventsByFilter([
                'PROPERTY_CITIES' => array_column($cities, 'ID'),
                '><PROPERTY_EVENT_DATE' => [$currentDateStart, $currentDateEnd],
            ]);
            $arMembers = [];
            foreach ($events as $event) {
                foreach ($event['PROPERTIES']['MEMBERS']["VALUE"] as $memberID) {
                    $arMembers[$memberID] = $memberID;
                }
            }
            if ($arMembers) {
                $members = $this->getMembersByFilter($arMembers);
            }

            foreach ($cities as $cityID => &$arCity) {
                foreach ($events as &$arEvent) {
                    foreach ($arEvent['PROPERTIES']['MEMBERS']['VALUE'] as $arMemberID) {
                        $arEvent['MEMBERS'][$arMemberID] = $members[$arMemberID];
                    }
                    if (in_array($cityID, $arEvent['PROPERTIES']['CITIES']['VALUE'])) {
                        $arCity['EVENTS'][$arEvent['ID']] = $arEvent;
                    }
                }
                unset($arEvent);
            }
            unset($arCity);
        }
        return $cities;
    }

    /**
     * @return void
     */
    public function setResult(): void
    {
        $this->arResult['CITIES'] = $this->getResult();
    }

    /**
     * @return mixed|void
     */
    public function executeComponent(): void
    {
        CJSCore::Init(["fx", "ajax"]);
        $this->setResult();
        $this->includeComponentTemplate();
    }

}
