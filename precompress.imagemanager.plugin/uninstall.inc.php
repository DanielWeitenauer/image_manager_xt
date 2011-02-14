<?php
/**
* ImageMagick Precompress Plugin for image_manager Addon
*
* @package redaxo4.3
* @version 0.1
* $Id$:
*/

$error = '';

if ($error != '')
  $REX['ADDON']['installmsg']['precompress.imagemanager.plugin'] = $error;
else
  $REX['ADDON']['install']['precompress.imagemanager.plugin'] = 0;
