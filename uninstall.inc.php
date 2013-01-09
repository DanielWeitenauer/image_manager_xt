<?php
/**
* Custom Folder Plugin for image_manager Addon
*
* @package redaxo 4.3.x/4.4.x
* @version 0.2.15
*/

$error = '';

if ($error != '')
  $REX['ADDON']['installmsg']['rex_img_dir.image_manager.plugin'] = $error;
else
  $REX['ADDON']['install']['rex_img_dir.image_manager.plugin'] = 0;
