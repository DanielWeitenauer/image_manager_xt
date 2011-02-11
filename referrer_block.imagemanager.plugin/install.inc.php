<?php
/**
* Referrer Blocker Plugin for image_manager Addon
*
* @package redaxo4.3
* @version 0.1
* $Id$:
*/

$error = '';

if ($error != '')
  $REX['ADDON']['installmsg']['referrer_block.imagemanager.plugin'] = $error;
else
  $REX['ADDON']['install']['referrer_block.imagemanager.plugin'] = true;
