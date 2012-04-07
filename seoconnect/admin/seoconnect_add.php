<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
if(!$USER->IsAdmin()) $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/seoconnect.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/prolog.php");
IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage("SEOLINK_LIST"));
if($back_url == '')
$back_url = '/bitrix/admin/seoconnect_list.php?lang='.LANG;

$arIBTLang = Array();
$l = CLanguage::GetList($lby="sort", $lorder="asc");
while($ar = $l->ExtractFields("l_"))
$arIBTLang[]=$ar;

$aTabs = array();
$aTabs[] = array("DIV" => "edit1", "TAB" => GetMessage("SEOLINK_MAIN"), "ICON"=>"iblock_type", "TITLE"=>GetMessage("SEOLINK_TYPE_OPT"));

$tabControl = new CAdminTabControl("tabControl", $aTabs);


$aContext = array(
	array(
		"ICON"=>"btn_list",
		"TEXT"=>GetMessage("SEOLINK_LISTS"),
		"LINK"=>"seoconnect_list.php?lang=".LANG,
		"TITLE"=>GetMessage("SEOLINK_LISTS")
		),
);




$bVarsFromForm = false;
$Update = $_REQUEST['Update'];
$SACTIVE = $_REQUEST['SACTIVE'];
$SNAME = $_REQUEST['SNAME'];
$IBLOCKS = $_REQUEST['IBLOCKS'];
if(strlen($Update) > 0 && check_bitrix_sessid())
{

	if ($SACTIVE == '') $SACTIVE='N';
	if ($FIRST == '') $FIRST='N';
	$arFields["NAME"] = $SNAME;
	$arFields["CSS"] = $CSS;
	$arFields["SORT"] = $SORT;
	$arFields["ACTIVE"] = $SACTIVE;
	$arFields["IBLOCKS"] = $IBLOCKS;
	$arFields["FIRST"] = $FIRST;

	$SeoLink = new CSeoLink;
	$ID = $SeoLink->Add($arFields);
	$res = (strlen($ID)>0);
	
	$LINK = $_REQUEST['LINK'];
	$NAME = $_REQUEST['NAME'];
	$ACTIVE = $_REQUEST['ACTIVE'];
	$WORDS = array();
	if(is_array($NAME))
	{
		foreach($NAME as $index => $value)
		{
			if($ACTIVE[$index]['VALUE'] == '')
			    $ACTIVE[$index]['VALUE'] = 'N';
			$WORDS[$index]['NAME']= $value;
			$WORDS[$index]['LINK']=$LINK[$index];
			$WORDS[$index]['ACTIVE']=$ACTIVE[$index];
			if($ID > 0) 
				$WORDS[$index]['LID'] = $ID;
		}
	//delete links
		if($ID>0) CSeoLinkWords::DeleteAll($ID);
		//add links
		CSeoLinkWords::Add($WORDS);
	}

	if(!$res)
	{
		$strWarning.= GetMessage("SEOLINK_ERR").$SeoLink->LAST_ERROR."";
		$DB->Rollback();
		$bVarsFromForm = true;
	}
	else
	{
		$DB->Commit();
		LocalRedirect("/bitrix/admin/seoconnect_edit.php?lang=".$lang."&ID=".urlencode($ID)."&"."&".$tabControl->ActiveTabParam());
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
<?=bitrix_sessid_post()?>
<?echo GetFilterHiddens("find_");?>
<input type="hidden" name="Update" value="Y">
<input type="hidden" name="ID" value="<?echo $ID?>">
<?if(strlen($back_url)>0):?><input type="hidden" name="back_url" value="<?=htmlspecialchars($back_url)?>"><?endif?>
<?
$tabControl->Begin();
$tabControl->BeginNextTab();


?>
<?php 
		// Список типов инф. блоков
$arIBlocks = array();
$db_iblock_type = CIBlockType::GetList();
while($ar_iblock_type = $db_iblock_type->Fetch())
{   
	if($arIBType = CIBlockType::GetByIDLang($ar_iblock_type["ID"], LANG))   
	{      
		$res = CIBlock::GetList(Array(),Array('TYPE'=>$arIBType["ID"], 'ACTIVE'=>'Y'), true);
		while($ar_res = $res->Fetch())
		{
	
			$arr = array();
			$arr["NAME"] = $ar_res['NAME'];
			$arr["ID"] = $ar_res['ID'];
			$arIBlocks[$arIBType["ID"]][] = $arr;
		}
		   
	}   
}

?>
<tr>
		<td width="40%"><label for="SECTIONS"><?=GetMessage("SEOLINK_ACTIVITY")?></label></td>
		<td width="60%"><input type="checkbox" id="ACTIVE" name="SACTIVE" value="Y"<?if($str_ACTIVE!="N")echo " checked"?>></td>
	</tr>
	<tr>
		<td width="40%"><label for="SECTIONS">Заменять только первое встретившееся  словосочетание</label></td>
		<td width="60%"><input type="checkbox" id="FIRST" name="FIRST" value="Y"<?if($str_FIRST!="N")echo " checked"?>></td>
	</tr>
	<tr>
		<td><?=GetMessage("SEOLINK_SORT")?></td>
		<td><input type="text" name="SORT" size="5" maxlength="5" value="<?=($str_SORT!=''?$str_SORT:"100")?>"></td>
	</tr>
	<tr>
		<td><span class="required">*</span><?=GetMessage("SEOLINK_NAME")?></td>
		<td><input type="text" name="SNAME" size="45" maxlength="45" value="<?=htmlspecialchars($str_NAME)?>"></td>
	</tr>
	<tr>
		<td><span class="required">*</span><?=GetMessage("SEOLINK_IBLOCKS")?></td>
		<td>
			<select name="IBLOCKS[]" multiple="multiple" size="10">
				
			<?foreach($arIBlocks as $type => $items):?>
				<?foreach($items as $item):?>
					<OPTION VALUE="<?=$item["ID"];?>"><?=$item["NAME"];?></OPTION>
				<?endforeach;?>
			<?endforeach;?>
			</select>
		</td>		
	
	</tr>
	<tr>
		<td valign="top"><?=GetMessage("SEOLINK_CSS")?></td>
		<td>
		<textarea name="CSS" cols="34" rows="5"><?=$str_CSS?></textarea>
		</td>
	</tr>
		
	<tr>
	
	<td valign="top" colspan="2">
	<?=GetMessage("SEOLINK_FILL")?>
	<br/><br/>
	<?
	echo '<table cellpadding="0" cellspacing="5" border="0" class="nopadding" width="100%" id="tb_seoconnect">';

	for($d = $k; $d < ($k + 5);$d++)
	{
		echo '<tr><td>'.GetMessage("SEOLINK_WORDS").' <input name="NAME['.$d.']" value="" type="text" size="18">';
		echo ' <span title="'.GetMessage("SEOLINK_LINKS").'"> '.GetMessage("SEOLINK_LINKS").' <input name="LINK['.$d.']" value="" size="18" type="text" id="LINK['.$d.']"></span>';
		echo ' <span title="'.GetMessage("SEOLINK_ACTIVITY").'"> Акт. <input name="ACTIVE['.$d.']" value="Y" checked type="checkbox"/></span>';
		echo '</td></tr>';
	}

?>
	
	<?
	echo '<tr><td><input type="button" value="'.GetMessage("SEOLINK_ADD").'" onClick="addNewRow(\'tb_seoconnect\')"></td></tr></table>';
	?>
	</td>
		
	</tr>

<input type="hidden" name="ID" value="<?=$str_ID?>"/>


	<?
	$tabControl->Buttons(array("disabled"=>false, "back_url"=>$back_url));
	$tabControl->End();
?>
</form>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");