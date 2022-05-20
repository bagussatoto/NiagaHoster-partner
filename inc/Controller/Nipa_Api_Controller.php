<?php
/**
 * @package  NiagahosterPartner
 */
namespace Inc\Controller;

use Exception;
use Inc\Api\Nipa_Api;
use Inc\Controller\Controller;
use Inc\Model\Cart;
use WP_REST_Request;

/**
*
*/
class Nipa_Api_Controller extends Controller
{
    private $api;

    public function __construct()
    {
        $this->namespace = 'nipa';
        $option = get_option( 'nipa' );
        $this->api = new Nipa_Api( $option['email'], $option['api_key'] );
    }

	public function register()
	{
        add_action( 'rest_api_init', array( $this, 'setup_routes' ) );
    }

    public function setup_routes() {
        $this->register_routes( 'get', 'products', array( $this, 'get_products' ) );
        $this->register_routes( 'get', 'product(?:/(?P<id>\d+))?', array( $this, 'get_product' ) );
        $this->register_routes( 'get', 'get-tld-list', array( $this, 'get_tld_list' ) );
        $this->register_routes( 'post', 'domain-check', array( $this, 'domain_check' ) );
        $this->register_routes( 'get', 'get-tld-pricelist', array( $this, 'get_tld_pricelist' ) );
        $this->register_routes( 'post', 'create-client', array( $this, 'create_client' ) );
        $this->register_routes( 'post', 'create-order', array( $this, 'create_order' ) );
        $this->register_routes( 'get', 'reseller-login', array( $this, 'reseller_login' ) );
        $this->register_routes( 'post', 'client-login', array( $this, 'client_login' ) );
        $this->register_routes( 'post', 'client-dashboard', array( $this, 'jump_to_client_dashboard' ) );
        $this->register_routes( 'post', 'client-logout', array( $this, 'client_logout' ) );
        $this->register_routes( 'get', 'client', array( $this, 'get_client' ) );
        $this->register_routes( 'get', 'templates', array( $this, 'get_templates' ) );
        $this->register_routes( 'get', 'whatsapp-support', array( $this, 'get_wa_support' ) );
        $this->register_routes( 'get', 'get-free-tlds-list', array( $this, 'get_free_tlds_list' ) );
        $this->register_routes( 'get', 'website-products', array( $this, 'get_website_products' ) );
    }

    private function make_request( $request, $message, $params = null )
    {
        $try     = 0;
        $result  = [];
        $success = false;
        do {
            try {
                $result  = $this->api->$request( $params );
                $success = true;
            } catch ( Exception $e ) {
                $message = $e->getMessage();
            }
            $try++;
        } while (
            $try <= 2
            && ! empty( $result['error'] )
            && strtolower( $result['error'] ) === 'unauthorized'
            && $this->api->make_auth()
        );
        if ( ! $success && ! empty( $result['error'] ) ) {
            $message = $result['error'];
        }
        return $this->return_json( $success, $message, $result );
    }

	public function get_products( WP_REST_Request $req )
	{
        $message = 'Products fetched successfully';
        return $this->make_request( 'get_products', $message );
    }

    public function get_product( WP_REST_Request $req )
	{
        $message = 'Products fetched successfully';
        $success = true;
        $result = [];
        try {
            $result = $this->api->get_product( $req->get_url_params()['id'] );
        } catch( Exception $e ) {
            $message = $e->getMessage();
            $success = false;
        }
        return $this->return_json( $success, $message, $result );
	}

    public function get_tld_list( WP_REST_Request $req )
    {
        $message = 'Domain list fetched successfuly';
        $success = true;
        $result = [];
        try {
            $result = $this->api->get_tld_list();
        } catch( Exception $e ) {
            $message = $e->getMessage();
            $success = false;
        }
        return $this->return_json( $success, $message, $result );
    }

    public function domain_check( WP_REST_Request $req )
    {
        $message = 'Checked successfully';
        $success = true;
        $result = [];
        try {
            $result = $this->api->domain_check( $req->get_params() );
        } catch( Exception $e ) {
            $message = $e->getMessage();
            $success = false;
        }
        return $this->return_json( $success, $message, $result);
    }

    public function get_tld_pricelist( WP_REST_Request $req )
    {
        $message = 'Domain list fetched successfuly';
        $success = true;
        $result  = [];
        try {
            $result = $this->api->get_domain_pricelist();
        } catch( Exception $e ) {
            $message = $e->getMessage();
            $success = false;
        }
        return $this->return_json( $success, $message, $result );
    }

    public function create_client( WP_REST_Request $req )
    {
		$message = 'New client created successflly';
		$success = true;
        $result = [];
        try {
            $return = $this->api->create_client( $req->get_params() );
            if ( $return['error'] ) {
                $message = $return['message'];
                $success = false;
            } else {
                $result = $return['result'];
                $_SESSION['nipa_client_id'] = $result;
            }
        } catch( Exception $e ) {
            $message = $e->getMessage();
            $success = false;
        }
        return $this->return_json( $success, $message, $result );
    }

    public function create_order( WP_REST_Request $req )
    {
        $message = 'New order created successflly';
        $success = true;
        $result = [];
        if ( ! $this->is_login() ) {
            throw new Exception( 'You have to login first!' );
        }
        $cart = (new Cart())->get_cart();
        try {
            $result = $this->api->create_order( $cart['items'] );
        } catch(Exception $e) {
            $message = $e->getMessage();
            $success = false;
        }

        if ( ! empty( $result['error'] ) && ! empty( $result['message'] ) ) {
            $success = false;
            $message = $result['message'];
        }

        return $this->return_json( $success, $message, $result );
    }

    public function reseller_login( WP_REST_Request $req )
    {
        $message = 'Reseller login token created and retrieved successfully';
        $success = true;
        $result = [];
        try {
            $params = ['email' => get_option( 'nipa' )['email']];
            if ( isset( $req->get_params()['plugin-changelog'] ) ) {
                $params['next'] = 'wp-plugin-changelog';
            }
            $result = $this->api->reseller_login( $params );
        } catch(Exception $e) {
            $message = $e->getMessage();
            $success = false;
        }
        return $this->return_json( $success, $message, $result );
    }

    public function client_login( WP_REST_Request $req )
    {
        $message = 'Client login failed!';
        $success = false;
        $result = [];
        $params = $req->get_params();
        if ( empty( $params['email'] ) ) {
            throw new Exception('Email is required!');
        }
        if ( empty( $params['password'] ) ) {
            throw new Exception('password is required!');
        }
        try {
            $result = $this->api->client_login( $params );
            if ( ! empty( $result['data']['client_id'] ) ) {
                $_SESSION['nipa_client_id'] = $result['data']['client_id'];
                $message = 'Client login Success!';
                $success = true;
            }
        } catch(Exception $e) {
            $message = $e->getMessage();
        }
        return $this->return_json( $success, $message, $result['data'] );
    }

    public function jump_to_client_dashboard()
	{
        $message = 'Session for client not found!';
        $success = false;
        $result = [];
        try {
            if ( $this->is_login() ) {
                $result = $this->api->client_login( array( 'id' => $_SESSION['nipa_client_id'] ) );
                $message = 'Client fetched successfully';
                $success = true;
            }
        } catch( Exception $e ) {
            $message = $e->getMessage();
        }
        return $this->return_json( $success, $message, $result['data'] );
    }

    public function client_logout()
	{
        $message = 'You are not logged in, no need for logout!';
        $success = false;
        if ( $this->is_login() ) {
            unset( $_SESSION['nipa_client_id'] );
            $message = 'Logged out!';
            $success = true;
        }
        return $this->return_json( $success, $message );
    }

    public function get_client( WP_REST_Request $req ) {
        $message = 'You are not logged in!';
        $success = false;
        $result = [];
        if ( $this->is_login() ) {
            try {
                $client = $this->api->get_client( $_SESSION['nipa_client_id'] )['result'];
                $result = array(
                    'id'         => $client['id'],
                    'first_name' => $client['first_name'],
                    'last_name'  => $client['last_name'],
                    'email'      => $client['email'],
                    'phone'      => $client['phone'],
                    'company'    => $client['company'],
                    'address_1'  => $client['address_1'],
                    'address_2'  => $client['address_2'],
                    'city'       => $client['city'],
                    'postcode'   => $client['postcode']
                );
                $message = 'Data fetched!';
                $success = true;
            } catch( Exception $e ) {
                $message = $e->getMessage();
            }
        }

        return $this->return_json( $success, $message, $result );
    }

    public function get_templates()
	{
        $message = 'Templates fetched successfully';
        $success = true;
        $result = [];
        try {
            $result = $this->api->get_templates();
        } catch( Exception $e ) {
            $message = $e->getMessage();
            $success = false;
        }
        return $this->return_json( $success, $message, $result );
    }

    public function get_wa_support()
    {
        $number = $this->api->whatsapp_support();
        if (!$number) {
            return false;
        }

        $number = preg_replace('/^0|\+62/', '62', $number);
        return 'https://wa.me/' . $number;
    }

    public function get_free_tlds_list( WP_REST_Request $req )
    {
        $message = 'Free tlds list fetched successfuly';
        $success = true;
        $result = [];
        try {
            $result = $this->api->get_free_tlds_list();
        } catch( Exception $e ) {
            $message = $e->getMessage();
            $success = false;
        }
        return $this->return_json( $success, $message, $result );
    }

    public function get_website_products( WP_REST_Request $req )
	{
        $message = 'Products fetched successfully';
        $success = true;
        $result = [];
        try {
            $result = $this->api->get_website_products();
        } catch( Exception $e ) {
            $message = $e->getMessage();
            $success = false;
        }
        return $this->return_json( $success, $message, $result );
    }
}