<?php
/**
* rex_resize Plugin for image_manager Addon
*
* @package redaxo4.3
* @version 0.1
* $Id$:
*/

$error = '';

if ($error != '')
  $REX['ADDON']['installmsg']['_rex_resize.imagemanager.plugin'] = $error;
else
  $REX['ADDON']['install']['_rex_resize.imagemanager.plugin'] = 0;
