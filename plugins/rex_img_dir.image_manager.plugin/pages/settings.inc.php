<?php
/**
 * Custom Folder Plugin for image_manager Addon
 *
 * @author jdlx c/o http://rexdev.de/
 * @link https://github.com/jdlx/image_manager_xt
 *
 * @package redaxo 4.4.x/4.5.x
 * @version 1.4.1
 */


// UPDATE/WRITE USER SETTINGS
////////////////////////////////////////////////////////////////////////////////
if ($func == 'save_settings')
{
  $img_dirs = rex_request('rex_img_dirs','string');
  $img_dirs = img_dir_string_2_array($img_dirs);
  $REX['ADDON']['image_manager']['PLUGIN']['rex_img_dir.image_manager.plugin']['img_dirs'] = $img_dirs;
  $content =
'$REX["ADDON"]["image_manager"]["PLUGIN"]["rex_img_dir.image_manager.plugin"]["img_dirs"] = '.stripslashes(var_export($img_dirs,true)).';
';

  $file = $REX['INCLUDE_PATH'].'/addons/image_manager/plugins/'.$plugin.'/config.inc.php';
  rex_replace_dynamic_contents($file, $content);
  echo rex_info('Einstellungen wurden gespeichert.');
}

function img_dir_array_2_string($arr)
{
  $str = '';
  foreach($arr as $k => $v)
  {
    $str .= $k.' '.$v.PHP_EOL;
  }
  return $str;
}

function img_dir_string_2_array($str)
{
  $arr = array();
  $tmp = array_filter(preg_split("/\n|\r\n|\r/", $str));
  foreach($tmp as $k => $v)
  {
    $a    = explode(' ',trim($v));
    $id   = trim($a[0]);
    $path = trim($a[1]);
    $path = trim($path,'/');
    $arr[$id] = $path;
  }
  return $arr;
}

// FORM
return
'
<div class="rex-form-row">
  <p class="rex-form-col-a rex-form-textarea">
    <label for="img_dirs">Image Directories</label>
    <textarea id="img_dirs" cols="50" rows="6" class="rex-form-textarea" name="rex_img_dirs">'.img_dir_array_2_string($REX['ADDON']['image_manager']['PLUGIN']['rex_img_dir.image_manager.plugin']['img_dirs']).'</textarea>
  </p>
</div><!-- .rex-form-row -->
';
