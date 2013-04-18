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
 * @version 1.3.0
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
      $set   = rex_register_extension_point('IMAGE_MANAGER_FILTERSET', $set, array('rex_image_type' => $type, 'img' => $image));

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
    return image_manager_init(array(),true,$rex_img_file,$rex_img_type);
  }

  /*public*/ function sendImage(/*rex_image*/ $image, $type)
  {
    $this->image_cacher->sendImage($image, $type);
  }
}
