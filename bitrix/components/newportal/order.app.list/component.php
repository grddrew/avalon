<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();



if (!CModule::IncludeModule('order'))
{
    ShowError(GetMessage('ORDER_MODULE_NOT_INSTALLED'));
    return;
}
CJSCore::Init ();
global $USER_FIELD_MANAGER, $USER, $APPLICATION;

$COrderPerms = COrderPerms::GetCurrentUserPermissions();
if ($COrderPerms->HavePerm('APP', BX_ORDER_PERM_NONE, 'READ'))
{
    ShowError(GetMessage('ORDER_PERMISSION_DENIED'));
    return;
}


$arResult['STATUS_LIST_WRITE']=COrderHelper::GetEnumList('APP',"STATUS");



$userID = COrderHelper::GetCurrentUserID();
$isAdmin = COrderPerms::IsAdmin();


$arResult['CURRENT_USER_ID'] = $userID;
$arParams['PATH_TO_APP_LIST'] = OrderCheckPath('PATH_TO_APP_LIST', $arParams['PATH_TO_APP_LIST'], $APPLICATION->GetCurPage());
$arParams['PATH_TO_APP_EDIT'] = OrderCheckPath('PATH_TO_APP_EDIT', $arParams['PATH_TO_APP_EDIT'], '/order/app/edit/#app_id#');
$arParams['PATH_TO_REG_EDIT'] = OrderCheckPath('PATH_TO_REG_EDIT', $arParams['PATH_TO_REG_EDIT'], '/order/reg/edit/#reg_id#');
$arParams['PATH_TO_PHYSICAL_EDIT'] = OrderCheckPath('PATH_TO_PHYSICAL_EDIT', $arParams['PATH_TO_PHYSICAL_EDIT'], '/order/physical/edit/#physical_id#');
$arParams['PATH_TO_CONTACT_EDIT'] = OrderCheckPath('PATH_TO_CONTACT_EDIT', $arParams['PATH_TO_CONTACT_EDIT'], '/order/contact/edit/#contact_id#');
$arParams['PATH_TO_AGENT_EDIT'] = OrderCheckPath('PATH_TO_AGENT_EDIT', $arParams['PATH_TO_AGENT_EDIT'], '/order/agent/edit/#agent_id#');
$arParams['PATH_TO_GROUP_EDIT'] = OrderCheckPath('PATH_TO_GROUP_EDIT', $arParams['PATH_TO_GROUP_EDIT'], '/order/group/edit/#group_id#');
$arParams['PATH_TO_FORMED_GROUP_EDIT'] = OrderCheckPath('PATH_TO_FORMED_GROUP_EDIT', $arParams['PATH_TO_FORMED_GROUP_EDIT'], '/order/formed_group/edit/#formed_group_id#');
$arParams['PATH_TO_DIRECTION_EDIT'] = OrderCheckPath('PATH_TO_DIRECTION_EDIT', $arParams['PATH_TO_DIRECTION_EDIT'], '/order/direction/edit/#direction_id#');
$arParams['PATH_TO_NOMEN_EDIT'] = OrderCheckPath('PATH_TO_NOMEN_EDIT', $arParams['PATH_TO_NOMEN_EDIT'], '/order/nomen/edit/#nomen_id#');
$arParams['PATH_TO_COURSE_EDIT'] = OrderCheckPath('PATH_TO_COURSE_EDIT', $arParams['PATH_TO_COURSE_EDIT'], '/order/course/edit/#course_id#');
$arParams['PATH_TO_STAFF_EDIT'] = OrderCheckPath('PATH_TO_STAFF_EDIT', $arParams['PATH_TO_STAFF_EDIT'], '/company/personal/user/#staff_id#/');
$arParams['NAME_TEMPLATE'] = empty($arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#","#/NOBR#"), array("",""), $arParams["NAME_TEMPLATE"]);

$arResult['IS_AJAX_CALL'] = isset($_REQUEST['bxajaxid']) || isset($_REQUEST['AJAX_CALL']);
$arResult['SESSION_ID'] = bitrix_sessid();
$arResult['GRID_ID']='ORDER_APP_LIST_V12';

$COrderApp=new COrderApp(false);

CUtil::InitJSCore(array('ajax', 'tooltip'));


$arResult['FORM_ID'] = isset($arParams['FORM_ID']) ? $arParams['FORM_ID'] : '';
$arResult['TAB_ID'] = isset($arParams['TAB_ID']) ? $arParams['TAB_ID'] : '';
$bInternal=false;

$allDelete=false;
if(check_bitrix_sessid()) {
    if(isset($_POST['save']) && $_POST['save'] ==GetMessage('ORDER_BUTTON_SAVE')) {

        foreach($_POST['FIELDS'] as $id => $fields) {
            if(!$COrderApp->Update($id,$fields))
                $arrError[]=$COrderApp->LAST_ERROR;
        }
        
        if(!empty($arrError)) {
            foreach($arrError as $err)
                ShowError($err);
        } else {
            LocalRedirect(
                CComponentEngine::MakePathFromTemplate(
                    $arParams['PATH_TO_APP_LIST']
                )
            );
        }

    }
    if(isset($_POST['apply']) && $_POST['apply']==GetMessage('ORDER_BUTTON_APPLY') &&
        ($_POST['ACTION_STATUS_ID']!='' || $_POST['ACTION_DESCRIPTION']!='' || $_POST['ACTION_PERIOD']!=''
            || $_POST['ACTION_ASSIGNED_ID']!='')
    ) {
        $prop=array();
        if($_POST['action_button_'.$arResult['GRID_ID']]=='set_status' && $_POST['ACTION_STATUS_ID']!='') {
            $prop['STATUS'] = $_POST['ACTION_STATUS_ID'];
        }

        if($_POST['action_button_'.$arResult['GRID_ID']]=='set_description' && $_POST['ACTION_DESCRIPTION']!='') {
            $prop['DESCRIPTION'] = $_POST['ACTION_DESCRIPTION'];
        }

        if($_REQUEST['action_button_'.$arResult['GRID_ID']]=='set_assigned' && $_POST['ACTION_ASSIGNED_ID']!='') {
            $prop['ASSIGNED_ID'] = $_POST['ACTION_ASSIGNED_ID'];
        }
        foreach($_POST['ID'] as $id) {
            if(!$COrderApp->Update($id,$prop))
                $arrError[]=$COrderApp->LAST_ERROR;
        }

        if(!empty($arrError)) {
            foreach($arrError as $err)
                ShowError($err);
        } else {
            LocalRedirect(
                CComponentEngine::MakePathFromTemplate(
                    $arParams['PATH_TO_APP_LIST']
                )
            );
        }
    }
    if($_REQUEST['action_button_'.$arResult['GRID_ID']]=='delete') {
        if(isset($_REQUEST['action_all_rows_'.$arResult['GRID_ID']])
            && $_REQUEST['action_all_rows_'.$arResult['GRID_ID']]=='Y'
        ) {
            $allDelete=true;
        } else {
            foreach ($_REQUEST['ID'] as $id) {
                if (!$COrderApp->Delete($id))
                    $arrError[]=$COrderApp->LAST_ERROR;
            }

            if(!empty($arrError)) {
                foreach($arrError as $err)
                    ShowError($err);
            } else {
                LocalRedirect(
                    CComponentEngine::MakePathFromTemplate(
                        $arParams['PATH_TO_APP_LIST']
                    )
                );
            }
        }
    }
}





ob_start();
$APPLICATION->IncludeComponent('newportal:order.entity.selector',
    '',
    array(
        'ENTITY_TYPE' => 'AGENT',
        'INPUT_NAME' => 'AGENT_ID',
        'INPUT_VALUE' => isset($_REQUEST['AGENT_ID']) ? intval($_REQUEST['AGENT_ID']) : '',
        'FORM_NAME' => $arResult['GRID_ID'],
        'MULTIPLE' => 'N',
        'FILTER' => true
    ),
    false,
    array('HIDE_ICONS' => 'Y')
);
$sValAgent = '<div style="padding-top:0.5em;">'.ob_get_contents().'</div>';
ob_end_clean();

/*ob_start();
$APPLICATION->IncludeComponent('newportal:order.entity.selector',
    '',
    array(
        'ENTITY_TYPE' => 'CONTACT',
        'INPUT_NAME' => 'CONTACT_ID',
        'INPUT_VALUE' => isset($_REQUEST['CONTACT_ID']) ? intval($_REQUEST['CONTACT_ID']) : '',
        'FORM_NAME' => $arResult['GRID_ID'],
        'MULTIPLE' => 'N',
        'FILTER' => true
    ),
    false,
    array('HIDE_ICONS' => 'Y')
);
$sValContact = '<div style="padding-top:0.5em;">'.ob_get_contents().'</div>';
ob_end_clean();*/

ob_start();
$APPLICATION->IncludeComponent('newportal:order.entity.selector',
    '',
    array(
        'ENTITY_TYPE' => 'STAFF',
        'INPUT_NAME' => 'ASSIGNED_ID',
        'INPUT_VALUE' => isset($_REQUEST['ASSIGNED_ID']) ? intval($_REQUEST['ASSIGNED_ID']) : '',
        'FORM_NAME' => $arResult['GRID_ID'],
        'MULTIPLE' => 'N',
        'FILTER' => true
    ),
    false,
    array('HIDE_ICONS' => 'Y')
);
$sValAssigned = '<div style="padding-top:0.5em;">'.ob_get_contents().'</div>';
ob_end_clean();

ob_start();
$APPLICATION->IncludeComponent('newportal:order.entity.selector',
    '',
    array(
        'ENTITY_TYPE' => 'STAFF',
        'INPUT_NAME' => 'MODIFY_BY_ID',
        'INPUT_VALUE' => isset($_REQUEST['MODIFY_BY_ID']) ? intval($_REQUEST['MODIFY_BY_ID']) : '',
        'FORM_NAME' => $arResult['GRID_ID'],
        'MULTIPLE' => 'N',
        'FILTER' => true
    ),
    false,
    array('HIDE_ICONS' => 'Y')
);
$sValModifyBy = '<div style="padding-top:0.5em;">'.ob_get_contents().'</div>';
ob_end_clean();

$rootDirectionList=COrderHelper::GetRootDirectionList();
$res=COrderDirection::GetListEx(array(),array('ID'=>array_keys($rootDirectionList)));
while($el=$res->Fetch()) {
    $rootDirectionList[$el['ID']]=$el['TITLE'];
}

$arFilter = $arSort = array();

$arResult['FILTER'] = array(
    array('id' => 'ID', 'name' => GetMessage('ORDER_COLUMN_ID')),
    array('id' => 'AGENT_ID', 'name' => GetMessage('ORDER_COLUMN_AGENT_ID_LIST'), 'type' => 'custom', 'value' => $sValAgent, 'default' => true),
    array('id' => 'AGENT_TITLE', 'name' => GetMessage('ORDER_COLUMN_AGENT_ID')),
    //array('id' => 'CONTACT_ID', 'name' => GetMessage('ORDER_COLUMN_CONTACT_LIST'), 'type' => 'custom', 'value' => $sValContact, 'default' => true),
    //array('id' => 'CONTACT_FULL_NAME', 'name' => GetMessage('ORDER_COLUMN_CONTACT')),
    array('id' => 'ROOT_ID', 'name' => GetMessage('ORDER_COLUMN_DIRECTION'), 'default' => true, 'type' => 'list', 'items' =>$rootDirectionList),
    array('id' => 'STATUS', 'params' => array('multiple' => 'Y'), 'name' => GetMessage('ORDER_COLUMN_STATUS'), 'default' => true, 'type' => 'list', 'items' =>$arResult['STATUS_LIST_WRITE'], 'default' => true),
    array('id' => 'ASSIGNED_ID', 'name' => GetMessage('ORDER_COLUMN_ASSIGNED_ID_LIST'), 'type' => 'custom', 'value' => $sValAssigned, 'default' => true),
    array('id' => 'ASSIGNED_FULL_NAME', 'name' => GetMessage('ORDER_COLUMN_ASSIGNED_ID')),
    array('id' => 'PERIOD', 'name' => GetMessage('ORDER_COLUMN_PERIOD'), 'type' => 'date'),
    array('id' => 'EXPIRED', 'name' => GetMessage('ORDER_COLUMN_EXPIRED'), 'type'=>'checkbox'),
    array('id' => 'ATTENTION', 'name' => GetMessage('ORDER_COLUMN_ATTENTION'), 'type'=>'checkbox'),
    array('id' => 'DESCRIPTION', 'name' => GetMessage('ORDER_COLUMN_DESCRIPTION')),
    array('id' => 'MODIFY_DATE', 'name' => GetMessage('ORDER_COLUMN_MODIFY_DATE'), 'type' => 'date'),
    array('id' => 'MODIFY_BY_ID',  'name' => GetMessage('ORDER_COLUMN_MODIFY_BY_ID'), 'type' => 'custom', 'value' => $sValModifyBy),

);

$arResult['HEADERS']=array(
    array('id' => 'ID', 'name' => GetMessage('ORDER_COLUMN_ID'), 'default' => true, 'sort' => 'ID', 'editable' => false),
    array('id' => 'AGENT_ID', 'name' => GetMessage('ORDER_COLUMN_AGENT_ID'), 'default' => true, 'sort' => 'AGENT_FULL_NAME', 'editable' => false),
    //array('id' => 'CONTACT_ID', 'name' => GetMessage('ORDER_COLUMN_CONTACT'), 'default' => true, 'sort' => 'CONTACT_FULL_NAME', 'editable' => false),
    array('id' => 'STATUS', 'name' => GetMessage('ORDER_COLUMN_STATUS'), 'default' => true, 'sort' => 'STATUS_TITLE', 'editable' => array('items' => $arResult['STATUS_LIST_WRITE']), 'type' => 'list'),
    array('id' => 'ASSIGNED_ID', 'name' => GetMessage('ORDER_COLUMN_ASSIGNED_ID'), 'default' => true, 'sort' => 'ASSIGNED_FULL_NAME', 'editable' => false),
    array('id' => 'PERIOD', 'name' => GetMessage('ORDER_COLUMN_PERIOD'), 'default' => true, 'sort' => 'PERIOD', 'editable' => false),
    array('id' => 'EXPIRED', 'name' => GetMessage('ORDER_COLUMN_EXPIRED'), 'default' => true, 'sort' => 'EXPIRED', 'editable' => false,'type'=>'checkbox'),
    array('id' => 'ATTENTION', 'name' => GetMessage('ORDER_COLUMN_ATTENTION'), 'default' => true, 'sort' => 'ATTENTION', 'editable' => false,'type'=>'checkbox'),
    array('id' => 'DESCRIPTION', 'name' => GetMessage('ORDER_COLUMN_DESCRIPTION'), 'default' => true, 'sort' => 'DESCRIPTION', 'editable' => true,'type'=>'textarea'),
    array('id' => 'MODIFY_DATE', 'name' => GetMessage('ORDER_COLUMN_MODIFY_DATE'), 'default' => true, 'sort' => 'MODIFY_DATE', 'editable' => false, 'type' => 'date'),
    array('id' => 'MODIFY_BY_ID', 'name' => GetMessage('ORDER_COLUMN_MODIFY_BY_ID'), 'default' => true, 'sort' => 'MODIFY_BY_FULL_NAME', 'editable' => false),
);


$arResult['SORT_VARS']=array('by'=>'by','order'=>'order');
if(isset($_REQUEST['by']) && isset($_REQUEST['order'])) {
    $arResult['SORT']=array($_REQUEST['by']=>$_REQUEST['order']);
}
else {
    $arResult['SORT'] = array('MODIFY_DATE' => 'desc');
}

if (intval($arParams['APP_COUNT']) <= 0)
    $arParams['APP_COUNT'] = 20;

$arNavParams = array(
    'nPageSize' => $arParams['APP_COUNT']
);

$arNavigation = CDBResult::GetNavParams($arNavParams);
$CGridOptions = new COrderGridOptions($arResult['GRID_ID']);
$arNavParams = $CGridOptions->GetNavParams($arNavParams);
$arNavParams['bShowAll'] = false;



$arFilter = array();
$arFilter += $CGridOptions->GetFilter($arResult['FILTER']);

if($arFilter['GRID_FILTER_APPLIED']==false)
    $arFilter=array();

if(isset($arFilter['PERIOD_datesel']) && $arFilter['PERIOD_datesel'] === 'days' && isset($arFilter['PERIOD_from']))
{
    $arFilter['PERIOD_to'] = ConvertTimeStamp(strtotime(date("Y-m-d", time())));
}


// converts data from filter
if (isset($arFilter['FIND_list']) && !empty($arFilter['FIND']))
{
    if ($arFilter['FIND_list'] == 't_n_ln')
    {
        $arFilter['TITLE'] = $arFilter['FIND'];
        $arFilter['NAME'] = $arFilter['FIND'];
        $arFilter['LAST_NAME'] = $arFilter['FIND'];
        $arFilter['LOGIC'] = 'OR';
    }
    else
        $arFilter[strtoupper($arFilter['FIND_list'])] = $arFilter['FIND'];
    unset($arFilter['FIND_list'], $arFilter['FIND']);
}

//CCrmEntityHelper::PrepareMultiFieldFilter($arFilter);
$arImmutableFilters = array('ID', 'AGENT_ID', 'ASSIGNED_ID', 'MODIFY_BY_ID');
foreach ($arFilter as $k => $v)
{
    if(in_array($k, $arImmutableFilters, true))
    {
        continue;
    }

    $arMatch = array();

    if(in_array($k, array('STATUS')))
    {
        // Bugfix #23121 - to suppress comparison by LIKE
        $arFilter['='.$k] = $v;
        unset($arFilter[$k]);
    }
    elseif($k === 'ORIGINATOR_ID')
    {
        // HACK: build filter by internal entities
        $arFilter['=ORIGINATOR_ID'] = $v !== '__INTERNAL' ? $v : null;
        unset($arFilter[$k]);
    }
    elseif($k === 'ROOT_ID')
    {
        $arFilter['%ROOT_ID'] = $v;
        unset($arFilter[$k]);
    }
    elseif (preg_match('/(.*)_from$/i'.BX_UTF_PCRE_MODIFIER, $k, $arMatch))
    {
        if(strlen($v) > 0)
        {
            $arFilter['>='.$arMatch[1]] = $v;
        }
        unset($arFilter[$k]);
    }
    elseif (preg_match('/(.*)_to$/i'.BX_UTF_PCRE_MODIFIER, $k, $arMatch))
    {
        if(strlen($v) > 0)
        {
            if (($arMatch[1] == 'DATE_CREATE' || $arMatch[1] == 'DATE_MODIFY') && !preg_match('/\d{1,2}:\d{1,2}(:\d{1,2})?$/'.BX_UTF_PCRE_MODIFIER, $v))
            {
                $v .=  ' 23:59:59';
            }
            $arFilter['<='.$arMatch[1]] = $v;
        }
        unset($arFilter[$k]);
    }
    elseif (in_array($k, $arResult['FILTER2LOGIC']))
    {
        // Bugfix #26956 - skip empty values in logical filter
        $v = trim($v);
        if($v !== '')
        {
            $arFilter['?'.$k] = $v;
        }
        unset($arFilter[$k]);
    }
    elseif (strpos($k, 'UF_') !== 0 && $k != 'LOGIC')
    {
        $arFilter['%'.$k] = $v;
        unset($arFilter[$k]);
    }
}

$dbRes=COrderApp::GetListEx($arResult['SORT'],$arFilter);
//var_dump($arFilter);
$tree=COrderDirection::GetTree();
/*while($el=$dbRes->Fetch()) {
    $pDir = COrderApp::GetDirections($el);
    if ($arFilter['%GRID_FILTER_APPLIED'] === true && isset($arFilter['%ROOT_ID'])
        && !in_array('DIRECTION' . $arFilter['%ROOT_ID'], $pDir)
    ) {
        continue;
    }
    $el['pDir']=$pDir;
    $arFiltedElems[$el['ID']]=$el;
}
$dbRes = new CDBResult;
$dbRes->InitFromArray($arFiltedElems);*/
$dbRes->NavStart($arNavParams['nPageSize'],$arNavParams['bShowAll']);
$arResult['NAV_OBJECT']=$dbRes;
$arResult['ROWS_COUNT']=$dbRes->SelectedRowsCount();
$arResult['PAGE_SIZE']=$arNavParams['nPageSize'];
$arResult['PAGE_NUM']=$dbRes->NavPageNomer;

$CAccess = new CAccess();
$arResult['PROVIDER_NAMES']=$CAccess->GetProviderNames();
while($el=$dbRes->Fetch()) {
    if($allDelete) {
        $COrderApp->Delete($el['ID']);
        //var_dump($el['ID']);
        continue;
    }
    //$pDir=$el['pDir'];
    $pDir=COrderApp::GetDirections($el);
    //var_dump($el);
    foreach($el as $code=>$val) {
        $arTilt['~'.$code]=$val;
        if(isset($dbRes->arUserFields[$code]) && $dbRes->arUserFields[$code]['MULTIPLE']=='Y') {
            $arTilt[$code]=unserialize(htmlspecialcharsback($val));
        }
    }
    $el=array_merge($el,$arTilt);
    $bAssigned=(isset($el['ASSIGNED_ID']) && $el['ASSIGNED_ID']!='')?in_array($el['ASSIGNED_ID'],CAccess::GetUserCodesArray($USER->GetID())):false;
    $el=array_merge($el,array(
        "PATH_TO_APP_EDIT" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_APP_EDIT'],
            array('app_id' => $el["ID"])
        ),
        "PATH_TO_APP_DELETE" => $arParams['PATH_TO_APP_LIST'].'?action_button_'.$arResult['GRID_ID'].'=delete&ID[]='.$el["ID"].'&sessid='.$arResult['SESSION_ID'],
        "PATH_TO_REG_EDIT" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_REG_EDIT'],
            array('reg_id' => $el["REG_ID"])
        ),
        "PATH_TO_AGENT_EDIT" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_AGENT_EDIT'],
            array('agent_id' => $el["AGENT_ID"])
        ),
        /*"PATH_TO_CONTACT_EDIT" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_CONTACT_EDIT'],
            array('contact_id' => $el["CONTACT_ID"])
        ),*/
        "PATH_TO_STAFF_EDIT" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_STAFF_EDIT'],
            array('staff_id' => $userID)
        ),
        "PATH_TO_USER_MODIFIER" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_STAFF_EDIT'],
            array('staff_id' => $el["MODIFY_BY_ID"])
        ),
        //"PERM_ADD"=>$COrderPerms->CheckEnityAccess('APP', 'ADD',array_merge(array('STATUS'.$el['STATUS']),$pDir)),
        "PERM_EDIT"=>$bAssigned || $COrderPerms->CheckEnityAccess('APP', 'EDIT',array_merge(array('STATUS'.$el['STATUS']),$pDir)),
        "PERM_DELETE"=>$bAssigned || $COrderPerms->CheckEnityAccess('APP', 'DELETE',array_merge(array('STATUS'.$el['STATUS']),$pDir)),
        "PERM_FILTER"=>array_merge(array('STATUS'.$el['STATUS']),$pDir),
    ));
    $arAssigned[]=$el['ASSIGNED_ID'];
    $arElems[$el['ID']]=$el;
}
$arNames = $CAccess->GetNames($arAssigned);
foreach($arElems as $id=>$el) {
    $arElems[$id]['ASSIGNED_TITLE']='<b>'.htmlspecialcharsbx(COrderHelper::GetProviderName($arNames[$el['ASSIGNED_ID']]['provider_id'],$el['ASSIGNED_ID'],$arResult['PROVIDER_NAMES'])).'</b>: '.htmlspecialcharsbx($arNames[$el['ASSIGNED_ID']]['name']);
}
//var_dump($arElems);
$arResult["APP"]=$arElems;


$arResult['AJAX_MODE']=$arParams['AJAX_MODE'];
$arResult['AJAX_ID']=$arParams['AJAX_ID'];
$arResult['AJAX_OPTION_JUMP']=$arParams['AJAX_OPTION_JUMP'];
$arResult['AJAX_OPTION_HISTORY']=$arParams['AJAX_OPTION_HISTORY'];
$arResult['PERMS']['ADD']    = !$COrderPerms->HavePerm('APP', BX_ORDER_PERM_NONE, 'ADD');
$arResult['PERMS']['EDIT']   = !$COrderPerms->HavePerm('APP', BX_ORDER_PERM_NONE, 'EDIT');
$arResult['PERMS']['DELETE'] = !$COrderPerms->HavePerm('APP', BX_ORDER_PERM_NONE, 'DELETE');






$this->IncludeComponentTemplate();
?>