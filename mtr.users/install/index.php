<?php

use Bitrix\Highloadblock\HighloadBlockTable;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Application;
use \additionalFilters\FileInstaller;


class mtr_users extends CModule
{

    protected $moduleDir;

    public function __construct()
    {
        Loc::loadMessages(__FILE__);
        $this->MODULE_ID = 'users';
        $this->MODULE_NAME = 'Пользователи';
        $this->MODULE_VERSION = '0.0.1';
        $this->MODULE_VERSION_DATE = '12.12.2021';
        $this->MODULE_DESCRIPTION = 'Пользователи';
        $this->PARTNER_NAME = 'MTR';
        $this->PARTNER_URI = "mtr";
        $this->MODULE_GROUP_RIGHTS = 'Y';
    }


    public function DoUninstall()
    {
        Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);

        $this->UnInstallDB();
        $this->UnInstallEvents();
        $this->UnInstallFiles();

    }

    public function DoInstall()
    {
        global $APPLICATION;

        $this->installDB();
        $this->InstallEvents();
        $this->InstallFiles();

        Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile(Loc::getMessage("USERS:INSTALL_TITLE"), "/bitrix/admin/settings.php");
    }

    public function InstallDB()
    {
        $this->createHlBlock();
    }

    // перенос файлов модуля
    function InstallFiles($arParams = array())
    {
        CopyDirFiles($this->GetPath() . "/install/components", $_SERVER["DOCUMENT_ROOT"] . "/local/components", true, true);
        if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . '/admin')) {
            CopyDirFiles($this->GetPath() . "/install/admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin", true, true);
            if ($dir = opendir($path)) {
                while (false !== $item = readdir($dir)) {
                    if (in_array($item, $this->exclusionAdminFiles))
                        continue;
                    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item, '<' . '? require($_SERVER["DOCUMENT_ROOT"]."' . $this->GetPath(true) . '/admin/' . $item . '");?' . '>');
                }
                closedir($dir);
            }
        }
        return true;
    }

    // удаление файлов модуля
    function UnInstallFiles()
    {
        \Bitrix\Main\IO\Directory::deleteDirectory($_SERVER["DOCUMENT_ROOT"] . '/local/components/my-components-namespace/');

        if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . '/admin')) {
            DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . $this->GetPath() . '/install/admin', $_SERVER["DOCUMENT_ROOT"] . '/bitrix/admin');
            if ($dir = opendir($path)) {
                while (false !== $item = readdir($dir)) {
                    if (in_array($item, $this->exclusionAdminFiles))
                        continue;
                    \Bitrix\Main\IO\File::deleteFile($_SERVER["DOCUMENT_ROOT"] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item);
                }
                closedir($dir);
            }
        }
        return true;
    }

    public function InstallEvents()
    {
        return true;
    }

    public function UninstallDB()
    {
        return true;
    }

    public function UninstallEvents()
    {
        return true;
    }

    // Определяем место размещения модуля
    public function GetPath($notDocumentRoot = false)
    {
        if ($notDocumentRoot) {
            return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
        } else {
            return dirname(__DIR__);
        }

    }

}
