<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/lol.metrika/prolog.php");

$LOL_METRIKA_RIGHT = $APPLICATION->GetGroupRight("lol.metrika");
if($LOL_METRIKA_RIGHT=="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/lol.metrika/colors.php");

IncludeModuleLangFile(__FILE__);
CModule::IncludeModule("lol.metrika");

$arrParams = array(
	"day" => array(
		GetMessage("LOL_METRIKA_PAGES_DAYS"),
		GetMessage("LOL_METRIKA_GRAPH_BY_DAYS"),
		GetMessage("LOL_METRIKA_TRAFFIC_DAY_GRAPH_TITLE"),
		GetMessage("LOL_METRIKA_TRAFFIC_DAY_TABLE_TITLE"),
	),
	"week" => array(
		GetMessage("LOL_METRIKA_PAGES_WEEKS"),
		GetMessage("LOL_METRIKA_GRAPH_BY_WEEKS"),
		GetMessage("LOL_METRIKA_TRAFFIC_WEEK_GRAPH_TITLE"),
		GetMessage("LOL_METRIKA_TRAFFIC_WEEK_TABLE_TITLE"),
	),
	"month" => array(
		GetMessage("LOL_METRIKA_PAGES_MONTHS"),
		GetMessage("LOL_METRIKA_GRAPH_BY_MONTHS"),
		GetMessage("LOL_METRIKA_TRAFFIC_MONTH_GRAPH_TITLE"),
		GetMessage("LOL_METRIKA_TRAFFIC_MONTH_TABLE_TITLE"),
	),
);

if(isset($graph_type))
{
	if($graph_type!="day" && $graph_type!="week" && $graph_type!="month")
		$graph_type="day";
	$saved_graph_type = $graph_type;
}
else
{
	$graph_type=false;
}

$sTableID = "tbl_lol_metrika_traffic_summary";
$oSort = new CAdminSorting($sTableID);
$lAdmin = new CAdminList($sTableID, $oSort);

$ref = $ref_id = array();
$rs = CLOLYandexMetrika::GetCounterList();
foreach ($rs as $ar)
{
	$ref[] = "[".$ar["SITE"]."] ".$ar["NAME"];
	$ref_id[] = $ar["ID"];
}
$arCounterDropdown = array("reference" => $ref, "reference_id" => $ref_id);


if($lAdmin->IsDefaultFilter())
{
	$find_date1_DAYS_TO_BACK=90;
	$find_date2 = ConvertTimeStamp(time()-86400, "SHORT");
	$find_visitors = "Y";
	$find_visits = "Y";
	$find_new_visitors = "Y";
	$set_filter = "Y";
	$find_counter_id=$ref_id[0];
}

$FilterArr1 = array(
	"find_page_views",
	"find_visitors",
	"find_visits",
	"find_new_visitors",
);
$FilterArr2 = array(
	"find_counter_id",
	"find_date1",
	"find_date2",
);

$FilterArr = array_merge($FilterArr1, $FilterArr2);

$lAdmin->InitFilter($FilterArr);

//Restore & Save settings (windows registry like)
$arSettings = array ("saved_graph_type");
InitFilterEx($arSettings, $sTableID."_settings", "get");
if($graph_type===false)//Restore saved setting
	$graph_type=$saved_graph_type;
if($graph_type!="day" && $graph_type!="week" && $graph_type!="month")
	$graph_type="day";
if($saved_graph_type!=$graph_type)//Set if changed
	$saved_graph_type=$graph_type;
InitFilterEx($arSettings, $sTableID."_settings", "set");

$arFilter = Array(
	"date1"		=> $find_date1,
	"date2"		=> $find_date2,
	"id"	=> $find_counter_id,
	"group" => $graph_type
);

$lAdmin->BeginPrologContent();

/***************************************************************************
		   HTML form
****************************************************************************/
$arrData=CLOLYandexMetrika::GetTrafficSummary($arFilter);

if (count($arrData["data"])<=1):
	CAdminMessage::ShowMessage(GetMessage("LOL_METRIKA_NOT_ENOUGH_DATA"));

elseif (!function_exists("ImageCreate")) :
	CAdminMessage::ShowMessage(GetMessage("LOL_METRIKA_GD_NOT_INSTALLED"));
elseif (count($lAdmin->arFilterErrors)==0) :
		$width = 500;
		$height = 400;
	?>
<div class="graph">
<h2><?echo $arrParams[$graph_type][2]?></h2>
<div class="metrika_graph">
<table cellspacing="0" cellpadding="0" class="graph" border="0" align="center">
<tr>
	<td valign="top" class="graph">
		<img class="graph" src="/bitrix/admin/lol_metrika_traffic_summary_graph.php?width=<?=$width?>&height=<?=$height?>&lang=<?=LANG?>&rand=<?=rand()?><?=GetFilterParams($FilterArr, false)?>&find_graph_type=<?=$graph_type?>" width="<?=$width?>" height="<?=$height?>">
	</td>
	<td valign="middle">
	<table cellpadding="2" cellspacing="0" border="0" class="legend">
		<?if ($find_page_views=="Y"):?>
		<tr>
			<td valign="center"><img src="/bitrix/admin/lol_metrika_legend.php?color=<?=$arrColor["PAGE_VIEWS"]?>" width="7" height="7"></td>
			<td nowrap><img src="/bitrix/images/1.gif" width="3" height="1"><?=GetMessage("LOL_METRIKA_PAGE_VIEWS_2")?></td>
		</tr>
		<?endif;?>
		<?if ($find_visitors=="Y"):?>
		<tr>
			<td valign="center"><img src="/bitrix/admin/lol_metrika_legend.php?color=<?=$arrColor["VISITORS"]?>" width="7" height="7"></td>
			<td nowrap><img src="/bitrix/images/1.gif" width="3" height="1"><?=GetMessage("LOL_METRIKA_VISITORS_2")?></td>
		</tr>
		<?endif;?>
		<?if ($find_visits=="Y"):?>
		<tr>
			<td valign="center"><img src="/bitrix/admin/lol_metrika_legend.php?color=<?=$arrColor["VISITS"]?>" width="7" height="7"></td>
			<td nowrap><img src="/bitrix/images/1.gif" width="3" height="1"><?=GetMessage("LOL_METRIKA_VISITS_2")?></td>
		</tr>
		<?endif;?>
		<?if ($find_new_visitors=="Y"):?>
		<tr>
			<td valign="center"><img src="/bitrix/admin/lol_metrika_legend.php?color=<?=$arrColor["NEW_VISITORS"]?>" width="7" height="7"></td>
			<td nowrap><img src="/bitrix/images/1.gif" width="3" height="1"><?=GetMessage("LOL_METRIKA_NEW_VISITORS_2")?></td>
		</tr>
		<?endif;?>
	</table>
	</td>
</tr>
</table>
</div>
</div>
<?endif;?>
<h2><?echo $arrParams[$graph_type][3]?></h2>
<?
$lAdmin->EndPrologContent();

/*if($graph_type=="day")
{*/
	$rsData = new CDBResult;
	$rsData->InitFromArray($arrData["data"]);
	//$rsData = CTraffic::GetDailyList($by, $order, $arMaxMin, $arFilter, $is_filtered);
/*}
else
{
	switch ($graph_type)
	{
		case "hour":
			$start = 0; $end = 23; break;
		case "week":
			$start = 0; $end = 6; break;
		case "month":
			$start = 1; $end = 12; break;
	}

	$rsData = new CDBResult;
	$rsData->InitFromArray($ra);
}*/

$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint($arrParams[$graph_type][0]));

$arHeaders = array();
switch($graph_type)
{
	case "day":
		$arHeaders[]=
			array(	"id"		=>"date",
				"content"	=>GetMessage("LOL_METRIKA_DATE"),
				"sort"		=>false,
				"default"	=>true,
			);
		$arHeaders[]=
			array(	"id"		=>"WDAY",
				"content"	=>GetMessage("LOL_METRIKA_WEEKDAY"),
				"sort"		=>false,
				"default"	=>true,
			);
		break;
	case "week":
		$arHeaders[]=
			array(	"id"		=>"date",
				"content"	=>GetMessage("LOL_METRIKA_DATE"),
				"sort"		=>false,
				"default"	=>true,
			);
		break;
	case "month":
		$arHeaders[]=
			array(	"id"		=>"date",
				"content"	=>GetMessage("LOL_METRIKA_MONTH"),
				"sort"		=>false,
				"align"		=>"right",
				"default"	=>true,
			);
		break;
}

$arHeaders[]=
	array(	"id"		=>"page_views",
		"content"	=>GetMessage("LOL_METRIKA_PAGE_VIEWS"),
		"sort"		=>false,
		"align"		=>"right",
		"default"	=>true,
	);
$arHeaders[]=
	array(	"id"		=>"visitors",
		"content"	=>GetMessage("LOL_METRIKA_VISITORS"),
		"sort"		=>false,
		"align"		=>"right",
		"default"	=>true,
	);
$arHeaders[]=
	array(	"id"		=>"visits",
		"content"	=>GetMessage("LOL_METRIKA_VISITS"),
		"sort"		=>false,
		"align"		=>"right",
		"default"	=>true,
	);
$arHeaders[]=
	array(	"id"		=>"new_visitors",
		"content"	=>GetMessage("LOL_METRIKA_NEW_VISITORS_S"),
		"sort"		=>false,
		"align"		=>"right",
		"default"	=>true,
	);
$arHeaders[]=
	array(	"id"		=>"denial",
		"content"	=>GetMessage("LOL_METRIKA_DENIAL"),
		"sort"		=>false,
		"align"		=>"right",
		"default"	=>true,
	);
$arHeaders[]=
	array(	"id"		=>"depth",
		"content"	=>GetMessage("LOL_METRIKA_DEPTH"),
		"sort"		=>false,
		"align"		=>"right",
		"default"	=>true,
	);	
$arHeaders[]=
	array(	"id"		=>"visit_time",
		"content"	=>GetMessage("LOL_METRIKA_VISIT_TIME"),
		"sort"		=>false,
		"align"		=>"right",
		"default"	=>true,
	);			
$lAdmin->AddHeaders($arHeaders);


while($arRes = $rsData->NavNext(true, "f_")):
	$f_WDAY=date("w",MakeTimeStamp($f_date));
	$row =& $lAdmin->AddRow($f_ID, $arRes);
	
	$strHTML=round($f_denial*100)."%";
	$row->AddViewField("denial",$strHTML);

	$strHTML=number_format($f_depth,2);
	$row->AddViewField("depth",$strHTML);

	$strHTML=date("H:i:s", $f_visit_time-36000);
	$row->AddViewField("visit_time",$strHTML);

	if($f_WDAY==0)
		$strHTML='<span class="required">'.GetMessage("LOL_METRIKA_WEEKDAY_".$f_WDAY."_S").'</span>';
	else
		$strHTML=GetMessage("LOL_METRIKA_WEEKDAY_".$f_WDAY."_S");
	$row->AddViewField("WDAY",$strHTML);

endwhile;

$arFooter = array();
$arFooter[] = array(
	"title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"),
	"value"=>$rsData->SelectedRowsCount(),
	);
$lAdmin->AddFooter($arFooter);

$aContext = array(
		array(
			"TEXT"=>GetMessage("LOL_METRIKA_GROUPED")." ".$arrParams[$graph_type][1],
			"MENU"=>array(
				array(
					"TEXT"=>GetMessage("LOL_METRIKA_GROUP_BY")." ".$arrParams["day"][1],
					"ACTION"=>$lAdmin->ActionDoGroup(0, "", "graph_type=day"),
					"ICON"=>($graph_type=="day"?"checked":""),
				),
				array(
					"TEXT"=>GetMessage("LOL_METRIKA_GROUP_BY")." ".$arrParams["week"][1],
					"ACTION"=>$lAdmin->ActionDoGroup(0, "", "graph_type=week"),
					"ICON"=>($graph_type=="week"?"checked":""),
				),
				array(
					"TEXT"=>GetMessage("LOL_METRIKA_GROUP_BY")." ".$arrParams["month"][1],
					"ACTION"=>$lAdmin->ActionDoGroup(0, "", "graph_type=month"),
					"ICON"=>($graph_type=="month"?"checked":""),
				),
			),
		),
	);

$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("LOL_METRIKA_PAGE_TITLE"));

/***************************************************************************
		   HTML form
****************************************************************************/

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$oFilter = new CAdminFilter($sTableID."_filter");
?>

<form name="find_form" method="GET" action="<?=$APPLICATION->GetCurPage()?>?">
<?
$oFilter->Begin();
?>
<tr>
	<td><?echo GetMessage("LOL_METRIKA_COUNTER")?>:</td>
	<td><?echo SelectBoxFromArray("find_counter_id", $arCounterDropdown, $find_counter_id, "", "");?></td>
</tr>
<tr>
	<td><?echo GetMessage("LOL_METRIKA_PERIOD")." (".FORMAT_DATE."):"?></td>
	<td><?echo CalendarPeriod("find_date1", $find_date1, "find_date2", $find_date2, "find_form", "Y")?></td>
</tr>
<tr valign="top">
	<td><?=GetMessage("LOL_METRIKA_SHOW")?>:</td>
	<td>
		<?echo InputType("checkbox","find_page_views","Y",$find_page_views,false,false,'id="find_page_views"');?>
		<label for="find_hit"><?=GetMessage("LOL_METRIKA_PAGE_VIEWS_2")?></label><br>
		<?echo InputType("checkbox","find_visitors","Y",$find_visitors,false,false,'id="find_visitors"'); ?>
		<label for="find_visitors"><?=GetMessage("LOL_METRIKA_VISITORS_2")?></label><br>
		<?echo InputType("checkbox","find_visits","Y",$find_visits,false,false,'id="find_visits"'); ?>
		<label for="find_visits"><?=GetMessage("LOL_METRIKA_VISITS_2")?></label><br>
		<?echo InputType("checkbox","find_new_visitors","Y",$find_new_visitors,false,false,'id="find_new_visitors"'); ?>
		<label for="find_new_visitors"><?=GetMessage("LOL_METRIKA_NEW_VISITORS_2")?></label><br>
	</td>
</tr>
<?
$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form" => "find_form", "report"=>true));
$oFilter->End();
?>
</form>

<?
if($message)
	echo $message->Show();
$lAdmin->DisplayList();
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
