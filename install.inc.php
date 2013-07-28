<?php
/**
 * image_manager Addon
 *
 * @author office[at]vscope[dot]at Wolfgang Hutteger
 * @author markus.staab[at]redaxo[dot]de Markus Staab
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 *
 * @author jdlx c/o http://rexdev.de/
 * @link https://github.com/jdlx/image_manager_xt
 *
 * @package redaxo 4.4.x/4.5.x
 * @version 1.4.2
 */

$myself = 'image_manager';


// CHECK ADDON FOLDER NAME
////////////////////////////////////////////////////////////////////////////////
$addon_folder = basename(dirname(__FILE__));
if($addon_folder != $myself)
{
  $REX['ADDON']['installmsg'][$addon_folder] = '<br />Der Name des Addon-Ordners ist inkorrekt: <code style="color:black;font-size:12px;">'.$addon_folder.'</code>
                                                <br />Addon-Ordner in <code style="color:black;font-size:1.23em;">'.$myself.'</code> umbenennen und Installation wiederholen';
  $REX['ADDON']['install'][$addon_folder] = 0;
  return;
}


// CHECK PHP VERSION
////////////////////////////////////////////////////////////////////////////////
if(version_compare(PHP_VERSION, '5.3.0', '<'))
{
  $REX['ADDON']['installmsg'][$myself] = 'Dieses Addon ben&ouml;tigt PHP 5.3.0 oder neuer.';
  $REX['ADDON']['install'][$myself]    = 0;
  return;
}


// CHECK GD AVAIL
////////////////////////////////////////////////////////////////////////////////
if (!extension_loaded('gd'))
{
  $REX['ADDON']['installmsg'][$myself] = 'GD-LIB-extension not available! See <a href="http://www.php.net/gd">http://www.php.net/gd</a>';
  $REX['ADDON']['install'][$myself]    = 0;
  return;
}


// CHECK CONFIG WRITEABLE
////////////////////////////////////////////////////////////////////////////////
$file = $REX['INCLUDE_PATH'] .'/addons/image_manager/config.inc.php';
if(($state = rex_is_writable($file)) !== true) {
  $REX['ADDON']['installmsg'][$myself] = $state;
  $REX['ADDON']['install'][$myself]    = 0;
  return;
}


// SETUP/CHECK CACHE DIR
////////////////////////////////////////////////////////////////////////////////
$dir = $REX['INCLUDE_PATH'] .'/generated/image_manager/';
if(file_exists($dir) && !is_dir($dir)) {
  $REX['ADDON']['installmsg'][$myself] = '"'.$dir.'"" is not a directory!';
  $REX['ADDON']['install'][$myself]    = 0;
  return;
}

if(!file_exists($dir)){
  mkdir($dir, $REX['DIRPERM'], true);
}
if(($state = rex_is_writable($dir)) !== true) {
  $REX['ADDON']['installmsg'][$myself] = $state;
  $REX['ADDON']['install'][$myself]    = 0;
  return;
}


$REX['ADDON']['install']['image_manager'] = 1;
