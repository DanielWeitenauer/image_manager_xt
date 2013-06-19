<?php
/**
 * rex_resize Plugin for image_manager Addon
 *
 * @author jdlx c/o http://rexdev.de/
 * @link https://github.com/jdlx/image_manager_xt
 *
 * @package redaxo 4.4.x/4.5.x
 * @version 1.4.1
 */

class rex_resize_legacy
{

  var $img_file;
  var $img_type;
  var $effect_set = array();

  /*public*/  function rex_resize_legacy($rex_resize, $rex_filter='',$rex_img_type='')
  {
    global $REX;

    $rex_resize = str_replace("/","",$rex_resize);
    preg_match('@([0-9]+)([awhcf])__(([0-9]+)h__)?((\-?[0-9]+)o__)?(.*)@', $rex_resize, $resize);

    $this->img_file                 = $resize[7];
    $this->img_type                 = 'RR_'.$resize[1].$resize[2].'_';
    if($resize[4]) $this->img_type .= $resize[4].'h_';
    if($resize[6]) $this->img_type .= $resize[6].'o_';


    // RESIZE TRANSLATION
    ////////////////////////////////////////////////////////////////////////////
    $size          = $resize[1];
    $mode          = $resize[2];
    $height        = $resize[4];
    $resize_params = false;
    $crop_params   = false;

    switch($mode)
    {
      case'a':
        $resize_params = array('width' => $size,'height' => $size,'style' => 'maximum','allow_enlarge' => 'not_enlarge');
        break;

      case'h':
        $resize_params = array('width' => 1,'height' => $size,'style' => 'minimum','allow_enlarge' => 'not_enlarge');
        break;

      case'w':
        if($height)
        {
          $resize_params = array('width' => $size,'height' => $height,'style' => 'exact','allow_enlarge' => 'not_enlarge');
        }
        else
        {
          $resize_params = array('width' => $size,'height' => 1,'style' => 'minimum','allow_enlarge' => 'not_enlarge');
        }
        break;

      case'f':
        if($height)
        {
          $resize_params = array('width' => $size,'height' => $height,'style' => 'maximum','allow_enlarge' => 'not_enlarge');
        }
        else
        {
          $resize_params = array('width' => $size,'height' => $size,'style' => 'maximum','allow_enlarge' => 'not_enlarge');
        }
        break;

      case'c':
        // CROP TRANSLATION
        ////////////////////////////////////////////////////////////////////////
        if(isset($resize[6]))
        {
          $offset = (int) $resize[6];

          $img = OOMedia::getMediaByFileName($this->img_file);
          $img_width = $img->_width;
          $img_height = $img->_height;

          $width_ratio = $img_width / $size;
          $height_ratio = $img_height / $height;

          if ($width_ratio > $height_ratio)
          {
            $crop_width    = (int) (($img_height * $size) / $height);
            $crop_height   = $img_height;
            $offset_width  = $offset;
            $offset_height = '';
            // class rex_effect_crop v0.9
            $position      = 'middle_center';
            // class rex_effect_crop v1.0
            $hpos          = 'center';
            $vpos          = 'middle';
          }
          else
          {
            $crop_width    = $img_width;
            $crop_height   = (int) (($img_width * $height) / $size);
            $offset_width  = '';
            $offset_height = $offset;
            // class rex_effect_crop v0.9
            $position      = 'middle_center';
            // class rex_effect_crop v1.0
            $hpos          = 'center';
            $vpos          = 'middle';
          }

          $crop_params   = array('width' => $crop_width,'height' => $crop_height,'offset_width' => $offset_width,'offset_height' => $offset_height,'position' => $position,'hpos'=>$hpos,'vpos'=>$vpos);
          $resize_params = array('width' => 1,'height' => $height,'style' => 'minimum','allow_enlarge' => 'not_enlarge');
        }
        else
        {
          $crop_params   = array('width' => $crop_width,'height' => $crop_height,'offset_width' => $offset,'offset_height' => '','position' => 'middle_center','hpos'=>'center','vpos'=>'middle');
          $resize_params = array('width' => $size,'height' => 1,'style' => 'minimum','allow_enlarge' => 'not_enlarge');
        }
        break;
    }

    if(is_array($crop_params))
    {
      $this->effect_set[] = array('effect' => 'crop','params' => $crop_params);
    }

    if(is_array($resize_params))
    {
      $this->effect_set[] = array('effect' => 'resize','params' => $resize_params);
    }


    // REX_FILTER TRANSLATION
    ////////////////////////////////////////////////////////////////////////////
    if(is_array($rex_filter))
    {
      foreach($rex_filter as $filter)
      {
        switch($filter)
        {
          case 'sharpen':
            $this->effect_set[] = array('effect' => 'filter_sharpen','params' => array('amount'=>80,'radius'=>0.5,'threshold'=>3));
            $this->img_type .= 'sharpen_';
            break;

          case 'blur':
            $this->effect_set[] = array('effect' => 'filter_blur','params'    => array('amount'=>80,'radius'=>8,'threshold'=>3));
            $this->img_type .= 'blur_';
            break;

          case 'sepia':
            $this->effect_set[] = array('effect' => 'filter_sepia','params'   => array());
            $this->img_type .= 'sepia_';
            break;

          case 'brand':
            $this->effect_set[] = array('effect' => 'insert_image','params'   => array('brandimage'=>'brand.png','hpos'=>'right','vpos'=>'bottom','padding_x'=>0,'padding_y'=>0));
            $this->img_type .= 'brand_';
            break;

          case 'grayscale':
          case 'greyscale':
            $this->effect_set[] = array('effect' => 'filter_greyscale','params' => array());
            $this->img_type .= 'greyscale_';
            break;
        }
      }
    }


    // MERGE VIRTUAL & REAL IMAGE_TYPE EFFECT/FILTER SETS
    ////////////////////////////////////////////////////////////////////////////
    if($rex_img_type!='')
    {
      $imagepath = $REX['HTDOCS_PATH'].'files/'.$this->img_file;
      $cachepath = $REX['INCLUDE_PATH'].'/generated/image_manager/';

      $tmp         = new rex_image($imagepath);
      $tmp_cacher  = new rex_image_cacher($cachepath);
      $tmp_manager = new rex_image_manager($tmp_cacher);
      $set = $tmp_manager->effectsFromType($rex_img_type);
      unset($tmp,$tmp_cacher,$tmp_manager);

      $this->effect_set =  array_merge_recursive($this->effect_set, $set);
      $this->img_type .= 'IM_'.$rex_img_type;
    }


    // DELETE OLDEST CACHE FILE IF MAX-CACHEFILES REACHED
    ////////////////////////////////////////////////////////////////////////////
    $cachepath = $REX['INCLUDE_PATH'].'/generated/image_manager/';
    $pattern = $cachepath.'image_manager__RR_*_'.$this->img_file;
    $glo = glob($pattern);
    if($REX['ADDON']['image_manager']['PLUGIN']['_rex_resize.image_manager.plugin']['max_cachefiles']<=count($glo))
    {
      $cachefile = '';
      $cachetime = -1;

      foreach($glo as $gl)
      {
        if ($cachetime == -1 || filectime($gl) < $cachetime)
        {
          $cachetime = filectime($gl);
          $cachefile = $gl;
        }
      }
      if ($cachefile != "") unlink ($cachefile);
    }

  }

}
