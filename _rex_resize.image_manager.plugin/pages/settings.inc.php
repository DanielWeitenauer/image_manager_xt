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
  $REX['ADDON']['image_manager']['PLUGIN']['_rex_resize.image_manager.plugin']['max_cachefiles'] = rex_request('max_cachefiles','int');
  $REX['ADDON']['image_manager']['PLUGIN']['_rex_resize.image_manager.plugin']['tiny_support'] = rex_request('tiny_support','int');
  $content = '
$REX["ADDON"]["image_manager"]["PLUGIN"]["_rex_resize.image_manager.plugin"]["max_cachefiles"] = '.rex_request('max_cachefiles','int').';
$REX["ADDON"]["image_manager"]["PLUGIN"]["_rex_resize.image_manager.plugin"]["tiny_support"]   = '.rex_request('tiny_support','int').';
';

  $file = $REX['INCLUDE_PATH'].'/addons/image_manager/plugins/'.$plugin.'/config.inc.php';
  rex_replace_dynamic_contents($file, $content);
  echo rex_info('Einstellungen wurden gespeichert.');
}

// SELECT BOX
////////////////////////////////////////////////////////////////////////////////
$tmp = new rex_select();
$tmp->setSize(1);
$tmp->setName('tiny_support');
$tmp->addOption('On',1);
$tmp->addOption('Off',0);
$tmp->setSelected($REX['ADDON']['image_manager']['PLUGIN']['_rex_resize.image_manager.plugin']['tiny_support']);
$tiny_support = $tmp->get();

// FORM
return
'
<div class="rex-form-row">
  <p class="rex-form-col-a rex-form-text">
    <label for="max_cachefiles">Max. Cachefiles: </label>
    <input id="max_cachefiles" class="rex-form-text" type="text" name="max_cachefiles" value="'.$REX['ADDON']['image_manager']['PLUGIN']['_rex_resize.image_manager.plugin']['max_cachefiles'].'" />
  </p>
</div><!-- /rex-form-row -->

<div class="rex-form-row">
  <p class="rex-form-col-a rex-form-select">
    <label for="tiny_support">Tiny Support: </label>

    '.$tiny_support.'

  </p>
</div><!-- /rex-form-row -->


';