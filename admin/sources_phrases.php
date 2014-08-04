<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/lol.metrika/prolog.php");

$LOL_METRIKA_RIGHT = $APPLICATION->GetGroupRight("lol.metrika");
if($LOL_METRIKA_RIGHT=="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);
CModule::IncludeModule("lol.metrika");

$sTableID = "tbl_lol_metrika_sources_phrases";
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
}

$FilterArr = array(
	"find_counter_id",
	"find_date1",
	"find_date2",
);

$lAdmin->InitFilter($FilterArr);

$arFilter = Array(
	"date1"		=> $find_date1,
	"date2"		=> $find_date2,
	"id"	=> $find_counter_id
);

$arrData=CLOLYandexMetrika::GetSourcesPhrases($arFilter);
//print_r($arrData);

$rsData = new CDBResult;
$rsData->InitFromArray($arrData["data"]);

$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(""));

$arHeaders = array();

$arHeaders[]=
	array(	"id"		=>"phrase",
		"content"	=>GetMessage("LOL_METRIKA_PHRASE"),
		"sort"		=>false,
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
	array(	"id"		=>"page_views",
		"content"	=>GetMessage("LOL_METRIKA_PAGE_VIEWS"),
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
	$row =& $lAdmin->AddRow($f_ID, $arRes);

	//$strHTML='<a href="'.$f_phrase.'" target="_blank">'.$f_phrase.'</a>';
	//$row->AddViewField("phrase",$strHTML);
	
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
