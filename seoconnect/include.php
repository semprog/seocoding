<?
global $DBType;


require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/classes/general/seoconnect.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/classes/general/RobotsTxt.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/classes/".$DBType."/CSeoConnectAnchor.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/classes/".$DBType."/CSeoConnectPage.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/classes/".$DBType."/CSeoConnectTitle.php");


?>