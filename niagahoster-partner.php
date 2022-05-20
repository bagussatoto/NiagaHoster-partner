<?php

/**
 * Plugin Name: Niagahoster Partner
 * Plugin URI: https://niagahoster.co.id/plugin-niagahoster-partner
 * Description: An easy way to sell domain and hosting from your website
 * Version: 1.0.9
 * Author: Niagahoster Dev Team
 * Author URI: https://niagahoster.co.id
 * Requires PHP: 7.1
 * License: GPLv2 or later
 * Text Domain: niagahoster-partner
 */

// If this file is called firectly, abort!!!
defined( 'ABSPATH' ) or die( 'Hey, what are you doing here? You silly human!' );

if ( ! is_admin() ){
    // Avoid session locks with REST_API
    add_action( 'init','register_new_session' );
    add_action( 'wp_logout', 'destroy_this_session' );
    add_action( 'wp_login', 'destroy_this_session' );
}

if ( ! defined( 'NIPA_API_BASE_URL' ) ) {
    define( 'NIPA_API_BASE_URL', 'https://partner.niagahoster.co.id/api/v2/' );
}

if ( ! defined( 'NIPA_PLUGIN' ) ) {
    define( 'NIPA_PLUGIN',  plugin_basename( dirname( __FILE__ ) ) . '/niagahoster-partner.php' );
}

if ( ! defined( 'NIPA_SLUG' ) ) {
    define( 'NIPA_SLUG',  plugin_basename( dirname( __FILE__ ) ) );
}

if ( ! defined( 'NIPA_PLUGIN_PATH' ) ) {
    define( 'NIPA_PLUGIN_PATH',  plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'NIPA_PLUGIN_URL' ) ) {
    define( 'NIPA_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'DOMAIN' ) ) {
    define( 'DOMAIN', str_replace(['http://', 'https://'], '', get_home_url()) );
}

// Require once the Composer Autoload
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
    require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

function register_new_session()
{
    if( ! session_id() ){
        session_start();
    }
}

function destroy_this_session()
{
    session_destroy ();
}

// this function is to revert loopback test
// to wordpress 5.5 loopback test.
// will be removed if the bug fixed by Wordpress dev.
//

function is_version( $operator = '=', $version = '5.6' ) {
	global $wp_version;
	return version_compare( $wp_version, $version, $operator );
}

function revert_async_loopback_requests_test( $test_type ) {
	$test_type['async']['loopback_requests']['test'] = 'loopback_requests';
	$test_type['async']['loopback_requests']['has_rest'] = false;

	return $test_type;
}

if( is_version() ){
	add_filter( 'site_status_tests', 'revert_async_loopback_requests_test', 10, 1 );
}


/**
 * The code that runs during plugin activation
 */
function activate_niagahoster_partner()
{
    Inc\Hook\Activate::init();
}
register_activation_hook( __FILE__, 'activate_niagahoster_partner' );

/**
 * The code that runs during plugin deactivation
 */
function deactivate_niagahoster_partner()
{
    Inc\Hook\Deactivate::init();
}
register_deactivation_hook( __FILE__, 'deactivate_niagahoster_partner' );

/**
 * Initialize all the core classes of the plugin
 */
if (class_exists('Inc\\Init')) {
    Inc\Init::registerServices();
}

add_filter( 'plugin_row_meta', 'nipa_plugin_row_meta', 10, 2 );

function nipa_plugin_row_meta( $links, $file )
{
    if ( plugin_basename( __FILE__ ) == $file ) {
        $row_meta = array(
          'whatsapp-support' => '<a href="#" aria-label="Plugin Additional Links" style="color:green;" id="nipa-wa-support">Whatsapp Support</a>'
        );
        return array_merge( $links, $row_meta );
    }
    return (array) $links;
}