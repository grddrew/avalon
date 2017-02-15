<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$requiredModules = array('order', 'report', 'intranet', 'socialnetwork');

foreach ($requiredModules as $requiredModule)
{
	if (!CModule::IncludeModule($requiredModule))
	{
		ShowError(strtoupper($requiredModule).'_MODULE_NOT_INSTALLED');
		return 0;
	}
}


$reportID = $arResult['REPORT_ID'] = isset($arParams['REPORT_ID']) ? intval($arParams['REPORT_ID']) : 0;
$reportData = $arResult['REPORT_DATA'] = COrderReportManager::getReportData($reportID);
$reportOwnerID = $arResult['REPORT_OWNER_ID'] = $reportData && isset($reportData['OWNER_ID']) ? $reportData['OWNER_ID'] : '';
$arResult['REPORT_HELPER_CLASS'] = $reportOwnerID !== '' ? COrderReportManager::getOwnerHelperClassName($reportOwnerID) : '';
$arResult['NAME_TEMPLATE'] = empty($arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#","#/NOBR#"), array("",""), $arParams["NAME_TEMPLATE"]);

$this->IncludeComponentTemplate();
?>