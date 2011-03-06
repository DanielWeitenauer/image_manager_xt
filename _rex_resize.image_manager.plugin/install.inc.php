<?php
/**
* rex_resize Plugin for image_manager Addon
*
* @package redaxo4.3
* @version 0.2
* @link    http://svn.rexdev.de/redmine/projects/image-manager-ep
* @author  http://rexdev.de/
* $Id$:
*/

// INSTALL SETTINGS
////////////////////////////////////////////////////////////////////////////////
$myself            = '_rex_resize.image_manager.plugin';


$REX['ADDON']['install'][$myself] = 1;

// PLUINGS CAN'T CHECK IF ADDONS INSTALLED OR AVAILLABLE -> MANUALLY NOTIFY USER
echo rex_warning('Bitte beachten: das <em>image_resize</em> Addon muß - falls noch aktiv - deinstalliert werden!');