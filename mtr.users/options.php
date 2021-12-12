<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;

$moduleId = $module_id = 'mtr.users';

/** @global \CMain $APPLICATION */
$moduleAccessLevel = $APPLICATION->GetGroupRight($moduleId);

\IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/options.php');
\IncludeModuleLangFile(__FILE__);

if ($moduleAccessLevel < 'W') {
    \ShowError(Loc::getMessage('USERS:ACCESS_DENIED'));
    return;
}

$request = Context::getCurrent()->getRequest();
$logLevelOption = Option::get($moduleId, 'select_name', 's1');

$logLevels = [
    'none', 'error', 'all'
];

if ($request->isPost() && check_bitrix_sessid()) {
    //Тут сохранение прав
    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/admin/group_rights.php';

    $logLevel = $request->getPost('select_name');
    if ($logLevel !== $logLevelOption) {
        Option::set($moduleId, 'select_name', $logLevel);
        $logLevelOption = $logLevel;
    }

    LocalRedirect($request->getRequestUri());
    die;
}

Loader::IncludeModule($moduleId);
Loc::loadMessages(__FILE__);

$tabs = [
    [
        'DIV' => 'users_options',
        'TAB' => Loc::getMessage('USERS:MODULE_OPTIONS'),
        'ICON' => '',
        'TITLE' => Loc::getMessage('USERS:MODULE_OPTIONS'),
    ],
    [
        'DIV' => 'users_access',
        'TAB' => Loc::getMessage('USERS:MODULE_ACCESS_OPTIONS'),
        'ICON' => '',
        'TITLE' => Loc::getMessage('USERS:MODULE_ACCESS_OPTIONS'),
    ],
];
$tabControl = new \CAdminTabControl('users_options', $tabs, true, true);
$tabControl->Begin();
?>

<form method="POST"
      action="<?= $APPLICATION->GetCurPage() ?>?lang=<?= LANGUAGE_ID ?>&mid=<?= $moduleId ?>"
      name="users_settings">
    <?= bitrix_sessid_post() ?>
    <? $tabControl->BeginNextTab(); ?>
    <tr>
        <td width="50%"><label for="logging"><?= Loc::getMessage('USERS:LOGGING') ?></label>:</td>
        <td width="50%">
            <select name="select_name" id="logging">
                <? foreach ($logLevels as $level): ?>
                    <option value="<?= $level ?>" <?= $level === $logLevelOption ? 'selected' : '' ?>>
                        <?= Loc::getMessage('USERS:LOG_LEVEL:' . ToUpper($level)) ?>
                    </option>
                <? endforeach; ?>
            </select>
        </td>
    </tr>
    <? $tabControl->BeginNextTab(); ?>
    <? require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/admin/group_rights.php'; ?>
    <? $tabControl->Buttons(); ?>
    <input type="submit" value="<?= Loc::getMessage('USERS:SAVE') ?>">
    <input type="hidden" name="Update" value="Y">
    <? $tabControl->End(); ?>
</form>
