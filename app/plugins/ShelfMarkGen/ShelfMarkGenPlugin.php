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
require_once("KeyProviderFactory.php");
require_once("IKeyProvider.php");
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

	private $keyProviderFactory;

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
		$this->keyProviderFactory = new KeyProviderFactory();
	}

	public function hookDuplicateItem($args){
		if ($args["table_name"] !== "ca_objects") {
			return;
		}

		$item = $args["duplicate"];
		if(!($item instanceof ca_objects)){
			return null;
		}

		$parent = $this->getParent($item);
		$keyProvider = $this->getKeyProvider($parent);

		//Appears that we don't need to take action
		if($keyProvider === null){
			return;
		}

		$item->setMode(ACCESS_WRITE);
		$this->setShelfMark($parent, $item, $keyProvider);
		$this->setObjectSource($parent, $item, $keyProvider);
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
		if(!($item instanceof ca_objects)){
			return;
		}

		$parent = $this->getParent($item);
		$keyProvider = $this->getKeyProvider($parent);

		//Appears that we don't need to take action
		if($keyProvider === null){
			return;
		}

		$item->setMode(ACCESS_WRITE);
		$this->setShelfMark($parent, $item, $keyProvider);
		$this->setObjectSource($parent, $item, $keyProvider);
		$item->update();
		$item->doSearchIndexing(null, true, null);
	}

	/**
	 * Sets the shelf mark to an auto generated value, if none is set.
	 * @param $parent ca_objects The parent object which provides us with the information we need
	 * @param $keyProvider IKeyProvider A key provider
	 */
	private function setShelfMark($parent, &$copy, $keyProvider){
		//Appears that a shelf mark is already set, but duplicates need a new shelfmark.
		if (preg_match('/^\w-\w{1,10}-\w+$/', $copy->get("ca_objects.preferred_labels.name"))
			&& !preg_match('/^[\w-]+\s\[Duplicate\]$/', $copy->get("ca_objects.preferred_labels.name"))) {
			return;
		}

		$author = $parent->get("ca_entities", array("returnAsArray" => true, "restrictToRelationshipTypes" => array($keyProvider->getMainAuthorRelationshipKey())));
		$authorSurname = reset($author)["surname"];

		$categoryId = $parent->get($keyProvider->getCategoryAttributeKey());
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
	 * @param $parent
	 * @param $copy
	 * @param $keyProvider IKeyProvider
	 * @internal param ca_objects $item The copy which is saved.
	 */
	private function setObjectSource($parent, &$copy, $keyProvider){
		//We need to set the source only on items that can potentially come from SLUB.
		if(!in_array($copy->getTypeCode(), $keyProvider->getTypesWithObjectSource(), true)){
			return;
		}
		$barCode = $copy->get("ca_objects.barcode");
		$listItems = new ca_list_items();
		if($barCode != "") {
			$slubSourceId = $listItems->find(array("idno" => "obj_src_slub"), array("returnAs" => "firstId"));
			$copy->set("source_id", $slubSourceId);
		} else {
			$wiseSourceId = $listItems->find(array("idno" => "obj_src_own"), array("returnAs" => "firstId"));
			$copy->set("source_id", $wiseSourceId);
		}
	}

	private function getKeyProvider($parent){
		$keyProvider = null;
		if(!($parent instanceof ca_objects)){
			return $keyProvider;
		}
		try{
			$keyProvider = $this->keyProviderFactory->createKeyProvider($parent->getTypeCode());
		} catch (InvalidArgumentException $ex){
			//Let it fail silently, we don't really care
		}
		return $keyProvider;
	}

	private function getParent($item){
		$parent = new ca_objects($item->get("parent_id"));
		if(!($parent instanceof ca_objects)){
			return null;
		}
		return $parent;
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