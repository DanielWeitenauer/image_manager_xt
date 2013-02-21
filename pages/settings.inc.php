<?php
/**
* ImageMagick Precompress Plugin for "Image Manager Pro" Addon
*
* @author http://rexdev.de
* @link https://github.com/jdlx/precompress.image_manager.plugin
* @link https://github.com/jdlx/image_manager_ep
*
* @package redaxo 4.3.x/4.4.x
* @version 1.3.1
*/

$myself = 'precompress.image_manager.plugin';

// UPDATE/WRITE USER SETTINGS
////////////////////////////////////////////////////////////////////////////////
if ($func == 'save_settings')
{
  $REX['ADDON']['image_manager']['PLUGIN'][$myself]['trigger_width']   = rex_request('trigger_width','int');
  $REX['ADDON']['image_manager']['PLUGIN'][$myself]['trigger_height']  = rex_request('trigger_height','int');
  $REX['ADDON']['image_manager']['PLUGIN'][$myself]['path_to_convert'] = rex_request('path_to_convert','string');
  $REX['ADDON']['image_manager']['PLUGIN'][$myself]['service_url'] = rex_request('service_url','string');

  $content =
'$REX["ADDON"]["image_manager"]["PLUGIN"]["'.$myself.'"]["trigger_width"]   = '.rex_request('trigger_width','int').';
$REX["ADDON"]["image_manager"]["PLUGIN"]["'.$myself.'"]["trigger_height"]  = '.rex_request('trigger_height','int').';
$REX["ADDON"]["image_manager"]["PLUGIN"]["'.$myself.'"]["path_to_convert"] = \''.rex_request('path_to_convert','string').'\';
$REX["ADDON"]["image_manager"]["PLUGIN"]["'.$myself.'"]["service_url"] = \''.rex_request('service_url','string').'\';
';

  $file = $REX['INCLUDE_PATH'].'/addons/image_manager/plugins/'.$myself.'/config.inc.php';
  rex_replace_dynamic_contents($file, $content);
  refresh_precompress_img_list();
  echo rex_info('Einstellungen wurden gespeichert.');
}

// REX INFO/WARNING FROM CONFIG
////////////////////////////////////////////////////////////////////////////////
if(isset($REX["ADDON"]["image_manager"]["PLUGIN"]["precompress.image_manager.plugin"]['rex_warning'])){
  foreach($REX["ADDON"]["image_manager"]["PLUGIN"]["precompress.image_manager.plugin"]['rex_warning'] as $w){
    echo rex_warning($w);
  }
}
if(isset($REX["ADDON"]["image_manager"]["PLUGIN"]["precompress.image_manager.plugin"]['rex_info'])){
  foreach($REX["ADDON"]["image_manager"]["PLUGIN"]["precompress.image_manager.plugin"]['rex_info'] as $i){
    echo rex_info($i);
  }
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

  </div><!-- /rex-form-wrapper -->
</fieldset>

<fieldset class="rex-form-col-1">
  <legend style="font-size:1.2em">Fallback Service</legend>
    <div class="rex-form-wrapper">

      <div class="rex-form-row">
        <p class="rex-form-col-a rex-form-text">
          <label for="service_url">Service URL</label>
          <input id="service_url" class="rex-form-text" type="text" name="service_url" value="'.
          $REX['ADDON']['image_manager']['PLUGIN'][$myself]['service_url'].
          '" />
        </p>
      </div><!-- /rex-form-row -->

';
