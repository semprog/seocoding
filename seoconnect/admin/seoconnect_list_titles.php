<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/seoconnect.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/prolog.php");
IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage("SEOCONNECT_PAGE_TITLE"));


$sTableID='tbl_seoconnect_admin';

$oSort = new CAdminSorting($sTableID, "TIMESTAMP_X", "desc");


$lAdmin = new CAdminList($sTableID, $oSort);





if($lAdmin->EditAction())
{
	foreach($FIELDS as $ID=>$arFields)
	{
		$DB->StartTransaction();
		$ID = IntVal($ID);

		if(!$lAdmin->IsUpdated($ID))
			continue;

		

		$ch = new CSeoConnectTitle;
		if(!$ch->update($ID, $arFields))
		{
			$lAdmin->AddUpdateError(GetMessage("SEOCONNECT_ERR").$ID.": ".$ch->LAST_ERROR."", $ID);
			$DB->Rollback();
		}
		$DB->Commit();
	}
}

if($arID = $lAdmin->GroupAction())
{
	
	foreach($arID as $ID)
	{
		if(strlen($ID)<=0)
			continue;

		switch($_REQUEST['action'])
		{
    		case "delete":
    			if(!$USER->IsAdmin())
    				break;
    			@set_time_limit(0);
    			$DB->StartTransaction();
    			if(!CSeoConnectTitle::delete($ID))
    			{
    				$DB->Rollback();
    				$lAdmin->AddGroupError(GetMessage("SEOCONNECT_DEL_ERR"), $ID);
    			}
    			CSeoConnectTitle::delete($ID);
    			$DB->Commit();
    		break;
    		case "activate":
    	    case "deactivate":
    			$ch = new CSeoConnectTitle();
    			$arFields = Array("ACTIVE"=>($_REQUEST['action']=="activate"?"Y":"N"));
    			if(!$ch->update($ID, $arFields))
    				$lAdmin->AddGroupError(GetMessage("SEOLINK_EDIT_ERR").$ch->LAST_ERROR, $ID);
    				break;

            break;
		
		}
	}
}






	
$arHeader = array(
    array("id"=>"ID", 	"content"=>"ID", "sort"=>"name",	"default"=>true),
	array("id"=>"TITLE", 	"content"=>GetMessage("SEOCONNECT_TITLE"),	"sort"=>"title",	"default"=>true),
	array("id"=>"ACTIVE",  "content"=>GetMessage("SEOCONNECT_ACTIVITY"),	"sort"=>"active", "default"=>true, "align"=>"center"),
);


$lAdmin->AddHeaders($arHeader);


$rsSeolinks= CSeoConnectTitle::getList();

$rsSeolinks = new CAdminResult($rsSeolinks, $sTableID);
$rsSeolinks->NavStart();
$lAdmin->NavText($rsSeolinks->GetNavPrint(GetMessage("SEOCONNECT_LIST")));

while($dbrs = $rsSeolinks->NavNext(true, "f_"))
{


		$row =& $lAdmin->AddRow($f_ID, $dbrs, $urlSectionAdminPage.'?lang='.LANG, GetMessage("ADD_SEOCONNECT_PAGE"));

	

		
		$row->AddViewField("ID", $f_ID);
		$row->AddInputField("TITLE",Array("size"=>"45"));
		$row->AddViewField("TITLE", '<a href="seoconnect_add_title.php?ID='.$f_ID.'&lang='.LANG.'" title="'.GetMessage("SEOCONNECT_EDIT_PAGE").'">'.$f_TITLE.'</a>');
		$row->AddCheckField("ACTIVE",false);
		$row->AddCheckField("ACTIVE");
$arActions = Array();
//$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("SEOLINK_CLEAR"), "ACTION"=>"if(confirm('".GetMessage("SEOLINK_CLEAR_SHURE")."')) ".$lAdmin->ActionDoGroup($f_ID, "clear"));
$arActions[] = array("ICON"=>"edit", "TEXT"=>GetMessage("MAIN_ADMIN_MENU_EDIT"), "DEFAULT"=>$_REQUEST["admin"]=="Y", "ACTION"=>"window.location='seoconnect_add_title.php?ID=".$f_ID."'");
$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("MAIN_ADMIN_MENU_DELETE"), "ACTION"=>"if(confirm('".GetMessage("SEOCONNECT_DEL_SHURE")."')) ".$lAdmin->ActionDoGroup($f_ID, "delete"));



$row->AddActions($arActions);

}

$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("SEOCONNECT_SELECTED"), "value"=>$rsSeolinks->SelectedRowsCount()),
		array("counter"=>true, "title"=>GetMessage("SEOCONNECT_CHECKED"), "value"=>"0"),
	)
);




if(1 || $USER->IsAdmin() )
{
	$aContext = array(
		array(
			"ICON"=>"btn_new",
			"TEXT"=>GetMessage("SEOCONNECT_ADD"),
			"LINK"=>"seoconnect_add_title.php?lang=".LANG,
			"TITLE"=>GetMessage("SEOCONNECT_ADD")
		),
	);
	$lAdmin->AddAdminContextMenu($aContext);
	$lAdmin->AddGroupActionTable(Array(
		"delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE"),
		"activate"=>GetMessage("MAIN_ADMIN_LIST_ACTIVATE"),
		"deactivate"=>GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
		));
	
}
else
{
	$lAdmin->AddAdminContextMenu(array());
	
}


$lAdmin->CheckListMode();
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");


$lAdmin->DisplayList();




require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");