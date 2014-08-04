<?
$module_id = "lol.metrika";
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/lol.metrika/include.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/admin/task_description.php");

CUtil::InitJSCore();

/*
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_REQUEST['save_token']) && check_bitrix_sessid())
{
	$APPLICATION->RestartBuffer();

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
			echo 'OK';
	}

	die();
}*/

if (!$USER->CanDoOperation('lol_metrika_view_all_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

function isValidLang($lang)
{
	$rsLang = CLanguage::GetList($by="sort", $order="desc");
	$is_valid_lang = false;
	while ($arLang = $rsLang->Fetch())
	{
		if ($lang==$arLang["LID"])
		{
			$is_valid_lang = true;
			break;
		}
	}
	return $is_valid_lang;
}

if ($REQUEST_METHOD=="GET" && $USER->CanDoOperation('lol_metrika_edit_all_settings') && strlen($RestoreDefaults)>0 && check_bitrix_sessid())
{
	COption::RemoveOption("lol.metrika");
	$z = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
	while($zr = $z->Fetch())
		$APPLICATION->DelGroupRight($module_id, array($zr["ID"]));
}


global $MESS;
IncludeModuleLangFile(__FILE__);

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_TOKENS"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_TOKENS")),
	array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
	array("DIV" => "edit3", "TAB" => GetMessage("MAIN_TAB_ABOUT"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_ABOUT")),
	);

$siteList = array();
$rsSites = CSite::GetList($by="sort", $order="asc", Array());
$i = 0;
while($arRes = $rsSites->Fetch())
{
	$siteList[$i]["ID"] = $arRes["ID"];
	$siteList[$i]["NAME"] = $arRes["NAME"];
	$i++;
}
$siteCount = $i;

unset($rsSites);
unset($arRes);

$tabControl = new CAdmintabControl("tabControl", $aTabs);


if($REQUEST_METHOD == "POST" && strlen($Update)>0 && $USER->CanDoOperation('lol_metrika_edit_all_settings') && check_bitrix_sessid())
{
	$arTokens = array();
	if (isset($_POST['tokens']))
	{
		$arTokens = $_POST['tokens'];
		foreach ($arTokens as $k => $token)
		{
			$token = trim($token);
			if (strlen($token) <= 0)
				unset($arTokens[$k]);
			else
				$arTokens[$k] = $token;
		}
	}

	$value = (count($arTokens) <= 0) ? '' : serialize($arTokens);
	COption::SetOptionString('lol.metrika', 'tokens', $value);

	ob_start();
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights2.php");
	ob_end_clean();

	if($strError=="")
	{
		if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0)
			LocalRedirect($_REQUEST["back_url_settings"]);
		else
			LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
	}

}

if(strlen($strWarning)>0)
	CAdminMessage::ShowMessage($strWarning);

if(strlen($strOK)>0)
	CAdminMessage::ShowNote($strOK);


$tabControl->Begin();

?>

<form method="POST" enctype="multipart/form-data" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?echo LANG?>">
<?=bitrix_sessid_post()?>
<?if(strlen($_REQUEST["back_url_settings"])>0):?>
	<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
<?endif?>
<?$tabControl->BeginNextTab();
?>
	<tr class="heading">
		<td colspan="2">
			<?echo GetMessage("METRIKA_OPTION_TOKENS_TITLE");?>
		</td>
	</tr>
<?
	$strTokens = COPtion::GetOptionString('lol.metrika', 'tokens', '');

	$arTokens = array();
	if ($strTokens != '')
	{
		$arTokens = unserialize($strTokens);
	}

	if (is_array($arTokens) && count($arTokens) > 0)
	{
		foreach ($arTokens as $k=>$token)
		{
?>
			<tr>
				<td width="100%"><input type="text" size="50" name="tokens[<?=$k?>]" value="<?echo htmlspecialchars($token)?>" /></td>
			</tr>
<?
		}
	}
	else
	{
?>
	<tr>
		<td align="center" colspan="2"><?echo BeginNote(),GetMessage('METRIKA_OPTION_TOKENS_NOTOKENS'),EndNote();?></td>
	</tr>
<?
	}
?>
	<tr><td colspan="2"><table width="50%" align="center">
		<tr class="heading"><td width="100%"><?= GetMessage('METRIKA_OPTION_TOKENS_TOKEN_ADD')?></td></tr>
		<tr>
			<td><input type="text" style="width: 100%;" name="tokens[]" value="" /></td>
		</tr>
		
			<tr>
				<td width="100%"><input type="button" name="new_token" value="<?=GetMessage('METRIKA_OPTION_TOKENS_NEW')?>" onClick="NewToken();" /></td>
			</tr>	
		
	</table></td></tr>
	
<?$tabControl->BeginNextTab();?>
<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights2.php");?>

<?$tabControl->BeginNextTab();?>
	<tr>
		<td colspan="2"><?echo GetMessage('METRIKA_OPTION_ABOUT_LOL')?></td>
	</tr>


	<tr>
		<td colspan="2"><?echo BeginNote(),GetMessage('METRIKA_OPTION_ABOUT_YANDEX'),EndNote();?></td>
	</tr>

<?$tabControl->Buttons();?>
<script>
	function RestoreDefaults()
	{
		if(confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
			window.location = "<?= $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANG?>&mid=<?echo urlencode($mid)?>&<?=bitrix_sessid_get()?>";
	}
	
	function NewToken()
	{
		window.location = "https://oauth.yandex.ru/authorize?response_type=token&client_id=<?=LOL_METRIKA_CLIENT_ID?>&state=<?=urlencode("http://".$_SERVER["SERVER_NAME"])?>";
	}
</script>
<input type="submit" <?if (!$USER->CanDoOperation('lol_metrika_edit_all_settings')) echo "disabled" ?> name="Update" value="<?= GetMessage('METRIKA_OPTION_SAVE')?>">
<input type="reset" name="reset" value="<?= GetMessage('METRIKA_OPTION_RESET')?>">
<input type="hidden" name="Update" value="Y">
<input <?if (!$USER->CanDoOperation('lol_metrika_edit_all_settings')) echo "disabled" ?> type="button" title="<?= GetMessage('METRIKA_OPTION_RESTORE_DEFAULTS')?>" OnClick="RestoreDefaults();" value="<?= GetMessage('METRIKA_RESTORE_DEFAULTS')?>">
<?$tabControl->End();?>
</form>
