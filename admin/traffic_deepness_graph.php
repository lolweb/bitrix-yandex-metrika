<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$STAT_RIGHT = $APPLICATION->GetGroupRight("lol.metrika");
if($STAT_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/lol.metrika/colors.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/img.php");
CModule::IncludeModule("lol.metrika");

$diameter = 200;

if(isset($graph_type))
{
	if($graph_type!="depth")
		$graph_type="time";
}
else
	$graph_type="time";

$arFilter = Array(
	"date1"				=> $find_date1,
	"date2"				=> $find_date2,
	"id"				=> $find_counter_id,
);

$arrData=CLOLYandexMetrika::GetTrafficDeepness($arFilter);

if($graph_type=="depth")
	$data_key="data_depth";
else
	$data_key="data_time";

$total = count($arrData[$data_key]);
$arChart=array();
foreach($arrData[$data_key] as $key => $arVal)
{
	$color = GetNextRGB($color, $total);
	$arChart[] = array("COUNTER"=>$arVal["visits"], "COLOR"=>$color);
}

$ImageHandle = CreateImageHandle($diameter, $diameter);
Circular_Diagram($ImageHandle, $arChart, "FFFFFF", $diameter, $diameter/2, $diameter/2);
ShowImageHeader($ImageHandle);

?>