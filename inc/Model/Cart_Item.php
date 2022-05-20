<?php

/**
 * @package  NiagahosterPartner
 */

namespace Inc\Model;

class Cart_Item
{
    private $wpdb;
    private $table_name;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'nipa_cart_item';
    }

    public function setup_table()
    {
        $this->create_cart_item_table_if_not_exist();
    }

    public function create_cart_item_table_if_not_exist()
    {
        $charset_collate = $this->wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $this->table_name (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `cart_id` bigint(20) DEFAULT NULL,
            `product_id` bigint(20) DEFAULT NULL,
            `config` text,
            PRIMARY KEY (`id`),
            KEY `cart_id_idx` (`cart_id`),
            KEY `product_id_idx` (`product_id`)
          ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
}
