<?php
/**
 * Referrer Blocker Plugin for image_manager Addon
 *
 * @author jdlx c/o http://rexdev.de/
 * @link https://github.com/jdlx/image_manager_xt
 *
 * @package redaxo 4.4.x/4.5.x
 * @version 1.4.1
 */

$myself = 'referrer_block.image_manager.plugin';

// UPDATE/WRITE USER SETTINGS
////////////////////////////////////////////////////////////////////////////////
if ($func == 'save_settings')
{
  $REX['ADDON']['image_manager']['PLUGIN'][$myself]['rex_img_file']  = rex_request('rex_img_file','string');
  $REX['ADDON']['image_manager']['PLUGIN'][$myself]['rex_img_type']  = rex_request('rex_img_type','string');
  $REX['ADDON']['image_manager']['PLUGIN'][$myself]['allowed_hosts'] = rex_request('allowed_hosts','string');

  $content =
'$REX["ADDON"]["image_manager"]["PLUGIN"]["'.$myself.'"]["rex_img_file"] = \''.rex_request('rex_img_file','string').'\';
$REX["ADDON"]["image_manager"]["PLUGIN"]["'.$myself.'"]["rex_img_type"] = \''.rex_request('rex_img_type','string').'\';
$REX["ADDON"]["image_manager"]["PLUGIN"]["'.$myself.'"]["allowed_hosts"] = \''.rex_request('allowed_hosts','string').'\';
';

  $file = $REX['INCLUDE_PATH'].'/addons/image_manager/plugins/'.$myself.'/config.inc.php';
  rex_replace_dynamic_contents($file, $content);
  echo rex_info('Einstellungen wurden gespeichert.');
}

// FORM
return
'
<div class="rex-form-row">
  <p class="rex-form-col-a rex-form-text">
    <label for="rex_img_file">Ersatz-Bilddatei: </label>
    <input id="rex_img_file" class="rex-form-text" type="text" name="rex_img_file" value="'.
    $REX['ADDON']['image_manager']['PLUGIN'][$myself]['rex_img_file'].
    '" />
  </p>
</div><!-- /rex-form-row -->


<div class="rex-form-row">
  <p class="rex-form-col-a rex-form-text">
    <label for="rex_img_type">Ersatz-Bildtyp: </label>
    <input id="rex_img_type" class="rex-form-text" type="text" name="rex_img_type" value="'.
    $REX['ADDON']['image_manager']['PLUGIN'][$myself]['rex_img_type'].
    '" />
  </p>
</div><!-- /rex-form-row -->


<div class="rex-form-row">
  <p class="rex-form-col-a rex-form-text">
    <label for="allowed_hosts">Hosts Whitelist:<br /><span style="color:gray;">( foo.com,bar.org,.. )</span></label>
    <input id="allowed_hosts" class="rex-form-text" type="text" name="allowed_hosts" value="'.
    $REX['ADDON']['image_manager']['PLUGIN'][$myself]['allowed_hosts'].
    '" />
  </p>
</div><!-- /rex-form-row -->

';
