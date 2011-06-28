<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Andreas Schempp 2009-2011
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 * @version    $Id$
 */


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_page']['palettes']['root'] = str_replace('{layout_legend:hide}', '{registration_legend:hide},auto_activate_registration,auto_activate_where,auto_login_registration,auto_login_activation;{layout_legend:hide}', $GLOBALS['TL_DCA']['tl_page']['palettes']['root']);


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['auto_activate_registration'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_page']['auto_activate_registration'],
	'exclude'		=> true,
	'inputType'		=> 'checkbox',
	'eval'			=> array('tl_class'=>'w50 m12'),
);

$GLOBALS['TL_DCA']['tl_page']['fields']['auto_activate_where'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_page']['auto_activate_where'],
	'exclude'		=> true,
	'inputType'		=> 'text',
	'eval'			=> array('decodeEntities'=>true, 'tl_class'=>'w50'),
);

$GLOBALS['TL_DCA']['tl_page']['fields']['auto_login_registration'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_page']['auto_login_registration'],
	'exclude'		=> true,
	'inputType'		=> 'checkbox',
	'eval'			=> array('tl_class'=>'w50'),
);

$GLOBALS['TL_DCA']['tl_page']['fields']['auto_login_activation'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_page']['auto_login_activation'],
	'exclude'		=> true,
	'inputType'		=> 'checkbox',
	'eval'			=> array('tl_class'=>'w50'),
);

