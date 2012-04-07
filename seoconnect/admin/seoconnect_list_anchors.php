<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/seoconnect.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/prolog.php");
IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage("SEOCONNECT_PAGE_TITLE"));


$sTableID='tbl_seoconnect_admin';

$oSort = new CAdminSorting($sTableID, "ID", "desc");


$lAdmin = new CAdminList($sTableID, $oSort);





if($lAdmin->EditAction())
{
	foreach($FIELDS as $ID=>$arFields)
	{
		$DB->StartTransaction();
		$ID = IntVal($ID);

		if(!$lAdmin->IsUpdated($ID))
			continue;

		

		$ch = new CSeoConnectAnchor;
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
    			if(!CSeoConnectAnchor::delete($ID))
    			{
    				$DB->Rollback();
    				$lAdmin->AddGroupError(GetMessage("SEOCONNECT_DEL_ERR"), $ID);
    			}
    			CSeoConnectAnchor::delete($ID);
    			$DB->Commit();
    		break;
    		case "activate":
    	    case "deactivate":
    			$ch = new CSeoConnectAnchor();
    			$arFields = Array("ACTIVE"=>($_REQUEST['action']=="activate"?"Y":"N"));
    			if(!$ch->update($ID, $arFields))
    				$lAdmin->AddGroupError(GetMessage("SEOLINK_EDIT_ERR").$ch->LAST_ERROR, $ID);
    				break;

            break;
		
		}
	}
}






	
$arHeader = array(
    array("id"=>"ID", 	"content"=>"ID", "sort"=>"id",	"default"=>true),
	array("id"=>"URL", 	"content"=>GetMessage("SEOCONNECT_URL"),	"sort"=>"url",	"default"=>true),
	array("id"=>"ANCHOR", 	"content"=>GetMessage("SEOCONNECT_ANCHOR"),	"sort"=>"anchor",	"default"=>true),
	array("id"=>"WEIGHT", 	"content"=>GetMessage("SEOCONNECT_WEIGHT"),	"sort"=>"weight",	"default"=>true, "align"=>"center"),
	array("id"=>"ACTIVE",  "content"=>GetMessage("SEOCONNECT_ACTIVITY"),	"sort"=>"active", "default"=>true, "align"=>"center"),
	array("id"=>"PROBABILITY",  "content"=>GetMessage("SEOCONNECT_PROBABILITY"),	"sort"=>"probability", "default"=>true, "align"=>"center"),
);


$lAdmin->AddHeaders($arHeader);


$rsSeolinks= CSeoConnectAnchor::getList(array(), false, false, array('PROBABILITY'));

$rsSeolinks = new CAdminResult($rsSeolinks, $sTableID);
$rsSeolinks->NavStart();
$lAdmin->NavText($rsSeolinks->GetNavPrint(GetMessage("SEOCONNECT_LIST")));

while($dbrs = $rsSeolinks->NavNext(true, "f_"))
{


		$row =& $lAdmin->AddRow($f_ID, $dbrs, $urlSectionAdminPage.'?lang='.LANG, GetMessage("ADD_SEOCONNECT_PAGE"));
	
	

		
		$row->AddViewField("ID", $f_ID);
		$row->AddInputField("URL",Array("size"=>"45"));
		$row->AddViewField("URL", '<a href="seoconnect_add_anchor.php?ID='.$f_ID.'&lang='.LANG.'" title="'.GetMessage("SEOCONNECT_EDIT_PAGE").'">'.$f_URL.'</a>');
		$row->AddInputField("ANCHOR",Array("size"=>"45"));
		$row->AddViewField("ANCHOR", '<a href="seoconnect_add_anchor.php?ID='.$f_ID.'&lang='.LANG.'" title="'.GetMessage("SEOCONNECT_EDIT_PAGE").'">'.$f_ANCHOR.'</a>');
		$row->AddInputField("WEIGHT",Array("size"=>"5"));
		$row->AddCheckField("ACTIVE",false);
		$row->AddCheckField("ACTIVE");
        $row->AddViewField("PROBABILITY", number_format($f_PROBABILITY*100,2) . ' %');
$arActions = Array();
//$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("SEOLINK_CLEAR"), "ACTION"=>"if(confirm('".GetMessage("SEOLINK_CLEAR_SHURE")."')) ".$lAdmin->ActionDoGroup($f_ID, "clear"));
$arActions[] = array("ICON"=>"edit", "TEXT"=>GetMessage("MAIN_ADMIN_MENU_EDIT"), "DEFAULT"=>$_REQUEST["admin"]=="Y", "ACTION"=>"window.location='seoconnect_add_anchor.php?ID=".$f_ID."'");
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
			"LINK"=>"seoconnect_add_anchor.php?lang=".LANG,
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