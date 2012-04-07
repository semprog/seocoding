<?
$module_id = "seoconnect";
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
IncludeModuleLangFile(__FILE__);


CModule::IncludeModule('seoconnect');




if($REQUEST_METHOD=="POST" && $USER->IsAdmin() && $_POST['rewind']) {

      $STEP = intval($_POST['step']);
      $pages = new CSeoConnectPage;
      $STEP = $pages->reIndex($STEP);
      if($STEP === false) {
           $result = array('status' => 'done');
      } else {
           $result = array('status' => 'proccess','step' => $STEP);
      }

      echo json_encode($result);
      die;
}


$APPLICATION->AddHeadScript( 'http://yandex.st/jquery/1.7.2/jquery.min.js' );
$APPLICATION->AddHeadScript( 'http://yandex.st/jquery-ui/1.8.18/jquery-ui.min.js' );
$APPLICATION->AddHeadString('<link href="http://yandex.st/jquery-ui/1.8.15/themes/humanity/jquery.ui.all.min.css";  type="text/css" rel="stylesheet" />',true);



$FORM_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($FORM_RIGHT>="R"){

global $USER;

if ($REQUEST_METHOD=="GET" && $USER->IsAdmin() && strlen($RestoreDefaults)>0 && check_bitrix_sessid())
{
	COption::RemoveOption("seoconnect");
	$arGROUPS = array();
	$z = CGroup::GetList($v1, $v2, array("ACTIVE" => "Y", "ADMIN" => "N"));
	while($zr = $z->Fetch())
	{
		$ar = array();
		$ar["ID"] = intval($zr["ID"]);
		$ar["NAME"] = htmlspecialchars($zr["NAME"])." [<a title=\"".GetMessage("MAIN_USER_GROUP_TITLE")."\" href=\"/bitrix/admin/group_edit.php?ID=".intval($zr["ID"])."&lang=".LANGUAGE_ID."\">".intval($zr["ID"])."</a>]";
		$groups[$zr["ID"]] = "[".$zr["ID"]."] ".$zr["NAME"];
		$arGROUPS[] = $ar;
	}
	reset($arGROUPS);
	while (list(,$value) = each($arGROUPS))
		$APPLICATION->DelGroupRight($module_id, array($value["ID"]));
}
$arAllOptions = array(
//	array("USE_HTML_EDIT", GetMessage("FORM_USE_HTML_EDIT"), array("checkbox", "Y")),
//	array("SIMPLE", GetMessage("SIMPLE_MODE"), array("checkbox", "Y")),
//	array("SHOW_TEMPLATE_PATH", GetMessage("FORM_SHOW_TEMPLATE_PATH"), array("text", 45)),
//	array("SHOW_RESULT_TEMPLATE_PATH", GetMessage("FORM_SHOW_RESULT_TEMPLATE_PATH"), array("text", 45)),
//	array("PRINT_RESULT_TEMPLATE_PATH", GetMessage("FORM_PRINT_RESULT_TEMPLATE_PATH"), array("text", 45)),
//	array("EDIT_RESULT_TEMPLATE_PATH", GetMessage("FORM_EDIT_RESULT_TEMPLATE_PATH"), array("text", 45)),
//	Array("RECORDS_LIMIT", GetMessage("FORM_RECORDS_LIMIT"), Array("text", 5)),
	Array("ANCHORS_CNT", GetMessage("SEOCONNECT_ANCHORS_CNT"), Array("text", 5))
	);

if($REQUEST_METHOD=="POST" && strlen($Update)>0 && $USER->IsAdmin() && check_bitrix_sessid())
{
	foreach($arAllOptions as $ar)
	{
		$name = $ar[0];
		$val = $$name;
		if($ar[2][0] == "checkbox" && $val != "Y")
		{
			$val = "N";
		}
		COption::SetOptionString($module_id, $name, $val);
	}

}

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "form_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
	array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "form_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
?>
<?
$tabControl->Begin();
?><form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?=LANGUAGE_ID?>"><?=bitrix_sessid_post()?><?
$tabControl->BeginNextTab();
?>
	<?
	if (is_array($arAllOptions)):
	foreach($arAllOptions as $Option):
		$val = COption::GetOptionString($module_id, $Option[0]);
		$type = $Option[2];
	?>
	<tr>
		<td width="200" valign="top"><?	if($type[0]=="checkbox")
							echo "<label for=\"".htmlspecialchars($Option[0])."\">".$Option[1]."</label>";
						else
							echo $Option[1];?>
		</td>
		<td valign="top" nowrap><?
			if($type[0]=="checkbox"):
				?><input type="checkbox" name="<?echo htmlspecialchars($Option[0])?>" id="<?echo htmlspecialchars($Option[0])?>" value="Y"<?if($val=="Y")echo" checked";?>><?
			elseif($type[0]=="text"):
				?><input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialchars($val)?>" name="<?echo htmlspecialchars($Option[0])?>"><?
			elseif($type[0]=="textarea"):
				?><textarea rows="<?echo $type[1]?>" cols="<?echo $type[2]?>" name="<?echo htmlspecialchars($Option[0])?>"><?echo htmlspecialchars($val)?></textarea><?
			endif;
			?></td>
	</tr>
    <tr>
      <td >
           <input type="button" class="rewind" value="<?=GetMessage("SEOCONNECT_REWIND")?>" />
      </td>
      <td>
           <div class="rewind_status" style="width: 200px; display:none; color: green; font-weight: bold; padding: 5px; border: 1px solid green;"><?=GetMessage("SEOCONNECT_REWIND_STATUS")?>: <span class="status_cnt"></span></div>
           <div class="rewind_done" style="width: 200px; display:none; color: green; font-weight: bold; padding: 5px; border: 1px solid green;"><?=GetMessage("SEOCONNECT_REWIND_DONE")?></div>
           <script>
              $(function(){

                   $('.rewind').click(function(){
                          if(window.confirm('<?=GetMessage("SEOCONNECT_REWIND")?>?')) {
                              proccessPages({step:0, status:'proccess'});
                          }
                          return false;
                   })

                   function proccessPages(data) {
                         if(data.status == 'done') {
                              $('.rewind_status').hide();
                              $('.rewind_done').show();
                              return;
                         }

                         if(data.status == 'proccess') {
                                $('.rewind_status').show();
                                $('.rewind_status .status_cnt').html(data.step);
                                $.post('<?=$APPLICATION->GetCurPageParam()?>', {rewind:'Y', step:data.step}, proccessPages,'json' );
                         }

                   }

              } )
           </script>
      </td>

    </tr>
	<?
	endforeach;
	endif;
	?>


<?$tabControl->BeginNextTab();?>
<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
<?$tabControl->Buttons();?>
<script language="JavaScript">
function RestoreDefaults()
{
	if(confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
		window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?=LANGUAGE_ID?>&mid=<?echo urlencode($mid)?>&<?=bitrix_sessid_get()?>";
}
</script>
<input <?if ($FORM_RIGHT<"W") echo "disabled" ?> type="submit" name="Update" value="<?=GetMessage("SEOCONNECT_SAVE")?>">
<input type="hidden" name="Update" value="Y">
<input type="reset" name="reset" value="<?=GetMessage("SEOCONNECT_RESET")?>">
<input <?if ($FORM_RIGHT<"W") echo "disabled" ?> type="button" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="RestoreDefaults();" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
<?$tabControl->End();?>
</form>

<?}?>