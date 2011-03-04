<?php
/**
* Referrer Blocker Plugin for image_manager Addon
*
* @package redaxo4.3
* @version 0.1
* $Id$:
*/

$myself = 'referrer_block.imagemanager.plugin';

// UPDATE/WRITE USER SETTINGS
////////////////////////////////////////////////////////////////////////////////
if ($func == 'save_settings')
{
  $REX['ADDON']['image_manager']['PLUGIN'][$myself]['rex_img_file']   = rex_request('rex_img_file','string');
  $REX['ADDON']['image_manager']['PLUGIN'][$myself]['rex_img_type'] = rex_request('rex_img_type','string');

  $content =
'$REX["ADDON"]["image_manager"]["PLUGIN"]["'.$myself.'"]["rex_img_file"] = \''.rex_request('rex_img_file','string').'\';
$REX["ADDON"]["image_manager"]["PLUGIN"]["'.$myself.'"]["rex_img_type"] = \''.rex_request('rex_img_type','string').'\';
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

';