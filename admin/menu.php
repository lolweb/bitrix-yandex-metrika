<?
IncludeModuleLangFile(__FILE__);
if(
	CModule::IncludeModule('lol.metrika')
	&& $APPLICATION->GetGroupRight("lol.metrika") != "D"
)
{
	$aMenu = Array(
		array(
			"parent_menu" => "global_menu_statistics",
			"sort" => 100,
			"text" => GetMessage("MENU_LOL_METRIKA"),
			"title"=>GetMessage("MENU_LOL_METRIKA_TITLE"),
			"url" => "lol_metrika_traffic_summary.php?lang=".LANGUAGE_ID."&amp;set_default=Y",
			"more_url" => array("lol_metrika_traffic_summary.php"),
			"icon" => "lol_metrika_menu_icon",
			"page_icon" => "lol_metrika_page_icon",
			"items_id" => "menu_lol_metrika",
			"items" => array(
				array(
					"text" => GetMessage("MENU_LOL_METRIKA_TRAFFIC"),
					"title"=>GetMessage("MENU_LOL_METRIKA_TRAFFIC_TITLE"),
					"url" => "lol_metrika_traffic_summary.php?lang=".LANGUAGE_ID."&amp;set_default=Y&amp;graph_type=day",
					"more_url"=>array("lol_metrika_traffic_summary.php?graph_type=day"),
					"items_id" => "menu_lol_metrika_traffic",
					"items" =>
						array(
							array(
								"text" => GetMessage("MENU_LOL_METRIKA_TRAFFIC_SUMMARY"),
								"title"=>"",
								"url" => "lol_metrika_traffic_summary.php?lang=".LANGUAGE_ID."&amp;set_default=Y&amp;graph_type=day",
								"more_url"=>array("lol_metrika_traffic_summary.php?graph_type=day"),
							),
							array(
								"text" => GetMessage("MENU_LOL_METRIKA_TRAFFIC_DEEPNESS"),
								"title"=>"",
								"url" => "lol_metrika_traffic_deepness.php?lang=".LANGUAGE_ID."&amp;set_default=Y",
								"more_url"=>array("lol_metrika_traffic_deepness.php"),
							),
							array(
								"text" => GetMessage("MENU_LOL_METRIKA_TRAFFIC_HOURLY"),
								"title"=>"",
								"url" => "lol_metrika_traffic_hourly.php?lang=".LANGUAGE_ID."&amp;set_default=Y",
								"more_url"=>array("lol_metrika_traffic_hourly.php"),
							),
							/*array(
								"text" => GetMessage("MENU_LOL_METRIKA_TRAFFIC_LOAD"),
								"title"=>"",
								"url" => "lol_metrika_traffic_summary.php?lang=".LANGUAGE_ID."&amp;set_default=Y&amp;graph_type=month",
								"more_url"=>array("lol_metrika_traffic_summary.php?graph_type=month"),
							),*/

						)
				),
				array(
					"text" => GetMessage("MENU_LOL_METRIKA_SOURCES"),
					"title"=>GetMessage("MENU_LOL_METRIKA_SOURCES_TITLE"),
					"url" => "lol_metrika_sources_sites.php?lang=".LANGUAGE_ID."&amp;set_default=Y",
					"more_url"=>array("lol_metrika_sources_sites.php"),
					"items_id" => "menu_lol_metrika_sources",
					"items" =>
						array(
							array(
								"text" => GetMessage("MENU_LOL_METRIKA_SOURCES_SITES"),
								"title"=>"",
								"url" => "lol_metrika_sources_sites.php?lang=".LANGUAGE_ID."&amp;set_default=Y",
								"more_url"=>array("lol_metrika_sources_sites.php"),
							),
							array(
								"text" => GetMessage("MENU_LOL_METRIKA_SOURCES_SEARCHENGINES"),
								"title"=>"",
								"url" => "lol_metrika_sources_searchengines.php?lang=".LANGUAGE_ID."&amp;set_default=Y",
								"more_url"=>array("lol_metrika_sources_searchengines.php"),
							),
							array(
								"text" => GetMessage("MENU_LOL_METRIKA_SOURCES_PHRASES"),
								"title"=>"",
								"url" => "lol_metrika_sources_phrases.php?lang=".LANGUAGE_ID."&amp;set_default=Y",
								"more_url"=>array("lol_metrika_sources_phrases.php"),
							),
							array(
								"text" => GetMessage("MENU_LOL_METRIKA_SOURCES_MARKETING"),
								"title"=>"",
								"url" => "lol_metrika_sources_marketing.php?lang=".LANGUAGE_ID."&amp;set_default=Y",
								"more_url"=>array("lol_metrika_sources_marketing.php"),
							),	
							array(
								"text" => GetMessage("MENU_LOL_METRIKA_SOURCES_DIRECT"),
								"title"=>"",
								"url" => "lol_metrika_sources_direct_summary.php?lang=".LANGUAGE_ID."&amp;set_default=Y",
								"more_url"=>array("lol_metrika_sources_direcy_summary.php"),
								"items_id" => "menu_lol_metrika_sources_direct",
								"items" =>
									array(
										array(
											"text" => GetMessage("MENU_LOL_METRIKA_SOURCES_DIRECT_SUMMARY"),
											"title"=>"",
											"url" => "lol_metrika_sources_direct_summary.php?lang=".LANGUAGE_ID."&amp;set_default=Y",
											"more_url"=>array("lol_metrika_sources_direct_summary.php"),
										),
										array(
											"text" => GetMessage("MENU_LOL_METRIKA_SOURCES_DIRECT_PLATFORMS"),
											"title"=>"",
											"url" => "lol_metrika_sources_direct_platforms.php?lang=".LANGUAGE_ID."&amp;set_default=Y",
											"more_url"=>array("lol_metrika_sources_direct_platforms.php"),
										),
										array(
											"text" => GetMessage("MENU_LOL_METRIKA_SOURCES_DIRECT_REGIONS"),
											"title"=>"",
											"url" => "lol_metrika_sources_direct_regions.php?lang=".LANGUAGE_ID."&amp;set_default=Y",
											"more_url"=>array("lol_metrika_sources_direct_regions.php"),
										),
									),
							),
							/*array(
								"text" => GetMessage("MENU_LOL_METRIKA_TRAFFIC_LOAD"),
								"title"=>"",
								"url" => "lol_metrika_traffic_summary.php?lang=".LANGUAGE_ID."&amp;set_default=Y&amp;graph_type=month",
								"more_url"=>array("lol_metrika_traffic_summary.php?graph_type=month"),
							),*/

						)
				),
				array(
					"text" => GetMessage("MENU_LOL_METRIKA_CONTENT"),
					"title"=>GetMessage("MENU_LOL_METRIKA_CONTENT_TITLE"),
					"url" => "lol_metrika_content_popular.php?lang=".LANGUAGE_ID."&amp;set_default=Y",
					"more_url"=>array("lol_metrika_content_popular.php"),
					"items_id" => "menu_lol_metrika_content",
					"items" =>
						array(
							array(
								"text" => GetMessage("MENU_LOL_METRIKA_CONTENT_POPULAR"),
								"title"=>"",
								"url" => "lol_metrika_content_popular.php?lang=".LANGUAGE_ID."&amp;set_default=Y",
								"more_url"=>array("lol_metrika_content_popular.php"),
							),
						)
				),
				array(
					"text" => GetMessage("MENU_LOL_METRIKA_GEO"),
					"title"=>GetMessage("MENU_LOL_METRIKA_GEO_TITLE"),
					"url" => "lol_metrika_geo_countries.php?lang=".LANGUAGE_ID."&amp;set_default=Y",
					"more_url"=>array("lol_metrika_geo_countries.php"),
					"items_id" => "menu_lol_metrika_geo",
					"items" =>
						array(
							array(
								"text" => GetMessage("MENU_LOL_METRIKA_GEO_COUNTRIES"),
								"title"=>"",
								"url" => "lol_metrika_geo_countries.php?lang=".LANGUAGE_ID."&amp;set_default=Y",
								"more_url"=>array("lol_metrika_geo_countries.php"),
							),
						)
				),

			)
		),
	);
	return $aMenu;
}
return false;
?>
