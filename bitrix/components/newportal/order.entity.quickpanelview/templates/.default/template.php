<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
global $APPLICATION;
$APPLICATION->SetAdditionalCSS('/bitrix/js/order/css/order.css');
$APPLICATION->AddHeadScript('/bitrix/js/order/interface_form.js');
$APPLICATION->AddHeadScript('/bitrix/js/order/common.js');
$APPLICATION->AddHeadScript('/bitrix/js/main/dd.js');

$entityTypeName = $arResult['ENTITY_TYPE_NAME'];
$entityTypeID = $arResult['ENTITY_TYPE_ID'];
$entityID = $arResult['ENTITY_ID'];
$entityFields = $arResult['ENTITY_FIELDS'];
$entityData = $arResult['ENTITY_DATA'];
$entityContext = $arResult['ENTITY_CONTEXT'];

$guid = $arResult['GUID'];
$innerWrapperClassName = 'order-lead-header-table order-lead-header-offer';
$quickPanelHeaderIcon = '';
if($entityTypeID === COrderOwnerType::Contact)
{
	$innerWrapperClassName .= ' order-lead-header-table-contact';
}
elseif($entityTypeID === COrderOwnerType::Company)
{
	$innerWrapperClassName .= ' order-lead-header-table-company';
}
elseif($entityTypeID === COrderOwnerType::Deal)
{
	$innerWrapperClassName .= ' order-lead-header-table-deal';
}
elseif($entityTypeID === COrderOwnerType::Lead)
{
	$innerWrapperClassName .= ' order-lead-header-table-lid';
}
elseif($entityTypeID === COrderOwnerType::Invoice)
{
	$innerWrapperClassName .= ' order-lead-header-table-bill';
}
$config = $arResult['CONFIG'];

$isExpanded = $config['expanded'] === 'Y';
$isFixed = $config['fixed'] === 'Y';

$headerConfig = array();
if(!function_exists('__OrderQuickPanelViewRenderSection'))
{
	function __OrderQuickPanelViewRenderSection($sectionID, &$config, &$entityData, &$entityFields, &$entityContext, $panelID)
	{
		if(!isset($config[$sectionID]))
		{
			return;
		}

		$sectionConfig = $config[$sectionID];
		$sectionConfig = $sectionConfig !== '' ? explode(',', $sectionConfig) : array();
		foreach($sectionConfig as $fieldID)
		{
			$fieldID = trim($fieldID);
			if(!isset($entityData[$fieldID]))
			{
				continue;
			}

			$fieldData = $entityData[$fieldID];
			$type = isset($fieldData['type']) ? $fieldData['type'] : '';
			$data = isset($fieldData['data']) ? $fieldData['data'] : array();
			$enableCaption = isset($fieldData['enableCaption']) ? $fieldData['enableCaption'] : true;
			$editable = $enableEditButton = isset($fieldData['editable']) ? $fieldData['editable'] : false;
			$visible = isset($fieldData['visible']) ? $fieldData['visible'] : true;

			$containerID = $panelID.'_'.$sectionID.'_'.strtolower($fieldID);
			echo '<tr id="', htmlspecialcharsbx($containerID), '"',
				$visible ? '' : ' style="display:none;"',
				'>';

			echo '<td class="order-lead-header-inner-cell order-lead-header-inner-cell-move"><div class="order-lead-header-inner-move-btn"></div></td>';
			if($enableCaption)
			{
				echo '<td class="order-lead-header-inner-cell order-lead-header-inner-cell-title">';
				echo isset($fieldData['caption']) ? htmlspecialcharsbx($fieldData['caption']) : $fieldID;
				echo '</td>';
				if($sectionID !== 'bottom')
				{
					echo '<td class="order-lead-header-inner-cell">';
				}
				else
				{
					echo '<td class="order-lead-header-inner-cell order-lead-header-com-cell">';
				}
			}
			else
			{
				if($sectionID !== 'bottom')
				{
					echo '<td class="order-lead-header-inner-cell order-lead-header-inf-block" colspan="2">';
				}
				else
				{
					echo '<td class="order-lead-header-inner-cell order-lead-header-com-cell order-lead-header-inf-block" colspan="2">';
				}
			}

			if($type === 'datetime')
			{
				echo '<div class="order-lead-header-date-wrapper">';
				echo '<div class="order-lead-header-date-view-wrapper">', isset($data['text']) ? htmlspecialcharsbx($data['text']) : '', '</div>';
				echo '<div class="order-lead-header-date-edit-wrapper" style="display: none;"></div>';
				echo '</div>';
			}
			elseif($type === 'boolean')
			{
				if(isset($data['baseType']) && $data['baseType'] === 'char')
				{
					$checked = isset($data['value']) && strtoupper($data['value']) === 'Y';
				}
				else
				{
					$checked = isset($data['value']) && $data['value'] > 0;
				}
				echo '<div class="order-lead-header-boolean-wrapper">';
				echo '<div class="order-lead-header-boolean-view-wrapper">', GetMessage($checked ? 'MAIN_YES' : 'MAIN_NO'), '</div>';
				echo '<div class="order-lead-header-boolean-edit-wrapper" style="display: none;"></div>';
				echo '</div>';
			}
			elseif($type === 'enumeration')
			{
				echo '<div class="order-lead-header-enumeration-wrapper">';
				echo '<div class="order-lead-header-enumeration-view-wrapper">', $data['text'] !== '' ? htmlspecialcharsbx($data['text']) : GetMessage('ORDER_ENTITY_QPV_CONTROL_NOT_SELECTED'), '</div>';
				echo '<div class="order-lead-header-enumeration-edit-wrapper" style="display: none;"></div>';
				echo '</div>';
			}
			elseif($type === 'link')
			{
				echo '<div class="order-lead-header-link-wrapper">';
				$text = isset($data['text']) ? htmlspecialcharsbx($data['text']) : '';
				$url = isset($data['url']) ? htmlspecialcharsbx($data['url']) : '';
				echo '<a class="order-link" target="_blank" href="', $url, '">', $text, '</a>';
				echo '</div>';
			}
			elseif($type === 'multiField')
			{
				$typeName = isset($data['type']) ? $data['type'] : array();
				$options = isset($entityContext['MULTI_FIELDS_OPTIONS'])? $entityContext['MULTI_FIELDS_OPTIONS'] : array();
				$options['TOPMOST'] = true;
				echo COrderViewHelper::PrepareFormMultiField(
					$entityFields,
					$typeName,
					strtolower($panelID).'_'.uniqid(),
					null,
					$options
				);
			}
			elseif($type === 'address')
			{
				$lines = isset($data['lines']) ? $data['lines'] : array();
				$lineQty = count($lines);

				if($lineQty > 0)
				{
					if($sectionID === 'bottom')
					{
						echo '<div class="order-lead-header-lhe-wrapper">';
						echo '<div class="order-lead-header-lhe-view-wrapper">';
						echo implode(', ', $lines);
						echo '</div>';
						echo '</div>';
					}
					else
					{
						$className = $lineQty > 1
							? "order-detail-info-item-text order-detail-info-item-list"
							: "order-detail-info-item-text";
						echo '<span class="', $className , '">';
						echo $lines[0];

						if($lineQty > 1)
						{
							echo '<span class="order-item-tel-list"></span>';
						}
						echo '</span>';
					}
				}
			}
			elseif($type === 'responsible')
			{
				if($enableEditButton)
				{
					$enableEditButton = false;
				}

				$guid = strtolower($panelID).'_'.strtolower($data['fieldID']).'_'.uniqid();
				COrderViewHelper::RenderResponsiblePanel(
					array(
						'FIELD_ID' => $data['fieldID'],
						'USER_ID' => $data['userID'],
						'NAME' => $data['name'],
						'PHOTO' => $data['photoID'],
						'PHOTO_URL' => $data['photoUrl'],
						'WORK_POSITION' => $data['position'],
						'USER_PROFILE_URL_TEMPLATE' => $data['profileUrlTemplate'],
						'PREFIX' => $guid,
						'EDITABLE' => $editable,
						'INSTANT_EDITOR_ID' => $data['editorID'],
						'SERVICE_URL' => $data['serviceUrl'],
						'USER_INFO_PROVIDER_ID' => $data['userInfoProviderId'],
						'ENABLE_LAZY_LOAD'=> true,
						'USER_SELECTOR_NAME' => $guid
					)
				);
			}
			elseif($type === 'client')
			{
				$entityTypeName = isset($data['ENTITY_TYPE_NAME']) ? $data['ENTITY_TYPE_NAME'] : '';
				$key = $entityTypeName === COrderOwnerType::CompanyName ? 'COMPANY_INFO' : 'CONTACT_INFO';
				$entityInfo = isset($entityContext[$key]) ? $entityContext[$key] : null;
				if(is_array($entityInfo))
				{
					$data['PREFIX'] = strtolower($panelID) . '_' . strtolower($fieldID) . '_' . uniqid();
					if(isset($entityInfo['FM']))
					{
						$data['FM'] = $entityInfo['FM'];
					}

					//if(isset($fieldData['caption']))
					//{
					//	$data['TITLE'] = $fieldData['caption'];
					//}

					$options = isset($entityInfo['MULTI_FIELDS_OPTIONS']) ? $entityInfo['MULTI_FIELDS_OPTIONS'] : array();
					$options['TOPMOST'] = true;
					COrderViewHelper::RenderClientSummaryPanel($data, $options);
				}
			}
			elseif($type === 'html')
			{
				echo '<div class="order-lead-header-lhe-wrapper">';
				echo '<div class="order-lead-header-lhe-view-wrapper">', isset($data['html']) ? $data['html'] : '', '</div>';
				echo '<div class="order-lead-header-lhe-edit-wrapper" style="display: none;"></div>';
				echo '</div>';
			}
			elseif($type === 'custom')
			{
				echo '<div class="order-lead-header-custom-wrapper">';
				if(isset($data['html']))
				{
					echo $data['html'];
				}
				echo '</div>';
			}
			elseif($type === 'text')
			{
				$html = isset($data['text']) ? htmlspecialcharsbx($data['text']) : '';
				if(isset($data['multiline']) && $data['multiline'])
				{
					$html = preg_replace('/(\n)/', '<br/>', $html);
				}

				echo '<div class="order-lead-header-text-wrapper">';
				echo '<div class="order-lead-header-text-view-wrapper">', $html, '</div>';
				echo '<div class="order-lead-header-text-edit-wrapper" style="display: none;"></div>';
				echo '</div>';
			}
			else
			{
				echo '<div class="order-lead-header-text-wrapper">';
				echo '<div class="order-lead-header-text-view-wrapper">', isset($data['text']) ? htmlspecialcharsbx($data['text']) : '', '</div>';
				echo '<div class="order-lead-header-text-edit-wrapper" style="display: none;"></div>';
				echo '</div>';
			}
			echo '</td>';

			echo '<td class="order-lead-header-inner-cell order-lead-header-inner-cell-del">';
			//echo '<div class="order-lead-header-inner-del-btn"></div>';
			if($enableEditButton)
			{
				echo '<div class="order-lead-header-inner-edit-btn"></div>';
			}
			echo '</td>';

			echo '</tr>';
		}
	}
}
?><div id="<?="{$guid}_placeholder"?>" class="order-lead-header-table-placeholder">
<div id="<?="{$guid}_wrap"?>" class="order-lead-header-table-wrap">
	<div class="order-lead-header-table-inner-wrap">
		<table id="<?="{$guid}_inner_wrap"?>" class="<?=$innerWrapperClassName?>">
		<tbody>
			<tr id="<?="{$guid}_header"?>">
				<td class="order-lead-header-header" colspan="3">
					<div class="order-lead-header-header-left"><div class="order-lead-header-left-inner">
					<span class="order-lead-header-icon"></span><?
					if($entityTypeID === COrderOwnerType::Contact):
						$formattedName = isset($entityFields['FORMATTED_NAME']) ? $entityFields['FORMATTED_NAME'] : '';
						if($arResult['HEAD_IMAGE_URL'] !== ''):
							?><span class="order-lead-header-img">
								<img alt="" src="<?=htmlspecialcharsbx($arResult['HEAD_IMAGE_URL'])?>" />
							</span><?
						endif;
					?><div class="order-lead-header-title">
						<span class="order-lead-header-title-text"><?=$formattedName?></span>
					</div><?
					elseif($entityTypeID === COrderOwnerType::Company):
						$title = $arResult['HEAD_TITLE'];
						$headerConfig['TITLE'] = array('fieldId' => $arResult['HEAD_TITLE_FIELD_ID']);
						if($arResult['HEAD_IMAGE_URL'] !== ''):
						?><div class="order-lead-header-company-img">
							<img alt="" src="<?=htmlspecialcharsbx($arResult['HEAD_IMAGE_URL'])?>" />
						</div><?
						endif;
						?><div class="order-lead-header-company-title">
							<div id="<?="{$guid}_title"?>" class="order-lead-header-title">
								<span class="order-lead-header-title-text"><?=$title?></span>
								<span class="order-lead-header-title-edit-wrapper" style="display: none;"></span>
								<div class="order-lead-header-title-edit"></div>
							</div>
						</div><?
					elseif($entityTypeID === COrderOwnerType::Deal || $entityTypeID === COrderOwnerType::Lead || $entityTypeID === COrderOwnerType::Quote || $entityTypeID === COrderOwnerType::Invoice):
						$title = $arResult['HEAD_TITLE'];
						$headerConfig['TITLE'] = array('fieldId' => $arResult['HEAD_TITLE_FIELD_ID']);
						?><div id="<?="{$guid}_title"?>" class="order-lead-header-title">
							<span class="order-lead-header-title-text"><?=$title?></span>
						<span class="order-lead-header-title-edit-wrapper" style="display: none;"></span>
							<div class="order-lead-header-title-edit"></div>
						</div><?
					endif;
					?></div></div>
					<div class="order-lead-header-header-right"><div class="order-lead-header-right-inner"><?
						if($entityTypeID === COrderOwnerType::Deal || $entityTypeID === COrderOwnerType::Lead || $entityTypeID === COrderOwnerType::Quote || $entityTypeID === COrderOwnerType::Invoice):
							$headerConfig['SUM'] = array('fieldId' => $arResult['HEAD_SUM_FIELD_ID']);
							?><div id="<?="{$guid}_progress"?>" class="order-lead-header-status">
								<span id="<?="{$guid}_progress_legend"?>" class="order-lead-header-status-title"><?=htmlspecialcharsbx($arResult['HEAD_PROGRESS_LEGEND'])?></span>
									<div class="order-detail-stage"><?=$arResult['HEAD_PROGRESS_BAR']?></div>
									<span id="<?="{$guid}_sum"?>" class="order-lead-header-status-sum"><?=GetMessage('ORDER_ENTITY_QPV_SUM_HEADER')?>: <span class="order-lead-header-status-sum-num"><?=$arResult['HEAD_FORMATTED_SUM']?></span></span>
							</div><?
						endif;
						?><div class="order-lead-header-contact-btns">
							<span id="<?="{$guid}_menu_btn"?>" class="order-lead-header-contact-btn order-lead-header-contact-btn-menu"></span>
							<span id="<?="{$guid}_pin_btn"?>" class="order-lead-header-contact-btn <?=$isFixed ? 'order-lead-header-contact-btn-pin' : 'order-lead-header-contact-btn-unpin'?>"></span>
							<span id="<?="{$guid}_toggle_btn"?>" class="order-lead-header-contact-btn <?=$isExpanded ? 'order-lead-header-contact-btn-open' : 'order-lead-header-contact-btn-close'?>"></span>
						</div>
					</div></div>
				</td>
			</tr>
			<tr>
				<td class="order-lead-header-white" colspan="3"></td>
			</tr>
			<tr>
				<td class="order-lead-header-blue" colspan="3"></td>
			</tr>
			<tr>
				<td class="order-lead-header-cell">
					<table id="<?="{$guid}_left_container"?>" class="order-lead-header-inner-table">
						<tbody>
							<colgroup>
								<col class="order-lead-header-inner-cell order-lead-header-inner-cell-move" />
								<col class="order-lead-header-inner-cell order-lead-header-inner-cell-title" />
								<col class="order-lead-header-inner-cell" />
								<col class="order-lead-header-inner-cell order-lead-header-inner-cell-del" />
							</colgroup>
							<?__OrderQuickPanelViewRenderSection('left', $config, $entityData, $entityFields, $entityContext, $guid);?>
						</tbody>
					</table>
				</td>
				<td class="order-lead-header-cell">
					<table id="<?="{$guid}_center_container"?>" class="order-lead-header-inner-table">
						<tbody>
							<colgroup>
								<col class="order-lead-header-inner-cell order-lead-header-inner-cell-move" />
								<col class="order-lead-header-inner-cell order-lead-header-inner-cell-title" />
								<col class="order-lead-header-inner-cell" />
								<col class="order-lead-header-inner-cell order-lead-header-inner-cell-del" />
							</colgroup>
							<?__OrderQuickPanelViewRenderSection('center', $config, $entityData, $entityFields, $entityContext, $guid);?>
						</tbody>
					</table>
				</td>
				<td class="order-lead-header-cell">
					<table id="<?="{$guid}_right_container"?>" class="order-lead-header-inner-table">
						<tbody>
							<colgroup>
								<col class="order-lead-header-inner-cell order-lead-header-inner-cell-move" />
								<col class="order-lead-header-inner-cell order-lead-header-inner-cell-title" />
								<col class="order-lead-header-inner-cell" />
								<col class="order-lead-header-inner-cell order-lead-header-inner-cell-del" />
							</colgroup>
							<?__OrderQuickPanelViewRenderSection('right', $config, $entityData, $entityFields, $entityContext, $guid);?>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td class="order-lead-header-cell order-lead-header-comments" colspan="3">
					<table id="<?="{$guid}_bottom_container"?>" class="order-lead-header-inner-table">
						<tbody>
							<colgroup>
								<col class="order-lead-header-inner-cell order-lead-header-inner-cell-move" />
								<col class="order-lead-header-inner-cell order-lead-header-inner-cell-title" />
								<col class="order-lead-header-inner-cell order-lead-header-com-cell" />
								<col class="order-lead-header-inner-cell order-lead-header-inner-cell-del" />
							</colgroup>
							<?__OrderQuickPanelViewRenderSection('bottom', $config, $entityData, $entityFields, $entityContext, $guid);?>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	</div>
</div>
</div>
<div id="<?="{$guid}_message_wrap"?>"></div>
<?

$sipData = isset($entityContext['SIP_MANAGER_CONFIG']) ? $entityContext['SIP_MANAGER_CONFIG'] : array();
if(!empty($sipData)):
?><script type="text/javascript">
	BX.ready(
			function()
			{
				var mgr = BX.OrderSipManager.getCurrent();<?
				foreach($sipData as $item):
				?>
				mgr.setServiceUrl(
					"ORDER_<?=CUtil::JSEscape($item['ENTITY_TYPE'])?>",
					"<?=isset($item['SERVICE_URL']) ? CUtil::JSEscape($item['SERVICE_URL']) : ''?>"
				);<?
				endforeach;
				?>
				if(typeof(BX.OrderSipManager.messages) === 'undefined')
				{
					BX.OrderSipManager.messages =
					{
						"unknownRecipient": "<?= GetMessageJS('ORDER_ENTITY_QPV_SIP_MGR_UNKNOWN_RECIPIENT')?>",
						"enableCallRecording": "<?= GetMessageJS('ORDER_ENTITY_QPV_SIP_MGR_ENABLE_CALL_RECORDING')?>",
						"makeCall": "<?= GetMessageJS('ORDER_ENTITY_QPV__SIP_MGR_MAKE_CALL')?>"
					};
				}
			}
	);
</script><?
endif;
?><script type="text/javascript">
	BX.ready(
		function() {
			BX.OrderQuickPanelModel.messages =
			{
				notSelected: "<?=GetMessageJS('ORDER_ENTITY_QPV_NOT_SELECTED')?>"
			};

			BX.OrderQuickPanelItem.messages =
			{
				editMenuItem: "<?=CUtil::JSEscape(GetMessage('ORDER_ENTITY_QPV_EDIT_CONTEXT_MENU_ITEM'))?>",
				deleteMenuItem: "<?=CUtil::JSEscape(GetMessage('ORDER_ENTITY_QPV_DELETE_CONTEXT_MENU_ITEM'))?>",
				deletionConfirmation: "<?=GetMessageJS('ORDER_ENTITY_QPV_DELETION_CONFIRMATION')?>"
			};

			BX.OrderQuickPanelControl.messages =
			{
				dataNotSaved: "<?=GetMessageJS('ORDER_ENTITY_QPV_CONTROL_FIELD_DATA_NOT_SAVED')?>",
				notSelected: "<?=GetMessageJS('ORDER_ENTITY_QPV_NOT_SELECTED')?>",
				yes: "<?=GetMessageJS('MAIN_YES')?>",
				no: "<?=GetMessageJS('MAIN_NO')?>"
			};

			BX.OrderQuickPanelResponsible.messages =
			{
				change: "<?=GetMessageJS('ORDER_ENTITY_QPV_RESPONSIBLE_CHANGE')?>"
			};

			BX.OrderQuickPanelClientInfo.messages =
			{
				contactNotSelected: "<?=GetMessageJS('ORDER_ENTITY_QPV_CONTACT_NOT_SELECTED')?>",
				companyNotSelected: "<?=GetMessageJS('ORDER_ENTITY_QPV_COMPANY_NOT_SELECTED')?>"
			};

			BX.OrderQuickPanelView.messages =
			{
				resetMenuItem: "<?=CUtil::JSEscape(GetMessage('ORDER_ENTITY_QPV_RESET_MENU_ITEM'))?>",
				saveForAllMenuItem: "<?=CUtil::JSEscape(GetMessage('ORDER_ENTITY_QPV_SAVE_FOR_ALL_MENU_ITEM'))?>",
				resetForAllMenuItem: "<?=CUtil::JSEscape(GetMessage('ORDER_ENTITY_QPV_RESET_FOR_ALL_MENU_ITEM'))?>",
				dragDropErrorTitle: "<?=CUtil::JSEscape(GetMessage('ORDER_ENTITY_QPV_DRAG_DROP_ERROR_TITLE'))?>",
				dragDropErrorFieldNotSupported: "<?=CUtil::JSEscape(GetMessage('ORDER_ENTITY_QPV_DRAG_DROP_ERROR_FIELD_NOT_SUPPORTED'))?>",
				dragDropErrorFieldAlreadyExists: "<?=CUtil::JSEscape(GetMessage('ORDER_ENTITY_QPV_DRAG_DROP_ERROR_FIELD_ALREADY_EXISTS'))?>"
			};

			BX.OrderQuickPanelView.create(
				"<?=CUtil::JSEscape($guid)?>",
				{
					entityTypeName: "<?=CUtil::JSEscape($entityTypeName)?>",
					entityId: <?=CUtil::JSEscape($entityID)?>,
					prefix: "<?=CUtil::JSEscape($guid)?>",
					canSaveSettingsForAll: <?=$arResult['CAN_EDIT_OTHER_SETTINGS'] ? 'true' : 'false'?>,
					formId: "<?=CUtil::JSEscape($arResult['FORM_ID'])?>",
					entityData: <?=CUtil::PhpToJSObject($arResult['ENTITY_DATA'])?>,
					config: <?=CUtil::PhpToJSObject($config)?>,
					headerConfig: <?=CUtil::PhpToJSObject($headerConfig)?>,
					enableInstantEdit: <?=$arResult['ENABLE_INSTANT_EDIT'] ? 'true' : 'false'?>
				}
			);

			BX.OrderDragDropBin.messages =
			{
				prompting: "<?=GetMessageJS("ORDER_ENTITY_QPV_DD_BIN_PROMPTING")?>"
			};
			BX.OrderDragDropBin.getInstance().showPromptingIfRequired(BX("<?="{$guid}_message_wrap"?>"));
		}
	);
</script>