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

$error = '';

if ($error != '')
  $REX['ADDON']['installmsg']['_rex_resize.image_manager.plugin'] = $error;
else
  $REX['ADDON']['install']['_rex_resize.image_manager.plugin'] = true;
