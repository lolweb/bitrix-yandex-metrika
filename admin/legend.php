<?
@require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/tools.php");
@include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/lol.metrika/colors.php");
@require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/img.php");

$ImageHandle = CreateImageHandle(7, 7);

$dec=ReColor($_REQUEST["color"]);
$color = ImageColorAllocate($ImageHandle,$dec[0],$dec[1],$dec[2]);
ImageFill($ImageHandle, 0, 0, $color);

ShowImageHeader($ImageHandle);
?>