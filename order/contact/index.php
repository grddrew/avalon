<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/order/public/contact/index.php");
$APPLICATION->SetTitle(GetMessage("ORDER_TITLE"));
?><?$APPLICATION->IncludeComponent(
	"newportal:order.contact",
	"",
	array(
		"SEF_MODE" => "Y",
		"PATH_TO_CONTACT_EDIT" => "/order/contact/edit/#contact_id#/",
		"PATH_TO_AGENT_EDIT" => "/order/agent/edit/#agent_id#/",
		"PATH_TO_PHYSICAL_EDIT" => "/order/physical/edit/#physical_id#/",
		"PATH_TO_STAFF_PROFILE" => "/company/personal/user/#staff_id#/",
		"ELEMENT_ID" => $_REQUEST["contact_id"],
		"SEF_FOLDER" => "/order/contact/",
		"COMPONENT_TEMPLATE" => "",
		"NAME_TEMPLATE" => "",
		"SEF_URL_TEMPLATES" => array(
			"index" => "index.php",
			"list" => "list/",
			"edit" => "edit/#contact_id#/",
		),
		"VARIABLE_ALIASES" => Array(
			"index" => Array(),
			"list" => Array(),
			"edit" => Array(),
		),
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>