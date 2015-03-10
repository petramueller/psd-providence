<?php
 /* ----------------------------------------------------------------------
 * MyCheckoutsPlugin.php
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

/**
 * Class MyCheckoutsPlugin
 * Provides extended checkout features.
 */
class MyCheckoutsPlugin extends BaseApplicationPlugin
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

		$this->opo_config = Configuration::load($ps_plugin_path . "/conf/mycheckouts.conf");
	}

	# -------------------------------------------------------
	/**
	 * Insert activity menu
	 */
	public function hookRenderMenuBar($pa_menu_bar) {
		if ($o_req = $this->getRequest()) {
			if (!$o_req->user->canDoAction('can_manage_own_checkouts')) { return true; }

			if (isset($pa_menu_bar['MyCheckouts'])) {
				$va_menu_items = $pa_menu_bar['MyCheckouts']['navigation'];
				if (!is_array($va_menu_items)) { $va_menu_items = array(); }
			} else {
				$va_menu_items = array();
			}

			$va_menu_items['MyCheckouts'] = array(
				'displayName' => _t('Overview'),
				"default" => array(
					'module' => 'MyCheckouts',
					'controller' => 'MyCheckouts',
					'action' => 'Index'
				)
			);

			if (isset($pa_menu_bar['MyCheckouts'])) {
				$pa_menu_bar['MyCheckouts']['navigation'] = $va_menu_items;
			} else {
				$pa_menu_bar['MyCheckouts'] = array(
					'displayName' => _t('My Checkouts'),
					'navigation' => $va_menu_items
				);
			}
		}

		return $pa_menu_bar;
	}

	# -------------------------------------------------------
	/**
	 * Override checkStatus() to return true
	 */
	//TODO: Issue warning if table does not exists
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
		return array('can_manage_own_checkouts' => array(
			'label' => _t('Can manage own checkouts'),
			'description' => _t('User can annotate and see his checkout.')
		));
	}
}