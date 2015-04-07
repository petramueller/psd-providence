<?php
 /* ----------------------------------------------------------------------
 * PrintLabelsController.php
 * ----------------------------------------------------------------------
 *
 * Copyright (c) 2015, Jasper Dunker.
 * All rights reserved.
 *
 *  ----------------------------------------------------------------------
 */

require_once(__CA_APP_DIR__.'/controllers/find/SearchObjectsController.php');
include_once(__CA_LIB_DIR__."/ca/Search/ObjectSearch.php");

class PrintLabelsController extends SearchObjectsController {
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
		$pa_view_paths[] = __CA_APP_DIR__."/plugins/ShelfMarkGen/themes/".__CA_THEME__."/views";

		// Load plugin configuration file
		$this->opo_config = Configuration::load(__CA_APP_DIR__.'/plugins/ShelfMarkGen/conf/shelfmarkgen.conf');
		$this->ops_tablename = "ca_objects";
		parent::__construct($po_request, $po_response, $pa_view_paths);

		MetaTagManager::addLink('stylesheet', __CA_URL_ROOT__."/app/plugins/ShelfMarkGen/themes/".__CA_THEME__."/css/shelfmarkgen.css",'text/css');
		$this->userId = (int)$this->request->user->get("user_id");
	}

	public function Index(){
		if (!$this->request->user->canDoAction('can_print_labels')) {
			$this->render("forbidden.php");
			return;
		}

		$labelService = new LabelService();
		$labels = $labelService->getLabels();
		$this->view->setVar('labels', $labels);
		$this->render("printlabels_index.php");
	}

	public function Delete(){
		if (!$this->request->user->canDoAction('can_print_labels')) {
			$this->render("forbidden.php");
			return;
		}

		$param = $this->request->getParameter("print", pArray);
		$shelfMarks = array();

		foreach($param as $p){
			if(isset($p["shelfmark"])){
				array_push($shelfMarks, $p["shelfmark"]);
			}
		}

		$labelService = new LabelService();
		$labelService->removeObjectFromPrintQueue($shelfMarks);
		$this->opo_response->addHeader("Location", $this->request->getBaseUrlPath().'/'.$this->request->getScriptName() . "/ShelfMarkGen/PrintLabels/Index");
	}
}