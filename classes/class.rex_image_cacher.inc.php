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

class rex_image_cacher
{
  var $cache_path;


  function rex_image_cacher($cache_path)
  {
    global $REX;

    $this->cache_path = $cache_path;
  }

  /*public*/ function isCached(/*rex_image*/ $image, $cacheParams)
  {
    if(!rex_image::isValid($image))
    {
      trigger_error('Given image is not a valid rex_image', E_USER_ERROR);
    }

    $cache_file = $this->getCacheFile($image, $cacheParams);

    // ----- check for cache file
    if (file_exists($cache_file))
    {
      // time of cache
      $cachetime = filectime($cache_file);
      $imagepath = $image->getFilePath();

      // file exists?
      if (file_exists($imagepath))
      {
        $filetime = filectime($imagepath);
      }
      else
      {
        $image->sendError('Missing original file for cache-validation!');
        exit();
      }
      // cache is newer?
      if ($cachetime > $filetime)
      {
        return true;
      }
    }

    return false;
  }


  /**
   * Returns a rex_image instance representing the cached image.
   * This Method requires a already cached file.
   *
   * Use rex_image_manager::getImageCache() if the cache should be created if needed.
   */
  /*public*/ function getCachedImage($filename, $cacheParams)
  {
    $cacheFile = $this->_getCacheFile($filename, $cacheParams);
    $rex_image = new rex_image($cacheFile);
    $rex_image->prepare();
    return $rex_image;
  }


  /*public*/ function getCacheFile(/*rex_image*/ $image, $cacheParams)
  {
    return $this->_getCacheFile($image->getFileName(), $cacheParams);
  }

  /*protected*/ function _getCacheFile($filename, $cacheParams)
  {
    if(!is_string($cacheParams))
    {
      $cacheParams = md5(serialize($cacheParams));
    }
    $cachefile = $this->cache_path .'image_manager__'. $cacheParams .'_'. $filename;
    $cachefile = rex_register_extension_point('IMAGE_MANAGER_CACHEFILE', $cachefile, array('cacheParams' => $cacheParams));
    return $cachefile;
  }


  /*public*/ function sendImage(/*rex_image*/ $image, $cacheParams, $lastModified = null)
  {
    global $REX;

    if(!rex_image::isValid($image))
    {
      trigger_error('Given image is not a valid rex_image', E_USER_ERROR);
    }

    $cacheFile = $this->getCacheFile($image, $cacheParams);

    // save image to file
    if(!$this->isCached($image, $cacheParams))
    {
      $image->prepare();
      $image->save($cacheFile);
    }

    $tmp = $REX['USE_GZIP'];
    $REX['USE_GZIP'] = 'false';

    $format = $image->getFormat() == 'JPG' ? 'jpeg' : strtolower($image->getFormat());
    $format = 'image/'.$format;
    $scope  = 'frontend';

    $IMG = rex_register_extension_point(
      'IMAGE_MANAGER_SEND_IMAGE',
      array(
        'image'        => $image,
        'cacheparams'  => $cacheParams,
        'lastmodified' => $lastModified,
        'cachefile'    => $cacheFile,
        'format'       => $format,
        'scope'        => $scope,
        )
      );

    $IMG['image']->sendHeader();
    rex_send_file($IMG['cachefile'], $IMG['format'], $IMG['scope']);

    $REX['USE_GZIP'] = $tmp;
  }


  /*
   * Static Method: Returns True, if the given cacher is a valid rex_image_cacher
   */
  /*public static*/ function isValid($cacher)
  {
    return is_object($cacher) && is_a($cacher, 'rex_image_cacher');
  }


  /**
   * deletes all cache files for the given filename.
   * if not filename is provided all cache files are cleared.
   *
   * Returns the number of cachefiles which have been removed.
   *
   * @param $filename
   */
  function deleteCache($filename = null, $cacheParams = null)
  {
    global $REX;

    if(!$filename)
    {
      $filename = '*';
    }

    if(!$cacheParams)
    {
      $cacheParams = '*';
    }

    $file = 'image_manager__'. $cacheParams . '_'. $filename;

    $needles = array();
    $needles[] = $REX['INCLUDE_PATH'] . '/generated/image_manager/'.$file;
    $needles[] = $REX['HTDOCS_PATH'] . 'files/'.$file;


    $needles = rex_register_extension_point('IMAGE_MANAGER_CLEAR_CACHE', $needles, array('filename' => $filename, 'cacheparams' => $cacheParams));

    $counter = 0;
    foreach($needles as $needle)
    {
      $glob = glob($needle);
      if($glob)
      {
        foreach ($glob as $file)
        {
          if(unlink($file))
          {
            $counter++;
          }
        }
      }
    }

    return $counter;
  }
}
