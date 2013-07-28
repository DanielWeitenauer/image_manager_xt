<?php
/**
 * image_manager Addon
 *
 * @author office[at]vscope[dot]at Wolfgang Hutteger
 * @author markus.staab[at]redaxo[dot]de Markus Staab
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 *
 * @author jdlx c/o http://rexdev.de/
 * @link https://github.com/jdlx/image_manager_xt
 *
 * @package redaxo 4.4.x/4.5.x
 * @version 1.4.2
 */

/**
 * @name    REXDEV IMAGE MANAGER PLUGINS GUI CONTROLLER
 * @link    http://rexdev.de/addons/image_manager-ep.html
 * @author  rexdev.de
 * @package redaxo 4.3.x/4.4.x
 * @version Addon: 0.1
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


// INCLUDE PARSER FUNCTION
////////////////////////////////////////////////////////////////////////////////
require_once $REX['INCLUDE_PATH'].'/addons/image_manager/functions/function.imm_incparse.inc.php';


// PLUGINS NAVI
$pluginnav = $pluginnav == '' ? 'Es sind keine Plugins installiert/aktiviert.' : $pluginnav;
echo '
<div class="rex-addon-output im-plugins">
  <h2 class="rex-hl2" style="font-size:1em;border-bottom:0;">'.$pluginnav.'</h2>
</div>';


// PLUGIN FORM WRAPPER
////////////////////////////////////////////////////////////////////////////////
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


// PLUGIN INFO WRAPPER
////////////////////////////////////////////////////////////////////////////////
$help = $plugin_root.$plugin.'/pages/help.textile';
if(file_exists($help))
{
  echo '
  <div class="rex-addon-output im-plugins">
    <h2 class="rex-hl2" style="font-size:1.2em">Infos</h2>
    <p style="float:right;color:gray;padding:16px 20px 0 0;">Version: '.$REX['ADDON']['plugins']['image_manager']['version'][$plugin].'</p>

    <div class="rex-addon-content">

    '.imm_incparse($plugin_root,$plugin.'/pages/help.textile','textile',true).'

    </div><!-- /rex-addon-content -->
  </div><!-- /rex-addon-output -->
  ';
}

// JS OPEN LINKS IN NEW WIN DOW VIA CLASS
////////////////////////////////////////////////////////////////////////////////
echo '
<script type="text/javascript">
// onload
window.onload = externalLinks;

// http://www.sitepoint.com/article/standards-compliant-world
function externalLinks()
{
 if (!document.getElementsByTagName) return;
 var anchors = document.getElementsByTagName("a");
 for (var i=0; i<anchors.length; i++)
 {
   var anchor = anchors[i];
   if (anchor.getAttribute("href"))
   {
     if (anchor.getAttribute("class") == "blank" || anchor.getAttribute("class") == "jsopenwin")
     {
     anchor.target = "_blank";
     }
   }
 }
}
</script>
';
