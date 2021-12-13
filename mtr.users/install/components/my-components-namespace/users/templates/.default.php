<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
} ?>
<p>Группы пользователей</p>
<ul>
    <?php foreach ($arResult['USER_GROUPS'] as $groupId => $arGroup): ?>
        <li><?= $arGroup['NAME'] ?><span style="font-style: italic"> (Пользователей в группе: <?= count($arGroup['USERS']) ?>)</span></li>
    <?php endforeach; ?>
</ul>

<p>Список пользователей</p>
<ul>
    <?php foreach ($arResult['USERS'] as $userId => $arUser): ?>
        <li><?= $arUser['NAME'] ?></li>
    <?php endforeach; ?>
</ul>

<br><br><br>

