<?php
/* ----------------------------------------------------------------------
 * ShelfMarkGenPlugin.php
 * ----------------------------------------------------------------------
 *
 * Copyright (c) 2015, Jasper Dunker.
 * All rights reserved.
 *
 *  ----------------------------------------------------------------------
 */
require_once(__CA_MODELS_DIR__ . "/ca_lists.php");
require_once(__CA_MODELS_DIR__ . "/ca_list_items.php");
require_once(__CA_MODELS_DIR__ . "/ca_objects.php");
require_once(__CA_MODELS_DIR__ . "/ca_object_labels.php");
require_once(__CA_MODELS_DIR__ . "/ca_locales.php");
require_once("ShelfMarkService.php");

/**
 * Class ShelfMarkGenPlugin
 * Plugin to automatically generate shelf marks (e.g. E-INF-G001)
 * and set item source (SLUB/WISE), based on bar code attribute
 */
class ShelfMarkGenPlugin extends BaseApplicationPlugin
{
	# -------------------------------------------------------
	/**
	 * Plugin description
	 */
	protected $description = null;

	/**
	 * Plugin config
	 */
	private $opo_config;

	/**
	 * Plugin path
	 */
	private $ops_plugin_path;

	# -------------------------------------------------------
	/**
	 * Default constructor
	 * @param $ps_plugin_path string Path to plugin
	 */
	public function __construct($ps_plugin_path)
	{
		$this->ops_plugin_path = $ps_plugin_path;
		$this->description = _t("Generates shelf marks for copies.");

		parent::__construct();

		$this->opo_config = Configuration::load($ps_plugin_path . "/conf/shelfmarkgen.conf");
	}

	public function hookDuplicateItem($args){
		if ($args["table_name"] !== "ca_objects") {
			return;
		}

		$item = $args["duplicate"];
		if(!($item instanceof ca_objects) || ($item->getTypeCode() !== "copy" && $item->getTypeCode() !== "paper_copy")){
			return;
		}

		$item->setMode(ACCESS_WRITE);
		$this->setShelfMark($item);
		$this->setObjectSource($item);
		$item->update();
		$item->doSearchIndexing(null, true, null);

	}

	# -------------------------------------------------------
	/**
	 * Hook is called by template every time a item or relationship was inserted.
	 * Not the best extension point for what we do, but the only one where we have all information we need.
	 * Used to set the shelf mark as well as source (WISE or SLUB) of copies
	 */
	public function hookSaveItem(&$args){
		if ($args["table_name"] !== "ca_objects") {
			return;
		}

		$item = $args["instance"];
		if(!($item instanceof ca_objects) || ($item->getTypeCode() !== "copy" && $item->getTypeCode() !== "paper_copy")){
			return;
		}

		$item->setMode(ACCESS_WRITE);
		$this->setShelfMark($item);
		$this->setObjectSource($item);
		$item->update();
		$item->doSearchIndexing(null, true, null);
	}

	/**
	 * Sets the shelf mark to an auto generated value, if none is set.
	 * @param $copy ca_objects The copy which is saved
	 */
	private function setShelfMark(&$copy){
		//Appears that a shelf mark is already set, but duplicates need a new shelfmark.
		if (preg_match('/^\w-\w{1,10}-\w+$/', $copy->get("ca_objects.preferred_labels.name"))
			&& !preg_match('/^[\w-]+\s\[Duplicate\]$/', $copy->get("ca_objects.preferred_labels.name"))) {
			return;
		}

		$book = new ca_objects($copy->get("parent_id"));
		if(!($book instanceof ca_objects) || ($book->getTypeCode() !== "book" && $book->getTypeCode() !== "paper")){
			return;
		}

		$identifier = array("relationship" => "main_book_author", "category" => "ca_objects.category_list_attr");
		if($copy->getTypeCode() === "paper_copy"){
			$identifier["category"] = "ca_objects.paper_category_list_attr";
			$identifier["relationship"] = "main_paper_author";
		}
		
		$author = $book->get("ca_entities", array("returnAsArray" => true, "restrictToRelationshipTypes" => array($identifier["relationship"])));
		$authorSurname = reset($author)["surname"];

		$categoryId = $book->get($identifier["category"]);
		$category = new ca_list_items($categoryId);
		$categoryValue = $category->get("item_value");
		if($categoryValue == "" || $categoryValue == null){
			$categoryValue = $category->get("idno");
		}

		$shelfMarkService = new ShelfMarkService();
		$shelfMark = $shelfMarkService->getShelfMark($authorSurname, $categoryValue);

		$locales = new ca_locales();
		$cataloguingLocales = $locales->find(array("dont_use_for_cataloguing" => 0), array("returnAs" => "ids"));

		$copy->removeAllLabels(__CA_LABEL_TYPE_PREFERRED__);
		foreach ($cataloguingLocales as $key => $value) {
			$copy->addLabel(array("name" => $shelfMark), $value, null, true);
		}
	}

	/**
	 * Sets the object source to WISE (no bar code) or SLUB if a bar code is present.
	 * @param $item ca_objects The copy which is saved.
	 */
	private function setObjectSource(&$item){
		//We don't need to set the source on items other than book copies
		if($item->getTypeCode() !== "copy"){
			return;
		}
		$barCode = $item->get("ca_objects.barcode");
		$listItems = new ca_list_items();
		if($barCode != "") {
			$slubSourceId = $listItems->find(array("idno" => "obj_src_slub"), array("returnAs" => "firstId"));
			$item->set("source_id", $slubSourceId);
		} else {
			$wiseSourceId = $listItems->find(array("idno" => "obj_src_own"), array("returnAs" => "firstId"));
			$item->set("source_id", $wiseSourceId);
		}
	}

	# -------------------------------------------------------
	/**
	 * Override checkStatus() to return true - the statisticsViewerPlugin always initializes ok... (part to complete)
	 */
	public function checkStatus()
	{
		return array(
			"description" => $this->getDescription(),
			"errors" => array(),
			"warnings" => array(),
			"available" => ((bool)$this->opo_config->get("enabled"))
		);
	}

	# -------------------------------------------------------
	/**
	 * Add plugin user actions
	 * We don't have any custom permissions, but need to fulfill the contract defined by
	 * IApplicationPlugin.
	 */
	static function getRoleActionList()
	{
		return array();
	}
}