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




if($REQUEST_METHOD=="POST"  && $_POST['rewind']) {

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




require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>


<table>
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
                              $('.loading_img').hide();
                              $('.rewind_done').show();
                              return;
                         }

                         if(data.status == 'proccess') {
                                $('.rewind_status').show();
                                $('.loading_img').show();
                                $('.rewind_status .status_cnt').html(data.step);
                                $.post('<?=$APPLICATION->GetCurPageParam()?>', {rewind:'Y', step:data.step}, proccessPages,'json' );
                         }

                   }

              } )
           </script>
      </td>
      <td>
        <div class="loading_img" style="display:none;">
          <img src="/bitrix/themes/.default/seoconnect_loading.gif" />
        </div>
      </td>
    </tr>
</table>




<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");