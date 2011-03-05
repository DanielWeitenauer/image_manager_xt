<?php
/**
* rex_resize Plugin for image_manager Addon
*
* @package redaxo4.3
* @version 0.2
* @link    http://svn.rexdev.de/redmine/projects/image-manager-ep
* @author  http://rexdev.de/
* $Id$:
*/

// INSTALL SETTINGS
////////////////////////////////////////////////////////////////////////////////
$myself            = '_rex_resize.image_manager.plugin';
$myroot            = $REX['INCLUDE_PATH'].'/addons/'.$myself;
$disable_addons    = array('image_resize');
$error             = array();


// CHECK ADDONS TO DISABLE
////////////////////////////////////////////////////////////////////////////////
foreach($disable_addons as $a)
{
  if (OOAddon::isInstalled($a) || OOAddon::isAvailable($a))
  {
    $error[] = 'Addon "'.$a.'" muß erst deinstalliert werden.  <span style="float:right;">[ <a href="index.php?page=addon&addonname='.$a.'&uninstall=1">'.$a.' de-installieren</a> ]</span>';
  }
}


if(count($error)>0)
{
  $REX['ADDON']['install'][$myself] = 1;
}
else
{
  $REX['ADDON']['installmsg'][$myself] = '<br />'.implode($error,'<br />');
  $REX['ADDON']['install'][$myself] = 0;
}