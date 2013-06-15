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


$Basedir = dirname(__FILE__);

$type_id = rex_request('type_id','int');
$func = rex_request('func','string');

$info = '';
$warning = '';

//-------------- delete cache on type_name change or type deletion
if((rex_post('func') == 'edit' || $func == 'delete')
   && $type_id > 0)
{
  $counter = rex_imanager_deleteCacheByType($type_id);
//  $info = $I18N->msg('imanager_cache_files_removed', $counter);
}


// DELETE IMAGE TYPE & ASSOCIATED EFFECTS
////////////////////////////////////////////////////////////////////////////////
if($func == 'delete' && $type_id > 0)
{
  $sql = rex_sql::factory();
  $sql->setTable($REX['TABLE_PREFIX'].'679_types');
  $sql->setWhere('id='. $type_id . ' LIMIT 1');

  if($sql->delete())
  {
    $sql->setTable($REX['TABLE_PREFIX'].'679_type_effects');
    $sql->setWhere('type_id='. $type_id);
    if($sql->delete())
    {
      $info = $I18N->msg('imanager_type_deleted');
    }
    else
    {
      $warning = $sql->getErrro();
    }
  }
  else
  {
    $warning = $sql->getErrro();
  }
  $func = '';
}

//-------------- delete cache by type-id
if($func == 'delete_cache' && $type_id > 0)
{
  $counter = rex_imanager_deleteCacheByType($type_id);
  $info = $I18N->msg('imanager_cache_files_removed', $counter);

  $func = '';
}


// DUPLICATE IMAGE TYPE
////////////////////////////////////////////////////////////////////////////////
if($func == 'duplicate' && $type_id > 0)
{
  $db       = rex_sql::factory();
  $type_qry = 'SELECT * FROM '.$REX['TABLE_PREFIX'].'679_types WHERE `id`='.$type_id;
  $db->setQuery($type_qry);
  if($db->getRows()==1)
  {
    // GET IMG_TYPE DATA
    ////////////////////////////////////////////////////////////////////////////
    $type        = array_shift($db->getArray($type_qry));
    $effects     = $db->getArray('SELECT * FROM '.$REX['TABLE_PREFIX'].'679_type_effects
                                  WHERE `type_id`='.$type['id']);

    // INSERT THE COPY
    ////////////////////////////////////////////////////////////////////////////
    if($db->setQuery('INSERT INTO '.$REX['TABLE_PREFIX'].'679_types VALUES (\'\',0,\''.$type['name'].'_copy\',\''.$type['description'].'\');'))
    {
      $insert_id = $db->getLastId();
      foreach($effects as $e)
      {
        if(!$db->setQuery('INSERT INTO '.$REX['TABLE_PREFIX'].'679_type_effects VALUES (\'\','.$insert_id.',\''.$e['effect'].'\',\''.$e['parameters'].'\',\''.$e['prior'].'\',\''.time().'\',\''.$REX['USER']->getValue('login').'\',\''.time().'\',\''.$REX['USER']->getValue('login').'\');'))
        {
          $warning = $I18N->msg('imanager_type_effect_insert_failed');
        }
      }
      if($warning=='')
      {
        $info = $I18N->msg('imanager_type_duplicated');
      }
    }
  }
  else
  {
    $warning = $I18N->msg('imanager_type_not_found');
  }

  $func = '';
}


//-------------- output messages
if ($info != '')
  echo rex_info($info);

if ($warning != '')
  echo rex_warning($warning);

echo '<div class="rex-addon-output-v2">';
if ($func == '')
{
  $db = rex_sql::factory();
  $effects_arr = array();
  foreach($db->getArray('SELECT * FROM '.$REX['TABLE_PREFIX'].'679_type_effects;') as $k => $v) {
    $effects_arr[$v['type_id']][] = '<a href="index.php?page=image_manager&subpage=effects&func=edit&type_id='.$v['type_id'].'&effect_id='.$v['id'].'">'.$v['effect'].'</a>';
  }


  // Nach Status sortieren, damit Systemtypen immer zuletzt stehen
  // (werden am seltesten bearbeitet)
  $query = 'SELECT * FROM '.$REX['TABLE_PREFIX'].'679_types ORDER BY status';

  $list = rex_list::factory($query);
  $list->setNoRowsMessage($I18N->msg('imanager_type_no_types'));
  $list->setCaption($I18N->msg('imanager_type_caption'));
  $list->addTableAttribute('summary', $I18N->msg('imanager_type_summary'));
  $list->addTableColumnGroup(array(40, 100, '*', 70, 70));

  $list->removeColumn('id');
  $list->removeColumn('status');
  #$list->removeColumn('description');
  $list->setColumnLabel('name',$I18N->msg('imanager_type_name'));
  $list->setColumnParams('name', array('subpage' => 'effects', 'type_id' => '###id###'));
  $list->setColumnLabel('description',$I18N->msg('imanager_type_description'));
  $list->setColumnSortable('name', $direction = 'asc');

  // icon column
  $thIcon = '<a class="rex-i-element rex-i-generic-add" href="'. $list->getUrl(array('func' => 'add')) .'"><span class="rex-i-element-text">'. $I18N->msg('imanager_type_create') .'</span></a>';
  $tdIcon = '<span class="rex-i-element rex-i-list"><span class="rex-i-element-text">###name###</span></span>';
  $list->addColumn($thIcon, $tdIcon, 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
  $list->setColumnParams($thIcon, array('func' => 'edit', 'type_id' => '###id###'));


  $list->addColumn('effects', 'effects', 4, array('<th>'.$I18N->msg('imanager_effects').'</th>','<td>###VALUE###</td>'));
  $list->setColumnLabel('effects',$I18N->msg('imanager_effects'));
  $list->setColumnSortable('effects', $direction = 'asc');
  $list->setColumnParams('effects', array('type_id' => '###id###', 'func' => 'effects'));
  $list->setColumnFormat('effects', 'custom',
    function($params) use($effects_arr)
    {
      $list = $params["list"];
      return implode(' | ', $effects_arr[$list->getValue("id")] );
    }
  );

  // functions column spans 2 data-columns
  $funcs = $I18N->msg('imanager_type_functions');
  $list->addColumn($funcs, $I18N->msg('imanager_clear'), -1, array('<th>###VALUE###</th>','<td>###VALUE###</td>'));
  $list->setColumnLabel($funcs,$I18N->msg('imanager_cache'));
  $list->setColumnParams($funcs, array('type_id' => '###id###', 'func' => 'delete_cache'));
  $list->addLinkAttribute($funcs, 'onclick', 'return confirm(\''.$I18N->msg('imanager_type_cache_delete').' ?\')');

  // remove delete link on internal types (status == 1)
  $deleteType = 'deleteType';
  $list->addColumn($deleteType, '', -1, array('<th>###VALUE###</th>','<td>###VALUE###</td>'));
  $list->setColumnLabel($deleteType,$I18N->msg('imanager_image_type'));
  $list->setColumnParams($deleteType, array('type_id' => '###id###', 'func' => 'delete'));
  $list->addLinkAttribute($deleteType, 'onclick', 'return confirm(\''.$I18N->msg('delete').' ?\')');
  $list->setColumnFormat($deleteType, 'custom',
    create_function(
      '$params',
      'global $REX;
       $list = $params["list"];
       if($list->getValue("status") == 1)
       {
         return \''. '<strike title="'.$I18N->msg('imanager_type_system').'">'.$I18N->msg('imanager_delete').'</strike>' .'\';
       }
       return $list->getColumnLink("'. $deleteType .'","'. $I18N->msg('imanager_delete') .'");'
    )
  );

  $list->show();

  // IMG TYPE DUPLICATOR SELECT
  ////////////////////////////////////////////////////////////////////////////
  $sel = new rex_select();
  $sel->setSize(1);
  $sel->setName('img_type_duplicator');
  $sel->setId('img_type_duplicator');
  $sel->addOption('','');
  $sel->addSqlOptions('SELECT `name`,`id` FROM `rex_679_types` ORDER BY `status` ASC');
  #$sel->setSelected(rex_request('type_id','int'));
  $img_type_duplicator = $sel->get();
  echo '<br />';
  echo rex_content_block('<strong>'.$I18N->msg('imanager_type_duplicate').'</strong>'.$img_type_duplicator);

  echo '
  <script>
      // IMG TYPE DUPLICATOR
      jQuery("#img_type_duplicator").change(function(){
        window.location = "index.php?page=image_manager&func=duplicate&type_id="+jQuery(this).val();
        return true;
      });
    </script>
  ';

}
elseif ($func == 'add' ||
        $func == 'edit' && $type_id > 0)
{
  if($func == 'edit')
  {
    $formLabel = $I18N->msg('imanager_type_edit');
  }
  else if ($func == 'add')
  {
    $formLabel = $I18N->msg('imanager_type_create');
  }

  rex_register_extension('REX_FORM_CONTROL_FIElDS', 'rex_imanager_handle_form_control_fields');
  $form = rex_form::factory($REX['TABLE_PREFIX'].'679_types',$formLabel,'id='.$type_id);

  $form->addErrorMessage(REX_FORM_ERROR_VIOLATE_UNIQUE_KEY, $I18N->msg('imanager_error_type_name_not_unique'));

  $field =& $form->addTextField('name');
  $field->setLabel($I18N->msg('imanager_type_name'));

  $field =& $form->addTextareaField('description');
  $field->setLabel($I18N->msg('imanager_type_description'));

  if($func == 'edit')
  {
    $form->addParam('type_id', $type_id);
  }

  $form->show();
}

echo '</div>';
?>
