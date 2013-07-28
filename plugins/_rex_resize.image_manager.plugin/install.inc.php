<?php
/**
 * rex_resize Plugin for image_manager Addon
 *
 * @author jdlx c/o http://rexdev.de/
 * @link https://github.com/jdlx/image_manager_xt
 *
 * @package redaxo 4.4.x/4.5.x
 * @version 1.4.2
 */

// INSTALL SETTINGS
////////////////////////////////////////////////////////////////////////////////
$myself            = '_rex_resize.image_manager.plugin';


$REX['ADDON']['install'][$myself] = 1;

// PLUINGS CAN'T CHECK IF ADDONS INSTALLED OR AVAILLABLE -> MANUALLY NOTIFY USER
echo rex_warning('Bitte beachten: das <em>image_resize</em> Addon muß - falls noch aktiv - deinstalliert werden!');
