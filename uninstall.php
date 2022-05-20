<?php

/**
 * Trigger this file on Plugin uninstall
 *
 * @package  NiagahosterPartner
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

global $wpdb;
$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'nipa_cart' );
$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'nipa_cart_item' );
delete_option( 'nipa' );
delete_option( 'nipa_order_page_id' );
delete_option( 'nipa_cart_page_id' );
delete_option( 'nipa_menu_id' );
delete_option( 'nipa_plugin_onactivate' );
(get_option( 'nipa_home_page_id' )) ? delete_option( 'nipa_home_page_id' ) : delete_option( 'nipa_domain_checker_page_id' );