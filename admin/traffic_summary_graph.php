<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$STAT_RIGHT = $APPLICATION->GetGroupRight("lol.metrika");
if($STAT_RIGHT=="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/lol.metrika/colors.php");
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/lol.metrika/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/img.php");

$width = intval($_REQUEST["width"])>0 ? intval($_REQUEST["width"]) : 500;
$height = intval($_REQUEST["height"])>0 ? intval($_REQUEST["height"]) : 400;

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
	"group" => $find_graph_type
);
$arrTTF_FONT = array();

if ($find_graph_type!="day" && $find_graph_type!="week")
{
	$arrData=CLOLYandexMetrika::GetTrafficSummary($arFilter);

	$arColors = array();
	if($find_page_views == "Y")
		$arColors[] = array($arrColor["PAGE_VIEWS"]);
	if($find_visitors == "Y")
		$arColors[] = array($arrColor["VISITORS"]);
	if($find_visits == "Y")
		$arColors[] = array($arrColor["VISITS"]);
	if($find_new_guest == "Y")
		$arColors[] = array($arrColor["NEW_VISITORS"]);

	$arGraphData = array();

	$arrY = array();
	$arrX = array();

	foreach($arrData["data"] as $arData)
	{
		$arRec = array();

		if($find_page_views == "Y")
			$arrY[] = $arRec[] = $arData["page_views"];
		if($find_visitors == "Y")
			$arrY[] = $arRec[] = $arData["visitors"];
		if($find_visits == "Y")
			$arrY[] = $arRec[] = $arData["visits"];
		if($find_new_visitors == "Y")
			$arrY[] = $arRec[] = $arData["new_visitors"];

		$arGraphData[$arData["date"]] = array(
			"DATA" => $arRec,
			"COLORS" => $arColors,
		);
		$arrX[] = $arData["date"];
	}
	
	$arrY = GetArrayY($arrY, $MinY, $MaxY);

	$arrTTF_FONT["type"] = "bar";
	$gridInfo = DrawCoordinatGrid($arrX, $arrY, $width, $height, $ImageHandle, "FFFFFF", "B1B1B1", "000000", 15, 2, $arrTTF_FONT);

	/******************************************************
			data plot
	*******************************************************/
	if(is_array($gridInfo))
		Bar_Diagram($ImageHandle, $arGraphData, $MinY, $MaxY, $gridInfo);
}
else
{
	$arrData=CLOLYandexMetrika::GetTrafficSummary($arFilter);
	foreach($arrData["data"] as $arData)
	{
		$date = MakeTimeStamp($arData["date"]);

		$date_tmp = 0;
		// when dates come not in order
		$next_date = AddTime($prev_date, 1, "D");
		if(($date > $next_date) && (intval($prev_date) > 0))
		{
			// fill date gaps
			$date_tmp = $next_date;
			while($date_tmp < $date)
			{
				$arrX[] = $date_tmp;
				if ($find_page_views=="Y") $arrY_page_views[] = 0;
				if ($find_visitors=="Y") $arrY_visitors[] = 0;
				if ($find_visits=="Y") $arrY_visits[] = 0;
				if ($find_event=="Y") $arrY_event[] = 0;
				if (!$site_filtered)
				{
					if ($find_guest=="Y") $arrY_guest[] = 0;
					if ($find_new_guest=="Y") $arrY_new_guest[] = 0;
				}
				$date_tmp = AddTime($date_tmp,1,"D");
			}
		}
		$arrX[] = $date;
		if ($find_page_views=="Y") $arrY_page_views[] = intval($arData["page_views"]);
		if ($find_visitors=="Y") $arrY_visitors[] = intval($arData["visitors"]);
		if ($find_visits=="Y") $arrY_visits[] = intval($arData["visits"]);
		if ($find_new_visitors=="Y") $arrY_new_visitors[] = intval($arData["new_visitors"]);
		$prev_date = $date;
	}

	/******************************************************
				axis X
	*******************************************************/

	$arrayX = GetArrayX($arrX, $MinX, $MaxX);

	/******************************************************
				axis Y
	*******************************************************/

	$arrY = array();
	if ($find_page_views=="Y") $arrY = array_merge($arrY,$arrY_page_views);
	if ($find_visitors=="Y") $arrY = array_merge($arrY,$arrY_visitors);
	if ($find_visits=="Y") $arrY = array_merge($arrY,$arrY_visits);
	if ($find_new_visitors=="Y") $arrY = array_merge($arrY,$arrY_new_visitors);

	$arrayY = GetArrayY($arrY, $MinY, $MaxY);

	DrawCoordinatGrid($arrayX, $arrayY, $width, $height, $ImageHandle, "FFFFFF", "B1B1B1", "000000", 15, 2, $arrTTF_FONT);

	/******************************************************
			data plot
	*******************************************************/

	if ($find_page_views=="Y")
		Graf($arrX, $arrY_page_views, $ImageHandle, $MinX, $MaxX, $MinY, $MaxY, $arrColor["PAGE_VIEWS"], "N");

	if ($find_visitors=="Y")
		Graf($arrX, $arrY_visitors, $ImageHandle, $MinX, $MaxX, $MinY, $MaxY, $arrColor["VISITORS"], "N");

	if ($find_visits=="Y")
		Graf($arrX, $arrY_visits, $ImageHandle, $MinX, $MaxX, $MinY, $MaxY, $arrColor["VISITS"], "N");

	if ($find_new_visitors=="Y")
		Graf($arrX, $arrY_new_visitors, $ImageHandle, $MinX, $MaxX, $MinY, $MaxY, $arrColor["NEW_VISITORS"], "N");
}

/******************************************************
		send image to client
*******************************************************/

ShowImageHeader($ImageHandle);
?>