<?php
/**
* Custom Folder Plugin for image_manager Addon
*
* @package redaxo4.3
* @version 0.2
* $Id$:
*/

$error = '';

if ($error != '')
  $REX['ADDON']['installmsg']['rex_img_dir.image_manager.plugin'] = $error;
else
  $REX['ADDON']['install']['rex_img_dir.image_manager.plugin'] = true;
