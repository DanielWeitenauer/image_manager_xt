<?php
/**
* Custom Folder Plugin for image_manager Addon
*
* @package redaxo4.3
* @version 0.1
* $Id$:
*/

// MAIN
////////////////////////////////////////////////////////////////////////////////
if(isset($_GET['rex_img_dir']))
{
  rex_register_extension('IMAGE_MANAGER_INIT','rex_img_dir_init');

  function rex_img_dir_init($params)
  {
    global $REX;

    $rex_img_dir = rex_get('rex_img_dir','string');
    
    // SANITIZE USER INPUT
    $rex_img_dir = preg_replace('/[^a-zA-Z0-9\/-_]/','',$rex_img_dir);
    $rex_img_dir = trim($rex_img_dir,'/');
    $rex_img_dir = array_diff(explode('/',$rex_img_dir),array('redaxo','..'));
    $rex_img_dir = implode('/',$rex_img_dir);

    $imagepath = $params['subject']['imagepath'];

    $params['subject']['imagepath'] = str_replace('files',$rex_img_dir,$imagepath);

    return $params['subject'];
  }

}
