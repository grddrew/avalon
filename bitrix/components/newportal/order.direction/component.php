<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();


global $APPLICATION;
if (!CModule::IncludeModule('order'))
{
    ShowError(GetMessage('ORDER_MODULE_NOT_INSTALLED'));
    return;
};

/* if (!CModule::IncludeModule('currency'))
{
    ShowError(GetMessage('CRM_MODULE_NOT_INSTALLED_CURRENCY'));
    return;
}*/

$APPLICATION->AddHeadScript('/bitrix/js/order/interface_form.js');
$APPLICATION->AddHeadScript('/bitrix/js/order/common.js');

$arDefaultUrlTemplates404 = array(
    'index' => 'index.php',
    'list' => 'list/',
    'edit' => 'edit/#direction_id#/'
);

$arDefaultVariableAliases404 = array(

);
$arDefaultVariableAliases = array();
$componentPage = '';
$arComponentVariables = array('direction_id');

$arParams['NAME_TEMPLATE'] = empty($arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#","#/NOBR#"), array("",""), $arParams["NAME_TEMPLATE"]);


if ($arParams['SEF_MODE'] == 'Y')
{
    $arVariables = array();
    $arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams['SEF_URL_TEMPLATES']);
    $arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases404, $arParams['VARIABLE_ALIASES']);
    $componentPage = CComponentEngine::ParseComponentPath($arParams['SEF_FOLDER'], $arUrlTemplates, $arVariables);

    if (empty($componentPage) || (!array_key_exists($componentPage, $arDefaultUrlTemplates404)))
        $componentPage = 'index';

    CComponentEngine::InitComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

    foreach ($arUrlTemplates as $url => $value)
    {
        if(strlen($arParams['PATH_TO_DIRECTION_'.strToUpper($url)]) <= 0)
            $arResult['PATH_TO_DIRECTION_'.strToUpper($url)] = $arParams['SEF_FOLDER'].$value;
        else
            $arResult['PATH_TO_DIRECTION_'.strToUpper($url)] = $arParams['PATH_TO_'.strToUpper($url)];
    }
}
else
{
    $arComponentVariables[] = $arParams['VARIABLE_ALIASES']['direction_id'];

    $arVariables = array();
    $arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases, $arParams['VARIABLE_ALIASES']);
    CComponentEngine::InitComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);

    $componentPage = 'index';
    if (isset($_REQUEST['edit']))
        $componentPage = 'edit';
    else if (isset($_REQUEST['copy']))
        $componentPage = 'edit';

    $arResult['PATH_TO_DIRECTION_LIST'] = $APPLICATION->GetCurPage();
    $arResult['PATH_TO_DIRECTION_EDIT'] = $APPLICATION->GetCurPage()."?$arVariableAliases[direction_id]=#direction_id#&edit";
}

$arResult = array_merge(
    array(
        'VARIABLES' => $arVariables,
        'ALIASES' => $arParams['SEF_MODE'] == 'Y'? array(): $arVariableAliases,
        'ELEMENT_ID' => $arParams['ELEMENT_ID'],
        'PATH_TO_LEAD_CONVERT' => $arParams['PATH_TO_LEAD_CONVERT'],
        'PATH_TO_LEAD_EDIT' => $arParams['PATH_TO_LEAD_EDIT'],
        'PATH_TO_CONTACT_EDIT' => $arParams['PATH_TO_CONTACT_EDIT'],
        'PATH_TO_REG_EDIT' => $arParams['PATH_TO_REG_EDIT'],
        'PATH_TO_GROUP_EDIT' => $arParams['PATH_TO_GROUP_EDIT'],
        'PATH_TO_DIRECTION_EDIT' => $arParams['PATH_TO_DIRECTION_EDIT'],
        'PATH_TO_NOMEN_EDIT' => $arParams['PATH_TO_NOMEN_EDIT'],
        'PATH_TO_COURSE_EDIT' => $arParams['PATH_TO_COURSE_EDIT'],
        'PATH_TO_STAFF_EDIT' => $arParams['PATH_TO_STAFF_EDIT'],
    ),
    $arResult
);

$arResult['NAVIGATION_CONTEXT_ID'] = 'DIRECTION';
$this->IncludeComponentTemplate($componentPage);
?>