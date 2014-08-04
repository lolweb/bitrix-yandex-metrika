<?
// *******************************************************************************************************
// Install new right system: operation and tasks
// *******************************************************************************************************
// ############ LOL METRIKA MODULE OPERATION ###########
$arFOp = Array();
$arFOp[] = Array('lol_metrika_view_all_settings', 'lol.metrika', '', 'module');
$arFOp[] = Array('lol_metrika_view_stats', 'lol.metrika', '', 'module');
$arFOp[] = Array('lol_metrika_edit_all_settings', 'lol.metrika', '', 'module');

// ############ LOL METRIKA MODULE TASKS ###########
$arTasksF = Array();
$arTasksF[] = Array('lol_metrika_denied', 'D', 'lol.metrika', 'Y', '', 'module');
$arTasksF[] = Array('lol_metrika_view_stats', 'L', 'lol.metrika', 'Y', '', 'module');
$arTasksF[] = Array('lol_metrika_view_settings', 'R', 'lol.metrika', 'Y', '', 'module');
$arTasksF[] = Array('lol_metrika_edit_settings', 'W', 'lol.metrika', 'Y', '', 'module');

//Operations in Tasks
$arOInT = Array();
//LOL METRIKA: module
$arOInT['lol_metrika_view_stats'] = Array(
	'lol_metrika_view_stats'
);

$arOInT['lol_metrika_view_settings'] = Array(
	'lol_metrika_view_all_settings',
);

$arOInT['lol_metrika_edit_settings'] = Array(
	'lol_metrika_edit_all_settings',
);


foreach($arFOp as $ar)
	$DB->Query("
		INSERT INTO b_operation
		(NAME,MODULE_ID,DESCRIPTION,BINDING)
		VALUES
		('".$ar[0]."','".$ar[1]."','".$ar[2]."','".$ar[3]."')
	", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

foreach($arTasksF as $ar)
	$DB->Query("
		INSERT INTO b_task
		(NAME,LETTER,MODULE_ID,SYS,DESCRIPTION,BINDING)
		VALUES
		('".$ar[0]."','".$ar[1]."','".$ar[2]."','".$ar[3]."','".$ar[4]."','".$ar[5]."')
	", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

// ############ b_group_task ###########
$sql_str = "
	INSERT INTO b_group_task
	(GROUP_ID,TASK_ID)
	SELECT MG.GROUP_ID, T.ID
	FROM
		b_task T
		INNER JOIN b_module_group MG ON MG.G_ACCESS = T.LETTER
	WHERE
		T.SYS = 'Y'
		AND T.BINDING = 'module'
		AND MG.MODULE_ID = 'lol.metrika'
		AND T.MODULE_ID = MG.MODULE_ID
";
$z = $DB->Query($sql_str, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

// ############ b_task_operation ###########
foreach($arOInT as $tname => $arOp)
{
	$sql_str = "
		INSERT INTO b_task_operation
		(TASK_ID,OPERATION_ID)
		SELECT T.ID, O.ID
		FROM
			b_task T
			,b_operation O
		WHERE
			T.SYS='Y'
			AND T.NAME='".$tname."'
			AND O.NAME in ('".implode("','", $arOp)."')
	";
	$z = $DB->Query($sql_str, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
}
?>