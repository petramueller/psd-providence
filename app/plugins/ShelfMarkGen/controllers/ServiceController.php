<?php
 /* ----------------------------------------------------------------------
 * ServiceController.php
 * ----------------------------------------------------------------------
 *
 * Copyright (c) 2015, Jasper Dunker.
 * All rights reserved.
 *
 *  ----------------------------------------------------------------------
 */
require_once(__CA_LIB_DIR__.'/ca/Service/BaseServiceController.php');
require_once(__CA_MODELS_DIR__.'/ca_editor_ui_bundle_placements.php');
require_once('../ShelfMarkService.php');

class ServiceController extends BaseServiceController {
	public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
		parent::__construct($po_request, $po_response, $pa_view_paths);
	}

	//GET /service.php/shelfmarkgen/Service/Shelfmark
	public function Shelfmark(){
		header('Content-Type: application/json');
		$bookId = $this->request->getParameter("bookid", pInteger);
	}

	//GET /service.php/shelfmarkgen/Service/Placement
	public function Placement() {
		header('Content-Type: application/json');
		$placementcode = $this->request->getParameter("placementcode", pString);
		$screenid = $this->request->getParameter("screenid", pInteger);

		if($placementcode === "" || $screenid === ""){
			http_response_code(400);
		}

		$placement = new ca_editor_ui_bundle_placements();
		$placementId = $placement->find(array("screen_id" => $screenid, "placement_code" => $placementcode), array("returnAs" => "firstId"));

		$this->view->setVar('placementcode', array(
			'placementid' => $placementId
		));

		$this->render('service_placement_json.php');
	}
}