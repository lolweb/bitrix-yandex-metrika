<?
define("LOL_METRIKA_CLIENT_ID", "a2f1d9a879ed48508c2d3017510dc406");

global $MESS;
IncludeModuleLangFile(__FILE__);


class CLOLYandexMetrika
{
	function GetTokens()
	{
		$strTokens = COPtion::GetOptionString('lol.metrika', 'tokens', '');

		$arTokens = array();
		if ($strTokens != '')
			$arTokens = unserialize($strTokens);
		return $arTokens;
	}
	
	function GetCounterList()
	{
		$arTokens=self::GetTokens();
		$arCounters=array();
		foreach($arTokens as $sToken)
		{
			$arData=json_decode(file_get_contents("http://api-metrika.yandex.ru/counters.json?oauth_token=".$sToken), true);
			foreach($arData["counters"] as $arCounter)
			{
				$arCounters[]=array(
					"ID"=>$arCounter["id"],
					"NAME"=>self::ConvertEncoding($arCounter["name"]),
					"SITE"=>self::ConvertEncoding($arCounter["site"])
				);
			}
		}
		return $arCounters;
	}	
	
	function GetTokenByCounterID($counter_id)
	{
		$arTokens=self::GetTokens();
		$arCounters=array();
		foreach($arTokens as $sToken)
		{
			$arData=json_decode(file_get_contents("http://api-metrika.yandex.ru/counters.json?oauth_token=".$sToken), true);
			foreach($arData["counters"] as $arCounter)
			{
				if($arCounter["id"]==$counter_id)
					return $sToken;
			}
		}
	}
	
	function ConvertEncoding($data)
	{
		if(defined("BX_UTF"))
			return $data;
		if(is_array($data))
		{
			foreach($data as $k=>$v)
			{
				$data[$k]=self::ConvertEncoding($v);
			}
			return $data;
		}
		else
			return utf8win1251($data);
	}
	
	function ConvertToYandexDate($date)
	{
		return ConvertDateTime($date, "YYYYMMDD");
	}
	
	function ConvertFromYandexDate($date)
	{
		//global $DB;
		//return $DB->FormatDate($date, "YYYYMMDD", FORMAT_DATE); //doesn't work without dot
		return ConvertTimeStamp(mktime(0, 0, 0, substr($date,4,2), substr($date,6,2), substr($date,0,4)), "SHORT");
	}	
	
	function SortArrayByDate(&$arr)
	{
		usort($arr, array("self","SortArrayByDateCallback"));
	}
	
	function SortArrayByDateCallback($a, $b)
	{
		if ($a["date"] == $b["date"])
		{
        	return 0;
    	}
    	return ($a["date"] < $b["date"]) ? -1 : 1;
	}
	
	function BuildFilterQueryString($arFilter)
	{
		$sQuery="";
		foreach($arFilter as $k=>$v)	
		{
			$sQuery.="&".$k."=".$v;
		}
		return $sQuery;
	}
	
	function GetReport($sReport, $arFilter)
	{
		if(intval($arFilter["id"])<=0)
			return false;
		if(MakeTimeStamp($arFilter["date1"])>0)
		{
			$arFilter["date1"]=self::ConvertToYandexDate($arFilter["date1"]);
		}
		if(MakeTimeStamp($arFilter["date2"])>0)
		{
			$arFilter["date2"]=self::ConvertToYandexDate($arFilter["date2"]);
		}
		$bLimit=false;
		if(isset($arFilter["limit"]) && intval($arFilter["limit"])>0)
		{
			$arFilter["per_page"]=$arFilter["limit"];
			unset($arFilter["limit"]);
			$bLimit=true;
		}
		$sQuery="http://api-metrika.yandex.ru/".$sReport.".json?oauth_token=".self::GetTokenByCounterID($arFilter["id"]).self::BuildFilterQueryString($arFilter);
		$arData=json_decode(file_get_contents($sQuery), true);
		if(is_array($arData["links"]) && count($arData["links"])>0 && !$bLimit)
		{
			$arData1=array();
			foreach($arData["links"] as $k=>$v)
			{
				$arData1=json_decode(file_get_contents($v), true);
				$arData["data"]=array_merge($arData["data"], $arData1["data"]);
			}
		}
		$arData=self::ConvertEncoding($arData);
		return $arData;		
	}
	
	function GetTrafficSummary($arFilter)
	{
		$arrData=self::GetReport("stat/traffic/summary", $arFilter);
		if(isset($arrData["date1"]))
			$arrData["date1"]=self::ConvertFromYandexDate($arrData["date1"]);
		if(isset($arrData["date2"]))
			$arrData["date2"]=self::ConvertFromYandexDate($arrData["date2"]);
		if(is_array($arrData["data"]) && count($arrData["data"])>0)
		{
			foreach($arrData["data"] as $k=>$v)
			{
				if(isset($v["date"]))
					$arrData["data"][$k]["date"]=self::ConvertFromYandexDate($v["date"]);
			}
		}
		return $arrData;
	}

	function GetTrafficDeepness($arFilter)
	{
		$arrData=self::GetReport("stat/traffic/deepness", $arFilter);
		if(isset($arrData["date1"]))
			$arrData["date1"]=self::ConvertFromYandexDate($arrData["date1"]);
		if(isset($arrData["date2"]))
			$arrData["date2"]=self::ConvertFromYandexDate($arrData["date2"]);
		return $arrData;
	}
	
	function GetTrafficHourly($arFilter)
	{
		$arrData=self::GetReport("stat/traffic/hourly", $arFilter);
		if(isset($arrData["date1"]))
			$arrData["date1"]=self::ConvertFromYandexDate($arrData["date1"]);
		if(isset($arrData["date2"]))
			$arrData["date2"]=self::ConvertFromYandexDate($arrData["date2"]);
		return $arrData;
	}	

	function GetSourcesSites($arFilter)
	{
		$arrData=self::GetReport("stat/sources/sites", $arFilter);
		if(isset($arrData["date1"]))
			$arrData["date1"]=self::ConvertFromYandexDate($arrData["date1"]);
		if(isset($arrData["date2"]))
			$arrData["date2"]=self::ConvertFromYandexDate($arrData["date2"]);
		return $arrData;
	}	
	
	function GetSourcesSearchEngines($arFilter)
	{
		$arrData=self::GetReport("stat/sources/search_engines", $arFilter);
		if(isset($arrData["date1"]))
			$arrData["date1"]=self::ConvertFromYandexDate($arrData["date1"]);
		if(isset($arrData["date2"]))
			$arrData["date2"]=self::ConvertFromYandexDate($arrData["date2"]);
		return $arrData;
	}	
	
	function GetSourcesPhrases($arFilter)
	{
		$arrData=self::GetReport("stat/sources/phrases", $arFilter);
		if(isset($arrData["date1"]))
			$arrData["date1"]=self::ConvertFromYandexDate($arrData["date1"]);
		if(isset($arrData["date2"]))
			$arrData["date2"]=self::ConvertFromYandexDate($arrData["date2"]);
		return $arrData;
	}
		
	function GetSourcesMarketing($arFilter)
	{
		$arrData=self::GetReport("stat/sources/marketing", $arFilter);
		if(isset($arrData["date1"]))
			$arrData["date1"]=self::ConvertFromYandexDate($arrData["date1"]);
		if(isset($arrData["date2"]))
			$arrData["date2"]=self::ConvertFromYandexDate($arrData["date2"]);
		return $arrData;
	}		

	function GetSourcesDirectSummary($arFilter)
	{
		$arrData=self::GetReport("stat/sources/direct/summary", $arFilter);
		if(isset($arrData["date1"]))
			$arrData["date1"]=self::ConvertFromYandexDate($arrData["date1"]);
		if(isset($arrData["date2"]))
			$arrData["date2"]=self::ConvertFromYandexDate($arrData["date2"]);
		return $arrData;
	}
		
	function GetSourcesDirectPlatforms($arFilter)
	{
		$arrData=self::GetReport("stat/sources/direct/platforms", $arFilter);
		if(isset($arrData["date1"]))
			$arrData["date1"]=self::ConvertFromYandexDate($arrData["date1"]);
		if(isset($arrData["date2"]))
			$arrData["date2"]=self::ConvertFromYandexDate($arrData["date2"]);
		return $arrData;
	}
	
	function GetSourcesDirectRegions($arFilter)
	{
		$arrData=self::GetReport("stat/sources/direct/regions", $arFilter);
		if(isset($arrData["date1"]))
			$arrData["date1"]=self::ConvertFromYandexDate($arrData["date1"]);
		if(isset($arrData["date2"]))
			$arrData["date2"]=self::ConvertFromYandexDate($arrData["date2"]);
		return $arrData;
	}
		
	function GetContentPopular($arFilter)
	{
		$arrData=self::GetReport("stat/content/popular", $arFilter);
		if(isset($arrData["date1"]))
			$arrData["date1"]=self::ConvertFromYandexDate($arrData["date1"]);
		if(isset($arrData["date2"]))
			$arrData["date2"]=self::ConvertFromYandexDate($arrData["date2"]);
		return $arrData;
	}
	
	function GetGeoCountries($arFilter)
	{
		$arrData=self::GetReport("stat/geo", $arFilter);
		if(isset($arrData["date1"]))
			$arrData["date1"]=self::ConvertFromYandexDate($arrData["date1"]);
		if(isset($arrData["date2"]))
			$arrData["date2"]=self::ConvertFromYandexDate($arrData["date2"]);
		return $arrData;
	}	
}

?>