<?php

/**
 * @package  NiagahosterPartner
 */

namespace Inc\Callbacks;

class Admin_Callbacks
{
    public function admin_dashboard()
    {
        return require_once( NIPA_PLUGIN_PATH . 'templates/admin/dashboard.php' );
    }
}
