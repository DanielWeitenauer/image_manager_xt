<?php
/**
* Referrer Blocker Plugin for image_manager Addon
*
* @package redaxo4.3
* @version 0.2
* $Id$:
*/

$error = '';

if ($error != '')
  $REX['ADDON']['installmsg']['referrer_block.image_manager.plugin'] = $error;
else
  $REX['ADDON']['install']['referrer_block.image_manager.plugin'] = 0;
