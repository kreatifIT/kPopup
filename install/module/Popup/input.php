<?php

use RexGraphQL\Type\Popup\Popup;

$mform = \Kreatif\Project\MForm::factory();

$mform->addTab(function (\Kreatif\Project\MForm $tab) {
    $tab->addTitleTextField();
    $tab->addDefaultLinkField();
});

$mform->addTab(function (\Kreatif\Project\MForm $tab) {
    $id = Popup::SETTINGS_ID;
    $tab->addCheckboxField("$id.show_on_change", [
        1 => \rex_i18n::msg('label.module.popup.show_again_after_change'),
    ],                      null, null, 1);

    $tab->addSelectField("$id.time", [
        ''  => \rex_i18n::msg('label.module.popup.time_restriction.none'),
        'timer' => \rex_i18n::msg('label.module.popup.time_restriction.restrict'),
    ],                    [
        'label'    => \rex_i18n::msg('label.module.popup.time_restriction'),
        'class'    => 'layout-toggler',
        'onchange' => 'KreatifAddon.changeModuleLayout(this, \'time\', \'time-\');',
    ]);

    $tab->addTextField("$id.start_date", [
        'label' => \rex_i18n::msg('label.module.popup.start'),
        'class' => 'time-timer datetimepicker2',
    ]);

    $tab->addTextField("$id.end_date", [
        'label' => \rex_i18n::msg('label.module.popup.end'),
        'class' => 'time-timer datetimepicker2',
    ]);

    $tab->addSelectField("$id.article_limitation", [
        'everywhere' => \rex_i18n::msg('label.module.popup.visible_on.everywhere'),
        'blacklist'  => \rex_i18n::msg('label.module.popup.visible_on.blacklist'),
        'whitelist'  => \rex_i18n::msg('label.module.popup.visible_on.whitelist'),
    ],                    [
        'label'    => \rex_i18n::msg('label.module.popup.visible_on'),
        'class'    => 'layout-toggler',
        'onchange' => 'KreatifAddon.changeModuleLayout(this, \'page\', \'page-\');',
    ]);
    $tab->addHtml('<div class="form-group"> <div class="page-blacklist page-whitelist">');

    $tab->addLinklistField("$id.article_list", [
        'label' => \rex_i18n::msg('label.module.popup.pages'),
    ]);
    $tab->addHtml('</div></div>');

    $tab->addSelectField("$id.visibility", [
        'always'  => \rex_i18n::msg('label.module.popup.visibility.always'),
        'once'  => \rex_i18n::msg('label.module.popup.visibility.once'),
        'close'  => \rex_i18n::msg('label.module.popup.visibility.until_close'),
    ],                    [
        'label'    => \rex_i18n::msg('label.module.popup.visibility'),
        'class'    => 'layout-toggler',
        'onchange' => 'KreatifAddon.changeModuleLayout(this, \'reopen\', \'reopen-\');',
    ]);

    $tab->addCheckboxField("$id.show_reopen_button", [
        1 => \rex_i18n::msg('label.module.popup.show_reopen_button'),
    ], [
        'class' => 'reopen-once reopen-close'
    ], null, 0);
}, 'settings');

echo $mform->showWithSettings(false);
