<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/seoconnect.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/prolog.php");
IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage("SEOCONNECT_LIST_PAGES"));


$sTableID='tbl_seoconnect_admin';

$oSort = new CAdminSorting($sTableID, "ID", "desc");


$lAdmin = new CAdminList($sTableID, $oSort);

function CheckFilter()
{
	global $FilterArr, $lAdmin;
	foreach ($FilterArr as $f) global $$f;

  ///	echo $find_id;

	return count($lAdmin->arFilterErrors) == 0; // если ошибки есть, вернем false;
}

// опишем элементы фильтра
$FilterArr = Array(
    	"find_id",
	    "find_page",
	    "find_title",
	);

// инициализируем фильтр
$lAdmin->InitFilter($FilterArr);

// если все значения фильтра корректны, обработаем его
$arFilter = Array();
if (CheckFilter())
{
	// создадим массив фильтрации для выборки CRubric::GetList() на основе значений фильтра

    if($find_id) {
        $arFilter['ID'] = $find_id;
    }
    if($find_page) {
        $arFilter['PAGE'] = $find_page;
    }
    if($find_title) {
        $arFilter['TITLE'] = $find_title;
    }
}



if($lAdmin->EditAction())
{
	foreach($FIELDS as $ID=>$arFields)
	{
		$DB->StartTransaction();
		$ID = IntVal($ID);

		if(!$lAdmin->IsUpdated($ID))
			continue;

		

		$ch = new CSeoConnectPage;
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
    			if(!CSeoConnectPage::delete($ID))
    			{
    				$DB->Rollback();
    				$lAdmin->AddGroupError(GetMessage("SEOCONNECT_DEL_ERR"), $ID);
    			}
    			CSeoConnectPage::delete($ID);
    			$DB->Commit();
    		break;
    		case "activate":
    	    case "deactivate":
    //			$ch = new CSeolink();
   // 			$arFields = Array("ACTIVE"=>($_REQUEST['action']=="activate"?"Y":"N"));
    //			if(!$ch->Update($ID, $arFields))
    //				$lAdmin->AddGroupError(GetMessage("SEOLINK_EDIT_ERR").$ch->LAST_ERROR, $ID);
    //				break;

            break;
		
		}
	}
}






	
$arHeader = array(
    array("id"=>"ID", 	"content"=>"ID", "sort"=>"name",	"default"=>true),
	array("id"=>"PAGE", 	"content"=>GetMessage("SEOCONNECT_PAGE"), "sort"=>"name",	"default"=>true),
	array("id"=>"TITLE", 	"content"=>GetMessage("SEOCONNECT_PAGE_TITLE"),	"sort"=>"sort",	"default"=>true, "align"=>"right"),
	array("id"=>"ANCHORS", 	"content"=>GetMessage("SEOCONNECT_PAGE_ANCHORS"),  "default"=>true),

);
	

$lAdmin->AddHeaders($arHeader);

if($_COOKIE['debug']) {
    var_dump($arFilter);
}

$rsSeolinks= CSeoConnectPage::getList($arFilter);

$rsSeolinks = new CAdminResult($rsSeolinks, $sTableID);
$rsSeolinks->NavStart();
$lAdmin->NavText($rsSeolinks->GetNavPrint(GetMessage("SEOCONNECT_LIST")));

while($dbrs = $rsSeolinks->NavNext(true, "f_"))
{


		$row =& $lAdmin->AddRow($f_ID, $dbrs, $urlSectionAdminPage.'?lang='.LANG, GetMessage("ADD_SEOCONNECT_PAGE"));

	    $anh = array();
        $arA = CSeoConnectPage::getAnchors($f_ID);
        while($tmpA = $arA->Fetch())
        {
            $title = htmlspecialchars("<a href='".$tmpA['URL']."'>".$tmpA['ANCHOR']."</a>");
            $a = '<a href="seoconnect_add_anchor.php?ID='.$tmpA['ID'].'&lang='.LANG.'" title="'.$title.'">'.$tmpA['ID'].'</a>';
            $anh[] = $a;
        }


		$row->AddViewField("ID", $f_ID);
		$row->AddInputField("PAGE",Array("size"=>"45"));
		$row->AddViewField("PAGE", '<a href="seoconnect_add_page.php?ID='.$f_ID.'&lang='.LANG.'" title="'.GetMessage("SEOCONNECT_EDIT_PAGE").'">'.$f_PAGE.'</a>');
		$row->AddInputField("TITLE", Array("size"=>"3"));

        $title =  CSeoConnectTitle::getById($f_TITLE)->Fetch();

        $row->AddViewField("TITLE",  '[<a href="seoconnect_add_title.php?ID='.$f_TITLE.'&lang='.LANG.'" >'.$f_TITLE.'</a>] ' . $title['TITLE']);

        $row->AddViewField("ANCHORS", implode(", ", $anh));

$arActions = Array();
//$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("SEOLINK_CLEAR"), "ACTION"=>"if(confirm('".GetMessage("SEOLINK_CLEAR_SHURE")."')) ".$lAdmin->ActionDoGroup($f_ID, "clear"));
$arActions[] = array("ICON"=>"edit", "TEXT"=>GetMessage("MAIN_ADMIN_MENU_EDIT"), "DEFAULT"=>$_REQUEST["admin"]=="Y", "ACTION"=>"window.location='seoconnect_add_page.php?ID=".$f_ID."'");
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
			"LINK"=>"seoconnect_add_page.php?lang=".LANG,
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




// создадим объект фильтра
$oFilter = new CAdminFilter(
  $sTableID."_filter",
  array(
    "ID",
    GetMessage("SEOCONNECT_PAGE"),
    GetMessage("SEOCONNECT_PAGE_TITLE"),
  )
);
?>
<form name="find_form" method="get" action="<?echo $APPLICATION->GetCurPage();?>">
<?$oFilter->Begin();?>
<tr>
  <td><?="ID"?>:</td>
  <td>
    <input type="text" name="find_id" size="47" value="<?echo htmlspecialchars($find_id)?>">
  </td>
</tr>
<tr>
  <td><?=GetMessage("SEOCONNECT_PAGE").":"?></td>
  <td><input type="text" name="find_page" size="47" value="<?echo htmlspecialchars($find_page)?>"></td>
</tr>
<tr>
  <td><?=GetMessage("SEOCONNECT_PAGE_TITLE").":"?></td>
  <td><input type="text" name="find_title" size="47" value="<?echo htmlspecialchars($find_title)?>"></td>
</tr>


<?
$oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form"=>"find_form"));
$oFilter->End();
?>
</form>
<?


$lAdmin->DisplayList();




require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");