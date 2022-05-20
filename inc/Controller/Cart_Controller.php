<?php

/**
 * @package  NiagahosterPartner
 */

namespace Inc\Controller;

use Exception;
use Inc\Controller\Controller;
use Inc\Model\Cart;
use WP_REST_Request;

/**
 *
 */
class Cart_Controller extends Controller
{
    private $cart;

    public function __construct()
    {
        $this->namespace = 'cart';
        $this->cart = new Cart();
    }

    public function register()
    {
        add_action( 'rest_api_init', array( $this, 'setup_routes' ) );
    }

    public function setup_routes()
    {
        $this->register_routes( 'get', 'get', array( $this, 'get_cart' ) );
        $this->register_routes( 'post', 'add-item', array( $this, 'add_item' ) );
        $this->register_routes( 'post', 'remove-item', array( $this, 'remove_item' ) );
        $this->register_routes( 'post', 'reset', array( $this, 'reset' ) );
    }

    public function get_cart( WP_REST_Request $req )
    {
        $message = 'Cart fetched successfully';
        $success = true;
        $result = [];
        try {
            $result = $this->cart->get_cart();
        } catch ( Exception $e ) {
            $message = $e->getMessage();
            $success = false;
        }
        $this->return_json( $success, $message, $result ) ;
    }

    public function add_item( WP_REST_Request $req )
    {
        $message = 'Item added successfully';
        $success = true;
        $result = [];
        try {
            $result = $this->cart->add_item( $req->get_params() );
        } catch ( Exception $e ) {
            $message = $e->getMessage();
            $success = false;
        }
        $this->return_json( $success, $message, $result );
    }

    public function remove_item( WP_REST_Request $req )
    {
        $message = 'Item removed successfully';
        $success = true;
        $result = [];
        try {
            $result = $this->cart->remove_item( $req->get_params()['id'] );
        } catch ( Exception $e ) {
            $message = $e->getMessage();
            $success = false;
        }
        $this->return_json( $success, $message, $result );
    }

    public function reset( WP_REST_Request $req )
    {
        $message = 'Cart resetted successfully';
        $success = true;
        $result = [];
        try {
            $result = $this->cart->reset( $req->get_params()['cart_id'] );
        } catch ( Exception $e ) {
            $message = $e->getMessage();
            $success = false;
        }
        $this->return_json( $success, $message, $result );
    }
}
