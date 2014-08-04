<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$STAT_RIGHT = $APPLICATION->GetGroupRight("lol.metrika");
if($STAT_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/lol.metrika/colors.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/img.php");
CModule::IncludeModule("lol.metrika");

$diameter = 200;

$arFilter = Array(
	"date1"				=> $find_date1,
	"date2"				=> $find_date2,
	"id"				=> $find_counter_id,
);

$arrData=CLOLYandexMetrika::GetSourcesSearchEngines($arFilter);

$total = count($arrData["data"]);
$arChart=array();
foreach($arrData["data"] as $key => $arVal)
{
	$color = GetNextRGB($color, $total);
	$arChart[] = array("COUNTER"=>$arVal["visits"], "COLOR"=>$color);
}

$ImageHandle = CreateImageHandle($diameter, $diameter);
Circular_Diagram($ImageHandle, $arChart, "FFFFFF", $diameter, $diameter/2, $diameter/2);
ShowImageHeader($ImageHandle);

?>