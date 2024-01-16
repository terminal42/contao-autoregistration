<?php

use Contao\CoreBundle\DataContainer\PaletteManipulator;

$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'reg_allowLogin';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['reg_allowLogin'] = 'reg_autoActivate';

$pm = PaletteManipulator::create()
    ->addField('reg_activateLogin', 'reg_jumpTo', PaletteManipulator::POSITION_BEFORE)
    ->applyToSubpalette('reg_activate', 'tl_module')
;

if (isset($GLOBALS['TL_DCA']['tl_module']['subpalettes']['nc_registration_auto_activate'])) {
    $pm->applyToSubpalette('nc_registration_auto_activate', 'tl_module');
}

unset($pm);

/*
 * Fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['reg_allowLogin']['eval']['submitOnChange'] = true;

$GLOBALS['TL_DCA']['tl_module']['fields']['reg_autoActivate'] = [
    'exclude' => true,
    'inputType' => 'radio',
    'options' => ['disable', 'activate', 'login'],
    'reference' => &$GLOBALS['TL_LANG']['tl_module']['reg_autoActivate'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 8, 'notnull' => true, 'default' => 'disable'],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['reg_activateLogin'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr'],
    'sql' => ['type' => 'string', 'length' => 1, 'notnull' => true, 'fixed' => true, 'default' => ''],
];
