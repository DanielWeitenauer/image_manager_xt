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


class rex_effect_filter_greyscale extends rex_effect_abstract
{
    function execute()
    {
        $gdimage = & $this->image->getImage();
        $w = $this->image->getWidth();
        $h = $this->image->getHeight();

        $src_x = ceil($w);
        $src_y = ceil($h);
        $dst_x = $src_x;
        $dst_y = $src_y;
        $dst_im = ImageCreateTrueColor($dst_x, $dst_y);

        ImageCopyResampled( $dst_im, $gdimage, 0, 0, 0, 0, $dst_x, $dst_y,
            $src_x, $src_y );

        for ($c = 0; $c < 256; $c++) {
            $palette[$c] = imagecolorallocate($dst_im, $c, $c, $c);
        }

        for ($y = 0; $y < $src_y; $y++) {
            for ($x = 0; $x < $src_x; $x++) {
                $rgb = imagecolorat($dst_im, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $gs = $r * 0.299 + $g * 0.587 + $b * 0.114;
                imagesetpixel($dst_im, $x, $y, $palette[$gs]);
            }
        }

        $gdimage = $dst_im;
    }

    function getParams()
    {
        global $REX, $I18N;
        return array();
    }
}
