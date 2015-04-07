<?php
/* ReportsController is used handle requests to the plugin and serve plugin content
*
*/

require_once(__CA_APP_DIR__ . "/plugins/Reports/models/functions.php");
require_once(__CA_MODELS_DIR__ . "/ca_objects.php");
require_once(__CA_MODELS_DIR__ . "/ca_entities.php");
include_once(__CA_LIB_DIR__ . "/ca/Search/ObjectSearch.php");
include_once(__CA_LIB_DIR__ . "/ca/Search/BaseSearch.php");
include_once(__CA_LIB_DIR__ . "/ca/Search/ObjectSearchResult.php");

class ReportsController extends ActionController
{
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
    public function __construct(&$po_request, &$po_response, $pa_view_paths = null)
    {
        // Set view path for plugin views directory
        if (!is_array($pa_view_paths)) {
            $pa_view_paths = array();
        }
        $pa_view_paths[] = __CA_APP_DIR__ . "/plugins/Reports/themes/" . __CA_THEME__ . "/views";

        // Load plugin configuration file
        $this->opo_config = Configuration::load(__CA_APP_DIR__ . '/plugins/Reports/conf/Reports.conf');

        parent::__construct($po_request, $po_response, $pa_view_paths);

        /*
		if (!$this->request->user->canDoAction('can_manage_own_checkouts')) {
			$this->response->setRedirect($this->request->config->get('error_display_url').'/n/3000?r='.urlencode($this->request->getFullUrlPath()));
			return;
		}
		$this->userId = (int)$this->request->user->get("user_id");
        */
    }

    //full inventory
    public function inventory()
    {
        /* if (!$this->request->user->canDoAction('can_manage_own_checkouts')) { return; }*/

        $this->render("reports_inventory.php");


    }

    //SLUB inventory
    public function slub()
    {
        /* if (!$this->request->user->canDoAction('can_manage_own_checkouts')) { return; }*/

        $this->render("reports_slub.php");

    }

    //get unused_days input
    public function unused()
    {
        /* if (!$this->request->user->canDoAction('can_manage_own_checkouts')) { return; }*/

        $this->render("reports_unused.php");

    }

    //print unused items list
    public function unused_list()
    {
        /* if (!$this->request->user->canDoAction('can_manage_own_checkouts')) { return; }*/

        $this->render("reports_unused_list.php");

    }

    //save unused items list
    public function unused_save()
    {
        /* if (!$this->request->user->canDoAction('can_manage_own_checkouts')) { return; }*/

        $this->render("reports_unused_save.php");

    }

    //print remove list
    public function remove()
    {

        $this->render("reports_remove.php");

    }

    //clear remove list
    public function remove_clear()
    {
        /* if (!$this->request->user->canDoAction('can_manage_own_checkouts')) { return; }*/

        $this->render("reports_remove_clear.php");

    }

    //delete remove list from DB
    public function remove_delete()
    {
        /* if (!$this->request->user->canDoAction('can_manage_own_checkouts')) { return; }*/

        $func = new Functions();

        $IDs = $func->getRemoveIDs();

        $error = $func->deleteItems($IDs);

        //always clear remove list ?!
        $func->clearRemoveList();

        $this->view->setVar('error', $error);
        $this->render("reports_remove_delete.php");

    }

}