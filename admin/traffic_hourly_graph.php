<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$STAT_RIGHT = $APPLICATION->GetGroupRight("lol.metrika");
if($STAT_RIGHT=="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/lol.metrika/colors.php");
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/lol.metrika/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/img.php");

$width = 500;
$height = 400;

// create image canvas
$ImageHandle = CreateImageHandle($width, $height, "FFFFFF", true);

$colorFFFFFF = ImageColorAllocate($ImageHandle,255,255,255);
ImageFill($ImageHandle, 0, 0, $colorFFFFFF);

$arrX=Array();
$arrY=Array();
$arrayX=Array();
$arrayY=Array();

/******************************************************
                Plot data
*******************************************************/

$arFilter = Array(
	"date1" => $find_date1,
	"date2" => $find_date2,
	"id" => $find_counter_id,
);
$arrTTF_FONT = array();

$arColors = array();
if($find_avg_visits == "Y")
	$arColors[] = array($arrColor["VISITS"]);
if($find_denial == "Y")
	$arColors[] = array($arrColor["DENIAL"]);
if($find_depth == "Y")
	$arColors[] = array($arrColor["DEPTH"]);
if($find_visit_time == "Y")
	$arColors[] = array($arrColor["VISIT_TIME"]);

$arGraphData = array();

$arrY = array();
$arrX = array();

$arrData=CLOLYandexMetrika::GetTrafficHourly($arFilter);

$i=0;
foreach($arrData["data"] as $arItem)
{
	$arRec = array();

	if($find_avg_visits == "Y")
		$arrY[] = $arRec[] = $arItem["avg_visits"];
	if($find_denial == "Y")
		$arrY[] = $arRec[] = intval($arItem["denial"]*100);
	if($find_depth == "Y")
		$arrY[] = $arRec[] = $arItem["depth"];
	if($find_visit_time == "Y")
		$arrY[] = $arRec[] = $arItem["visit_time"]/60;

	$i++;
	
	$arGraphData[$i] = array(
		"DATA" => $arRec,
		"COLORS" => $arColors,
	);

	$arrX[] = $i;
}

$arrY = GetArrayY($arrY, $MinY, $MaxY);

$arrTTF_FONT["type"] = "bar";
$gridInfo = DrawCoordinatGrid($arrX, $arrY, $width, $height, $ImageHandle, "FFFFFF", "B1B1B1", "000000", 15, 2, $arrTTF_FONT);

if(is_array($gridInfo))
	Bar_Diagram($ImageHandle, $arGraphData, $MinY, $MaxY, $gridInfo);

ShowImageHeader($ImageHandle);
?>