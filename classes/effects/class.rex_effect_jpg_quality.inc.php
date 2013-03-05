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

/**
 * JPG image quality - ImageManager Effect
 *
 * @package redaxo 4.3.x/4.4.x
 * @version 0.1
 * @link http://rexdev.de/addons/image_manager-ep.html
 * @link http://svn.rexdev.de/redmine/projects/image-manager-ep
 */

class rex_effect_jpg_quality extends rex_effect_abstract
{

  function execute()
  {
    global $REX;
    $this->image->img['quality'] = $this->params['quality'];

  }

  function getParams()
  {
    global $REX,$I18N;

    return array(
      array(
        'label' => 'JPG quality',
        'name' => 'quality',
        'type'  => 'int',
        'default' => $REX['ADDON']['image_manager']['jpg_quality']
      )
    );
  }

}
