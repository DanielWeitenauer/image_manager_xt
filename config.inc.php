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
 * @version 1.2.6
 */

$mypage = 'image_manager';

/* Addon Parameter */
$REX['ADDON']['rxid'][$mypage] = '679';
$REX['ADDON']['name'][$mypage] = 'Image Manager Pro';
$REX['ADDON']['perm'][$mypage] = 'image_manager[]';
$REX['ADDON']['version'][$mypage] = '1.2.6';
$REX['ADDON']['author'][$mypage] = 'Markus Staab, Jan Kristinus, jdlx';
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';
$REX['PERM'][] = 'image_manager[]';


// --- DYN
$REX['ADDON']['image_manager']['max_resizekb'] = 5000;
$REX['ADDON']['image_manager']['max_resizepixel'] = 5000;
$REX['ADDON']['image_manager']['jpg_quality'] = 85;
// --- /DYN

$REX['ADDON']['image_manager']['classpaths']['effects'] = array();
$REX['ADDON']['image_manager']['classpaths']['effects'][] = dirname(__FILE__). '/classes/effects/';

require_once (dirname(__FILE__). '/classes/class.rex_image.inc.php');
require_once (dirname(__FILE__). '/classes/class.rex_image_cacher.inc.php');
require_once (dirname(__FILE__). '/classes/class.rex_image_manager.inc.php');
require_once (dirname(__FILE__). '/classes/class.rex_effect_abstract.inc.php');

//--- handle image request
$rex_img_file = rex_get('rex_img_file', 'string');
$rex_img_type = rex_get('rex_img_type', 'string');



// RUN ON EP ADDONS_INCLUDED
////////////////////////////////////////////////////////////////////////////////
if(!$REX['SETUP']){
  rex_register_extension('ADDONS_INCLUDED','image_manager_init');
}

if(!function_exists('image_manager_init')){
  function image_manager_init()
  {
    global $REX, $rex_img_file, $rex_img_type;

    $imagepath = $REX['HTDOCS_PATH'].'files/'.$rex_img_file;
    $cachepath = $REX['INCLUDE_PATH'].'/generated/files/';

    // REGISTER EXTENSION POINT
    $subject = array('rex_img_type' => $rex_img_type,
                     'rex_img_file' => $rex_img_file,
                     'imagepath'    => $imagepath,
                     'cachepath'    => $cachepath);
    $subject   = rex_register_extension_point('IMAGE_MANAGER_INIT',$subject);

    if(isset($subject['rex_img_file'])) $rex_img_file = $subject['rex_img_file'];
    if(isset($subject['rex_img_type'])) $rex_img_type = $subject['rex_img_type'];
    if(isset($subject['imagepath']))    $imagepath    = $subject['imagepath'];
    if(isset($subject['cachepath']))    $cachepath    = $subject['cachepath'];

    if($rex_img_file != '' && $rex_img_type != '')
    {
    $image         = new rex_image($imagepath);
    $image_cacher  = new rex_image_cacher($cachepath);
    $image_manager = new rex_image_manager($image_cacher);

    $image = $image_manager->applyEffects($image, $rex_img_type);
    $image_manager->sendImage($image, $rex_img_type);
    exit();
    }
  }
}


if($REX['REDAXO'])
{
  // delete thumbnails on mediapool changes
  if(!function_exists('rex_image_manager_ep_mediaupdated'))
  {
    rex_register_extension('MEDIA_UPDATED', 'rex_image_manager_ep_mediaupdated');
    function rex_image_manager_ep_mediaupdated($params){
      rex_image_cacher::deleteCache($params["filename"]);
    }
  }

  // handle backend pages
  $I18N->appendFile($REX['INCLUDE_PATH'].'/addons/'.$mypage.'/lang/');

  $confPage = new rex_be_page($I18N->msg('imanager_subpage_types'), array(
      'page'=>'image_manager',
      'subpage'=>''
    )
  );
  $confPage->setHref('index.php?page=image_manager');

  $settingsPage = new rex_be_page($I18N->msg('imanager_subpage_config'), array(
      'page'=>'image_manager',
      'subpage'=>'settings'
    )
  );
  $settingsPage->setHref('index.php?page=image_manager&subpage=settings');

  $ccPage = new rex_be_page($I18N->msg('imanager_subpage_clear_cache'), array(
      'page'=>'image_manager',
      'subpage'=>'clear_cache'
    )
  );
  $ccPage->setHref('index.php?page=image_manager&subpage=clear_cache');
  $ccPage->setLinkAttr('onclick', 'return confirm(\''.$I18N->msg('imanager_type_cache_delete').' ?\')');

  $REX['ADDON']['pages'][$mypage] = array (
    $confPage, $settingsPage, $ccPage
  );

  if(file_exists(dirname(__FILE__).'/plugins')!=false && is_dir(dirname(__FILE__).'/plugins')!=false)
  {
    $pluginsPage = new rex_be_page('Plugins', array(
        'page'=>'image_manager',
        'subpage'=>'plugins'
      )
    );
    $pluginsPage->setHref('index.php?page=image_manager&subpage=plugins');
    $REX['ADDON']['pages'][$mypage][] = $pluginsPage;
  }

  $descPage = new rex_be_page($I18N->msg('imanager_subpage_desc'), array(
      'page'=>'image_manager',
      'subpage'=>'overview'
    )
  );
  $descPage->setHref('index.php?page=image_manager&subpage=overview');

  $REX['ADDON']['pages'][$mypage][] = $descPage;
}
