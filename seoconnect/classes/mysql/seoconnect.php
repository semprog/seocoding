<?

//require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seoconnect/classes/general/seoconnect.php");
//
//
//class CSeoLink extends CAllSeoLink
//{
//
//
//
//	function GetList()
//	{
//		global $DB;
//
//		$res = $DB->Query("SELECT * FROM b_seolink", false, $err_mess.__LINE__);
//
//		return $res;
//	}
//
//	/**
//	 * Добавляет инфу о группе слов
//	 * @param unknown_type $arFields
//	 * @return boolean|Ambiguous
//	 */
//	function Add($arFields)
//	{
//		global $DB;
//		if(!$this->CheckFields($arFields))
//		return false;
//
//		$IBLOCKS = $arFields["IBLOCKS"];
//		unset($arFields["IBLOCKS"]);
//
//		$arInsert = $DB->PrepareInsert("b_seolink", $arFields);
//
//		$strSql =
//		"INSERT INTO b_seolink(".$arInsert[0].") "."VALUES(".$arInsert[1].")";
//		$DB->Query($strSql);
//		$ID = $DB->LastID();
//		foreach ($IBLOCKS as $item) {
//				$strSql = "INSERT INTO b_seolinkiblocks (GID, BID) VALUES('".$ID."','".$item."');";
//				$DB->Query($strSql);
//		}
//		return $ID;
//
//	}
//
//
//	/**
//	 * Обновляет информацию о группе слов
//	 * @param unknown_type $ID
//	 * @param unknown_type $arFields
//	 * @return boolean
//	 */
//	function Update($ID, $arFields)
//	{
//		global $DB;
//		if(!$this->CheckFields($arFields))
//		{
//
//			$this->LAST_ERROR='Неверно введено название.<br />';
//			return false;
//		}
//		else
//		{
//			$IBLOCKS = $arFields["IBLOCKS"];
//			unset($arFields["IBLOCKS"]);
//			$strUpdate = $DB->PrepareUpdate("b_seolink", $arFields);
//			$strSql = "UPDATE b_seolink SET ".$strUpdate." WHERE ID=".$ID;
//				$g = fopen($_SERVER["DOCUMENT_ROOT"]."/mylog.txt", "a+");
//			fwrite($g, print_r($IBLOCKS,true));
//			fclose($g);
//			$DB->Query($strSql);
//			self::DeleteIBlocksByID($ID);
//			foreach ($IBLOCKS as $item) {
//				$strSql = "INSERT INTO b_seolinkiblocks (GID, BID) VALUES('".$ID."','".$item."');";
//				$DB->Query($strSql);
//			}
//			return true;
//		}
//
//	}
//
//	/**
//	 * Удаляет связь группы слов к инфоблокам из бд
//	 * @param unknown_type $ID
//	 */
//	function DeleteIBlocksByID($ID) {
//		global $DB;
//		$strSql ="DELETE FROM b_seolinkiblocks WHERE GID=".$ID;
//		$DB->Query($strSql);
//		return true;
//	}
//
//	/**
//	 * Удаляет настройки группы слов из бд
//	 * @param unknown_type $ID
//	 * @return boolean
//	 */
//	function Delete($ID)
//	{
//		global $DB;
//		$strSql ="DELETE FROM b_seolink WHERE ID=".$ID;
//		$DB->Query($strSql);
//		self::DeleteIBlocksByID($ID);
//		return true;
//	}
//
//	/**
//	 * Возвращает идентификаторов инфоблоков,
//	 * в которых разрешено отображение заданой группы
//	 * @param unknown_type $ID
//	 */
//	function GetIBlocksByID($ID) {
//		global $DB;
//
//		$res = $DB->Query("SELECT BID FROM b_seolinkiblocks WHERE GID=".$ID, false, $err_mess.__LINE__);
//
//		return $res;
//	}
//	function GetSeoLinkByID($ID)
//	{
//		global $DB;
//
//		$res = $DB->Query("SELECT * FROM b_seolink WHERE ID=".$ID, false, $err_mess.__LINE__);
//
//		return $res;
//	}
//
//
//
//
//}
///**
// * Класс работы со словами
// * @author mr_max
// *
// */
//class CSeoLinkWords extends CAllSeoLinkwords
//{
//	/**
//	 * Удалить все слова конкретной группы
//	 * @param unknown_type $LID
//	 * @return boolean
//	 */
//	function DeleteAll($LID)
//	{
//		global $DB;
//		$strSql ="DELETE FROM b_seolinkwords WHERE LID=".$LID;
//		$DB->Query($strSql);
//		return true;
//	}
//	/**
//	 * Добавить в бд слова определенной группы
//	 * @param unknown_type $WORDS
//	 * @return boolean
//	 */
//	function Add($WORDS)
//	{
//		global $DB;
//		foreach ($WORDS as $k=>$arFields)
//		{
//
//			if($arFields["NAME"]!='')
//			{
//
//				$arInsert = $DB->PrepareInsert("b_seolinkwords", $arFields);
//
//				$strSql =
//				"INSERT INTO b_seolinkwords(".$arInsert[0].") "."VALUES(".$arInsert[1].")";
//				$DB->Query($strSql);
//				$ID=$DB->LastID();
//			}
//		}
//		return true;
//
//	}
//
//	/**
//	 * Возвращает слова для отдельного инфоблока
//	 * @param unknown_type $BID
//	 */
//	function GetListByBID($BID)
//	{
//		global $DB;
//		$strSQL = "SELECT
//				  b_seolinkwords.NAME,
//				  b_seolinkwords.LINK,
//				  b_seolink.`FIRST`
//				FROM
//				  b_seolink
//				  RIGHT OUTER JOIN b_seolinkiblocks ON (b_seolink.ID = b_seolinkiblocks.GID)
//				  RIGHT OUTER JOIN b_seolinkwords ON (b_seolink.ID = b_seolinkwords.LID)
//				WHERE
//				  b_seolink.ACTIVE = 'Y' AND
//				  b_seolinkwords.ACTIVE = 'Y' AND
//				  b_seolinkiblocks.BID = '".$BID."';";
//		$res = $DB->Query($strSQL, false, $err_mess.__LINE__);
//		return $res;
//	}
//	function GetList($LID)
//	{
//		global $DB;
//
//		$res = $DB->Query("SELECT * FROM b_seolinkwords WHERE LID=".$LID, false, $err_mess.__LINE__);
//
//		return $res;
//	}
//
//	/**
//	 * Пропатчить текст
//	 * @param unknown_type $text
//	 * @param unknown_type $word
//	 */
//	function Patch(&$text)
//	{
//
//		if (intval($text["IBLOCK_ID"]) == 0) return;
//		$res = $this->GetListByBID($text["IBLOCK_ID"]);
//		while($word = $res->GetNext()) {
//			$pattern = "#{$word["NAME"]}#iu";
//			$replace = "<a href='{$word["LINK"]}'>{$word["NAME"]}</a> ";
//			$limit = $word["FIRST"] == 'Y' ? 1 : -1;
//			$text["DETAIL_TEXT"] = preg_replace($pattern, $replace, $text["DETAIL_TEXT"], $limit);
//			$text["PREVIEW_TEXT"] = preg_replace($pattern, $replace, $text["PREVIEW_TEXT"], $limit);
//		}
//	}
//
//}
//
//
//
//
