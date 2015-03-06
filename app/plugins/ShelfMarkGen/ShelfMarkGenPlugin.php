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
require_once(__CA_MODELS_DIR__ . "/ca_objects.php");
require_once(__CA_MODELS_DIR__ . "/ca_object_labels.php");
require_once(__CA_MODELS_DIR__ . "/ca_locales.php");
require_once("ShelfMarkService.php");

class ShelfMarkGenPlugin extends BaseApplicationPlugin
{
	# -------------------------------------------------------
	/**
	 *
	 */
	protected $description = null;

	/**
	 *
	 */
	private $opo_config;

	/**
	 *
	 */
	private $ops_plugin_path;

	# -------------------------------------------------------
	public function __construct($ps_plugin_path)
	{
		$this->ops_plugin_path = $ps_plugin_path;
		$this->description = _t("Generates shelf marks for copies.");

		parent::__construct();

		$this->opo_config = Configuration::load($ps_plugin_path . "/conf/shelfmarkgen.conf");
	}

/*	public function hookSaveItem(&$args)
	{
		$item = $args["instance"];
		$vn_type_id = $item->get($item->getTypeFieldName());
		$va_type_list = $item->getTypeList(array("directChildrenOnly" => false, "returnHierarchyLevels" => false, "item_id" => null));
		$type = $va_type_list[$vn_type_id];
		if ($type["idno"] === "copy") {
			$relatedItems = $item->get("ca_objects.related.object_id", array("returnAsArray" => true));
			$shelfMark = $item->get("ca_objects.preferred_labels");
			$barcode = $item->get("ca_objects.barcode");
			$sourceId = $item->get("ca_objects.source_id");
			$idno = $item->get("ca_objects.idno");
			$listItems = new ca_list_items();
			$slubSourceId = $listItems->find(array("idno" => "obj_src_slub"));

			$item->setMode(ACCESS_WRITE);

		}
	}*/

	public function hookAfterBundleInsert(&$args){
		if($args["table_name"] !== "ca_objects_x_objects"){
			return;
		}

		$relationship = $args["instance"];
		$book = new ca_objects($relationship->get("object_left_id"));
		$copy = new ca_objects($relationship->get("object_right_id"));
		$copyId = $copy->get("ca_objects.object_id");

		if($book->getTypeCode() !== "book" || $copy->getTypeCode() !== "copy"){
			return;
		}

		//Appears that a shelf mark is already set
		if (preg_match('/^\w-\w{1,10}-\w+$/', $copy->get("ca_objects.preferred_labels.name"))){
			return;
		}

		$author = $book->get("ca_entities", array("returnAsArray" => true, "restrictToRelationshipTypes" => array("main_book_author")));
		$authorSurname = reset($author)["surname"];
		$category = $book->get("ca_list_items.value", array("restrictToRelationshipTypes" => "book_category"));
		if($category === ""){
			//TODO: Remove debug code
			$category = $book->get("ca_list_items.idno", array("restrictToRelationshipTypes" => "book_category"));
		}
		$shelfmarkService = new ShelfMarkService();
		$shelfmark = $shelfmarkService->getShelfmark($authorSurname, $category);

		$locales = ca_locales::getLocaleList();

		$a1 = $copy->setMode(ACCESS_WRITE);
		$a2 = $copy->addLabel(array("name" => $shelfmark), 1, null, true);
		$a3 = $copy->update(array("force" => true, "dontCheckCircularReferences" => true));

		//NOT WORKING FOR SOME REASON I DON'T UNDERSTAND, FALLING BACK TO RAW SQL
		/*
		unset($_REQUEST['form_timestamp']);

		$objectLabels = new ca_object_labels();
		$copyLabels = $objectLabels->find(array("object_id" => $relationship->get("object_right_id")), array("returnAs" => "firstModelInstance"));
		$copyLabels->setMode(ACCESS_WRITE);
		//$copyLabels->set("idno", $shelfmark);
		$a = $copyLabels->set("name_sort", $shelfmark);
		$b = $copyLabels->set("name", $shelfmark);
		$c = $copyLabels->setAsChanged("name_sort");
		$d = $copyLabels->setAsChanged("name");
		$e = $copyLabels->update(array("force" => true, "dontCheckCircularReferences" => true));
		//$u = $copy->update(array("force" => true, "dontCheckCircularReferences" => true));
		//$f = 1;//$copyLabels->update(array("force" => true, "dontCheckCircularReferences" => true));
		//BUG: Causes items to not appear in checkout search
		//$shelfmarkService->updateCopy($copyId, $shelfmark);
		*/
	}

/*	public function hookBeforeBundleInsert(&$args){
		$item = $args["instance"];
		$vn_type_id = $item->get($item->getTypeFieldName());
		$va_type_list =  $item->getTypeList(array("directChildrenOnly" => false, 'returnHierarchyLevels' => true, 'item_id' => null));
		$type = $va_type_list[$vn_type_id];
		$typeIdno = $type["idno"];
		if($type["idno"] === "copy") {
			$preferredLabelName = $item->get("ca_objects.preferred_labels");
			$preferredLabelName2 = $item->get("preferred_labels.name");
			$barcode = $item->get("ca_objects.barcode");
			$sourceId = $item->get("ca_objects.source_id");
			$relatedItems = $item->get("ca_objects.related.object_id");
			$sourceName = null;
			$listItems = new ca_list_items();
			$slubSourceId = $listItems->find(array("idno" => "obj_src_slub"));
			$item->set("preferred_labels.name", "mylabel");
			$item->set("source_id", 27);
			$preferredLabelNameNew = $item->get("ca_objects.preferred_labels");
			$sourceIdNew = $item->get("ca_objects.source_id");
			$foo = 1;
		}
	}*/

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
	 */
	static function getRoleActionList()
	{
		return array();
	}
	# -------------------------------------------------------
}

?>