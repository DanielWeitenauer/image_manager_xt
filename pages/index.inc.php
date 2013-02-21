<?php
/**
 * image_manager Addon
 *
 * @author office[at]vscope[dot]at Wolfgang Hutteger
 * @author markus.staab[at]redaxo[dot]de Markus Staab
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 *
 * @author jdlx / rexdev.de
 * @link https://github.com/jdlx/image_manager_ep
 *
 * @package redaxo 4.3.x/4.4.x
 * @version 1.2.5
 */

require_once (dirname(__FILE__). '/../functions/function_rex_effects.inc.php');
require_once (dirname(__FILE__). '/../functions/function_rex_extensions.inc.php');

// BACKEND CSS
////////////////////////////////////////////////////////////////////////////////
if ($REX['REDAXO'])
{
  rex_register_extension('PAGE_HEADER', 'im_plugins_header');

  function im_plugins_header($params)
  {
    global $REX;

    $params['subject'] .=
      PHP_EOL.'<!-- IMAGE_MANAGER -->'.
      PHP_EOL.'<link rel="stylesheet" type="text/css" href="'.$REX['HTDOCS_PATH'].'files/addons/image_manager/backend.css" media="screen, projection, print" />'.
      PHP_EOL.'<!-- /IMAGE_MANAGER -->'.PHP_EOL;

    return $params['subject'];
  }
}

require $REX['INCLUDE_PATH'] . '/layout/top.php';

$page = rex_request('page', 'string');
$subpage = rex_request('subpage', 'string');
$func = rex_request('func', 'string');
$msg = '';

if ($subpage == 'clear_cache')
{
  $c = rex_image_cacher::deleteCache();
  $msg = $I18N->msg('imanager_cache_files_removed', $c);
}

rex_title('Image Manager Pro <span class="addonversion">'.$REX['ADDON']['version']['image_manager'].'</span>', $REX['ADDON']['pages']['image_manager']);

// Include Current Page
switch($subpage)
{
  case 'types' :
  case 'effects' :
  case 'settings' :
  case 'plugins' :
  case 'overview' :
    break;

  default:
  {
    if ($msg != '')
      echo rex_info($msg);

    $subpage = 'types';
  }
}

require dirname(__FILE__) .'/'.$subpage.'.inc.php';
require $REX['INCLUDE_PATH'] . '/layout/bottom.php';
