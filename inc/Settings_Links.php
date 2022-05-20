<?php

/**
 * @package  NiagahosterPartner
 */

namespace Inc;

class Settings_Links
{
    public function register()
    {
        add_filter( 'plugin_action_links_' . NIPA_PLUGIN, array( $this, 'settings_link' ) );
    }

    public function settings_link($links)
    {
        $settings_link = '<a href="admin.php?page=nipa">Settings</a>';
        array_push( $links, $settings_link );
        return $links;
    }
}
