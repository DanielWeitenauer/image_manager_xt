<?php
/**
* Referrer Blocker Plugin for image_manager Addon
*
* @package redaxo4.3
* @version 0.2
* $Id$:
*/

// ADDON IDENTIFIER & ROOT DIR
////////////////////////////////////////////////////////////////////////////////
$myself = 'referrer_block.image_manager.plugin';
$myroot = $REX['INCLUDE_PATH'].'/addons/image_manager/plugins/'.$myself;


// REX COMMONS
////////////////////////////////////////////////////////////////////////////////
$Revision = '';
$REX['ADDON'][$myself]['VERSION'] = array
(
'VERSION'      => 0,
'MINORVERSION' => 2,
'SUBVERSION'   => preg_replace('/[^0-9]/','',"$Revision$")
);
$REX['ADDON']['version'][$myself]     = implode('.', $REX['ADDON'][$myself]['VERSION']);
$REX['ADDON']['title'][$myself]       = 'Referrer Block';
$REX['ADDON']['author'][$myself]      = 'rexdev.de';
$REX['ADDON']['supportpage'][$myself] = 'forum.redaxo.de';
$REX['ADDON']['perm'][$myself]        = $myself.'[]';
$REX['PERM'][]                        = $myself.'[]';

// DYN SETTINGS
////////////////////////////////////////////////////////////////////////////////
// --- DYN
$REX["ADDON"]["image_manager"]["PLUGIN"]["referrer_block.image_manager.plugin"]["rex_img_file"] = '';
$REX["ADDON"]["image_manager"]["PLUGIN"]["referrer_block.image_manager.plugin"]["rex_img_type"] = '';
// --- /DYN

// MAIN
////////////////////////////////////////////////////////////////////////////////
if(isset($_SERVER['HTTP_REFERER']) && isset($_GET['rex_img_file']) && isset($_GET['rex_img_type']))
{
  $referrer = parse_url($_SERVER['HTTP_REFERER']);
  if($referrer['host']!=$_SERVER['HTTP_HOST'])
  {
    rex_register_extension('IMAGE_MANAGER_INIT','referrer_block_init');

    function referrer_block_init($params)
    {
      global $REX;

      $replace_file = $REX["ADDON"]["image_manager"]["PLUGIN"]["referrer_block.image_manager.plugin"]["rex_img_file"];
      $replace_type = $REX["ADDON"]["image_manager"]["PLUGIN"]["referrer_block.image_manager.plugin"]["rex_img_type"];

      if($replace_file!='')
      {
        $params['subject']['rex_img_file'] = $replace_file;
        $params['subject']['imagepath'] = $REX['HTDOCS_PATH'].'/files/'.$replace_file;
      }

      if($replace_type!='')
      {
        $params['subject']['rex_img_type'] = $replace_type;
      }

      return $params['subject'];
    }
  }
}