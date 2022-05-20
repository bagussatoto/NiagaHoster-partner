<?php

/**
 * @package  NiagahosterPartner
 */

namespace Inc\Hook;

use Inc\Api\Nipa_Api;

class Plugin_Info_Update
{
    private $api;
    private $slug   = NULL;
    private $plugin = NULL;
    private $info   = NULL;

    public function __construct()
    {
        $nipa         = get_option( 'nipa' );
        $this->api    = new Nipa_Api( $nipa['email'], $nipa['api_key'] );
        $this->info   = $this->get_cache() ? $this->get_cache() : $this->get_info();
        $this->slug   = NIPA_SLUG;
        $this->plugin = NIPA_PLUGIN;
    }

    private function get_cache()
    {
        return get_transient( 'nipa_plugin_update_' . $this->slug );
    }

    private function get_info()
    {
        $remote = $this->api->plugin_info();

        if ( empty( $remote ) || !empty( $remote['error'] ) ) {
            return false;
        }

        set_transient( 'nipa_plugin_update_' . $this->slug, $remote, 3600 );

        return $remote;
    }

    public function register()
    {
        add_filter( 'site_transient_update_plugins', array( $this, 'niagahoster_plugin_update') );
    }

    function niagahoster_plugin_update( $transient ) {
        if ( empty( $transient->checked ) || !$this->info ) {
            return $transient;
        }

        $res               = new \stdClass();
        $res->slug         = $this->slug;
        $res->plugin       = $this->plugin;
        $res->new_version  = $this->info['version'];
        $res->package      = $this->info['download_url'];
        $res->requires_php = $this->info['requires_php'];

        if ( $transient->checked[NIPA_PLUGIN] == $this->info['version'] ) {
            $transient->no_update[$res->plugin] = $res;
        } else {
            $transient->response[$res->plugin] = $res;
        }

        return $transient;
    }
}

