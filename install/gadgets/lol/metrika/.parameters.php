<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("lol.metrika"))
	return false;

$arSites = array(
);
$arrCounter=CLOLYandexMetrika::GetCounterList();
foreach($arrCounter as $arCounter)
{
	$arSites[$arCounter["ID"]] = "[".$arCounter["SITE"]."] ".$arCounter["NAME"];
}	
	

$arGraphParams = array(
	"PAGE_VIEWS" => GetMessage("GD_STAT_P_PAGE_VIEWS"),
	"VISITORS" => GetMessage("GD_STAT_P_VISITORS"),
	"VISITS" => GetMessage("GD_STAT_P_VISITS"),
	"NEW_VISITORS" => GetMessage("GD_STAT_P_NEW_VISITORS"),
);

$arParameters = Array(
	"PARAMETERS"=> Array(),
	"USER_PARAMETERS"=> Array(
		"SITE_ID" => Array(
			"NAME" => GetMessage("GD_STAT_P_COUNTER_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arSites,
			"MULTIPLE" => "N",
			"DEFAULT" => ""
		),
		"HIDE_GRAPH" => Array(
			"NAME" => GetMessage("GD_STAT_P_HIDE_GRAPH"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y"
		),
	)
);

if (
	!is_array($arAllCurrentValues)
	|| !array_key_exists("HIDE_GRAPH", $arAllCurrentValues)
	|| $arAllCurrentValues["HIDE_GRAPH"]["VALUE"] != "Y"
)
{
	$arParameters["USER_PARAMETERS"]["GRAPH_PARAMS"]	= array(
		"NAME" => GetMessage("GD_STAT_P_GRAPH_PARAMS"),
		"TYPE" => "LIST",
		"VALUES" => $arGraphParams,
		"MULTIPLE" => "Y",
		"DEFAULT" => array("PAGE_VIEWS", "VISITORS", "VISITS", "NEW_VISITORS")
	);

	$arParameters["USER_PARAMETERS"]["GRAPH_DAYS"]	= array(
		"NAME" => GetMessage("GD_STAT_P_GRAPH_DAYS"),
		"TYPE" => "STRING",
		"DEFAULT" => "30"
	);

	$arParameters["USER_PARAMETERS"]["GRAPH_WIDTH"]	= array(
		"NAME" => GetMessage("GD_STAT_P_GRAPH_WIDTH"),
		"TYPE" => "STRING",
		"DEFAULT" => "500"
	);

	$arParameters["USER_PARAMETERS"]["GRAPH_HEIGHT"]	= array(
		"NAME" => GetMessage("GD_STAT_P_GRAPH_HEIGHT"),
		"TYPE" => "STRING",
		"DEFAULT" => "300"
	);
}
?>