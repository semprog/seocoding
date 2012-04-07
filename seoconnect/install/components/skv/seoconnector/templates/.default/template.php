<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
 //   var_dump($arResult);
?>

<div>
   <div>
      <h4><?=$arResult['TITLE']['TITLE']?></h4>
   </div>
   <div>
    <ul>
      <?foreach($arResult["ANCHORS"] as $anchor){?>
        <li><a title="<?=$anchor['ANCHOR']?>" href="<?=$anchor['URL']?>"><?=$anchor['ANCHOR']?></a></li>
      <?}?>
    </ul>
   </div>
</div>