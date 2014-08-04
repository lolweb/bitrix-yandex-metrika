<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<?
if(!CModule::IncludeModule("lol.metrika"))
	return false;

if($GLOBALS["APPLICATION"]->GetGroupRight("lol.metrika")=="D")
	return false;

include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/lol.metrika/colors.php");

$arGadgetParams["RND_STRING"] = randString(8);

$arGadgetParams["HIDE_GRAPH"] = ($arGadgetParams["HIDE_GRAPH"] == "Y" ? "Y" : "N");

if ($arGadgetParams["HIDE_GRAPH"] != "Y")
{
	if (intval($arGadgetParams["GRAPH_DAYS"]) <= 0 || intval($arGadgetParams["GRAPH_DAYS"]) > 400)
		$arGadgetParams["GRAPH_DAYS"] = 30;

	if (!is_array($arGadgetParams["GRAPH_PARAMS"])
		|| count($arGadgetParams["GRAPH_PARAMS"]) <= 0
	)
		$arGadgetParams["GRAPH_PARAMS"] = array("HOST", "SESSION", "EVENT", "GUEST");

	if (intval($arGadgetParams["GRAPH_WIDTH"]) <= 50 || intval($arGadgetParams["GRAPH_WIDTH"]) > 1000)
		$arGadgetParams["GRAPH_WIDTH"] = 500;
	if (intval($arGadgetParams["GRAPH_HEIGHT"]) <= 50 || intval($arGadgetParams["GRAPH_HEIGHT"]) > 1000)
		$arGadgetParams["GRAPH_HEIGHT"] = 300;
}

$arrCounter=CLOLYandexMetrika::GetCounterList();

if (strlen($arGadgetParams["COUNTER_ID"]) <= 0)
	$arGadgetParams["COUNTER_ID"] = false;
elseif (strlen($arGadgetParams["TITLE_STD"]) <= 0)
{
	foreach($arrCounter as $arCounter)
	{
		if($arCounter["ID"]==$arGadgetParams["COUNTER_ID"])
		{
			$arGadget["TITLE"] .= " / [".$arCounter["SITE"]."] ".$arSite["NAME"];
			break;
		}
	}	
}

if(!$arGadgetParams["COUNTER_ID"] && count($arrCounter)>0)
{
	$arGadgetParams["COUNTER_ID"]=$arrCounter[0]["ID"];
}
elseif(!$arGadgetParams["COUNTER_ID"])
{
	?><div class="bx-gadgets-content-padding-rl bx-gadgets-content-padding-t"><?=CAdminMessage::ShowMessage(GetMessage("STAT_NOT_ENOUGH_DATA"));?></div><?
	return;
}

$now_date = GetTime(time());
$yesterday_date = GetTime(time()-86400);
$bef_yesterday_date = GetTime(time()-172800);

$arFilter = array();
$arFilter["id"] = $arGadgetParams["COUNTER_ID"];
$strFilterCounter = "&counter_id=".$arGadgetParams["COUNTER_ID"];

$arCommData = $arrData=CLOLYandexMetrika::GetTrafficSummary(array(
	"id"=>$arGadgetParams["COUNTER_ID"],
	"group"=>"day",
	"date1"=>$bef_yesterday_date,
	"date2"=>$now_date
));

$arRows = array(
	"PAGE_VIEWS" => array("NAME" => GetMessage("GD_STAT_PAGE_VIEWS")),
	"VISITORS" => array("NAME" => GetMessage("GD_STAT_VISITORS")),
	"VISITS" => array("NAME" => GetMessage("GD_STAT_VISITS")),
	"NEW_VISITORS" => array("NAME" => GetMessage("GD_STAT_NEW_VISITORS")),
);

foreach($arCommData["data"] as $k=>$arData)
{
	if($k==0)
		$key="TODAY";
	elseif($k==1)
		$key="YESTERDAY";
	elseif($k==2)
		$key="B_YESTERDAY";
	else
		break;
	
	$arRows["PAGE_VIEWS"][$key]=$arData["page_views"];
	$arRows["VISITORS"][$key]=$arData["visitors"];
	$arRows["VISITS"][$key]=$arData["visits"];
	$arRows["NEW_VISITORS"][$key]=$arData["new_visitors"];
}

$date_beforeyesterday = ConvertTimeStamp(AddToTimeStamp(array("DD" => -2, "MM" => 0, "YYYY" => 0, "HH" => 0, "MI" => 0, "SS" => 0), mktime(0, 0, 0, date("n"), date("j"), date("Y"))), "SHORT");
$date_yesterday = ConvertTimeStamp(AddToTimeStamp(array("DD" => -1, "MM" => 0, "YYYY" => 0, "HH" => 0, "MI" => 0, "SS" => 0), mktime(0, 0, 0, date("n"), date("j"), date("Y"))), "SHORT");
$date_today = ConvertTimeStamp(mktime(0, 0, 0, date("n"), date("j"), date("Y")), "SHORT");

if ($arGadgetParams["HIDE_GRAPH"] != "Y")
{
	$iGraphWidth = $arGadgetParams["GRAPH_WIDTH"];
	$iGraphHeight = $arGadgetParams["GRAPH_HEIGHT"];
	$dateGraph1 = ConvertTimeStamp(AddToTimeStamp(array("DD" => -($arGadgetParams["GRAPH_DAYS"]), "MM" => 0, "YYYY" => 0, "HH" => 0, "MI" => 0, "SS" => 0), time()), "SHORT");
	$dateGraph2 = ConvertTimeStamp(time(), "SHORT");

	$arFilter["date1"]=$dateGraph1;
	$arFilter["date2"]=$dateGraph2;	
	$arFilter["group"]="day";
	
	$arrData=CLOLYandexMetrika::GetTrafficSummary($arFilter);
	
	if (count($arrData)<=1)
	{
		?><div class="bx-gadgets-content-padding-rl bx-gadgets-content-padding-t"><?=CAdminMessage::ShowMessage(GetMessage("STAT_NOT_ENOUGH_DATA"));?></div><?
	}
	else
	{
		$strGraphParams = "";
		if(!is_array($arGadgetParams["GRAPH_PARAMS"]) || count($arGadgetParams["GRAPH_PARAMS"]<=0))
			$arGadgetParams["GRAPH_PARAMS"]=array("PAGE_VIEWS", "VISITORS", "VISITS", "NEW_VISITORS");
		if (in_array("PAGE_VIEWS", $arGadgetParams["GRAPH_PARAMS"]))
			$strGraphParams .= "find_page_views=Y&";
		if (in_array("VISITORS", $arGadgetParams["GRAPH_PARAMS"]))
			$strGraphParams .= "find_visitors=Y&";
		if (in_array("VISITS", $arGadgetParams["GRAPH_PARAMS"]))
			$strGraphParams .= "find_visits=Y&";
		if (in_array("NEW_VISITORS", $arGadgetParams["GRAPH_PARAMS"]))
			$strGraphParams .= "find_new_visitors=Y&";
		if (array_key_exists("id", $arFilter))
			$strGraphParams .= "find_counter_id=".$arFilter["id"]."&";
		$strGraphParams .= "find_date1=".$dateGraph1."&find_date2=".$dateGraph2;
		?><div class="bx-gadgets-content-padding-rl bx-gadgets-content-padding-t"><?
			?><img src="/bitrix/admin/lol_metrika_traffic_summary_graph.php?<?=$strGraphParams?>&width=<?=$iGraphWidth?>&height=<?=$iGraphHeight?>&rand=<?=rand()?>&find_graph_type=day" width="<?=$iGraphWidth?>" height="<?=$iGraphHeight?>"><?
			?><div style="padding: 0 0 10px 0;">
			<table cellpadding="2" cellspacing="0" border="0">
				<?if (in_array("PAGE_VIEWS", $arGadgetParams["GRAPH_PARAMS"])):?>
				<tr>
					<td valign="center"><img src="/bitrix/admin/lol_metrika_legend.php?color=<?=$arrColor["PAGE_VIEWS"]?>" width="7" height="7"></td>
					<td nowrap><img src="/bitrix/images/1.gif" width="3" height="1"><?=GetMessage("GD_STAT_PAGE_VIEWS")?></td>
				</tr>
				<?endif;?>
				<?if (in_array("VISITORS", $arGadgetParams["GRAPH_PARAMS"])):?>
				<tr>
					<td valign="center"><img src="/bitrix/admin/lol_metrika_legend.php?color=<?=$arrColor["VISITORS"]?>" width="7" height="7"></td>
					<td nowrap><img src="/bitrix/images/1.gif" width="3" height="1"><?=GetMessage("GD_STAT_VISITORS")?></td>
				</tr>
				<?endif;?>
				<?if (in_array("VISITS", $arGadgetParams["GRAPH_PARAMS"])):?>
				<tr>
					<td valign="center"><img src="/bitrix/admin/lol_metrika_legend.php?color=<?=$arrColor["VISITS"]?>" width="7" height="7"></td>
					<td nowrap><img src="/bitrix/images/1.gif" width="3" height="1"><?=GetMessage("GD_STAT_VISITS")?></td>
				</tr>
				<?endif;?>
				<?if (in_array("NEW_VISITORS", $arGadgetParams["GRAPH_PARAMS"])):?>
				<tr>
					<td valign="center"><img src="/bitrix/admin/lol_metrika_legend.php?color=<?=$arrColor["NEW_VISITORS"]?>" width="7" height="7"></td>
					<td nowrap><img src="/bitrix/images/1.gif" width="3" height="1"><?=GetMessage("GD_STAT_NEW_VISITORS")?></td>
				</tr>
				<?endif;?>
			</table>
			</div><?
		?></div><?
	}
}
?>
<script type="text/javascript">
	var gdMetrikaTabControl_<?=$arGadgetParams["RND_STRING"]?> = false;
</script><?
$aTabs = array(
	array(
		"DIV" => "bx_gd_metrika_common_".$arGadgetParams["RND_STRING"],
		"TAB" => GetMessage("GD_STAT_TAB_COMMON"),
		"ICON" => "",
		"TITLE" => "",
		"ONSELECT" => "gdMetrikaTabControl_".$arGadgetParams["RND_STRING"].".SelectTab(BX('bx_gd_metrika_common_".$arGadgetParams["RND_STRING"]."'));"
	)
);

$aTabs[] = array(
	"DIV" => "bx_gd_metrika_ref_".$arGadgetParams["RND_STRING"],
	"TAB" => GetMessage("GD_STAT_TAB_REF"),
	"ICON" => "",
	"TITLE" => "",
	"ONSELECT" => "gdMetrikaTabControl_".$arGadgetParams["RND_STRING"].".LoadTab(BX('bx_gd_metrika_ref_".$arGadgetParams["RND_STRING"]."'), '/bitrix/gadgets/lol/metrika/getdata.php?lang=".LANGUAGE_ID."&".($arGadgetParams["COUNTER_ID"] ? "counter_id=".$arGadgetParams["COUNTER_ID"]."&" : "")."table_id=sites');"
);

$aTabs[] = array(
	"DIV" => "bx_gd_metrika_phrase_".$arGadgetParams["RND_STRING"],
	"TAB" => GetMessage("GD_STAT_TAB_PHRASE"),
	"ICON" => "",
	"TITLE" => "",
	"ONSELECT" => "gdMetrikaTabControl_".$arGadgetParams["RND_STRING"].".LoadTab(BX('bx_gd_metrika_phrase_".$arGadgetParams["RND_STRING"]."'), '/bitrix/gadgets/lol/metrika/getdata.php?lang=".LANGUAGE_ID."&".($arGadgetParams["COUNTER_ID"] ? "counter_id=".$arGadgetParams["COUNTER_ID"]."&" : "")."table_id=phrases');"
);

$tabControl = new CAdminViewTabControl("metrikaTabControl", $aTabs);

?><div class="bx-gadgets-tabs-wrap" id="bx_gd_tabset_metrika_<?=$arGadgetParams["RND_STRING"]?>"><?
	$tabControl->Begin();
	for($i = 0; $i < count($aTabs); $i++)
		$tabControl->BeginNextTab();
	$tabControl->End();

	?><div class="bx-gadgets-tabs-cont"><?
		for($i = 0; $i < count($aTabs); $i++)
		{
			?><div id="<?=$aTabs[$i]["DIV"]?>_content" style="display: <?=($i==0 ? "block" : "none")?>;" class="bx-gadgets-tab-container"><?
				if ($i == 0)
				{
					?><table class="bx-gadgets-table">
						<tbody>
							<tr>
								<th>&nbsp;</th>
								<th><?=GetMessage("GD_STAT_TODAY")?><br><?=$now_date?></th>
								<th><?=GetMessage("GD_STAT_YESTERDAY")?><br><?=$date_yesterday?></th>
								<th><?=GetMessage("GD_STAT_B_YESTERDAY")?><br><?=$date_beforeyesterday?></th>
							</tr><?
							foreach($arRows as $row_code => $arRow):
								?><tr>
									<td><?=$arRow["NAME"]?></td><?
										?><td align="right"><?=intval($arRow["TODAY"])?></td><?
										?><td align="right"><?=intval($arRow["YESTERDAY"])?></td><?
										?><td align="right"><?=intval($arRow["B_YESTERDAY"])?></td><?
								?></tr><?
							endforeach;
						?></tbody>
					</table><?
				}
				else
				{
					?><div id="<?=$aTabs[$i]["DIV"]?>_content_node"></div><?
				}
			?></div><?
		}
	?></div><?
?></div>
<script type="text/javascript">
	BX.ready(function(){
		gdMetrikaTabControl_<?=$arGadgetParams["RND_STRING"]?> = new gdTabControl('bx_gd_tabset_metrika_<?=$arGadgetParams["RND_STRING"]?>');
	});
</script>