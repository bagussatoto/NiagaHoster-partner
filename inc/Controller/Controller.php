<?php

/**
 * @package  NiagahosterPartner
 */

namespace Inc\Controller;

class Controller
{
    protected $namespace;

    protected function register_routes( $method, $endpoint, $callback )
    {
        register_rest_route($this->namespace, "/$endpoint", array(
            'methods'       => $method,
            'callback'      => $callback,
            'show_in_index' => false,
            'permission_callback' => function () {
                return wp_verify_nonce( $_SERVER['HTTP_X_WP_NONCE'], 'wp_rest' );
            }
        ));
    }

    protected function return_json( $status, $message, $data = [] )
    {
        wp_send_json(array(
            'success' => $status,
            'message' => $message,
            'data'    => $data
        ));
        wp_die();
    }

    protected function is_login()
    {
        if ( isset( $_SESSION['nipa_client_id'] ) && $_SESSION['nipa_client_id'] ) {
            return true;
        }
        return false;
    }
}
