<?php
/**
 * ImageMagick Precompress Plugin for "Image Manager EP" Addon
 *
 * @author jdlx c/o http://rexdev.de/
 * @link https://github.com/jdlx/image_manager_xt
 *
 * @package redaxo 4.4.x/4.5.x
 * @version 1.4.2
 */

$myself = 'precompress.image_manager.plugin';

// CHECK IF EXEC() AVAILABLE
////////////////////////////////////////////////////////////////////////////////
if(!function_exists('exec')) {
  $REX['ADDON']['installmsg'][$myself] = '<br />PHP function <code>exec()</code> is disabled.';
  $REX['ADDON']['install'][$myself] = 0;
  return;
}


// SEARCH FOR CONVERT
////////////////////////////////////////////////////////////////////////////////
$cmd = 'which convert';
exec($cmd, $out ,$ret);
if($ret == 1) {
  $REX['ADDON']['installmsg'][$myself] = '<br />Could not determine path to <code>convert</code> using cmd "<code>which convert</code>" ..<br />most likely <code>Imagemagick</code> is not available on your server.';
  $REX['ADDON']['install'][$myself] = 0;
  return;
}


$REX['ADDON']['install'][$myself] = 1;
