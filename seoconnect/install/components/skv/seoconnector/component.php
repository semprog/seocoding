<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/*************************************************************************
	Processing of received parameters
*************************************************************************/
if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600;


/*************************************************************************
			Work with cache
*************************************************************************/


if($this->StartResultCache(false, $USER->GetGroups()))
{
	if(!CModule::IncludeModule("seoconnect"))
	{
		$this->AbortResultCache();
		ShowError("");
		return;
	}
    $page = new CSeoConnectPage;
    $arResult = $page->processUrl($_SERVER["REQUEST_URI"]);



	$this->IncludeComponentTemplate();
}
