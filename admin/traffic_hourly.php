<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/lol.metrika/prolog.php");

$LOL_METRIKA_RIGHT = $APPLICATION->GetGroupRight("lol.metrika");
if($LOL_METRIKA_RIGHT=="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/lol.metrika/colors.php");

IncludeModuleLangFile(__FILE__);
CModule::IncludeModule("lol.metrika");

$sTableID = "tbl_lol_metrika_traffic_hourly";
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
	$set_filter = "Y";
	$find_counter_id=$ref_id[0];
	
	$find_avg_visits = "Y";
	$find_denial = "Y";
	$find_depth = "Y";	
	$find_visit_time = "Y";	
}

$FilterArr1 = array(
	"find_avg_visits",
	"find_denial",
	"find_depth",
	"find_visit_time",
);

$FilterArr2 = array(
	"find_counter_id",
	"find_date1",
	"find_date2",
);

$FilterArr = array_merge($FilterArr1, $FilterArr2);

$lAdmin->InitFilter($FilterArr);

$arFilter = Array(
	"date1"		=> $find_date1,
	"date2"		=> $find_date2,
	"id"	=> $find_counter_id
);

$lAdmin->BeginPrologContent();
/***************************************************************************
		   HTML form
****************************************************************************/
$arrData=CLOLYandexMetrika::GetTrafficHourly($arFilter);
//print_r($arrData);

if (count($arrData["data"])<=0):
	CAdminMessage::ShowMessage(GetMessage("LOL_METRIKA_NOT_ENOUGH_DATA"));

elseif (!function_exists("ImageCreate")) :
	CAdminMessage::ShowMessage(GetMessage("LOL_METRIKA_GD_NOT_INSTALLED"));
elseif (count($lAdmin->arFilterErrors)==0) :
		$width = 500;
		$height = 400;
	?>
<div class="graph">
<h2><?echo GetMessage("LOL_METRIKA_HOURLY_GRAPH_TITLE")?></h2>
<div class="metrika_graph">
<table cellspacing="0" cellpadding="0" class="graph" border="0" align="center">
<tr>
	<td valign="top" class="graph">
		<img class="graph" src="/bitrix/admin/lol_metrika_traffic_hourly_graph.php?width=<?=$width?>&height=<?=$height?>&lang=<?=LANG?>&rand=<?=rand()?><?=GetFilterParams($FilterArr, false)?>" width="<?=$width?>" height="<?=$height?>">
	</td>
	<td valign="middle">
	<table cellpadding="2" cellspacing="0" border="0" class="legend">
		<?if ($find_avg_visits=="Y"):?>
		<tr>
			<td valign="center"><img src="/bitrix/admin/lol_metrika_legend.php?color=<?=$arrColor["VISITS"]?>" width="7" height="7"></td>
			<td nowrap><img src="/bitrix/images/1.gif" width="3" height="1"><?=GetMessage("LOL_METRIKA_AVG_VISITS")?></td>
		</tr>
		<?endif;?>
		<?if ($find_denial=="Y"):?>
		<tr>
			<td valign="center"><img src="/bitrix/admin/lol_metrika_legend.php?color=<?=$arrColor["DENIAL"]?>" width="7" height="7"></td>
			<td nowrap><img src="/bitrix/images/1.gif" width="3" height="1"><?=GetMessage("LOL_METRIKA_DENIAL")?></td>
		</tr>
		<?endif;?>
		<?if ($find_depth=="Y"):?>
		<tr>
			<td valign="center"><img src="/bitrix/admin/lol_metrika_legend.php?color=<?=$arrColor["DEPTH"]?>" width="7" height="7"></td>
			<td nowrap><img src="/bitrix/images/1.gif" width="3" height="1"><?=GetMessage("LOL_METRIKA_DEPTH")?></td>
		</tr>
		<?endif;?>
		<?if ($find_visit_time=="Y"):?>
		<tr>
			<td valign="center"><img src="/bitrix/admin/lol_metrika_legend.php?color=<?=$arrColor["VISIT_TIME"]?>" width="7" height="7"></td>
			<td nowrap><img src="/bitrix/images/1.gif" width="3" height="1"><?=GetMessage("LOL_METRIKA_VISIT_TIME")?></td>
		</tr>
		<?endif;?>
	</table>
	</td>
</tr>
</table>
</div>
</div>
<?endif;?>
<h2><?echo GetMessage("LOL_METRIKA_HOURLY_TABLE_TITLE")?></h2>
<?
$lAdmin->EndPrologContent();

$rsData = new CDBResult;
$rsData->InitFromArray($arrData["data"]);

$rsData = new CAdminResult($rsData, $sTableID);

$arHeaders = array();

$arHeaders[]=
	array(	"id"		=>"hours",
		"content"	=>GetMessage("LOL_METRIKA_HOURS"),
		"sort"		=>false,
		"align"		=>"right",
		"default"	=>true,
	);
$arHeaders[]=
	array(	"id"		=>"avg_visits",
		"content"	=>GetMessage("LOL_METRIKA_AVG_VISITS"),
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

$lAdmin->AddHeaders($arHeaders);


while($arRes = $rsData->NavNext(true, "f_")):
	$row =& $lAdmin->AddRow($f_hours, $arRes);

	$strHTML=round($f_denial*100)."%";
	$row->AddViewField("denial",$strHTML);

	$strHTML=number_format($f_depth,2);
	$row->AddViewField("depth",$strHTML);

	$strHTML=date("H:i:s", $f_visit_time-36000);
	$row->AddViewField("visit_time",$strHTML);

endwhile;

$arFooter = array();
$arFooter[] = array(
	"title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"),
	"value"=>$rsData->SelectedRowsCount(),
	);
$lAdmin->AddFooter($arFooter);

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
		<?echo InputType("checkbox","find_avg_visits","Y",$find_avg_visits,false,false,'id="find_avg_visits"');?>
		<label for="find_avg_visits"><?=GetMessage("LOL_METRIKA_AVG_VISITS")?></label><br>
		<?echo InputType("checkbox","find_denial","Y",$find_denial,false,false,'id="find_denial"'); ?>
		<label for="find_denial"><?=GetMessage("LOL_METRIKA_DENIAL")?></label><br>
		<?echo InputType("checkbox","find_depth","Y",$find_depth,false,false,'id="find_depth"'); ?>
		<label for="find_depth"><?=GetMessage("LOL_METRIKA_DEPTH")?></label><br>
		<?echo InputType("checkbox","find_visit_time","Y",$find_visit_time,false,false,'id="find_visit_time"'); ?>
		<label for="find_visit_time"><?=GetMessage("LOL_METRIKA_VISIT_TIME")?></label><br>
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
