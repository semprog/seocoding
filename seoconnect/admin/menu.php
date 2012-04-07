<?
IncludeModuleLangFile(__FILE__);

if($APPLICATION->GetGroupRight("seoconnect")!="D")
{
	$aMenu = array(
		"parent_menu" => "global_menu_services",
		"section" => "seoconnect",
		"sort" => 200,
		"text" => GetMessage("SEOCONNECT"),
		"title" => GetMessage("SEOCONNECT"),
		"url" => "seoconnect_index.php?lang=".LANGUAGE_ID,
		"more_url" => 	Array("seoconnect_add.php"),
		"icon" => "seoconnect_menu_icon",
		"page_icon" => "seoconnect_page_icon",
		"items_id" => "menu_seoconnect",
		"items" => array(
			array(
				"text" => GetMessage("SEOCONNECT_LISTS_PAGES"),
				"url" => "seoconnect_list_pages.php?lang=".LANGUAGE_ID,
				"more_url" => Array("seoconnect_delete_pages.php", "seoconnect_add_page.php"),
				"title" => GetMessage("SEOCONNECT_LISTS_PAGES")
			),
			array(
				"text" => GetMessage("SEOCONNECT_LISTS_TITLES"),
				"url" => "seoconnect_list_titles.php?lang=".LANGUAGE_ID,
				"more_url" => Array("seoconnect_delete_titles.php", "seoconnect_add_title.php"),
				"title" => GetMessage("SEOCONNECT_LISTS_TITLES")
			),
			array(
				"text" => GetMessage("SEOCONNECT_LISTS_ANCHORS"),
				"url" => "seoconnect_list_anchors.php?lang=".LANGUAGE_ID,
				"more_url" => Array("seoconnect_delete_anchors.php", "seoconnect_add_anchor.php"),
				"title" => GetMessage("SEOCONNECT_LISTS_ANCHORS")
			),
			array(
				"text" => GetMessage("SEOCONNECT_REINDEX"),
				"url" => "seoconnect_reindex.php?lang=".LANGUAGE_ID,
				"more_url" => Array(),
				"title" => GetMessage("SEOCONNECT_REINDEX")
			),
		)
	);
	return $aMenu;
}
return false;
?>
