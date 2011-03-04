<?php
/**
* ImageMagick Precompress Plugin for image_manager Addon
*
* @package redaxo4.3
* @version 0.2
* $Id$:
*/

$error = '';

if ($error != '')
  $REX['ADDON']['installmsg']['precompress.image_manager.plugin'] = $error;
else
  $REX['ADDON']['install']['precompress.image_manager.plugin'] = true;
