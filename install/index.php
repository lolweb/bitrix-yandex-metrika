<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-18);
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

class lol_metrika extends CModule
{
  var $MODULE_ID = "lol.metrika";
  var $MODULE_VERSION;
  var $MODULE_VERSION_DATE;
  var $MODULE_NAME;
  var $MODULE_DESCRIPTION;
  var $MODULE_GROUP_RIGHTS = "Y";

  function lol_metrika()
  {
    $arModuleVersion = array();

    $path = str_replace("\\", "/", __FILE__);
    $path = substr($path, 0, strlen($path) - strlen("/index.php"));
    include($path."/version.php");

    if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
    {
      $this->MODULE_VERSION = $arModuleVersion["VERSION"];
      $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
    }

    $this->MODULE_NAME = GetMessage("METRIKA_MODULE_NAME");
    $this->MODULE_DESCRIPTION = GetMessage("METRIKA_MODULE_DESCRIPTION");
    
    $this->PARTNER_NAME = GetMessage("LOL_NAME"); 
    $this->PARTNER_URI = "http://web.lol.su";
    
  }

  function DoInstall()
  {
    global $DOCUMENT_ROOT, $APPLICATION, $errors;

    $errors = false;

	$FM_RIGHT = $APPLICATION->GetGroupRight("lol.metrika");
		
	if ($FM_RIGHT!="D")
	{
    	$this->InstallFiles();
    	$this->InstallDB();

    	$APPLICATION->IncludeAdminFile(GetMessage("METRIKA_INSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/".$this->MODULE_ID."/install/step.php");
	}
  }

  function InstallFiles()
  {
    CopyDirFiles(
      $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin", 
      $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin" 
      );

    CopyDirFiles(
      $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/images", 
      $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/".$this->MODULE_ID,
      true,
      true
      );
    
    CopyDirFiles(
      $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes", 
      $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes",
      true,
      true
      );      
    
    return true;
  }
  
  function InstallDB()
  {
    global $DB;
    
    RegisterModule($this->MODULE_ID);
    
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/lol.metrika/install/tasks/install.php");
    
    return true;
  }
  
  function DoUninstall()
  {
    global $DOCUMENT_ROOT, $APPLICATION;
   
	$FM_RIGHT = $APPLICATION->GetGroupRight("lol.metrika");
		
	if ($FM_RIGHT!="D")
	{
    	$this->UnInstallFiles();
    	$this->UnInstallDB();
    
    	$APPLICATION->IncludeAdminFile(GetMessage("METRIKA_UNINSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/".$this->MODULE_ID."/install/unstep.php");
	}
  }
  
  function UnInstallDB()
  {
  	global $DB;
  	
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/lol.metrika/install/tasks/uninstall.php");
    
    UnRegisterModule($this->MODULE_ID);

    return true;
  }
  
  function UnInstallFiles()
  {
    DeleteDirFiles(
      $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin", 
      $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin" 
      );

    DeleteDirFilesEx(
      "/bitrix/images/".$this->MODULE_ID 
      );
      
    DeleteDirFiles(
      $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes", 
      $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes" 
      );   
      
    return true;
  }

	function GetModuleRightList()
	{
		$arr = array(
			"reference_id" => array("D","R","W"),
			"reference" => array(
				"[D] ".GetMessage("METRIKA_DENIED"),
				"[L] ".GetMessage("MERIKA_VIEW_STATS"),
				"[R] ".GetMessage("MERIKA_VIEW_SETTINGS"),
				"[W] ".GetMessage("METRIKA_EDIT_SETTINGS"))
			);
		return $arr;
	}
}
?>