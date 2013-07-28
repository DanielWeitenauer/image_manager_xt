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

/**
 * Roundcorners - ImageManager Effect
 *
 * @package redaxo 4.3.x/4.4.x
 * @version 0.1
 * @link http://svn.rexdev.de/redmine/projects/image-manager-ep
 */

class rex_effect_roundcorners extends rex_effect_abstract
{

  function execute()
  {
    $gdimage      =& $this->image->getImage();
    $radius       = $this->params['radius'];
    $color        = $this->params['color'];
    $transparency = $this->params['transparency'];
    $conversion   = $this->params['conversion'];

    switch($conversion)
    {
      case 'JPG->PNG @ transparency 127':
        if($transparency==127)
        {
          $this->image->img['format'] = 'PNG';
        }
        break;
      case 'JPG->PNG':
        $this->image->img['format'] = 'PNG';
        break;
      default:
        //no conversion
    }

    $gdimage = $this->round_corners($gdimage,$radius,$color,$transparency);
  }

  function getParams()
  {
    global $REX;

    return array(
      array(
        'label'=> 'Radius',
        'name' => 'radius',
        'type' => 'int',
        'default' => 20,
      ),
      array(
        'label'=> 'Transparency',
        'name' => 'transparency',
        'type' => 'select',
        'options' => $this->transparency_vals(),
        'default' => 127,
      ),
      array(
        'label'=> 'Color',
        'name' => 'color',
        'type' => 'string',
        'default' => 'ffffff',
      ),
      array(
        'label'=> 'Format Conversion',
        'name' => 'conversion',
        'type' => 'select',
        'options' => array(
                     'none',
                     'JPG->PNG @ transparency 127',
                     'JPG->PNG'),
        'default' => 0,
      )
     );
  }


  function transparency_vals()
  {
    $sel = array();
    for($i=0;$i<128;++$i)
    {
      $sel[$i]=$i;
    }
    return $sel;
  }

  /**
   * round_corners
   *
   * Round the corners of an image. Transparency and anti-aliasing are supported.
   *
   * @version 0.1
   * @author Contributors at eXorithm
   * @link http://www.exorithm.com/algorithm/view/round_corners Listing at eXorithm
   * @link http://www.exorithm.com/algorithm/history/round_corners History at eXorithm
   * @license http://www.exorithm.com/home/show/license
   *
   * @param resource $image (GD image)
   * @param number $radius Radius of the rounded corners.
   * @param string $color (hex color code) Color of the background.
   * @param mixed $transparency Level of transparency. 0 is no transparency, 127 is full transparency.
   * @return resource GD image
   */
  function round_corners($image=null,$radius=20,$color='ffffff',$transparency='127')
  {
    $width = imagesx($image);
    $height = imagesy($image);

    $image2 = imagecreatetruecolor($width, $height);
    imagecopy($image2, $image, 0, 0, 0, 0, $width, $height);

    imagesavealpha($image2, true);
    imagealphablending($image2, false);

    $full_color = $this->allocate_color($image2, $color, $transparency);

    // loop 4 times, for each corner...
    for ($left=0;$left<=1;$left++) {
      for ($top=0;$top<=1;$top++) {

        $start_x = $left * ($width-$radius);
        $start_y = $top * ($height-$radius);
        $end_x = $start_x+$radius;
        $end_y = $start_y+$radius;

        $radius_origin_x = $left * ($start_x-1) + (!$left) * $end_x;
        $radius_origin_y = $top * ($start_y-1) + (!$top) * $end_y;

        for ($x=$start_x;$x<$end_x;$x++) {
          for ($y=$start_y;$y<$end_y;$y++) {
            $dist = sqrt(pow($x-$radius_origin_x,2)+pow($y-$radius_origin_y,2));

            if ($dist>($radius+1)) {
              imagesetpixel($image2, $x, $y, $full_color);
            } else {
              if ($dist>$radius) {
                $pct = 1-($dist-$radius);
                $color2 = $this->antialias_pixel($image2, $x, $y, $full_color, $pct);
                imagesetpixel($image2, $x, $y, $color2);
              }
            }
          }
        }

      }
    }

    return $image2;
  }


  /**
   * allocate_color
   *
   * Helper function to allocate a color to an image. Color should be a 6-character hex string.
   *
   * @version 0.2
   * @author Contributors at eXorithm
   * @link http://www.exorithm.com/algorithm/view/allocate_color Listing at eXorithm
   * @link http://www.exorithm.com/algorithm/history/allocate_color History at eXorithm
   * @license http://www.exorithm.com/home/show/license
   *
   * @param resource $image (GD image) The image that will have the color allocated to it.
   * @param string $color (hex color code) The color to allocate to the image.
   * @param mixed $transparency The level of transparency from 0 to 127.
   * @return mixed
   */
  function allocate_color($image=null,$color='268597',$transparency='0')
  {
    if (preg_match('/[0-9ABCDEF]{6}/i', $color)==0) {
      throw new Exception("Invalid color code.");
    }
    if ($transparency<0 || $transparency>127) {
      throw new Exception("Invalid transparency.");
    }

    $r  = hexdec(substr($color, 0, 2));
    $g  = hexdec(substr($color, 2, 2));
    $b  = hexdec(substr($color, 4, 2));
    if ($transparency>127) $transparency = 127;

    if ($transparency<=0)
      return imagecolorallocate($image, $r, $g, $b);
    else
      return imagecolorallocatealpha($image, $r, $g, $b, $transparency);
  }


  /**
   * antialias_pixel
   *
   * Helper function to apply a certain weight of a certain color to a pixel in an image. The index of the resulting color is returned.
   *
   * @version 0.1
   * @author Contributors at eXorithm
   * @link http://www.exorithm.com/algorithm/view/antialias_pixel Listing at eXorithm
   * @link http://www.exorithm.com/algorithm/history/antialias_pixel History at eXorithm
   * @license http://www.exorithm.com/home/show/license
   *
   * @param resource $image (GD image) The image containing the pixel.
   * @param number $x X-axis position of the pixel.
   * @param number $y Y-axis position of the pixel.
   * @param number $color The index of the color to be applied to the pixel.
   * @param number $weight Should be between 0 and 1,  higher being more of the original pixel color, and 0.5 being an even mixture.
   * @return mixed
   */
  function antialias_pixel($image=null,$x=0,$y=0,$color=0,$weight=0.5)
  {
    $c = imagecolorsforindex($image, $color);
    $r1 = $c['red'];
    $g1 = $c['green'];
    $b1 = $c['blue'];
    $t1 = $c['alpha'];

    $color2 = imagecolorat($image, $x, $y);
    $c = imagecolorsforindex($image, $color2);
    $r2 = $c['red'];
    $g2 = $c['green'];
    $b2 = $c['blue'];
    $t2 = $c['alpha'];

    $cweight = $weight+($t1/127)*(1-$weight)-($t2/127)*(1-$weight);

    $r = round($r2*$cweight + $r1*(1-$cweight));
    $g = round($g2*$cweight + $g1*(1-$cweight));
    $b = round($b2*$cweight + $b1*(1-$cweight));

    $t = round($t2*$weight + $t1*(1-$weight));

    return imagecolorallocatealpha($image, $r, $g, $b, $t);
  }

}
