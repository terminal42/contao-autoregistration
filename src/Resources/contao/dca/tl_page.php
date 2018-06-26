<?php


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'auto_activate_registration';

$GLOBALS['TL_DCA']['tl_page']['palettes']['root'] = str_replace(
    '{layout_legend}',
    '{registration_legend:hide},auto_activate_registration,auto_login_activation;{layout_legend:hide}',
    $GLOBALS['TL_DCA']['tl_page']['palettes']['root']
);

$GLOBALS['TL_DCA']['tl_page']['subpalettes']['auto_activate_registration'] =
    'auto_login_registration'; // auto_activate_where

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['auto_activate_registration'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['auto_activate_registration'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => ['tl_class' => 'w50 m12'],
    'sql'       => ['type' => 'string', 'length' => 1, 'notnull' => true, 'fixed' => true, 'default' => '']
];

$GLOBALS['TL_DCA']['tl_page']['fields']['auto_activate_where'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['auto_activate_where'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => ['decodeEntities' => true, 'tl_class' => 'w50'],
    'sql'       => ['type' => 'string', 'length' => 255, 'notnull' => true, 'default' => '']
];

$GLOBALS['TL_DCA']['tl_page']['fields']['auto_login_registration'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['auto_login_registration'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => ['tl_class' => 'w50'],
    'sql'       => ['type' => 'string', 'length' => 1, 'notnull' => true, 'fixed' => true, 'default' => '']
];

$GLOBALS['TL_DCA']['tl_page']['fields']['auto_login_activation'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['auto_login_activation'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => ['tl_class' => 'clr w50'],
    'sql'       => ['type' => 'string', 'length' => 1, 'notnull' => true, 'fixed' => true, 'default' => '']
];
