<?php
/**
* rex_resize Plugin for image_manager Addon
*
* @package redaxo 4.3.x/4.4.x
* @version 0.2.29
* @link    http://svn.rexdev.de/redmine/projects/image-manager-ep
* @author  http://rexdev.de/
*/

$error = '';

if ($error != '')
  $REX['ADDON']['installmsg']['_rex_resize.image_manager.plugin'] = $error;
else
  $REX['ADDON']['install']['_rex_resize.image_manager.plugin'] = 0;
