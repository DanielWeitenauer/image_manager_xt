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
 * @version 1.4.1
 */

class rex_image_manager
{
  var $image_cacher;

  function rex_image_manager(/*rex_image_cacher*/ $image_cacher)
  {
    if(!rex_image_cacher::isValid($image_cacher))
    {
      trigger_error('Given cache is not a valid rex_image_cacher', E_USER_ERROR);
    }
    $this->image_cacher = $image_cacher;
  }

  function applyEffects(/*rex_image*/ $image, $type)
  {
    global $REX;

    if(!rex_image::isValid($image))
    {
      trigger_error('Given image is not a valid rex_image', E_USER_ERROR);
    }


    if(!$this->image_cacher->isCached($image, $type))
    {
      $set = $this->effectsFromType($type);

      // REGISTER EXTENSION POINT
      $set   = rex_register_extension_point('IMAGE_MANAGER_FILTERSET',$set,array('rex_image_type'=>$type));

      $image->prepare();

      // execute effects on image
      foreach($set as $effect_params)
      {
        $effect_class = 'rex_effect_'.$effect_params['effect'];
        require_once dirname(__FILE__).'/effects/class.'.$effect_class.'.inc.php';

        $effect = new $effect_class;
        $effect->setImage($image);
        $effect->setParams($effect_params['params']);
        $effect->execute();
      }
    }

    return $image;
  }

  /*public*/ function effectsFromType($type)
  {
    global $REX;

    $qry = '
      SELECT e.*
      FROM '. $REX['TABLE_PREFIX'].'679_types t, '. $REX['TABLE_PREFIX'].'679_type_effects e
      WHERE e.type_id = t.id AND t.name="'. $type .'" order by e.prior';

    $sql = rex_sql::factory();
//    $sql->debugsql = true;
    $sql->setQuery($qry);

    $effects = array();
    while($sql->hasNext())
    {
      $effname = $sql->getValue('effect');
      $params = unserialize($sql->getValue('parameters'));
      $effparams = array();

      // extract parameter out of array
      if(isset($params['rex_effect_'. $effname]))
      {
        foreach($params['rex_effect_'. $effname] as $name => $value)
        {
          $effparams[str_replace('rex_effect_'. $effname .'_', '', $name)] = $value;
          unset($effparams[$name]);
        }
      }

      $effect = array(
        'effect' => $effname,
        'params' => $effparams,
      );

      $effects[] = $effect;
      $sql->next();
    }
    return $effects;
  }

  /**
   * Returns a rex_image instance representing the image $rex_img_file
   * in respect to $rex_img_type.
   * If the result is not cached, the cache will be created.
   */
  /*public static*/ function getImageCache($rex_img_file, $rex_img_type)
  {
    global $REX;

    $imagepath = $REX['HTDOCS_PATH'].'files/'.$rex_img_file;
    $cachepath = $REX['INCLUDE_PATH'].'/generated/image_manager/';

    $image         = new rex_image($imagepath);
    $image_cacher  = new rex_image_cacher($cachepath);

    // create image with given image_type if needed
    if(!$image_cacher->isCached($image, $rex_img_type))
    {
      $image_manager = new rex_image_manager($image_cacher);
      $image_manager->applyEffects($image, $rex_img_type);
      $image->save($image_cacher->getCacheFile($image, $rex_img_type));
    }

    return $image_cacher->getCachedImage($rex_img_file, $rex_img_type);
  }

  /*public*/ function sendImage(/*rex_image*/ $image, $type)
  {
    $this->image_cacher->sendImage($image, $type);
  }


  static function mkdir($dir)
  {
    global $REX;

    if(!is_dir($dir)) {
      if(!mkdir($dir, $REX['DIRPERM'], true)) {
        trigger_error(__CLASS__.'::'.__FUNCTION__.': could not create dir '.$dir.' with mode '.$REX['DIRPERM'], E_USER_WARNING);
        return false;
      }
    }
    return true;
  }


  static function symlink($target, $symlink)
  {
    if(!symlink($target, $symlink)) {
      trigger_error(__CLASS__.'::'.__FUNCTION__.': could not symlink '.$symlink.' to '.$target , E_USER_WARNING);
      return false;
    }
    return true;
  }


} # end class
