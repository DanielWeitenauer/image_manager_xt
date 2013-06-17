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
 * @package redaxo 4.4.x/4.5.x
 * @version 1.4.0
 */


$Basedir = dirname(__FILE__);

$effect_id = rex_request('effect_id','int');
$type_id = rex_request('type_id','int');
$func = rex_request('func','string');

// ---- validate type_id
$sql = rex_sql::factory();
$sql->setQuery('SELECT * FROM '. $REX['TABLE_PREFIX'].'679_types WHERE id='. $type_id);
if($sql->getRows() != 1)
{
  unset($type_id);
}
$typeName = $sql->getValue('name');


$info = '';
$warning = '';

//-------------- delete cache on effect changes or deletion
if((rex_post('func') != '' || $func == 'delete')
   && $type_id > 0)
{
  $counter = rex_imanager_deleteCacheByType($type_id);
//  $info = $I18N->msg('imanager_cache_files_removed', $counter);
}

//-------------- delete effect
if($func == 'delete' && $effect_id > 0)
{
  $sql = rex_sql::factory();
//  $sql->debugsql = true;
  $sql->setTable($REX['TABLE_PREFIX'].'679_type_effects');
  $sql->setWhere('id='. $effect_id . ' LIMIT 1');

  if($sql->delete())
  {
     $info = $I18N->msg('imanager_effect_deleted') ;
  }
  else
  {
    $warning = $sql->getErrro();
  }
  $func = '';
}

if ($info != '')
  echo rex_info($info);

if ($warning != '')
  echo rex_warning($warning);


echo '<div class="rex-addon-output-v2">';
if ($func == '' && $type_id > 0)
{
  // IMG TYPE JUMP SELECT
  ////////////////////////////////////////////////////////////////////////////
  $sel = new rex_select();
  $sel->setSize(1);
  $sel->setName('img_type_jump');
  $sel->setId('img_type_jump');
  $sel->addSqlOptions('SELECT `name`,`id` FROM `rex_679_types` ORDER BY `status` ASC');
  $sel->setSelected(rex_request('type_id','int'));
  $img_type_select = $sel->get();

  echo rex_content_block($I18N->msg('imanager_effect_type_select_header').$img_type_select);

  $query = 'SELECT * FROM '.$REX['TABLE_PREFIX'].'679_type_effects WHERE type_id='.$type_id .' ORDER BY prior';

  $list = rex_list::factory($query);
  $list->setNoRowsMessage(htmlspecialchars($I18N->msg('imanager_effect_no_effects')));
  $list->setCaption(htmlspecialchars($I18N->msg('imanager_effect_caption', $typeName)));
  $list->addTableAttribute('summary', htmlspecialchars($I18N->msg('imanager_effect_summary', $typeName)));
  $list->addTableColumnGroup(array(40, '*', '*', 40, 130));

  $list->removeColumn('id');
  $list->removeColumn('type_id');
  $list->setColumnLabel('parameters',htmlspecialchars($I18N->msg('imanager_type_parameters')));
  $list->removeColumn('updatedate');
  $list->removeColumn('updateuser');
  $list->removeColumn('createdate');
  $list->removeColumn('createuser');
  $list->setColumnLabel('effect',htmlspecialchars($I18N->msg('imanager_type_name')));
  $list->setColumnLabel('prior',htmlspecialchars($I18N->msg('imanager_type_prior')));

  // icon column
  $thIcon = '<a class="rex-i-element rex-i-generic-add" href="'. $list->getUrl(array('type_id' => $type_id, 'func' => 'add')) .'"><span class="rex-i-element-text">'. htmlspecialchars($I18N->msg('imanager_effect_create')) .'</span></a>';
  $tdIcon = '<span class="rex-i-element rex-i-list"><span class="rex-i-element-text">###name###</span></span>';
  $list->addColumn($thIcon, $tdIcon, 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
  $list->setColumnParams($thIcon, array('func' => 'edit', 'type_id' => $type_id, 'effect_id' => '###id###'));

  // PARAMETERS COLUMN
  function imm_effect_parameters_format($params)
  {
    global $type_id;
    $parameters = unserialize($params['value']);
    $parameters = $parameters['rex_effect_'.$params['list']->getValue('effect')];
    return '<a href="index.php?list='.$params['list']->name.'&page=image_manager&subpage=effects&func=edit&type_id='.$type_id.'&effect_id=###id###" class="rex_effect_parameters">'.implode(' | ',array_values($parameters)).'</a>';
  }
  $list->setColumnFormat('parameters'  ,'custom', 'imm_effect_parameters_format');

  // functions column
  $funcs = $I18N->msg('imanager_effect_functions');
  $list->addColumn($funcs, $I18N->msg('imanager_effect_delete'), -1, array('<th>###VALUE###</th>','<td>###VALUE###</td>'));
  $list->setColumnParams($funcs, array('type_id' => $type_id, 'effect_id' => '###id###', 'func' => 'delete'));
  $list->addLinkAttribute($funcs, 'onclick', 'return confirm(\''.$I18N->msg('delete').' ?\')');

  $list->show();

  echo '
  <script>
      // IMG TYPE JUMP
      jQuery("#img_type_jump").change(function(){
        window.location = "index.php?page=image_manager&subpage=effects&type_id="+jQuery(this).val();
        return true;
      });
    </script>
  ';
}
elseif ($func == 'add' && $type_id > 0 ||
        $func == 'edit' && $effect_id > 0 && $type_id > 0)
{
  $effectNames = rex_imanager_supportedEffectNames();

  if($func == 'edit')
  {
    $formLabel = $I18N->msg('imanager_effect_edit_header', htmlspecialchars($typeName));
  }
  else if ($func == 'add')
  {
    $formLabel = $I18N->msg('imanager_effect_create_header', htmlspecialchars($typeName));
  }

  $form = rex_form::factory($REX['TABLE_PREFIX'].'679_type_effects',$formLabel,'id='.$effect_id);

  // image_type_id for reference to save into the db
  $form->addHiddenField('type_id', $type_id);

  // effect name als SELECT
  $field =& $form->addSelectField('effect');
  $field->setLabel($I18N->msg('imanager_effect_name'));
  $select =& $field->getSelect();
  $select->addOptions($effectNames, true);
  $select->setSize(1);

  $script = '
  <script type="text/javascript">
  <!--

  (function($) {
    var currentShown = null;
    $("#'. $field->getAttribute('id') .'").change(function(){
      if(currentShown) currentShown.hide();

      var effectParamsId = "#rex-rex_effect_"+ jQuery(this).val();
      currentShown = $(effectParamsId);
      currentShown.show();
    }).change();
  })(jQuery);

  //--></script>';

  // effect prio
  $field =& $form->addPrioField('prior');
  $field->setLabel($I18N->msg('imanager_effect_prior'));
  $field->setLabelField('effect');
  $field->setWhereCondition('type_id = '. $type_id);

  // effect parameters
  $fieldContainer =& $form->addContainerField('parameters');
  $fieldContainer->setAttribute('style', 'display: none');
  $fieldContainer->setSuffix($script);

  $effects = rex_imanager_supportedEffects();

  foreach($effects as $effectClass => $effectFile)
  {
    require_once($effectFile);
    $effectObj = new $effectClass();
    $effectParams = $effectObj->getParams();
    $group = $effectClass;

    if(empty($effectParams)) continue;

    foreach($effectParams as $param)
    {
      $name = $effectClass.'_'.$param['name'];
      $value = isset($param['default']) ? $param['default'] : null;
      $attributes = array();
      if (isset($param['attributes']))
        $attributes = $param['attributes'];

      switch($param['type'])
      {
        case 'int' :
        case 'float' :
        case 'string' :
          {
            $type = 'text';
            $field =& $fieldContainer->addGroupedField($group, $type, $name, $value, $attributes);
            $field->setLabel($param['label']);
            $field->setAttribute('id',"image_manager $name $type");
            if(!empty($param['notice'])) $field->setNotice($param['notice']);
            if(!empty($param['prefix'])) $field->setPrefix($param['prefix']);
            if(!empty($param['suffix'])) $field->setSuffix($param['suffix']);
            break;
          }
        case 'select' :
          {
            $type = $param['type'];
            $field =& $fieldContainer->addGroupedField($group, $type, $name, $value, $attributes);
            $field->setLabel($param['label']);
            $field->setAttribute('id',"image_manager $name $type");
            if(!empty($param['notice'])) $field->setNotice($param['notice']);
            if(!empty($param['prefix'])) $field->setPrefix($param['prefix']);
            if(!empty($param['suffix'])) $field->setSuffix($param['suffix']);

            $select =& $field->getSelect();
            if (!isset($attributes['multiple']))
              $select->setSize(1);
            $select->addOptions($param['options'], true);
            break;
          }
        case 'media' :
          {
            $type = $param['type'];
            $field =& $fieldContainer->addGroupedField($group, $type, $name, $value, $attributes);
            $field->setLabel($param['label']);
            $field->setAttribute('id',"image_manager $name $type");
            if(!empty($param['notice'])) $field->setNotice($param['notice']);
            if(!empty($param['prefix'])) $field->setPrefix($param['prefix']);
            if(!empty($param['suffix'])) $field->setSuffix($param['suffix']);
            break;
          }
        default:
          {
            trigger_error('Unexpected param type "'. $param['type'] .'"', E_USER_ERROR);
          }
      }
    }
  }

  // parameters for url redirects
  $form->addParam('type_id', $type_id);
  if($func == 'edit')
  {
    $form->addParam('effect_id', $effect_id);
  }
  @$form->show();
}

echo '</div>';
?>
