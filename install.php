<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 26.03.21
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$modules = glob($this->getPath('install/module/Popup/'));
$popupModuleId = null;
$module = $modules[0];

$input = glob($module . 'input.php');
$output = glob($module . 'output.php');

$sql = rex_sql::factory();
$sql->setTable('rex_module');
$sql->setWhere('`key` = :key', [
    'key' => 'popup'
]);
$sql->select();
$_mod = $sql->getArray();

if (empty ($_mod)) {
    if ($input) {
        $sql->setValue('input', file_get_contents($input[0]));
    }
    if ($output) {
        $sql->setValue('output', file_get_contents($output[0]));
    } else {
        $sql->setValue('output', '');
    }
    $sql->setTable('rex_module');
    $sql->setValue('name', 'translate:label.module_name.popup');
    $sql->setValue('key', 'popup');
    $sql->setDebug(false);
    $sql->insert();
    $popupModuleId = $sql->getLastId();
} else {
    $popupModuleId = $_mod['id'];
}

$sql = rex_sql::factory();
$sql->setTable('rex_template');
$sql->setWhere('`key` = :key', [
    'key' => 'popup'
]);
$sql->select();
$_temp = $sql->getArray();
$templateId = null;
if (empty($_temp)) {
    $sql = rex_sql::factory();
    $sql->setTable('rex_template');
    $sql->setValues([
        'key' => 'popup',
        'name' => 'Popup',
        'content' => '',
        'active' => 1,
        'updatedate' => time(),
        'createdate' => time(),
        'attributes' => json_encode([
            'ctype' => [],
            'modules' => [
                1 => [
                    '0' => $popupModuleId,
                    'all' => 0
                ]
            ],
            'categories' => [
                'all' => 1
            ]
        ])
    ]);
    $sql->insert();
    $templateId = $sql->getLastId();
} else {
    $templateId = $_temp['id'];
}
$sql = rex_sql::factory();
$sql->setWhere('id = 1');
$sql->setTable('rex_prj_advanced_settings');
$sql->select();
$settings = $sql->getArray()[0];


$articleLinks = json_decode($settings['article_links'], true);
$hasPopupSetting = false;
foreach ($articleLinks as $articleLink) {
    if ($articleLink[0] === 'popup_page_id' && $articleLink[1] > 0) {
        $hasPopupSetting = true;
        break;
    }
}
if (!$hasPopupSetting) {
    $sql = rex_sql::factory();
    $sql->setTable('rex_article');
    $sql->select('MAX(id) AS max');
    $max = $sql->getArray()[0]['max'];
    $newId = $max + 1;

    $clangs = rex_clang::getAll();
    foreach ($clangs as $clang) {
        $sql = rex_sql::factory();
        $sql->setTable('rex_article');
        $sql->setValues([
            'id' => $newId,
            'parent_id' => 0,
            'name' => 'Popup',
            'status' => 1,
            'clang_id' => $clang->getId(),
            'template_id' => $templateId,
            'yrewrite_index' => 2
        ]);
        $sql->insert();
    }


    $sql = rex_sql::factory();
    $sql->setWhere('id = 1');
    $sql->setTable('rex_prj_advanced_settings');
    $sql->setValue('article_links', json_encode([...$articleLinks, ['popup_page_id', $newId]]));
    $sql->update();
}
