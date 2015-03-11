<?php
 /* ----------------------------------------------------------------------
 * MyCheckoutService.php
 * ----------------------------------------------------------------------
 *
 * Copyright (c) 2015, Jasper Dunker.
 * All rights reserved.
 *
 *  ----------------------------------------------------------------------
 */

require_once(__CA_LIB_DIR__.'/ca/Service/BaseServiceController.php');
require_once(__CA_APP_DIR__ . "/plugins/MyCheckouts/models/Checkouts.php");
require_once(__CA_APP_DIR__ . "/plugins/MyCheckouts/models/Checkout.php");

class ServiceController extends BaseServiceController {
	public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
		parent::__construct($po_request, $po_response, $pa_view_paths);
	}

	//GET /service.php/MyCheckouts/Service/SetNote
	public function SetNote(){
		if (!$this->request->user->canDoAction('can_manage_own_checkouts')) {
			$this->response->addHeader("HTTP/1.0", "403 Forbidden");
			$this->response->setRedirect($this->request->config->get('error_display_url').'/n/3000?r='.urlencode($this->request->getFullUrlPath()));
			return;
		}

		$date = DateTime::createFromFormat("Y-m-d", $this->request->getParameter("due_date", pString));
		$date->setTime(0,0,0);
		$checkout = new Checkout();
		$checkout->checkout_id = $this->request->getParameter("checkout_id", pInteger);
		$checkout->prename = $this->request->getParameter("prename", pString);
		$checkout->surname = $this->request->getParameter("surname", pString);
		$checkout->email = $this->request->getParameter("email", pString);
		$checkout->student_due_date = $date;
		$checkout->note = $this->request->getParameter("note", pString);

		$checkouts = new Checkouts();
		$checkouts->setCheckoutNote($checkout);
	}

}