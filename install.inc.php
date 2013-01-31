<?php
/**
 * image_manager Addon
 *
 * @author office[at]vscope[dot]at Wolfgang Hutteger
 * @author markus.staab[at]redaxo[dot]de Markus Staab
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 *
 * @author jdlx / rexdev.de
 * @link https://github.com/jdlx/image_manager_ep
 *
 * @package redaxo 4.3.x/4.4.x
 * @version 1.2.3
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

$error = '';

if (!extension_loaded('gd'))
{
  $error = 'GD-LIB-extension not available! See <a href="http://www.php.net/gd">http://www.php.net/gd</a>';
}

if($error == '')
{
  $file = $REX['INCLUDE_PATH'] .'/addons/image_manager/config.inc.php';

  if(($state = rex_is_writable($file)) !== true)
    $error = $state;
}

if($error == '')
{
  $file = $REX['INCLUDE_PATH'] .'/generated/files';

  if(($state = rex_is_writable($file)) !== true)
    $error = $state;
}

if ($error != '')
  $REX['ADDON']['installmsg']['image_manager'] = $error;
else
  $REX['ADDON']['install']['image_manager'] = true;
