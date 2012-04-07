<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
//if(!$USER->IsAdmin()) $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/seoconnect.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/prolog.php");


$APPLICATION->AddHeadScript( 'http://yandex.st/jquery/1.7.2/jquery.min.js' );
$APPLICATION->AddHeadScript( 'http://yandex.st/jquery-ui/1.8.18/jquery-ui.min.js' );
$APPLICATION->AddHeadString('<link href="http://yandex.st/jquery-ui/1.8.15/themes/redmond/jquery.ui.all.min.css";  type="text/css" rel="stylesheet" />',true);

IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage("SEOCONNECT_LIST"));
if($back_url == '')
$back_url = '/bitrix/admin/seoconnect_list_pages.php?lang='.LANG;

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
		"LINK"=>"seoconnect_list_pages.php?lang=".LANG,
		"TITLE"=>GetMessage("SEOCONNECT_LISTS")
		),
);
$ID = intval($_REQUEST["ID"]);


	$SeoPage = new CSeoConnectPage;

if($ID > 0)
{

	$seoconnect_result = $SeoPage->getById($ID);
	$seoconnect_result->ExtractFields("str_");


}


$bVarsFromForm = false;




if(($_POST['saveForm'] == 'Y') && check_bitrix_sessid())
{


    $update = ($_REQUEST['update'] == 'Y');

	$arFields["PAGE"] = $_POST['PAGE'];
	$arFields["TITLE"] = $_POST['TITLE'];

    $anchors = $_POST['ANCHORS'];


        if(!$update) {

        	$ID = $SeoPage->add($arFields);
        	$res = (strlen($ID)>0);

            if($res && is_array($anchors)) {
                $SeoPage->setAnchors($ID, $anchors);
            }


        } else {
           $res = $SeoPage->update($ID, $arFields);

            if($res && is_array($anchors)) {
                $SeoPage->setAnchors($ID, $anchors);
            }
        }

	



	if(!$res)
	{
		$strWarning.= GetMessage("SEOCONNECT_ERR").$SeoLink->LAST_ERROR."";
		$DB->Rollback();
		$bVarsFromForm = true;
	}
	else
	{
		$DB->Commit();
       if($_POST['apply']) {
         LocalRedirect("/bitrix/admin/seoconnect_add_page.php?lang=".$lang."&ID=".urlencode($ID)."&"."&".$tabControl->ActiveTabParam());
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


	(function( $ ) {
		$.widget( "ui.combobox", {
			_create: function() {
				var self = this,
					select = this.element.hide(),
					selected = select.children( ":selected" ),
					value = selected.val() ? selected.text() : "";
				var input = this.input = $( "<input>" )
					.insertAfter( select )
					.val( value )
					.autocomplete({
						delay: 0,
						minLength: 0,
						source: function( request, response ) {
							var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
							response( select.children( "option" ).map(function() {
								var text = $( this ).text();
								if ( this.value && ( !request.term || matcher.test(text) ) )
									return {
										label: text.replace(
											new RegExp(
												"(?![^&;]+;)(?!<[^<>]*)(" +
												$.ui.autocomplete.escapeRegex(request.term) +
												")(?![^<>]*>)(?![^&;]+;)", "gi"
											), "<strong>$1</strong>" ),
										value: text,
										option: this
									};
							}) );
						},
						select: function( event, ui ) {
							ui.item.option.selected = true;
							self._trigger( "selected", event, {
								item: ui.item.option
							});
						},
						change: function( event, ui ) {
							if ( !ui.item ) {
								var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
									valid = false;
								select.children( "option" ).each(function() {
									if ( $( this ).text().match( matcher ) ) {
										this.selected = valid = true;
										return false;
									}
								});
								if ( !valid ) {
									// remove invalid value, as it didn't match anything
									$( this ).val( "" );
									select.val( "" );
									input.data( "autocomplete" ).term = "";
									return false;
								}
							}
						}
					})
					.addClass( "ui-widget ui-widget-content ui-corner-left" );

				input.data( "autocomplete" )._renderItem = function( ul, item ) {
					return $( "<li></li>" )
						.data( "item.autocomplete", item )
						.append( "<a>" + item.label + "</a>" )
						.appendTo( ul );
				};

				this.button = $( "<button type='button'>&nbsp;</button>" )
					.attr( "tabIndex", -1 )
					.attr( "title", "Show All Items" )
					.insertAfter( input )
					.button({
						icons: {
							primary: "ui-icon-triangle-1-s"
						},
						text: false
					})
					.removeClass( "ui-corner-all" )
					.addClass( "ui-corner-right ui-button-icon" )
					.click(function() {
						// close if already visible
						if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
							input.autocomplete( "close" );
							return;
						}

						// work around a bug (likely same cause as #5265)
						$( this ).blur();

						// pass empty string as value to search for, displaying all results
						input.autocomplete( "search", "" );
						input.focus();
					});
			},

			destroy: function() {
				this.input.remove();
				this.button.remove();
				this.element.show();
				$.Widget.prototype.destroy.call( this );
			}
		});
	})( jQuery );

    $(function(){


    	$(function() {
    		$( ".combobox_input" ).combobox();
    	});

    });



</script>
	<style>
	.ui-button { margin-left: -1px; }
	.ui-button-icon-only .ui-button-text { padding: 0.35em; }
	.ui-autocomplete-input { margin: 0; padding: 0.48em 0 0.47em 0.45em; }
    .ui-autocomplete-input {
      width: 300px;
    }

    .ui-menu .ui-menu-item a {

       font-size:14px;
    }
	</style>
<form method="POST" id="form" name="form" action="">
<?=bitrix_sessid_post()?>
<input type="hidden" name="saveForm" value="Y">
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
<?php 
		// Список якорей
        $arTitles = array();
        $rsTitles = CSeoConnectTitle::GetList(array('ACTIVE' => 'Y'));
        while($tmp = $rsTitles->Fetch()) {
            $arTitles[] = $tmp;
        }
?>


	<tr>
		<td><?=GetMessage("SEOCONNECT_PAGE")?></td>
		<td><input type="text" name="PAGE" size="30" <?if($ID){?>readonly="readonly"<?}?> value="<?=$str_PAGE?>"></td>
	</tr>
	<tr>
		<td><span class="required">*</span><?=GetMessage("SEOCONNECT_TITLE")?></td>
		<td>
			<select name="TITLE" class="combobox_input">
    			<?foreach($arTitles as $title):?>
    					<option value="<?=$title["ID"];?>" <?if($title['ID'] == $str_TITLE){?>selected="selected"<?}?>>[<?=$title["ID"];?>] <?=$title["TITLE"];?></option>
    			<?endforeach;?>
			</select>

        </td>
	</tr>



	<tr>
    <td valign="top"><?=GetMessage("SEOCONNECT_ANCHORS")?> </td>
	<td valign="top">



    <table cellpadding="0" cellspacing="5" border="0" class="nopadding" width="100%" id="tb_seoconnect">
   <? $k = 0;
    error_reporting(E_ALL ^ E_NOTICE);
    ini_set('display_errors','On');
    $MAX_ANCHORS = COption::GetOptionString('seoconnect','ANCHORS_CNT');
    $rsAnchors = CSeoConnectPage::getAnchors($ID);
    $pageAnchors = array();
    while($tmp = $rsAnchors->Fetch()){
        $pageAnchors[$tmp['ID']] = $tmp;
    }

    $anchors = array();
    $rsAnchors = CSeoConnectAnchor::getList(array('ACTIVE' => 'Y'));
    while($tmp = $rsAnchors->Fetch()){
        $anchors[$tmp['ID']] = $tmp;
    }

	foreach($pageAnchors as $pageAnchor)
	{
	  $k++;
      ?>
         <tr>
           <td>
              <select name="ANCHORS[]" class="combobox_input">
                    <?foreach($anchors as $anchor){?>
                       <option value="<?=$anchor['ID']?>" <?if($anchor['ID'] == $pageAnchor['ID']){?>selected="selected"<?}?>><?=$anchor['ANCHOR']?> [<?=$anchor['URL']?>]</option>
                    <?}?>
              </select>
           </td>
         </tr>
      <?
	}
   ?>
   <?
	for($d = $k; $d < $MAX_ANCHORS;$d++)
	{
      ?>
         <tr>
           <td>
              <select name="ANCHORS[]" class="combobox_input">
                    <option value="" selected="selected"></option>
                    <?foreach($anchors as $anchor){?>
                       <option value="<?=$anchor['ID']?>" <?if(array_key_exists($anchor['ID'], $pageAnchors)){?>selected="selected"<?}?>><?=$anchor['ANCHOR']?> [<?=$anchor['URL']?>]</option>
                    <?}?>
              </select>
           </td>
         </tr>
      <?
	}
   ?>
  </table>

	</td>

	</tr>



	<?
	$tabControl->Buttons(array("disabled"=>false, "back_url"=>$back_url));
	$tabControl->End();
?>
</form>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");