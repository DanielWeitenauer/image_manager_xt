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

class rex_effect_abstract
{
  var $image = array(); // rex_image
  var $params = array(); // effekt parameter

  function setImage(&$image)
  {
    if(!rex_image::isValid($image))
    {
      trigger_error('Given image is not a valid rex_image_abstract', E_USER_ERROR);
    }
    $this->image = &$image;
  }

  function setParams($params)
  {
    $this->params = $params;
  }

  function execute()
  {
    // exectute effect on $this->img
  }

  function getParams()
  {
    // returns an array of parameters which are required for the effect
  }
}
