<?php
/**
* ImageMagick Precompress Plugin for "Image Manager EP" Addon
*
* @author http://rexdev.de
* @link https://github.com/jdlx/precompress.image_manager.plugin
* @link https://github.com/jdlx/image_manager_ep
*
* @package redaxo 4.3.x/4.4.x
* @version 1.5.1
*/


$error = '';

if ($error != '')
  $REX['ADDON']['installmsg']['precompress.image_manager.plugin'] = $error;
else
  $REX['ADDON']['install']['precompress.image_manager.plugin'] = 0;
