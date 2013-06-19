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

function rex_imanager_handle_form_control_fields($params)
{
  $controlFields = $params['subject'];
  $form = $params['form'];
  $sql  = $form->getSql();

  // remove delete button on internal types (status == 1)
  if($sql->getRows() > 0 && $sql->hasValue('status') && $sql->getValue('status') == 1)
  {
    $controlFields['delete'] = '';
  }
  return $controlFields;
}
