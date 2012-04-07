<form action="<?echo $APPLICATION->GetCurPage()?>" name="form1">
<?=bitrix_sessid_post()?>
<input type="hidden" name="lang" value="<?echo LANG?>">
<input type="hidden" name="id" value="seoconnect">
<input type="hidden" name="install" value="Y">
<input type="hidden" name="step" value="2">
	<script language="JavaScript">
	<!--
	function ChangeInstallPublic(val)
	{
		document.form1.public_dir.disabled = !val;
		document.form1.public_rewrite.disabled = !val;
	}
	//-->
	</script>

	<table cellpadding="3" cellspacing="0" border="0" width="0%">
		<tr>
			
			<td><p><label for="id_install_public"><?= GetMessage("COPY_S_FILES") ?></label></p></td>
		</tr>
		
	</table>
	
	<br>
	<input type="submit" name="inst" value="<?= GetMessage("MOD_INSTALL")?>">
</form>