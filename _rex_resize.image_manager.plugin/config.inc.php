<?php
/**
* rex_resize Plugin for image_manager Addon
*
* @package redaxo4.3
* @version 0.1
* $Id$:
*/

// SETTINGS
////////////////////////////////////////////////////////////////////////////////
$REX['ADDON']['image_manager']['PLUGINS']['_rex_resize.imagemanager.plugin']['max_cachefiles'] = 5;

// PARAMS
////////////////////////////////////////////////////////////////////////////////
$rex_resize   = rex_get('rex_resize', 'string');

// MAIN
////////////////////////////////////////////////////////////////////////////////
if($rex_resize != '')
{
  require_once (dirname(__FILE__). '/classes/class.rex_resize_legacy.inc.php');

  rex_register_extension('IMAGE_MANAGER_INIT','rex_resize_init');
  rex_register_extension('IMAGE_MANAGER_FILTERSET','rex_resize_filterset');

  // CREATE VIRTUAL IMG_TYPE, SET IMG_FILE
  function rex_resize_init($params)
  {
    global $REX;

    $rex_resize   = rex_get('rex_resize', 'string');
    $rex_filter   = rex_get('rex_filter', 'array');
    $rex_img_type = rex_get('rex_img_type', 'string');

    $rr = new rex_resize_legacy($rex_resize, $rex_filter, $rex_img_type);

    $REX['ADDON']['image_manager']['PLUGINS']['_rex_resize.imagemanager.plugin']['effect_set'] = $rr->effect_set;

    $params['subject']['rex_img_file'] = $rr->img_file;
    $params['subject']['rex_img_type'] = $rr->img_type;
    $params['subject']['imagepath']    = $REX['HTDOCS_PATH'].'files/'.$rr->img_file;
    unset($rr);
    return $params['subject'];
  }

  // VIRTUAL FILTER SET
  function rex_resize_filterset($params)
  {
    global $REX;

    return $REX['ADDON']['image_manager']['PLUGINS']['_rex_resize.imagemanager.plugin']['effect_set'];
  }
}
