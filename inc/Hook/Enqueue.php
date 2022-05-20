<?php

/**
 * @package  NiagahosterPartner
 */

namespace Inc\Hook;

/**
 *
 */
class Enqueue
{
    public function register()
    {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_scripts' ) );
    }

    public function enqueue_admin_scripts()
    {
        $my_js_ver  = date( 'ymd-Gis', filemtime( NIPA_PLUGIN_PATH . 'assets/js/nipaadmin.js' ) );
        $my_css_ver = date( 'ymd-Gis', filemtime( NIPA_PLUGIN_PATH . 'assets/css/nipaadmin.css' ) );

        wp_enqueue_style( 'nipaadminstyle', NIPA_PLUGIN_URL . 'assets/css/nipaadmin.css', array(), $my_css_ver );
        wp_enqueue_script( 'nipaadminscript', NIPA_PLUGIN_URL . 'assets/js/nipaadmin.js', array(), $my_js_ver );
        $this->set_localize_script( 'nipaadminscript' );
    }

    public function enqueue_front_scripts()
    {
        $my_js_ver  = date( 'ymd-Gis', filemtime( NIPA_PLUGIN_PATH . 'assets/js/nipafront.js' ) );
        $my_css_ver = date( 'ymd-Gis', filemtime( NIPA_PLUGIN_PATH . 'assets/css/nipafront.css' ) );

        wp_enqueue_style( 'nipafrontstyle', NIPA_PLUGIN_URL . 'assets/css/nipafront.css', array(), $my_css_ver );
        wp_enqueue_script( 'nipafrontscript', NIPA_PLUGIN_URL . 'assets/js/nipafront.js', array(), $my_js_ver );
        $this->set_localize_script( 'nipafrontscript' );
    }

    private function set_localize_script( $handle )
    {
        $nipa_plugin_onactivate = get_option('nipa_plugin_onactivate');
        update_option('nipa_plugin_onactivate', false);

        wp_localize_script($handle, 'nipa', array(
            'ajax_url'          => $this->get_base_url() . '/wp-json',
            'nonce'             => wp_create_nonce( 'wp_rest' ),
            'plugin_onactivate' => $nipa_plugin_onactivate
        ));
    }

    private function get_base_url()
    {
        $url = get_home_url();
        $pattern = "/^https?:\/\/.+$/";
        if ( ! empty( $_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' && ! preg_match( $pattern, $url ) ) {
            return str_replace( 'http:', 'https:', $url );
        }
        return $url;
    }
}
