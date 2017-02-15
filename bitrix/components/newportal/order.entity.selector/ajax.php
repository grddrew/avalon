<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
global $DB, $APPLICATION;
define('LOG_FILENAME',dirname(__FILE__).'/log.txt');
if(!function_exists('__OrderEndResponse'))
{
    function __OrderEndResponse($result)
    {
        $GLOBALS['APPLICATION']->RestartBuffer();
        Header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);
        if(!empty($result))
        {
            echo CUtil::PhpToJSObject($result);
        }
        require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
        die();
    }
}
if (!CModule::IncludeModule('order'))
{
    __OrderEndResponse(array('ERROR' => 'Could not include order module.'));
}
/*
 * ONLY 'POST' METHOD SUPPORTED
 * SUPPORTED ACTIONS:
 *  'SAVE_AGENT'
 *  'ENABLE_SONET_SUBSCRIPTION'
 *  'FIND_DUPLICATES'
 *  'FIND_LOCALITIES'
 */
if (!COrderHelper::IsAuthorized() || !check_bitrix_sessid())
{
    __OrderEndResponse(array('ERROR' => 'Access denied.','data'=>$_REQUEST));
}
if ($_SERVER['REQUEST_METHOD'] != 'POST')
{
    __OrderEndResponse(array('ERROR' => 'Request method is not allowed.'));
}
__IncludeLang(dirname(__FILE__).'/lang/'.LANGUAGE_ID.'/'.basename(__FILE__));
CUtil::JSPostUnescape();
$GLOBALS['APPLICATION']->RestartBuffer();
Header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);
$action = isset($_POST['ACTION']) ? $_POST['ACTION'] : '';
if($action==='GET_POPUT') {
    $data=(isset($_REQUEST['DATA']))?$_REQUEST['DATA']:array();
    $field=CUtil::JsObjectToPhp($data['field']);
    /*$fullName=isset($data['fullName'])?$data['fullName']:'';
    $phone=isset($data['phone'])?$data['phone']:'';
    $email=isset($data['email'])?$data['email']:'';
    $formID=isset($data['formID'])?$data['formID']:'';
    $field=array(
        'id' => 'PHYSICAL_ID',
        'componentParams' => array(
            'ENTITY_TYPE' => 'PHYSICAL',
            'INPUT_NAME' => 'PHYSICAL_ID',
            'NEW_INPUT_NAME' => 'NEW_PHYSICAL_ID',
            'INPUT_VALUE' => $data['id'],
            'FORM_NAME' => $formID,
            'MULTIPLE' => 'N',
            //'NAME_TEMPLATE' => \Bitrix\Crm\Format\PersonNameFormatter::getFormat()
        ),
    );*/
    $APPLICATION->AddHeadScript('/bitrix/js/order/order.js');
    $params = isset($field['componentParams']) ? $field['componentParams'] : array();
    if(!empty($params)):
        /*echo COrderEntitySelectorHelper::GetSelector($params['ENTITY_TYPE'],array(
            'FORM_ID'=>$arParams['FORM_ID'],
            'INPUT_NAME'=>$params['INPUT_NAME'],
            'INPUT_VALUE' => isset($params['INPUT_VALUE']) ? $params['INPUT_VALUE'] : '',
            'ADD_FILES'=>false,
        ));*/
        $entityType = isset($params['ENTITY_TYPE']) ? $params['ENTITY_TYPE'] : '';
        $entityID = isset($params['INPUT_VALUE']) ? $params['INPUT_VALUE'] : '';
        $containerID = "{$arParams['FORM_ID']}_FIELD_CONTAINER_{$field['id']}";
        $changeButtonID = "{$arParams['FORM_ID']}_CHANGE_BTN_{$field['id']}";
        //$entityInfo = COrderEntitySelectorHelper::PrepareEntityInfo($entityType, $entityID,$entityValue);
        $advancedInfoHTML = COrderEntitySelectorHelper::PrepareEntityAdvancedInfoHTML($entityType, $entityInfo, array('CONTAINER_ID' => $containerID.'_descr'));
        $APPLICATION->IncludeComponent('newportal:order.entity.selector',
            '',
            array(
                'ENTITY_TYPE' => $entityType,
                'INPUT_NAME' => CUtil::JSEscape($changeButtonID),
                'INPUT_VALUE' => isset($entityID) ? $entityID : '',
                'FORM_NAME' => $arParams['FORM_ID'],
                'MULTIPLE' => 'N',
                'FILTER' => false
            ),
            false,
            array('HIDE_ICONS' => 'Y')
        );
    endif;
} elseif($action === 'GET_ADD_POPUP') {
    $data=(isset($_REQUEST['DATA']))?$_REQUEST['DATA']:array();
    $field=CUtil::JsObjectToPhp($data['field']);
    /*$fullName=isset($data['fullName'])?$data['fullName']:'';
    $phone=isset($data['phone'])?$data['phone']:'';
    $email=isset($data['email'])?$data['email']:'';
    $formID=isset($data['formID'])?$data['formID']:'';
    $field=array(
        'id' => 'PHYSICAL_ID',
        'componentParams' => array(
            'ENTITY_TYPE' => 'PHYSICAL',
            'INPUT_NAME' => 'PHYSICAL_ID',
            'NEW_INPUT_NAME' => 'NEW_PHYSICAL_ID',
            'INPUT_VALUE' => array(
                'FULL_NAME'=>$fullName,
                'PHONE'=>$data['phone'],
                'EMAIL'=>$data['email'],
            ),
            'FORM_NAME' => $formID,
            'MULTIPLE' => 'N',
            //'NAME_TEMPLATE' => \Bitrix\Crm\Format\PersonNameFormatter::getFormat()
        ),
    );*/
    $APPLICATION->SetAdditionalCSS('/bitrix/js/order/css/order.css');
    $APPLICATION->AddHeadScript('/bitrix/js/order/interface_form.js');
    $APPLICATION->AddHeadScript('/bitrix/js/order/common.js');
    $APPLICATION->AddHeadScript('/bitrix/js/order/order.js');
    $params = isset($field['componentParams']) ? $field['componentParams'] : array();
    if(!empty($params)):
        ob_start();
        ?><div class="order-offer-info-data-wrap"><?
        $advancedInfoHTML = '';
        $entityType = isset($params['ENTITY_TYPE']) ? $params['ENTITY_TYPE'] : '';
        $entityID = '';
        $entityValue = isset($params['INPUT_VALUE']) ? $params['INPUT_VALUE'] : array();
        $editorID = "{$formID}_{$field['id']}";
        $containerID = "{$formID}_FIELD_CONTAINER_{$field['id']}";
        $selectorID = "{$formID}_ENTITY_SELECTOR_{$field['id']}";
        $changeButtonID = "{$formID}_CHANGE_BTN_{$field['id']}";
        $dataInputName = isset($params['INPUT_NAME']) ? $formID.'_CHANGE_BTN_'.$params['INPUT_NAME'] : $formID.'_CHANGE_BTN_'.$field['id'];
        $dataInputID = "{$formID}_DATA_INPUT_{$dataInputName}";
        $newDataInputName = isset($params['NEW_INPUT_NAME']) ? $params['NEW_INPUT_NAME'] : '';
        $newDataInputID = $newDataInputName !== '' ? "{$formID}_NEW_DATA_INPUT_{$dataInputName}" : '';
        $entityInfo = COrderEntitySelectorHelper::PrepareEntityInfo($entityType, $entityID,$entityValue);
        $advancedInfoHTML = COrderEntitySelectorHelper::PrepareEntityAdvancedInfoHTML($entityType, $entityInfo, array('CONTAINER_ID' => $containerID.'_descr'));
        $arMultiFields = is_array($entityInfo['ADVANCED_INFO']['MULTI_FIELDS']) ? $entityInfo['ADVANCED_INFO']['MULTI_FIELDS'] : array();
        foreach ($arMultiFields as $mf)
        {
            $entityInfo[$mf['TYPE_ID']] = trim($mf['VALUE']);
        }
        unset($entityInfo['ADVANCED_INFO']);
        unset($entityInfo['URL']);
        ?><div id="<?=htmlspecialcharsbx($containerID)?>" class="bx-order-edit-order-entity-field">
            <div class="bx-order-entity-info-wrapper"><?=htmlspecialcharsEx($entityInfo['TITLE'])?></div>
            <input type="hidden" id="<?=htmlspecialcharsbx($dataInputID)?>" name="<?=htmlspecialcharsbx($dataInputName)?>" value="<?=htmlspecialcharsbx($entityID)?>" /><?
            if($newDataInputName !== ''):
                ?><input type="hidden" id="<?=htmlspecialcharsbx($newDataInputID)?>" name="<?=htmlspecialcharsbx($newDataInputName)?>" value="" /><?
            endif;
            ?><div class="bx-order-entity-buttons-wrapper">
                <span id="<?=htmlspecialcharsbx($changeButtonID)?>" class="bx-order-edit-order-entity-change"><?= htmlspecialcharsbx(GetMessage('interface_form_similar_select'))?></span><?
                if($newDataInputName !== ''):
                    ?> <span class="bx-order-edit-order-entity-add"><?=htmlspecialcharsEx(GetMessage('interface_form_add_new_entity'))?></span><?
                endif;
                ?></div>
        </div></div><?
        if ($advancedInfoHTML !== '') echo $advancedInfoHTML;
        $html = ob_get_contents();
        ob_end_clean();
        $serviceUrl = '';
        $actionName = '';
        $dialogSettings = array(
            'addButtonName' => GetMessage('interface_form_add_dialog_btn_add'),
            'cancelButtonName' => GetMessage('interface_form_cancel')
        );
        if($entityType === 'PHYSICAL')
        {
            $serviceUrl = '/bitrix/components/newportal/order.physical.edit/ajax.php?'.bitrix_sessid_get();
            $actionName = 'SAVE_PHYSICAL';

            $dialogSettings['title'] = GetMessage('interface_form_add_physical_dlg_title');
            $dialogSettings['lastNameTitle'] = GetMessage('interface_form_add_physical_fld_last_name');
            $dialogSettings['nameTitle'] = GetMessage('interface_form_add_physical_fld_name');
            $dialogSettings['secondNameTitle'] = GetMessage('interface_form_add_physical_fld_second_name');
            $dialogSettings['emailTitle'] = GetMessage('interface_form_add_physical_fld_email');
            $dialogSettings['phoneTitle'] = GetMessage('interface_form_add_physical_fld_phone');
            $dialogSettings['genderTitle'] = GetMessage('interface_form_add_physical_fld_gender');
            $dialogSettings['bDayTitle'] = GetMessage('interface_form_add_physical_fld_b_day');
            $dialogSettings['descriptionTitle'] = GetMessage('interface_form_add_physical_fld_description');
            $dialogSettings['genderList'] = COrderEntitySelectorHelper::PrepareListItems(array(
                GetMessage('interface_form_add_physical_fld_gender_m')=>GetMessage('interface_form_add_physical_fld_gender_m'),
                GetMessage('interface_form_add_physical_fld_gender_f')=>GetMessage('interface_form_add_physical_fld_gender_f'),
            ));
        }
        ?><script type="text/javascript">
        <?ob_start();?>
        var entitySelectorId = ORDER.Set(
            BX('<?=CUtil::JSEscape($changeButtonID) ?>'),
            '<?=CUtil::JSEscape($selectorID)?>',
            '',
            <?=CUtil::PhpToJsObject(COrderEntitySelectorHelper::PreparePopupItems($entityType, false))?>,
            false,
            false,
            ['<?=CUtil::JSEscape(strtolower($entityType))?>'],
            <?=CUtil::PhpToJsObject(COrderEntitySelectorHelper::PrepareCommonMessages())?>,
            true
        );
        BX.OrderEntityEditor.messages =
        {
            'unknownError': '<?=GetMessageJS('interface_form_ajax_unknown_error')?>',
            'prefContactType': '<?=GetMessageJS('interface_form_entity_selector_prefContactType')?>',
            'prefPhone': '<?=GetMessageJS('interface_form_entity_selector_prefPhone')?>',
            'prefEmail': '<?=GetMessageJS('interface_form_entity_selector_prefEmail')?>'
        };

        BX.OrderEntityEditor.create(
            '<?=CUtil::JSEscape($editorID)?>',
            {
                'typeName': '<?=CUtil::JSEscape($entityType)?>',
                'containerId': '<?=CUtil::JSEscape($containerID)?>',
                'dataInputId': '<?=CUtil::JSEscape($dataInputID)?>',
                'newDataInputId': '<?=CUtil::JSEscape($newDataInputID)?>',
                'entitySelectorId': entitySelectorId,
                'serviceUrl': '<?= CUtil::JSEscape($serviceUrl) ?>',
                'actionName': '<?= CUtil::JSEscape($actionName) ?>',
                'dialog': <?=CUtil::PhpToJSObject($dialogSettings)?>,
                'data':<?=CUtil::PhpToJSObject($entityInfo)?>
            }
        );
        <?$script = ob_get_clean();?>
    </script><?
    endif;
    __OrderEndResponse(array('html'=>$html,'script'=>$script,'adv'=>$advancedInfoHTML));

} elseif($action === 'GET_ITEMS') {
    $data=(isset($_REQUEST['DATA']))?$_REQUEST['DATA']:array();
    if(isset($data['ENTITIES']))
        __OrderEndResponse(COrderEntitySelectorHelper::PreparePopupItems($data['ENTITIES']));
    else
        __OrderEndResponse(array('ERROR'=>'NE'));
}
else
{
    __OrderEndResponse(array('ERROR' => "Action '{$action}' is not supported in current context."));
}