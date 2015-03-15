<?php
 /* ----------------------------------------------------------------------
 * DeleteChildrenPlugin.php
 * ----------------------------------------------------------------------
 *
 * Copyright (c) 2015, Jasper Dunker.
 * All rights reserved.
 *
 *  ----------------------------------------------------------------------
 */

require_once(__CA_APP_DIR__.'/models/ca_objects.php');

class DeleteChildrenPlugin extends BaseApplicationPlugin {
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
		$this->description = _t("Deletes all children upon object deletion.");
		parent::__construct();
		$this->opo_config = Configuration::load($ps_plugin_path . "/conf/DeleteChildren.conf");
	}

	public function hookDeleteItem($args){
		$item = $args["instance"];
		$itemId = (int)$args["id"];

		if(!($item instanceof ca_objects)){
			return;
		}

		$children = $item->find(array("parent_id" => $itemId), array("returnAs" => "modelInstances"));

		foreach ($children as $child) {
			$child->setMode(ACCESS_WRITE);
			$child->delete();
		}
	}

	# -------------------------------------------------------
	/**
	 * Override checkStatus() to return true, always correctly initialized
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