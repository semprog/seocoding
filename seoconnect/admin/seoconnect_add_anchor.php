<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
//if(!$USER->IsAdmin()) $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/seoconnect.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/prolog.php");


$APPLICATION->AddHeadScript( 'http://yandex.st/jquery/1.7.2/jquery.min.js' );
$APPLICATION->AddHeadScript( 'http://yandex.st/jquery-ui/1.8.18/jquery-ui.min.js' );
$APPLICATION->AddHeadString('<link href="http://yandex.st/jquery-ui/1.8.15/themes/humanity/jquery.ui.all.min.css";  type="text/css" rel="stylesheet" />',true);

IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage("SEOCONNECT_LIST"));
if($back_url == '')
$back_url = '/bitrix/admin/seoconnect_list_anchors.php?lang='.LANG;

$arIBTLang = Array();
$l = CLanguage::GetList($lby="sort", $lorder="asc");
while($ar = $l->ExtractFields("l_"))
$arIBTLang[]=$ar;

$aTabs = array();
$aTabs[] = array("DIV" => "edit1", "TAB" => GetMessage("SEOCONNECT_MAIN"), "ICON"=>"iblock_type", "TITLE"=>GetMessage("SEOCONNECT_TYPE_OPT"));

$tabControl = new CAdminTabControl("tabControl", $aTabs);


$aContext = array(
	array(
		"ICON"=>"btn_list",
		"TEXT"=>GetMessage("SEOCONNECT_LISTS"),
		"LINK"=>"seoconnect_list_anchors.php?lang=".LANG,
		"TITLE"=>GetMessage("SEOCONNECT_LISTS")
		),
);
$ID = intval($_REQUEST["ID"]);




if($ID > 0)
{
	$SeoTitle = new CSeoConnectAnchor;
	$seoconnect_result = $SeoTitle->getById($ID);
	$seoconnect_result->ExtractFields("str_");


}


$bVarsFromForm = false;


if(($_POST['saveForm'] == 'Y') && check_bitrix_sessid())
{
    $arFields = array();
    $update = ($_REQUEST['update'] == 'Y');
    $ACTIVE = $_REQUEST['ACTIVE'];
    $URL = $_REQUEST['URL'];
    $ANCHOR = $_REQUEST['ANCHOR'];
    $WEIGHT = intval($_REQUEST['WEIGHT']);

    $arFields = array(
       'ACTIVE' => $ACTIVE,
       'URL' => $URL,
       'ANCHOR' => $ANCHOR,
       'WEIGHT' => $WEIGHT,
    );

   	$SeoTitle = new CSeoConnectAnchor;

    if(!$update) {

      	$ID = $SeoTitle->add($arFields);
      	$res = (strlen($ID)>0);

    }  else if($ID) {
        $res = $SeoTitle->update($ID, $arFields) ;
    }


	if(!$res)
	{
		$strWarning.= GetMessage("SEOCONNECT_ERR").$SeoTitle->LAST_ERROR."";
		$DB->Rollback();
		$bVarsFromForm = true;
	}
	else
	{
		$DB->Commit();
       if($_POST['apply']) {
         LocalRedirect("/bitrix/admin/seoconnect_add_anchor.php?lang=".$lang."&ID=".urlencode($ID)."&"."&".$tabControl->ActiveTabParam());
       } else {
          LocalRedirect($back_url);
       }

	}



}




require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$context = new CAdminContextMenu($aContext);
$context->Show();

CAdminMessage::ShowOldStyleError($strWarning);?>
<script language="JavaScript">
<!--
function addNewRow(tableID)
{
	var tbl = document.getElementById(tableID);
	var cnt = tbl.rows.length;
	var oRow = tbl.insertRow(cnt-1);
	var oCell = oRow.insertCell(0);
	var sHTML=tbl.rows[cnt-2].cells[0].innerHTML;

	var p = 0;
	while(true)
	{
		var s = sHTML.indexOf('[n',p);
		if(s<0)break;
		var e = sHTML.indexOf(']',s);
		if(e<0)break;
		var n = parseInt(sHTML.substr(s+2,e-s));
		sHTML = sHTML.substr(0, s)+'[n'+(++n)+']'+sHTML.substr(e+1);
		p=s+1;
	}
	var p = 0;
	while(true)
	{
		var s = sHTML.indexOf('__n',p);
		if(s<0)break;
		var e = sHTML.indexOf('__',s+2);
		if(e<0)break;
		var n = parseInt(sHTML.substr(s+3,e-s));
		sHTML = sHTML.substr(0, s)+'__n'+(++n)+'__'+sHTML.substr(e+2);
		p=e+2;
	}
	var p = 0;
	while(true)
	{
		var s = sHTML.indexOf('__N',p);
		if(s<0)break;
		var e = sHTML.indexOf('__',s+2);
		if(e<0)break;
		var n = parseInt(sHTML.substr(s+3,e-s));
		sHTML = sHTML.substr(0, s)+'__N'+(++n)+'__'+sHTML.substr(e+2);
		p=e+2;
	}
	var p = 0;
	while(true)
	{
		var s = sHTML.indexOf('xxn',p);
		if(s<0)break;
		var e = sHTML.indexOf('xx',s+2);
		if(e<0)break;
		var n = parseInt(sHTML.substr(s+3,e-s));
		sHTML = sHTML.substr(0, s)+'xxn'+(++n)+'xx'+sHTML.substr(e+2);
		p=e+2;
	}
	oCell.innerHTML = sHTML;

	var patt = new RegExp ("<"+"script"+">[^\000]*?<"+"\/"+"script"+">", "ig");
	var code = sHTML.match(patt);
	if(code)
	{
		for(var i = 0; i < code.length; i++)
		{
			if(code[i] != '')
			{
				var s = code[i].substring(8, code[i].length-9);
				jsUtils.EvalGlobal(s);
			}
		}
	}
}
//-->
</script>
<form method="POST" id="form" name="form" action="">
<input type="hidden" name="saveForm" value="Y">
<?=bitrix_sessid_post()?>
<?echo GetFilterHiddens("find_");?>
<?if($ID){?>
<input type="hidden" name="update" value="Y">
<input type="hidden" name="ID" value="<?echo $ID?>">
<?}?>
<?if(strlen($back_url)>0):?><input type="hidden" name="back_url" value="<?=htmlspecialchars($back_url)?>"><?endif?>
<?
$tabControl->Begin();
$tabControl->BeginNextTab();


?>


    <tr>
		<td width="40%"><label for="ACTIVE"><?=GetMessage("SEOCONNECT_ACTIVITY")?></label></td>
		<td width="60%">
        <input type="hidden" name="ACTIVE" value="N">
        <input type="checkbox" id="ACTIVE" name="ACTIVE" value="Y"<?if($str_ACTIVE!="N")echo " checked"?>></td>
	</tr>
	<tr>
		<td><?=GetMessage("SEOCONNECT_URL")?></td>
		<td><input type="text" name="URL" size="50" value="<?=$str_URL?>"></td>
	</tr>
	<tr>
		<td><?=GetMessage("SEOCONNECT_ANCHOR")?></td>
		<td><input type="text" name="ANCHOR" size="50" value="<?=$str_ANCHOR?>"></td>
	</tr>
	<tr>
		<td><?=GetMessage("SEOCONNECT_WEIGHT")?></td>
		<td><input type="text" name="WEIGHT" size="5" value="<?=$str_WEIGHT ? $str_WEIGHT : 100 ?>"></td>
	</tr>




	<?
	$tabControl->Buttons(array("disabled"=>false, "back_url"=>$back_url));
	$tabControl->End();
?>
</form>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");