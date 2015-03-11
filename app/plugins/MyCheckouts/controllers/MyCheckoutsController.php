<?php
 /* ----------------------------------------------------------------------
 * MyCheckouts.php
 * ----------------------------------------------------------------------
 *
 * Copyright (c) 2015, Jasper Dunker.
 * All rights reserved.
 *
 *  ----------------------------------------------------------------------
 */

require_once(__CA_APP_DIR__ . "/plugins/MyCheckouts/models/Checkouts.php");

class MyCheckoutsController extends ActionController {
	# -------------------------------------------------------
	/**
	 * Plugin configuration file
	 */
	private $opo_config;

	/**
	 * @var int User ID of currently logged in user
	 */
	private $userId;

	/**
	 * Constructor
	 * @param RequestHTTP $po_request
	 * @param ResponseHTTP $po_response
	 * @param null|array $pa_view_paths
	 */
	public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
		// Set view path for plugin views directory
		if (!is_array($pa_view_paths)) { $pa_view_paths = array(); }
		$pa_view_paths[] = __CA_APP_DIR__."/plugins/MyCheckouts/themes/".__CA_THEME__."/views";

		// Load plugin configuration file
		$this->opo_config = Configuration::load(__CA_APP_DIR__.'/plugins/MyCheckouts/conf/mycheckouts.conf');

		parent::__construct($po_request, $po_response, $pa_view_paths);

		if (!$this->request->user->canDoAction('can_manage_own_checkouts')) {
			$this->response->setRedirect($this->request->config->get('error_display_url').'/n/3000?r='.urlencode($this->request->getFullUrlPath()));
			return;
		}
		MetaTagManager::addLink('stylesheet', __CA_URL_ROOT__."/app/plugins/MyCheckouts/themes/".__CA_THEME__."/css/mycheckouts.css",'text/css');
		$this->userId = (int)$this->request->user->get("user_id");
	}

	public function Index(){
		if (!$this->request->user->canDoAction('can_manage_own_checkouts')) { return; }
		$checkouts = new Checkouts();
		//2147483647: MySQL max integer value for signed integers
		//we don't limit the size of the result set here
		$currentCheckouts = $checkouts->getCurrentCheckouts($this->userId, 0, 50);
		$this->view->setVar('checkouts', $currentCheckouts);

		//Output hierarchical table in view
		$this->render("mycheckouts_index.php");
	}
}