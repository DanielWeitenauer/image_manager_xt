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
 * Image interlace Option - ImageManager Effect
 *
 * @package redaxo 4.3.x/4.4.x
 * @version 0.1
 * @link http://rexdev.de/addons/image_manager-ep.html
 * @link http://svn.rexdev.de/redmine/projects/image-manager-ep
 */

class rex_effect_img_interlace extends rex_effect_abstract
{

  function execute()
  {
    global $REX;

    switch($this->image->img['format'])
    {
      case 'JPG':
      case 'JPEG':
        if($this->params['jpg_interlace']=='on')
        {
          $this->image->img['interlace'] = true;
        }
        break;

      case 'PNG':
        if($this->params['png_interlace']=='on')
        {
          $this->image->img['interlace'] = true;
        }
        break;

      case 'GIF':
        if($this->params['png_interlace']=='on')
        {
          $this->image->img['interlace'] = true;
        }
        break;

      default:
        //$this->image->img['interlace'] = false;
    }
  }

  function getParams()
  {
    global $REX;

    return array(
      array(
        'label' => 'JPG interlace',
        'name' => 'jpg_interlace',
        'type'  => 'select',
        'options' => array(
                     'on',
                     'off'),
        'default' => 'off'
      ),
      array(
        'label' => 'PNG interlace',
        'name' => 'png_interlace',
        'type'  => 'select',
        'options' => array(
                     'on',
                     'off'),
        'default' => 'off'
      ),
      array(
        'label' => 'GIF interlace',
        'name' => 'gif_interlace',
        'type'  => 'select',
        'options' => array(
                     'on',
                     'off'),
        'default' => 'off'
      )
    );
  }

}
