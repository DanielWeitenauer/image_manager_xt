<?php
/**
* Referrer Blocker Plugin for image_manager Addon
*
* @package redaxo 4.3.x/4.4.x
* @version 1.1.4
*/

$error = '';

if ($error != '')
  $REX['ADDON']['installmsg']['referrer_block.image_manager.plugin'] = $error;
else
  $REX['ADDON']['install']['referrer_block.image_manager.plugin'] = 0;
