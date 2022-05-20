<?php

/**
 * @package  NiagahosterPartner
 */

namespace Inc\Model;

class Cart
{
    private $wpdb;
    private $table_name;
    private $item_table_name;
    private $session_id;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'nipa_cart';
        $this->item_table_name = $wpdb->prefix . 'nipa_cart_item';
        $this->init();
    }

    public function setup_table()
    {
        $this->create_cart_table_if_not_exist();
    }

    private function create_cart_table_if_not_exist()
    {
        $charset_collate = $this->wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $this->table_name (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `session_id` varchar(100) DEFAULT NULL,
            `created_at` varchar(35) DEFAULT NULL,
            `updated_at` varchar(35) DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `session_id_idx` (`session_id`)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    private function init()
    {
        if ( empty( $_COOKIE['nipa_cart'] ) ) {
            $url = $this->getDomainAndPath();
            $this->session_id = hash( 'sha256', time() );
            setcookie( 'nipa_cart', $this->session_id, time() + (86400 * 60), $url['path'], $url['domain'], false, true );
        } else {
            $this->session_id = $_COOKIE['nipa_cart'];
        }
        return $this->session_id;
    }

    private function getDomainAndPath() {
        $domain = DOMAIN;
        $path   = '/';
        $parse  = explode( '/', DOMAIN );
        if ( count( $parse ) > 1 ) {
            $domain = $parse[0];
            unset( $parse[0] );
            $path .= implode( '/', $parse ) . '/';
        }
        return [
            'domain' => $domain,
            'path' => $path
        ];
    }

    private function create_cart()
    {
        $sql = "SELECT id FROM $this->table_name WHERE session_id = %s";
        $cart_id = $this->wpdb->get_var( $this->wpdb->prepare( $sql, $this->session_id ) );
        $insert_sql = "INSERT INTO $this->table_name (session_id, created_at) VALUES (%s, %s)";
        if (!$cart_id) {
            $this->wpdb->query( $this->wpdb->prepare( $insert_sql, array( $this->session_id, date( 'c' ) ) ) );
            return $this->wpdb->insert_id;
        }
        return $cart_id;
    }

    public function get_cart()
    {
        $sql = "SELECT c.id AS cart_id, ci.* FROM $this->table_name AS c
                JOIN $this->item_table_name AS ci
                    ON c.id = ci.cart_id
                WHERE `session_id` = %s";
        $result = $this->wpdb->get_results( $this->wpdb->prepare( $sql, array( $this->session_id ) ) );
        $result = json_decode( json_encode( $result ), true );
        $cart = array(
            'id'         => null,
            'session_id' => $this->session_id,
            'items'      => array(),
            'total'      => 0
        );
        if ( empty( $result ) ) {
            return $cart;
        }
        $cart['id'] = $result[0]['cart_id'];
        foreach ( $result as $item ) {
            $config = json_decode( $item['config'], true );
            empty( $_SESSION['nipa_client_id'] ) ?: $config['client_id'] = $_SESSION['nipa_client_id'];
            unset( $item['cart_id'], $item['config'] );
            $item = array_merge( $item, $config );
            $cart['items'][] = $item;
            $cart['total'] += $item['price'];
        }
        return $cart;
    }

    public function add_item($data)
    {
        if ( empty( $data['cart_id'] ) ) {
            $data['cart_id'] = $this->create_cart();
        }
        $sql = "INSERT INTO $this->item_table_name (cart_id, product_id, config) VALUES (%d, %s, %s)";
        $this->wpdb->query($this->wpdb->prepare($sql, array(
            $data['cart_id'],
            $data['product_id'],
            json_encode( $data )
        )));
        $data['id'] = $this->wpdb->insert_id;
        return $data;
    }

    public function remove_item( $id )
    {
        return $this->wpdb->delete( $this->item_table_name, array( 'id' => $id ), array( '%d' ) );
    }

    public function reset($cart_id)
    {
        return $this->wpdb->delete( $this->item_table_name, array( 'cart_id' => $cart_id ), array( '%d' ));
    }
}
