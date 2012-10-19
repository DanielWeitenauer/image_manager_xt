<?php
/**
* ImageMagick Precompress Plugin for "Image Manager EP" Addon
*
* @author http://rexdev.de
* @link https://github.com/jdlx/precompress.image_manager.plugin
* @link https://github.com/jdlx/image_manager_ep
*
* @package redaxo 4.3.x/4.4.x
* @version 0.3.0
*/


// ADDON IDENTIFIER & ROOT DIR
////////////////////////////////////////////////////////////////////////////////
$myself = 'precompress.image_manager.plugin';
$myroot = $REX['INCLUDE_PATH'].'/addons/image_manager/plugins/'.$myself;


// REX COMMONS
////////////////////////////////////////////////////////////////////////////////
$Revision = '';
$REX['ADDON'][$myself]['VERSION'] = array
(
'VERSION'      => 0,
'MINORVERSION' => 3,
'SUBVERSION'   => 0
);
$REX['ADDON']['version'][$myself]     = implode('.', $REX['ADDON'][$myself]['VERSION']);
$REX['ADDON']['title'][$myself]       = 'Precompressor';
$REX['ADDON']['author'][$myself]      = 'rexdev.de';
$REX['ADDON']['supportpage'][$myself] = 'forum.redaxo.de';
$REX['ADDON']['perm'][$myself]        = $myself.'[]';
$REX['PERM'][]                        = $myself.'[]';

// SETTINGS
////////////////////////////////////////////////////////////////////////////////
$REX['ADDON']['image_manager']['PLUGIN']['precompress.image_manager.plugin']['cachefile'] = $REX['INCLUDE_PATH'].'/generated/files/precompress.image_manager.plugin_cache.php';
// --- DYN
$REX["ADDON"]["image_manager"]["PLUGIN"]["precompress.image_manager.plugin"]["trigger_width"]   = 1500;
$REX["ADDON"]["image_manager"]["PLUGIN"]["precompress.image_manager.plugin"]["trigger_height"]  = 1500;
$REX["ADDON"]["image_manager"]["PLUGIN"]["precompress.image_manager.plugin"]["path_to_convert"] = '/usr/bin/convert';
// --- /DYN


// MAIN
////////////////////////////////////////////////////////////////////////////////
if(!file_exists($REX['ADDON']['image_manager']['PLUGIN']['precompress.image_manager.plugin']['cachefile']))
{
  refresh_precompress_img_list();
}

rex_register_extension('IMAGE_MANAGER_INIT','precompress_init');
rex_register_extension('MEDIA_ADDED'       ,'refresh_precompress_img_list');
rex_register_extension('MEDIA_UPDATED'     ,'refresh_precompress_img_list');

function precompress_init($params)
{
  if($params['subject']['rex_img_file']!='')
  {
    global $REX;
    $myREX           = $REX['ADDON']['image_manager']['PLUGIN']['precompress.image_manager.plugin'];
    require_once($myREX['cachefile']);

    $trigger_width   = $myREX['trigger_width'];
    $trigger_height  = $myREX['trigger_height'];
    $path_to_convert = $myREX['path_to_convert'];

    $img             = $params['subject']['rex_img_file'];
    $imagepath       = $params['subject']['imagepath'];
    $cachepath       = $params['subject']['cachepath'];

    if(in_array($img,$precompress_img_list))
    {
      $compfile = $cachepath.'image_manager__PRECOMPRESS_'.$img;
      if(!file_exists($compfile))
      {
        $cmd = $path_to_convert.' -resize "'.$trigger_width.'x'.$trigger_height.'" '.realpath($imagepath).' '.$compfile;
        exec($cmd, $out = array(),$ret);
        if($ret!=0)
        {
          trigger_error('PRECOMPRESS.IMAGEMANAGER.PLUGIN: exec() returns error "'.$ret.'"', E_USER_WARNING);
        }
      }

      if(file_exists($compfile))
      {
        $params['subject']['imagepath'] = $compfile;
      }
      else
      {
        trigger_error('PRECOMPRESS.IMAGEMANAGER.PLUGIN: could not create precompressed file', E_USER_WARNING);
      }
    }

  }

  return $params['subject'];
}

function refresh_precompress_img_list()
{
  global $REX;

  $myREX           = $REX['ADDON']['image_manager']['PLUGIN']['precompress.image_manager.plugin'];
  $trigger_width   = $myREX['trigger_width'];
  $trigger_height  = $myREX['trigger_height'];
  $cachefile       = $myREX['cachefile'];
  $img_list        = array();

  if(file_exists($cachefile))
  {
    unlink($cachefile);
  }

  $db = new rex_sql();
  $db->setQuery('SELECT `filename`
                 FROM `rex_file`
                 WHERE `width` >='.$trigger_width.'
                 OR `height` >='.$trigger_height);
  while($db->hasNext())
  {
    $img_list[] = $db->getValue('filename');
    $db->next();
  }

  rex_put_file_contents($cachefile,'<?php'.PHP_EOL.'$precompress_img_list = '.var_export($img_list,true).PHP_EOL.'?>');
}
