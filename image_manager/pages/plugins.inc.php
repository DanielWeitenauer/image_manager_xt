<?php
/**
* @name    REXDEV IMAGE MANAGER PLUGINS GUI CONTROLLER
* @link    http://rexdev.de/addons/image_manager-ep.html
* @author  rexdev.de
* @package redaxo4
* @version Addon: 0.1
*
* $Id$:
*/

// PARAMS & ROOT DIR
////////////////////////////////////////////////////////////////////////////////
$mypage      = rex_request('page', 'string');
$subpage     = rex_request('subpage', 'string');
$plugin      = rex_request('plugin', 'string');
$func        = rex_request('func', 'string');
$plugin_root = $REX['INCLUDE_PATH'].'/addons/image_manager/plugins/';

// BUILD PLUGIN NAVIGATION
////////////////////////////////////////////////////////////////////////////////
$im_plugins = $REX['ADDON']['plugins']['image_manager'];
$pluginnav  = $separator = '';
foreach($im_plugins['status'] as $this_plugin => $status)
{
  if($status == 1)
  {
    if($plugin=='') $plugin = $this_plugin; // first active plugin as default

    if ($plugin != $this_plugin)
    {
      $pluginnav .= $separator.'<a href="?page=image_manager&subpage='.$subpage.'&plugin='.$this_plugin.'" class="plugin">'.$im_plugins['title'][$this_plugin].'</a>';
    }
    else
    {
      $pluginnav .= $separator.'<span class="plugin">'.$im_plugins['title'][$this_plugin].'</span>';
    }
    $separator = ' | ';
  }
}


// OUTPUT
////////////////////////////////////////////////////////////////////////////////


// PLUGINS NAVI
$pluginnav = $pluginnav == '' ? 'Es sind keine Plugins installiert/aktiviert.' : $pluginnav;
echo '
<div class="rex-addon-output im-plugins">
  <h2 class="rex-hl2" style="font-size:1em;border-bottom:0;">'.$pluginnav.'</h2>
</div>';


// PLUGIN FORM
$form = $plugin_root.$plugin.'/pages/settings.inc.php';
if(file_exists($form))
{
  $form = include $plugin_root.$plugin.'/pages/settings.inc.php';
  echo '
  <div class="rex-addon-output im-plugins">
    <div class="rex-form">

      <form action="index.php" method="post">
        <input type="hidden" name="page" value="image_manager" />
        <input type="hidden" name="subpage" value="plugins" />
        <input type="hidden" name="plugin" value="'.$plugin.'" />
        <input type="hidden" name="func" value="save_settings" />
  
        <fieldset class="rex-form-col-1">
          <legend style="font-size:1.2em">Settings</legend>
            <div class="rex-form-wrapper">
  
              '.$form;

  if(!isset($nosubmit))
  {
    echo '
              <div class="rex-form-row rex-form-element-v2">
                <p class="rex-form-submit">
                  <input class="rex-form-submit" type="submit" id="sendit" name="sendit" value="Einstellungen speichern" />
                </p>
              </div><!-- /rex-form-row -->';
  }

  echo '
              </div><!-- /rex-form-wrapper -->
        </fieldset>
      </form>
    </div><!-- /rex-form -->
  </div><!-- /rex-addon-output -->
  ';
}


// PLUGIN HELP
$help = $plugin_root.$plugin.'/pages/help.textile';
if(file_exists($help))
{
  echo '
  <div class="rex-addon-output im-plugins">
    <h2 class="rex-hl2" style="font-size:1.2em">Infos</h2>
    <p style="float:right;color:gray;padding:16px 20px 0 0;">Version: '.$REX['ADDON']['plugins']['image_manager']['version'][$plugin].'</p>
   
    <div class="rex-addon-content">

    '.rexdev_incparse($plugin_root,$plugin.'/pages/help.textile','textile',true).'

    </div><!-- /rex-addon-content -->
  </div><!-- /rex-addon-output -->
  ';
}
