<?php
/* ----------------------------------------------------------------------
* ReportsPlugin.php
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
 * Class ReportsPlugin
 * Provides extended checkout features.
 */
class ReportsPlugin extends BaseApplicationPlugin
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
        $this->description = _t("generates reports");

        parent::__construct();

        $this->opo_config = Configuration::load($ps_plugin_path . "/conf/Reports.conf");
    }

    # -------------------------------------------------------
    /**
     * Insert activity menu
     */

    public function hookRenderMenuBar($pa_menu_bar)
    {
        if ($o_req = $this->getRequest()) {
            if (!$o_req->user->canDoAction('can_generate_reports')) {
                return true;
            }

            if (isset($pa_menu_bar['Reports'])) {
                $va_menu_items = $pa_menu_bar['Reports']['navigation'];
                if (!is_array($va_menu_items)) {
                    $va_menu_items = array();
                }
            } else {
                $va_menu_items = array();
            }

            // define dropdown menu items
            $va_menu_items['inventory'] = array(
                'displayName' => _t('Inventar'),
                "default" => array(
                    'module' => 'Reports',
                    'controller' => 'Reports',
                    'action' => 'inventory'
                )
            );

            $va_menu_items['unused'] = array(
                'displayName' => _t('Ungenutzte Medien'),
                "default" => array(
                    'module' => 'Reports',
                    'controller' => 'Reports',
                    'action' => 'unused'
                )
            );

            $va_menu_items['remove'] = array(
                'displayName' => _t('Aussonderungsliste'),
                "default" => array(
                    'module' => 'Reports',
                    'controller' => 'Reports',
                    'action' => 'remove'
                )
            );

            $va_menu_items['slub'] = array(
                'displayName' => _t('SLUB-Bestand'),
                "default" => array(
                    'module' => 'Reports',
                    'controller' => 'Reports',
                    'action' => 'slub'
                )
            );


            //print menu items
            if (isset($pa_menu_bar['Reports'])) {
                $pa_menu_bar['Reports']['navigation'] = $va_menu_items;
            } else {
                $pa_menu_bar['Reports'] = array(
                    'displayName' => _t('Reports'),
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
        return array('can_generate_reports' => array(
            'label' => _t('can generate reports'),
            'description' => _t('can generate reports')
        ));
    }


    /*

          static function getRoleActionList()
        {
            return array('can_manage_own_checkouts' => array(
                'label' => _t('Can manage own checkouts'),
                'description' => _t('User can annotate and see his checkout.')
            ));
        }
    */


}