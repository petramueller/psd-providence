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

/**
 * Class ShelfMarkGenPlugin
 * Plugin to automatically generate shelf marks (e.g. E-INF-G001)
 * and set item source (SLUB/WISE), based on bar code attribute
 */
//TODO: Set item source
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

	# -------------------------------------------------------
	/**
	 * Hook is called by template every time a item or relationship is inserted.
	 * Not the best extension point for what we do, but the only one where we have all information we need.
	 * Used to set the shelf mark of copies
	 */
	public function hookAfterBundleInsert(&$args)
	{
		if ($args["table_name"] !== "ca_objects_x_objects") {
			return;
		}

		$relationship = $args["instance"];
		$book = new ca_objects($relationship->get("object_left_id"));
		$copy = new ca_objects($relationship->get("object_right_id"));

		if ($book->getTypeCode() !== "book" || $copy->getTypeCode() !== "copy") {
			return;
		}

		//Appears that a shelf mark is already set
		if (preg_match('/^\w-\w{1,10}-\w+$/', $copy->get("ca_objects.preferred_labels.name"))) {
			return;
		}

		$author = $book->get("ca_entities", array("returnAsArray" => true, "restrictToRelationshipTypes" => array("main_book_author")));
		$authorSurname = reset($author)["surname"];
		$category = $book->get("ca_list_items.value", array("restrictToRelationshipTypes" => "book_category"));
		if ($category === "") {
			//TODO: Remove debug code
			$category = $book->get("ca_list_items.idno", array("restrictToRelationshipTypes" => "book_category"));
		}
		$shelfMarkService = new ShelfMarkService();
		$shelfMark = $shelfMarkService->getShelfMark($authorSurname, $category);

		$locales = new ca_locales();
		$cataloguingLocales = $locales->find(array("dont_use_for_cataloguing" => 0), array("returnAs" => "ids"));

		$copy->setMode(ACCESS_WRITE);
		$copy->removeAllLabels(__CA_LABEL_TYPE_PREFERRED__);
		foreach ($cataloguingLocales as $key => $value) {
			$copy->addLabel(array("name" => $shelfMark), $value, null, true);
		}

		$copy->update(array("force" => true, "dontCheckCircularReferences" => true));
		$copy->doSearchIndexing(null, true, null);
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