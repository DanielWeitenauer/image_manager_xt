<?php
/**
* Referrer Blocker Plugin for image_manager Addon
*
* @package redaxo4.3
* @version 0.1
* $Id$:
*/

// MAIN
////////////////////////////////////////////////////////////////////////////////
if(isset($_SERVER['HTTP_REFERER']) && isset($_GET['rex_img_file']) && isset($_GET['rex_img_type']))
{
  $referrer = parse_url($_SERVER['HTTP_REFERER']);
  if($referrer['host']!=$_SERVER['HTTP_HOST'])
  {
    rex_register_extension('IMAGE_MANAGER_INIT'     ,'referrer_block_init');
    rex_register_extension('IMAGE_MANAGER_FILTERSET','referrer_block_filterset');

    // CREATE VIRTUAL IMG_TYPE, SET IMG_FILE
    function referrer_block_init($params)
    {
      global $REX;
      
      $rex_image_type = $params['subject']['rex_img_type'];

      $REX['ADDON']['image_manager']['PLUGINS']['referrer_block.imagemanager.plugin']['params'] = $params['subject'];

      $params['subject']['rex_img_type'] = 'REFBLOCK'.$rex_image_type;

      return $params['subject'];
    }

    // VIRTUAL FILTER SET
    function referrer_block_filterset()
    {
      global $REX;
      $params= $REX['ADDON']['image_manager']['PLUGINS']['referrer_block.imagemanager.plugin']['params'];

      // ORIGINAL IMG_TYPE FILTERSET
      $imagepath = $params['imagepath'];
      $cachepath = $params['cachepath'];
      $tmp         = new rex_image($imagepath);
      $tmp_cacher  = new rex_image_cacher($cachepath);
      $tmp_manager = new rex_image_manager($tmp_cacher);
      $set = $tmp_manager->effectsFromType($params['rex_img_type']);
      unset($tmp,$tmp_cacher,$tmp_manager);

      $my_set   = array(); 
      $my_set[] = array('effect' => 'filter_greyscale','params' => array());
      $my_set[] = array('effect' => 'filter_blur','params'    => array('amount'=>80,'radius'=>8,'threshold'=>3));
      $set = array_merge_recursive($set,$my_set);
      return $set;
    }
  }
}
