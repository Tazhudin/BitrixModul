<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}


class Users extends CBitrixComponent
{
    /**
     * Масиив групп пользователей
     * @var array
     */
    private $arUserGroups = [];

    /**
     * Массив пользователей
     * @var array
     */
    private $arUsers = [];


    /**
     * @behavior prepare component params
     * @param $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams['CACHE_TIME'] = ($arParams['CACHE_TIME']) ? intval($arParams['CACHE_TIME']) : 3600;
        return $arParams;
    }


    /**
     * @behavior execute component
     */
    public function executeComponent()
    {
        $this->initComponentVariables();
        $this->setComponentResult();
        $this->includeComponentTemplate();
    }

    /**
     * @behavior set component result variables
     */
    protected function setComponentResult()
    {
        $this->arResult['USER_GROUPS'] = $this->arUserGroups;
        $this->arResult['USERS'] = $this->arUsers;
    }

    /**
     * @behavior init component variables
     * @throws Exception
     */
    protected function initComponentVariables()
    {
        if ($this->StartResultCache($this->arParams['CACHE_TIME'])) {
            $resUsers = \Bitrix\Main\UserGroupTable::getList([
                'filter' => ['USER.ACTIVE' => 'Y'],
                'select' => ['*', 'NAME' => 'USER.NAME', 'LAST_NAME' => 'USER.LAST_NAME',],
                'order' => ['USER.ID' => 'DESC'],
            ]);
            while ($arResUsers = $resUsers->fetch()) {
                $this->arUsers[$arResUsers['USER_ID']] = $arResUsers;
                $this->arUserGroups[$arResUsers['GROUP_ID']]['USERS'][$arResUsers['USER_ID']] = $arResUsers;
            }

            $resGroups = \Bitrix\Main\GroupTable::GetList([
                'select' => ['ID', 'NAME'],
                'filter' => ['ID' => array_keys($this->arUserGroups)]
            ]);

            while ($arGroup = $resGroups->fetch()) {
                $this->arUserGroups[$arGroup['ID']] = array_merge($arGroup, $this->arUserGroups[$arGroup['ID']]);
            }
        }
    }

}