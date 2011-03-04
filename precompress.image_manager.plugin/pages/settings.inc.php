<?php
/**
* ImageMagick Precompress Plugin for image_manager Addon
*
* @package redaxo4.3
* @version 0.2
* $Id$:
*/

$myself = 'precompress.image_manager.plugin';

// UPDATE/WRITE USER SETTINGS
////////////////////////////////////////////////////////////////////////////////
if ($func == 'save_settings')
{
  $REX['ADDON']['image_manager']['PLUGIN'][$myself]['trigger_width']   = rex_request('trigger_width','int');
  $REX['ADDON']['image_manager']['PLUGIN'][$myself]['trigger_height']  = rex_request('trigger_height','int');
  $REX['ADDON']['image_manager']['PLUGIN'][$myself]['path_to_convert'] = rex_request('path_to_convert','string');

  $content =
'$REX["ADDON"]["image_manager"]["PLUGIN"]["'.$myself.'"]["trigger_width"]   = '.rex_request('trigger_width','int').';
$REX["ADDON"]["image_manager"]["PLUGIN"]["'.$myself.'"]["trigger_height"]  = '.rex_request('trigger_height','int').';
$REX["ADDON"]["image_manager"]["PLUGIN"]["'.$myself.'"]["path_to_convert"] = \''.rex_request('path_to_convert','string').'\';
';

  $file = $REX['INCLUDE_PATH'].'/addons/image_manager/plugins/'.$myself.'/config.inc.php';
  rex_replace_dynamic_contents($file, $content);
  refresh_precompress_img_list();
  echo rex_info('Einstellungen wurden gespeichert.');
}

// FORM
return
'
<div class="rex-form-row">
  <p class="rex-form-col-a rex-form-text">
    <label for="trigger_width">Trigger Width: </label>
    <input id="trigger_width" class="rex-form-text" type="text" name="trigger_width" value="'.
    $REX['ADDON']['image_manager']['PLUGIN'][$myself]['trigger_width'].
    '" />
  </p>
</div><!-- /rex-form-row -->


<div class="rex-form-row">
  <p class="rex-form-col-a rex-form-text">
    <label for="trigger_height">Trigger Height: </label>
    <input id="trigger_height" class="rex-form-text" type="text" name="trigger_height" value="'.
    $REX['ADDON']['image_manager']['PLUGIN'][$myself]['trigger_height'].
    '" />
  </p>
</div><!-- /rex-form-row -->


<div class="rex-form-row">
  <p class="rex-form-col-a rex-form-text">
    <label for="path_to_convert">Convert Path:  </label>
    <input id="path_to_convert" class="rex-form-text" type="text" name="path_to_convert" value="'.
    $REX['ADDON']['image_manager']['PLUGIN'][$myself]['path_to_convert'].
    '" />
  </p>
</div><!-- /rex-form-row -->

';