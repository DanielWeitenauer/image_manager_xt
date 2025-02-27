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

class rex_effect_filter_sepia extends rex_effect_abstract{

  function execute()
  {
    $img =& $this->image->getImage();

    if (!($t = imagecolorstotal($img)))
    {
      $t = 256;
      imagetruecolortopalette($img, true, $t);
    }
    $total = imagecolorstotal( $img );
    for ( $i = 0; $i < $total; $i++ ) {
      $index = imagecolorsforindex( $img, $i );
      $red = ( $index["red"] * 0.393 + $index["green"] * 0.769 + $index["blue"] * 0.189 );
      $green = ( $index["red"] * 0.349 + $index["green"] * 0.686 + $index["blue"] * 0.168 );
      $blue = ( $index["red"] * 0.272 + $index["green"] * 0.534 + $index["blue"] * 0.131 );
      if ($red > 255) { $red = 255; }
      if ($green > 255) { $green = 255; }
      if ($blue > 255) { $blue = 255; }
      imagecolorset( $img, $i, $red, $green, $blue );
    }

  }

  function getParams()
  {
    global $REX,$I18N;

    return array(

    );

  }

}
