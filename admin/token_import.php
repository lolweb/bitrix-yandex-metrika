<?
define("NOT_CHECK_PERMISSIONS",true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

if(strlen($_REQUEST["token"])>0)
{
	$token = $_REQUEST['token'];

	if ($token)
	{
		$strTokens = COPtion::GetOptionString('lol.metrika', 'tokens', '');
		$arTokens = array();
		if ($strTokens != '')
		{
			$arTokens = unserialize($strTokens);
		}

		$arTokens[] = $token;

		if (COption::SetOptionString('lol.metrika', 'tokens', serialize($arTokens)))
			LocalRedirect("settings.php?lang=".LANG."&mid=lol.metrika&mid_menu=1&strOK=".urlencode("Токен успешно добавлен"));
	}
}
else
	LocalRedirect("settings.php?lang=".LANG."&mid=lol.metrika&mid_menu=1?strWarning=".urlencode("Ошибка добавления токена"));

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_after.php");
?>