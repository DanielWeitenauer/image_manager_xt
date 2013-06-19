<?php
/**
 * ImageMagick Precompress Plugin for "Image Manager EP" Addon
 *
 * @author jdlx c/o http://rexdev.de/
 * @link https://github.com/jdlx/image_manager_xt
 *
 * @package redaxo 4.4.x/4.5.x
 * @version 1.4.1
 */


$error = '';

if ($error != '')
  $REX['ADDON']['installmsg']['precompress.image_manager.plugin'] = $error;
else
  $REX['ADDON']['install']['precompress.image_manager.plugin'] = 0;
