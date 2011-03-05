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


// UPDATE/WRITE USER SETTINGS
////////////////////////////////////////////////////////////////////////////////
if ($func == 'save_settings')
{
  $REX['ADDON']['image_manager']['PLUGIN']['_rex_resize.image_manager.plugin']['max_cachefiles'] = rex_request('max_cachefiles','string');
  $content =
'$REX["ADDON"]["image_manager"]["PLUGIN"]["_rex_resize.image_manager.plugin"]["max_cachefiles"] = '.rex_request('max_cachefiles','string').';
';

  $file = $REX['INCLUDE_PATH'].'/addons/image_manager/plugins/'.$plugin.'/config.inc.php';
  rex_replace_dynamic_contents($file, $content);
  echo rex_info('Einstellungen wurden gespeichert.');
}

// FORM
return
'
<div class="rex-form-row">
  <p class="rex-form-col-a rex-form-text">
    <label for="max_cachefiles">Max. Cachefiles: </label>
    <input id="max_cachefiles" class="rex-form-text" type="text" name="max_cachefiles" value="'.$REX['ADDON']['image_manager']['PLUGIN']['_rex_resize.image_manager.plugin']['max_cachefiles'].'" />
  </p>
</div><!-- /rex-form-row -->
';