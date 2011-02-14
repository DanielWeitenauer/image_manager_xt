<?php
/**
* ImageMagick Precompress Plugin for image_manager Addon
*
* @package redaxo4.3
* @version 0.1
* $Id$:
*/

// SETTINGS
////////////////////////////////////////////////////////////////////////////////
$REX['ADDON']['image_manager']['PLUGINS']['precompress.imagemanager.plugin'] = array(
'trigger_width'   => 1200,
'trigger_height'  => 1200,
'path_to_convert' => '/opt/local/bin/convert',
'cachefile'       => $REX['INCLUDE_PATH'].'/generated/files/precompress_img_list.php'
);


// MAIN
////////////////////////////////////////////////////////////////////////////////
$myREX = $REX['ADDON']['image_manager']['PLUGINS']['precompress.imagemanager.plugin'];

if(!file_exists($myREX['cachefile']))
{
  build_precompress_img_list();
}

rex_register_extension('IMAGE_MANAGER_INIT','precompress_init');
rex_register_extension('MEDIA_ADDED'       ,'build_precompress_img_list');
rex_register_extension('MEDIA_UPDATED'     ,'build_precompress_img_list');

function precompress_init($params)
{
  if($params['subject']['rex_img_file']!='')
  {
    global $REX;
    $myREX           = $REX['ADDON']['image_manager']['PLUGINS']['precompress.imagemanager.plugin'];
    require_once($myREX['cachefile']);                                          //fb($precompress_img_list,'$precompress_img_list');
    
    $trigger_width   = $myREX['trigger_width'];
    $trigger_height  = $myREX['trigger_height'];
    $path_to_convert = $myREX['path_to_convert'];

    $img             = $params['subject']['rex_img_file'];                      //fb($img,'$img');
    $imagepath       = $params['subject']['imagepath'];                         //fb($imagepath,'$imagepath');
    $cachepath       = $params['subject']['cachepath'];                         //fb($cachepath,'$cachepath');
    
    if(in_array($img,$precompress_img_list))
    {
      $compfile = $cachepath.'opi_'.$img;
      if(!file_exists($compfile))
      {
        $cmd = $path_to_convert.' -resize "'.$trigger_width.'x'.$trigger_height.'" '.realpath($imagepath).' '.$compfile; //fb($cmd,'$cmd');
        exec($cmd, $out = array(),$ret);                                        //fb($out,'$outoutput');fb($rretc,'$ret');
      }
      $params['subject']['imagepath'] = $compfile = $cachepath.'opi_'.$img;
    }

  }

  return $params['subject'];
}

function build_precompress_img_list()
{
  global $REX;
  
  $myREX           = $REX['ADDON']['image_manager']['PLUGINS']['precompress.imagemanager.plugin'];
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