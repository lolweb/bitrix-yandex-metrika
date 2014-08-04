<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/lol.metrika/prolog.php");
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/lol.metrika/colors.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/img.php");
$STAT_RIGHT = $APPLICATION->GetGroupRight("lol.metrika");
if($STAT_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);
CModule::IncludeModule("lol.metrika");

$ref = $ref_id = array();
$rs = CLOLYandexMetrika::GetCounterList();
foreach ($rs as $ar)
{
	$ref[] = "[".$ar["SITE"]."] ".$ar["NAME"];
	$ref_id[] = $ar["ID"];
}
$arCounterDropdown = array("reference" => $ref, "reference_id" => $ref_id);

if(isset($graph_type))
{
	if($graph_type!="depth")
		$graph_type="time";
}
else
{
	$graph_type=false;
}

$sTableID="tbl_lol_metrika_traffic_deepness";

if($graph_type===false)//Restore saved setting
{
	if (strlen($saved_group_by) > 0)
		$graph_type = $saved_group_by;
	else
		$graph_type = "time";
}
elseif($saved_group_by!=$graph_type)//Set if changed
	$saved_group_by=$graph_type;

InitFilterEx($arSettings, $sTableID."_settings", "set");


$oSort = new CAdminSorting($sTableID);
$lAdmin = new CAdminList($sTableID, $oSort);

$filter = new CAdminFilter($sTableID."_filter_id");

$arFilterFields = Array(
	"find_counter_id",	
	"find_date1", 
	"find_date2",
);

if($lAdmin->IsDefaultFilter())
{
	$find_date1_DAYS_TO_BACK = 90;
	$find_date2 = ConvertTimeStamp(time()-86400, "SHORT");
	$find_counter_id=$ref_id[0];
	$set_filter = "Y";
}

$lAdmin->InitFilter($arFilterFields);

$arFilter = Array(
	"date1"				=> $find_date1,
	"date2"				=> $find_date2,
	"id"				=> $find_counter_id
);

switch ($graph_type)
{
	case "depth":
		$graph_title = GetMessage("LOL_METRIKA_TRAFFIC_DEEPNESS_DEPTH_GRAPH_TITLE");
		$data_key="data_depth";
		break;
	case "time":
		$graph_title = GetMessage("LOL_METRIKA_TRAFFIC_DEEPNESS_TIME_GRAPH_TITLE");
		$data_key="data_time";
		break;
}

$arrData=CLOLYandexMetrika::GetTrafficDeepness($arFilter);

$rsData = new CDBResult;
$rsData->InitFromArray($arrData[$data_key]);

$rsData = new CAdminResult($rsData, $sTableID);
//$rsData->NavStart();

//$lAdmin->NavText($rsData->GetNavPrint(GetMessage("LOL_METRIKA_TRAFFIC_DEEPNESS_PAGES")));

$arHeaders = Array();

$arHeaders[] = array("id"=>"name", "content"=>GetMessage("LOL_METRIKA_TRAFFIC_DEEPNESS_VALUE"),"default"=>true,);
$arHeaders[] = array("id"=>"visits", "content"=>GetMessage("LOL_METRIKA_TRAFFIC_DEEPNESS_VISITS"), "default"=>true);
$arHeaders[] = array("id"=>"percent", "content"=>GetMessage("LOL_METRIKA_TRAFFIC_DEEPNESS_PERCENT"), "default"=>true, "align"=>"right",);

$lAdmin->AddHeaders($arHeaders);

$i=0;
while($arRes = $rsData->NavNext(true, "f_"))
{
	$i++;

	$row =& $lAdmin->AddRow($i, $arRes);
	$row->AddViewField("percent", number_format($arRes["percent"]*100,2)."%");
}

$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount())
	)
);

$aContext = array(
		array(
			"TEXT"=>$graph_title,
			"MENU"=>array(
				array(
					"TEXT"=>GetMessage("LOL_METRIKA_TRAFFIC_DEEPNESS_DEPTH_GRAPH_TITLE"),
					"ACTION"=>$lAdmin->ActionDoGroup(0, "", "graph_type=depth"),
					"ICON"=>($graph_type=="depth"?"checked":""),
				),
				array(
					"TEXT"=>GetMessage("LOL_METRIKA_TRAFFIC_DEEPNESS_TIME_GRAPH_TITLE"),
					"ACTION"=>$lAdmin->ActionDoGroup(0, "", "graph_type=time"),
					"ICON"=>($graph_type=="time"?"checked":""),
				),
			),
		),
	);

$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->BeginPrologContent();?>


<?if (is_array($arrData[$data_key]) && count($arrData[$data_key])>0):?>
<div class="graph">
<div class="metrika_graph">
<table cellpadding="0" cellspacing="0" border="0" class="graph" align="center">
	<tr>
		<td>
		<img class="graph" src="<?echo htmlspecialchars("lol_metrika_traffic_deepness_graph.php?lang=".LANG."
&find_date1=".urlencode($find_date1)."&find_date2=".urlencode($find_date2)."&find_counter_id=".urlencode($find_counter_id)."&graph_type=".urlencode($graph_type))?>" width="200" height="200">
		</td>
		<td>
		<table border="0" cellspacing="2" cellpadding="0" class="legend">
			<?
			$i = 1;
			$total = count($arrData[$data_key]);

			foreach($arrData[$data_key] as $key => $arVal):
			$color = GetNextRGB($color, $total);
			?>
			<tr>
					<td style="background-color: <?="#".$color?>"><img src="/bitrix/images/1.gif" width="12" height="12" border=0></td>
					<td style="padding-left: 5px;"><?=$arVal["name"]?></td>
					<td align="right"><?=$arVal["visits"]?></td>
					<td align="right"><?=number_format($arVal["percent"]*100, 2, '.', '')?>%</td>
			</tr>
			<?$i++;endforeach;?>
		</table>
		</td>
	</tr>
</table>
</div>
</div>
<?else:?>
	<?//CAdminMessage::ShowMessage(GetMessage("STAT_NO_DATA"))?>
<?endif?>

<h2><?=$graph_title?></h2>

<?
$lAdmin->EndPrologContent();

$lAdmin->CheckListMode();


if($graph_type == "DEPTH")
	$APPLICATION->SetTitle(GetMessage("LOL_METRIKA_TRAFFIC_DEEPNESS_DEPTH_PAGE_TITLE"));
else
	$APPLICATION->SetTitle(GetMessage("LOL_METRIKA_TRAFFIC_DEEPNESS_TIME_PAGE_TITLE"));

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>




<form name="find_form" method="GET" action="<?=$APPLICATION->GetCurPage()?>">
<?$filter->Begin();?>
<tr>
	<td><?echo GetMessage("LOL_METRIKA_PERIOD")." (".FORMAT_DATE."):"?></td>
	<td><?echo CalendarPeriod("find_date1", $find_date1, "find_date2", $find_date2, "find_form", "Y")?></td>
</tr>
<tr>
	<td><?echo GetMessage("LOL_METRIKA_COUNTER")?>:</td>
	<td><?echo SelectBoxFromArray("find_counter_id", $arCounterDropdown, $find_counter_id, "", "");?></td>
</tr>
<?$filter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"find_form"));$filter->End();?>
</form>

<?
if ($message)
	echo $message->Show();
?>

<?$lAdmin->DisplayList();?>

<?require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
