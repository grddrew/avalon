<?php
class COrderAuthorizationHelper
{
	private static $USER_PERMISSIONS = null;

	public static function GetUserPermissions()
	{
		if(self::$USER_PERMISSIONS === null)
		{
			self::$USER_PERMISSIONS = COrderPerms::GetCurrentUserPermissions();
		}

		return self::$USER_PERMISSIONS;
	}

	public static function CheckCreatePermission($enitityTypeName, $userPermissions = null)
	{
		$enitityTypeName = strval($enitityTypeName);

		if(!$userPermissions)
		{
			$userPermissions = self::GetUserPermissions();
		}

		return !$userPermissions->HavePerm($enitityTypeName, BX_ORDER_PERM_NONE, 'ADD');
	}

	public static function CheckUpdatePermission($enitityTypeName, $entityID, $userPermissions = null, $entityAttrs = null)
	{
		$enitityTypeName = strval($enitityTypeName);

		if(!$userPermissions)
		{
			$userPermissions = self::GetUserPermissions();
		}

		if($entityID <= 0)
		{
			return !$userPermissions->HavePerm($enitityTypeName, BX_ORDER_PERM_NONE, 'EDIT');
		}

		if(!is_array($entityAttrs))
		{
			$entityAttrs = $userPermissions->GetEntityAttr($enitityTypeName, $entityID);
		}
		return !$userPermissions->HavePerm($enitityTypeName, BX_ORDER_PERM_NONE, 'EDIT')
			&& $userPermissions->CheckEnityAccess($enitityTypeName, 'EDIT', isset($entityAttrs[$entityID]) ? $entityAttrs[$entityID] : array());
	}

	public static function CheckDeletePermission($enitityTypeName, $entityID, $userPermissions = null, $entityAttrs = null)
	{
		$enitityTypeName = strval($enitityTypeName);

		if($entityID =='')
		{
			return false;
		}

		if(!$userPermissions)
		{
			$userPermissions = self::GetUserPermissions();
		}

		if(!is_array($entityAttrs))
		{
			$entityAttrs = $userPermissions->GetEntityAttr($enitityTypeName, $entityID);
		}

		return !$userPermissions->HavePerm($enitityTypeName, BX_ORDER_PERM_NONE, 'DELETE')
			&& $userPermissions->CheckEnityAccess($enitityTypeName, 'DELETE', isset($entityAttrs[$entityID]) ? $entityAttrs[$entityID] : array());
	}

	public static function CheckReadPermission($enitityType, $entityID, $userPermissions = null, $entityAttrs = null)
	{
		$enitityTypeName = /*is_numeric($enitityType)
			? CCrmOwnerType::ResolveName($enitityType)
			: */strtoupper(strval($enitityType));


		if(!$userPermissions)
		{
			$userPermissions = self::GetUserPermissions();
		}

		if($entityID <= 0)
		{
			return !$userPermissions->HavePerm($enitityTypeName, BX_ORDER_PERM_NONE, 'READ');
		}

		if(!is_array($entityAttrs))
		{
			$entityAttrs = $userPermissions->GetEntityAttr($enitityTypeName, $entityID);
		}

		return !$userPermissions->HavePerm($enitityTypeName, BX_ORDER_PERM_NONE, 'READ')
			&& $userPermissions->CheckEnityAccess($enitityTypeName, 'READ', isset($entityAttrs[$entityID]) ? $entityAttrs[$entityID] : array());
	}

	public static function CheckConfigurationUpdatePermission($userPermissions = null)
	{
		if(!$userPermissions)
		{
			$userPermissions = self::GetUserPermissions();
		}

		return $userPermissions->HavePerm('CONFIG', BX_ORDER_PERM_CONFIG, 'WRITE');
	}
	public static function CheckConfigurationReadPermission($userPermissions = null)
	{
		if(!$userPermissions)
		{
			$userPermissions = self::GetUserPermissions();
		}

		return $userPermissions->HavePerm('CONFIG', BX_ORDER_PERM_CONFIG, 'READ');
	}
	public static function CanEditOtherSettings($user = null)
	{
		if(!($user !== null && ((get_class($user) === 'CUser') || ($user instanceof CUser))))
		{
			$user = COrderHelper::GetCurrentUser();
		}

		return $user->CanDoOperation('edit_other_settings');
	}
}
