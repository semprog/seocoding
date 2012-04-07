<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seolink/seolink.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seolink/prolog.php");
IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage("SEOLINK_LIST"));


$sTableID='tbl_seolink_admin';

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

		

		$ch = new CSeolink;
		if(!$ch->Update($ID, $arFields))
		{
			$lAdmin->AddUpdateError(GetMessage("SEOLINK_ERR").$ID.": ".$ch->LAST_ERROR."", $ID);
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
			if(!CSeolink::Delete($ID))
			{
				$DB->Rollback();
				$lAdmin->AddGroupError(GetMessage("SEOLINK_DEL_ERR"), $ID);
			}
			CSeolinkwords::DeleteAll($ID);
			$DB->Commit();
			break;
			case "activate":
		case "deactivate":
			$ch = new CSeolink();
			$arFields = Array("ACTIVE"=>($_REQUEST['action']=="activate"?"Y":"N"));
			if(!$ch->Update($ID, $arFields))
				$lAdmin->AddGroupError(GetMessage("SEOLINK_EDIT_ERR").$ch->LAST_ERROR, $ID);
				break;
		
			break;
		
		}
	}
}






	
$arHeader = array(
array("id"=>"ID", 	"content"=>"ID", "sort"=>"name",	"default"=>true),
	array("id"=>"NAME", 	"content"=>GetMessage("SEOLINK_NAME"), "sort"=>"name",	"default"=>true),
	array("id"=>"SORT", 	"content"=>GetMessage("SEOLINK_SORT"),	"sort"=>"sort",	"default"=>true, "align"=>"right"),
	array("id"=>"ACTIVE", "content"=>GetMessage("SEOLINK_ACTIVITY"),	"sort"=>"active", "default"=>true, "align"=>"center"),
	);
	

$lAdmin->AddHeaders($arHeader);


$rsSeolinks= CSeolink::GetList();

$rsSeolinks = new CAdminResult($rsSeolinks, $sTableID);
$rsSeolinks->NavStart();
$lAdmin->NavText($rsSeolinks->GetNavPrint(GetMessage("SEOLINK_LIST")));

while($dbrs = $rsSeolinks->NavNext(true, "f_"))
{


		$row =& $lAdmin->AddRow($f_ID, $dbrs, $urlSectionAdminPage.'?lang='.LANG, GetMessage("ADD_SEOLINK"));
	
	
	
		
		$row->AddViewField("ID", $f_ID);
		$row->AddInputField("NAME",Array("size"=>"45"));
		$row->AddViewField("NAME", '<a href="seolink_edit.php?ID='.$f_ID.'&lang='.LANG.'" title="'.GetMessage("SEOLINK_EDIT").'">'.$f_NAME.'</a>');
		$row->AddCheckField("ACTIVE",false);
		
		$row->AddInputField("SORT", Array("size"=>"3"));
		$row->AddCheckField("ACTIVE");
		
$arActions = Array();
//$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("SEOLINK_CLEAR"), "ACTION"=>"if(confirm('".GetMessage("SEOLINK_CLEAR_SHURE")."')) ".$lAdmin->ActionDoGroup($f_ID, "clear"));		
$arActions[] = array("ICON"=>"edit", "TEXT"=>GetMessage("MAIN_ADMIN_MENU_EDIT"), "DEFAULT"=>$_REQUEST["admin"]=="Y", "ACTION"=>"window.location='seolink_add.php?ID=".$f_ID."'");
$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("MAIN_ADMIN_MENU_DELETE"), "ACTION"=>"if(confirm('".GetMessage("SEOLINK_DEL_SHURE")."')) ".$lAdmin->ActionDoGroup($f_ID, "delete"));		



$row->AddActions($arActions);

}

$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("SEOLINK_SELECTED"), "value"=>$rsSeolinks->SelectedRowsCount()),
		array("counter"=>true, "title"=>GetMessage("SEOLINK_CHECKED"), "value"=>"0"),
	)
);




if($USER->IsAdmin() )
{
	$aContext = array(
		array(
			"ICON"=>"btn_new",
			"TEXT"=>GetMessage("SEOLINK_ADD"),
			"LINK"=>"seolink_add.php?lang=".LANG,
			"TITLE"=>GetMessage("SEOLINK_ADD")
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