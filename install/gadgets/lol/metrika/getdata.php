<?
define("BX_SECURITY_SHOW_MESSAGE", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
__IncludeLang(dirname(__FILE__)."/lang/".LANGUAGE_ID."/getdata.php");

if(!CModule::IncludeModule("lol.metrika"))
	die();

if($GLOBALS["APPLICATION"]->GetGroupRight("lol.metrika")=="D")
	die();

if (strlen($_REQUEST["counter_id"]) > 0)
{
	$counter_filter = "Y";
	$strFilterSite = "&amp;find_counter_id=".$_REQUEST["counter_id"];
}
else
{
	$counter_filter = "N";
	$strFilterSite = "";
}

$arFilter = Array(
	"id"	=> $_REQUEST["counter_id"],
);

$now_date = GetTime(time());
$yesterday_date = GetTime(time()-86400);
$bef_yesterday_date = GetTime(time()-172800);

$arFilter["limit"]=5;

if($_REQUEST["table_id"] == "sites"):
	$arrResult=array();
	$arFilter["date1"]=$bef_yesterday_date;
	$arFilter["date2"]=$bef_yesterday_date;
	$arrData=CLOLYandexMetrika::GetSourcesSites($arFilter);
	foreach($arrData["data"] as $arData)
	{
		$arrResult[$arData["url"]]["B_YESTERDAY"]=$arData["visits"];
	}
	
	$arFilter["date1"]=$yesterday_date;
	$arFilter["date2"]=$yesterday_date;
	$arrData=CLOLYandexMetrika::GetSourcesSites($arFilter);
	foreach($arrData["data"] as $arData)
	{
		$arrResult[$arData["url"]]["YESTERDAY"]=$arData["visits"];
	}	
	
	$arFilter["date1"]=$now_date;
	$arFilter["date2"]=$now_date;
	$arrData=CLOLYandexMetrika::GetSourcesSites($arFilter);
	foreach($arrData["data"] as $arData)
	{
		$arrResult[$arData["url"]]["TODAY"]=$arData["visits"];
	}	
		
	?>
	<table class="bx-gadgets-table">
	<tbody>
	<tr>
		<th><?=GetMessage("GD_STAT_SERVER")?></th>
		<th><a href="/bitrix/admin/lol_metrika_sources_sites.php?lang=<?=$_REQUEST["lang"]?><?=$strFilterSite?>&amp;find_date1=<?=$now_date?>&amp;find_date2=<?=$now_date?>&amp;set_filter=Y"><?=GetMessage("GD_STAT_TODAY")?></a><br><?=$now_date?></th>
		<th><a href="/bitrix/admin/lol_metrika_sources_sites.php?lang=<?=$_REQUEST["lang"]?><?=$strFilterSite?>&amp;find_date1=<?=$yesterday_date?>&amp;find_date2=<?=$yesterday_date?>&amp;set_filter=Y"><?=GetMessage("GD_STAT_YESTERDAY")?></a><br><?=$yesterday_date?></th>
		<th><a href="/bitrix/admin/lol_metrika_sources_sites.php?lang=<?=$_REQUEST["lang"]?><?=$strFilterSite?>&amp;find_date1=<?=$bef_yesterday_date?>&amp;find_date2=<?=$bef_yesterday_date?>&amp;set_filter=Y"><?=GetMessage("GD_STAT_BEFORE_YESTERDAY")?></a><br><?=$bef_yesterday_date?></th>
	</tr>
	<?
	foreach ($arrResult as $sReferer=>$arReferer):
		?>
		<tr>
			<td><a href="<?=$sReferer?>" target="_blank"><?=parse_url($sReferer, PHP_URL_HOST)?></a></td>
			<td align="center"><?=$arReferer["B_YESTERDAY"]?></td><td>
			<td align="center"><?=$arReferer["YESTERDAY"]?></td>
			<td align="center"><?=$arReferer["TODAY"]?></td>
		</tr>
		<?
	endforeach;
	?>
	</tbody>
	</table>
	<?
elseif($_REQUEST["table_id"] == "phrases"):
	$arrResult=array();
	$arFilter["date1"]=$bef_yesterday_date;
	$arFilter["date2"]=$bef_yesterday_date;
	$arrData=CLOLYandexMetrika::GetSourcesPhrases($arFilter);
	foreach($arrData["data"] as $arData)
	{
		$arrResult[$arData["phrase"]]["LINK"]=$arData["search_engines"][0]["se_url"];
		$arrResult[$arData["phrase"]]["B_YESTERDAY"]=$arData["visits"];
	}
	
	$arFilter["date1"]=$yesterday_date;
	$arFilter["date2"]=$yesterday_date;
	$arrData=CLOLYandexMetrika::GetSourcesPhrases($arFilter);
	foreach($arrData["data"] as $arData)
	{
		$arrResult[$arData["phrase"]]["LINK"]=$arData["search_engines"][0]["se_url"];
		$arrResult[$arData["phrase"]]["YESTERDAY"]=$arData["visits"];
	}	
	
	$arFilter["date1"]=$now_date;
	$arFilter["date2"]=$now_date;
	$arrData=CLOLYandexMetrika::GetSourcesPhrases($arFilter);
	foreach($arrData["data"] as $arData)
	{
		$arrResult[$arData["phrase"]]["LINK"]=$arData["search_engines"][0]["se_url"];
		$arrResult[$arData["phrase"]]["TODAY"]=$arData["visits"];
	}	
		
	?>
	<table class="bx-gadgets-table">
	<tbody>
	<tr>
		<th><?=GetMessage("GD_STAT_SERVER")?></th>
		<th><a href="/bitrix/admin/lol_metrika_sources_sites.php?lang=<?=$_REQUEST["lang"]?><?=$strFilterSite?>&amp;find_date1=<?=$now_date?>&amp;find_date2=<?=$now_date?>&amp;set_filter=Y"><?=GetMessage("GD_STAT_TODAY")?></a><br><?=$now_date?></th>
		<th><a href="/bitrix/admin/lol_metrika_sources_sites.php?lang=<?=$_REQUEST["lang"]?><?=$strFilterSite?>&amp;find_date1=<?=$yesterday_date?>&amp;find_date2=<?=$yesterday_date?>&amp;set_filter=Y"><?=GetMessage("GD_STAT_YESTERDAY")?></a><br><?=$yesterday_date?></th>
		<th><a href="/bitrix/admin/lol_metrika_sources_sites.php?lang=<?=$_REQUEST["lang"]?><?=$strFilterSite?>&amp;find_date1=<?=$bef_yesterday_date?>&amp;find_date2=<?=$bef_yesterday_date?>&amp;set_filter=Y"><?=GetMessage("GD_STAT_BEFORE_YESTERDAY")?></a><br><?=$bef_yesterday_date?></th>
	</tr>
	<?
	foreach ($arrResult as $sPhrase=>$arPhrase):
		?>
		<tr>
			<td><a href="<?=$arPhrase["LINK"]?>" target="_blank"><?=$sPhrase?></a></td>
			<td align="center"><?=$arPhrase["B_YESTERDAY"]?></td><td>
			<td align="center"><?=$arPhrase["YESTERDAY"]?></td>
			<td align="center"><?=$arPhrase["TODAY"]?></td>
		</tr>
		<?
	endforeach;
	?>
	</tbody>
	</table>
	<?
endif;
?>
