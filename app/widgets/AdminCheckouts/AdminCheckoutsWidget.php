<?php
 /* ----------------------------------------------------------------------
 * checkoutsWidget.php
 * ----------------------------------------------------------------------
 *
 * Copyright (c) 2015, Jasper Dunker.
 * All rights reserved.
 *
 *  ----------------------------------------------------------------------
 */

require_once(__CA_LIB_DIR__."/ca/BaseWidget.php");
require_once(__CA_LIB_DIR__."/ca/IWidget.php");
require_once(__CA_APP_DIR__."/plugins/MyCheckouts/models/Checkout.php");
require_once(__CA_APP_DIR__."/plugins/MyCheckouts/models/Checkouts.php");

class AdminCheckoutsWidget extends BaseWidget implements IWidget{
	# -------------------------------------------------------
	private $opo_config;

	private $userId;

	static $s_widget_settings = array();
	# -------------------------------------------------------
	public function __construct($ps_widget_path, $pa_settings) {
		$this->title = _t('Overdue Checkouts');
		$this->description = _t('Shows overdue checkouts');

		parent::__construct($ps_widget_path, $pa_settings);

		$this->opo_config = Configuration::load($ps_widget_path.'/conf/AdminCheckoutsWidget.conf');
	}

	public function renderWidget($ps_widget_id, &$pa_settings) {
		parent::renderWidget($ps_widget_id, $pa_settings);

		if (!$this->request->isLoggedIn() || (!$this->request->user->canDoAction('can_do_library_checkin') && !$this->request->user->canDoAction('can_do_library_checkout'))) {
			return $this->opo_view->render("not_allowed_html.php");
		}
		$now = new DateTime("now", new DateTimeZone("UTC"));
		$now = $now->getTimestamp();

		$checkouts = new Checkouts();
		$currentCheckouts = $checkouts->getOverdueCheckouts($now);

		$this->opo_view->setVar("checkouts", $currentCheckouts);
		return $this->opo_view->render("main_html.php");
	}

	# -------------------------------------------------------
	/**
	 * Override checkStatus() to return true
	 */
	public function checkStatus() {
		return array(
			'description' => $this->getDescription(),
			'errors' => array(),
			'warnings' => array(),
			'available' => ((bool)$this->opo_config->get('enabled'))
		);
	}

	# -------------------------------------------------------
	/**
	 * Get widget user actions
	 */
	static public function getRoleActionList() {
		return array();
	}
}