<?php
/**
* ImageMagick Precompress Plugin for image_manager Addon
*
* @package redaxo 4.3.x/4.4.x
* @version 0.2.26
*/


$error = '';

if ($error != '')
  $REX['ADDON']['installmsg']['precompress.image_manager.plugin'] = $error;
else
  $REX['ADDON']['install']['precompress.image_manager.plugin'] = 0;
