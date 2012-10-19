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
 * @version 1.2.1
 */

function rex_imanager_supportedEffects()
{
  global $REX;

  $dirs = $REX['ADDON']['image_manager']['classpaths']['effects'];

  $effects = array();
  foreach($dirs as $dir)
  {
    $files = glob($dir . 'class.rex_effect_*.inc.php');
    if($files)
    {
      foreach($files as $file)
      {
        $effects[rex_imanager_effectClass($file)] = $file;
      }
    }
  }
  return $effects;
}

function rex_imanager_supportedEffectNames()
{
  $effectNames = array();
  foreach(rex_imanager_supportedEffects() as $effectClass => $effectFile)
  {
    $effectNames[] = rex_imanager_effectName($effectFile);
  }
  return $effectNames;
}

function rex_imanager_effectName($effectFile)
{
  return str_replace(
      array('class.rex_effect_', '.inc.php'),
      '',
      basename($effectFile)
    );
}

function rex_imanager_effectClass($effectFile)
{
  return str_replace(
      array('class.', '.inc.php'),
      '',
      basename($effectFile)
    );
}

function rex_imanager_deleteCacheByType($type_id)
{
  global $REX;

  $qry = 'SELECT * FROM '. $REX['TABLE_PREFIX'].'679_types' . ' WHERE id='. $type_id;
  $sql = rex_sql::factory();
//  $sql->debugsql = true;
  $sql->setQuery($qry);

  $counter = 0;
  while($sql->hasNext())
  {
    $counter += rex_image_cacher::deleteCache(null, $sql->getValue('name'));
    $sql->next();
  }

  return $counter;
}
