<?php
/**
* ImageMagick Precompress Plugin for "Image Manager EP" Addon
*
* @author http://rexdev.de
* @link https://github.com/jdlx/precompress.image_manager.plugin
* @link https://github.com/jdlx/image_manager_ep
*
* @package redaxo 4.3.x/4.4.x
* @version 1.3.1
*/


// ADDON IDENTIFIER & ROOT DIR
////////////////////////////////////////////////////////////////////////////////
$myself = 'precompress.image_manager.plugin';         FB::log($myself,' $myself');
$myroot = $REX['INCLUDE_PATH'].'/addons/image_manager/plugins/'.$myself;


// REX COMMONS
////////////////////////////////////////////////////////////////////////////////
$REX['ADDON']['version'][$myself]     = '1.3.1';
$REX['ADDON']['title'][$myself]       = 'Precompressor';
$REX['ADDON']['author'][$myself]      = 'rexdev.de';
$REX['ADDON']['supportpage'][$myself] = 'forum.redaxo.de';
$REX['ADDON']['perm'][$myself]        = $myself.'[]';
$REX['PERM'][]                        = $myself.'[]';

// SETTINGS
////////////////////////////////////////////////////////////////////////////////
$REX['ADDON']['image_manager']['PLUGIN']['precompress.image_manager.plugin']['cachefile'] = $REX['INCLUDE_PATH'].'/generated/files/precompress.image_manager.plugin_cache.php';
// --- DYN
$REX["ADDON"]["image_manager"]["PLUGIN"]["precompress.image_manager.plugin"]["trigger_width"]   = 1000;
$REX["ADDON"]["image_manager"]["PLUGIN"]["precompress.image_manager.plugin"]["trigger_height"]  = 1000;
$REX["ADDON"]["image_manager"]["PLUGIN"]["precompress.image_manager.plugin"]["path_to_convert"] = '';
$REX["ADDON"]["image_manager"]["PLUGIN"]["precompress.image_manager.plugin"]["service_url"] = '';
// --- /DYN


// CHECK PATH TO CONVERT
////////////////////////////////////////////////////////////////////////////////
if($REX["ADDON"]["image_manager"]["PLUGIN"]["precompress.image_manager.plugin"]["path_to_convert"]=='')
{
  $cmd = 'which convertxr';
  exec($cmd, $out ,$ret);
  switch($ret)
  {
    case 0:
      $REX["ADDON"]["image_manager"]["PLUGIN"]["precompress.image_manager.plugin"]["path_to_convert"] = $out[0];
    break;
    case 1:
      $REX["ADDON"]["image_manager"]["PLUGIN"]["precompress.image_manager.plugin"]['rex_warning'][] = 'Could not determine path to <code>convert</code> using <code>which convert</code> ..<br />Check if your server does have <code>Imagemagick</code> available and provide path to convert manually.';
      return;
    break;
    default:
  }
}


// MAIN
////////////////////////////////////////////////////////////////////////////////
if(!file_exists($REX['ADDON']['image_manager']['PLUGIN']['precompress.image_manager.plugin']['cachefile']))
{
  refresh_precompress_img_list();         FB::log('refreshing..');
}

rex_register_extension('IMAGE_MANAGER_INIT','precompress_init');
rex_register_extension('MEDIA_ADDED'       ,'refresh_precompress_img_list');
rex_register_extension('MEDIA_UPDATED'     ,'refresh_precompress_img_list');

function precompress_init($params)
{                                                                               FB::group(__CLASS__.'::'.__FUNCTION__, array("Collapsed"=>false));
  if($params['subject']['rex_img_file']!='')
  {
    global $REX;
    $myREX           = $REX['ADDON']['image_manager']['PLUGIN']['precompress.image_manager.plugin'];
    require_once($myREX['cachefile']);

    $trigger_width   = $myREX['trigger_width'];
    $trigger_height  = $myREX['trigger_height'];
    $path_to_convert = $myREX['path_to_convert'];
    $service_url     = $myREX['service_url'];    FB::log($service_url,' $service_url');

    $img             = $params['subject']['rex_img_file'];
    $imagepath       = $params['subject']['imagepath'];
    $cachepath       = $params['subject']['cachepath'];

    if(in_array($img,$precompress_img_list))
    {
      $compfile = $cachepath.'image_manager__PRECOMPRESS_'.$img;         FB::log($compfile,' $compfile');
      if(!file_exists($compfile))
      {
        // USING IMAGEMAGICK
        if($path_to_convert!=''){
          $cmd = $path_to_convert.' -resize "'.$trigger_width.'x'.$trigger_height.'" '.realpath($imagepath).' '.$compfile;
          exec($cmd, $out = array(),$ret);
          if($ret!=0) {
            trigger_error('PRECOMPRESS.IMAGEMANAGER.PLUGIN: exec() returns error "'.$ret.'"', E_USER_WARNING);
          }
        // USING EXTERNAL SERVICE
        }elseif($service_url!=''){

              $file_name_with_full_path = realpath($imagepath);
              $post_data['name'] = "resize";
              $post_data['file'] = "@".$file_name_with_full_path;         FB::log($post_data,' $post_data');
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_URL, $service_url);
              curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
              curl_setopt($ch, CURLOPT_VERBOSE, 1);
              $response = curl_exec($ch);
              file_put_contents($compfile, $response);

        }
      }

      if(file_exists($compfile))
      {
        $params['subject']['imagepath'] = $compfile;
      }
      else
      {
        trigger_error('PRECOMPRESS.IMAGEMANAGER.PLUGIN: could not create precompressed file', E_USER_WARNING);
      }         FB::groupEnd();
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

  $db = new rex_sql();
  $db->setQuery('SELECT `filename`
                 FROM `rex_file`
                 WHERE (`width` >='.$trigger_width.'
                     OR `height` >='.$trigger_height.')
                 AND (`filetype`="image/gif"
                   OR `filetype`="image/png"
                   OR `filetype`="image/x-png"
                   OR `filetype`="image/pjpeg"
                   OR `filetype`="image/jpeg"
                   OR `filetype`="image/jpg"
                   OR `filetype`="image/bmp");');
  while($db->hasNext())
  {
    $img_list[] = $db->getValue('filename');
    $db->next();
  }

  rex_put_file_contents($cachefile,'<?php'.PHP_EOL.'$precompress_img_list = '.var_export($img_list,true).PHP_EOL.'?>');
}
