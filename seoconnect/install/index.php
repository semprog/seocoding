<?
IncludeModuleLangFile(__FILE__);


Class SeoConnect extends CModule
{
	var $MODULE_ID = "seoconnect";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	var $errors;

	function SeoConnect()
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
		else
		{
			$this->MODULE_VERSION = SEOCONNECT_VERSION;
			$this->MODULE_VERSION_DATE =SEOCONNECT_VERSION_DATE;
		}
		$this->PARTNER_NAME = "semprog";
		$this->PARTNER_URI = "http://www.semprog.ru";
		$this->MODULE_NAME = GetMessage("SEOCONNECT_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("SEOCONNECT_MODULE_DESC");
	}

	function InstallDB()
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;

		// Database tables creation
		if(!$DB->Query("SELECT 'x' FROM `b_seoconnect_pages` WHERE 1=0", true))
		{
			$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/install/db/".strtolower($DB->type)."/install.sql");
			
			
		}

		if($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}
		else
		{
			RegisterModule("seoconnect");
			CModule::IncludeModule("seoconnect");

			return true;
		}
	}

	function UnInstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;

		if(!array_key_exists("savedata", $arParams) || ($arParams["savedata"] != "Y"))
		{
			$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/install/db/".strtolower($DB->type)."/uninstall.sql");
		}



		UnRegisterModule("seoconnect");

		if($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}

		return true;
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles($arParams = array())
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/seoconnect/", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/install/images/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/seoconnect", true, true);
		//Theme
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/install/themes/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", True, True);

		$bReWriteAdditionalFiles = ($arParams["public_rewrite"] == "Y");

		if(array_key_exists("public_dir", $arParams) && strlen($arParams["public_dir"]))
		{
			$rsSite = CSite::GetList(($by="sort"),($order="asc"));
			while ($site = $rsSite->Fetch())
			{
				$source = $_SERVER['DOCUMENT_ROOT']."/bitrix/modules/seoconnect/install/public/";
				$target = $site['ABS_DOC_ROOT'].$site["DIR"].$arParams["public_dir"]."/";
				if(file_exists($source))
				{
					CheckDirPath($target);
					$dh = opendir($source);
					while($file = readdir($dh))
					{
						if($file == "." || $file == "..")
							continue;
						if($bReWriteAdditionalFiles || !file_exists($target.$file))
						{
							$fh = fopen($source.$file, "rb");
							$php_source = fread($fh, filesize($source.$file));
							fclose($fh);
							if(preg_match_all('/GetMessage\("(.*?)"\)/', $php_source, $matches))
							{
								IncludeModuleLangFile($source.$file, $site["LANGUAGE_ID"]);
								foreach($matches[0] as $i => $text)
								{
									$php_source = str_replace(
										$text,
										'"'.GetMessage($matches[1][$i]).'"',
										$php_source
									);
								}
							}
							$fh = fopen($target.$file, "wb");
							fwrite($fh, $php_source);
							fclose($fh);
						}
					}
				}
			}
		}

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");//css
		DeleteDirFilesEx("/bitrix/themes/.default/icons/seoconnect/");//icons
		DeleteDirFilesEx("/bitrix/images/seoconnect/");//images
		DeleteDirFilesEx("/bitrix/js/seoconnect/");//javascript

		return true;
	}

	function DoInstall()
	{


		global $DB, $DOCUMENT_ROOT, $APPLICATION, $step;
		$step = IntVal($step);
		if($step < 2)
		{
			$APPLICATION->IncludeAdminFile(GetMessage("SEOCONNECT_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/install/step1.php");
		}
		elseif($step == 2)
		{

            COption::SetOptionString($this->MODULE_ID,'ANCHORS_CNT', 5);
		    // var_dump($step); die(); 
			if($this->InstallDB())
			{
				$this->InstallEvents();
				$this->InstallFiles(array(
					"public_dir" => $_REQUEST["public_dir"],
					"public_rewrite" => $_REQUEST["public_rewrite"],
				));
			}
			$GLOBALS["errors"] = $this->errors;
			$APPLICATION->IncludeAdminFile(GetMessage("SEOCONNECT_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/install/step2.php");
		}
	}

	function DoUninstall()
	{
		global $DB, $DOCUMENT_ROOT, $APPLICATION, $step;
		$step = IntVal($step);
		if($step < 2)
		{
			$APPLICATION->IncludeAdminFile(GetMessage("SEOCONNECT_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/install/unstep1.php");
		}
		elseif($step == 2)
		{
            COption::RemoveOption($this->MODULE_ID);

			$this->UnInstallDB(array(
				"savedata" => $_REQUEST["savedata"],
			));
			$this->UnInstallFiles();
			$GLOBALS["errors"] = $this->errors;
			$APPLICATION->IncludeAdminFile(GetMessage("SEOCONNECT_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/install/unstep2.php");
		}
	}
}
?>