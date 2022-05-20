<?php

/**
 * @package  NiagahosterPartner
 */

namespace Inc\Hook;

use Inc\Api\Nipa_Api;

class Nav_Menu
{
    private $api;
    private $nipa;
    private $url_cart;
    private $url_order;

    public function __construct()
    {
        $this->nipa = get_option( 'nipa' );
        $this->api = new Nipa_Api( $this->nipa['email'], $this->nipa['api_key'] );

        $this->set_order_and_cart_url();
    }

    private function set_order_and_cart_url()
    {
        $this->url_order = get_home_url() . '/' . $this->nipa['order_page'];
        $this->url_cart  = get_home_url() . '/' . $this->nipa['cart_page'];
    }

    public function register()
    {
        $this->add_buttons('custom_nav_menu_order');
        $this->add_buttons('add_logout_and_member_area_button');
    }

    public function add_buttons($callback)
    {
        add_filter( 'wp_get_nav_menu_items', array( $this, $callback ), 10, 2 );
    }

    public function add_logout_and_member_area_button($items, $args)
    {
        if ( ! empty( $_SESSION['nipa_client_id'] ) && $this->check_if_nipa_page() && !$this->check_if_admin_site() ) {
            $items[] = $this->_custom_nav_menu_item( 'Member Area', '#', 'nipa-client-dashboard', ['nipa-client-dashboard'], 98 );
            $items[] = $this->_custom_nav_menu_item( 'Logout', '#', 'nipa-client-logout', ['nipa-client-logout'], 99 );
        }
        return $items;
    }

    public function check_if_nipa_page()
    {
        global $post;

        $nipaPages = [
            $this->nipa['cart_page'],
            $this->nipa['order_page'],
            $this->nipa['website_instant_page']
        ];

        return in_array($post->post_name, $nipaPages);
    }

    public function check_if_admin_site()
    {
        if ( is_admin() || is_customize_preview() ) {
            return true;
        }
        return false;
    }

    public function custom_nav_menu_order( $items, $args )
    {
        if ( $this->check_if_admin_site() ) {
            return $items;
        }

        return $this->prepare_create_menu( $items );
    }

    private function prepare_create_menu( $items )
    {
        $array  = $this->array_nav_menu();
        $markup = $this->get_product_markup();
        $menu   = $this->get_order_menu();

        if ( !empty( $this->nipa['dropdown_menu'] ) ) {
            return $this->create_sub_menu( $items, $menu, $markup, $array );
        }

        return $this->create_menu_item( $items, $menu, $markup, $array );
    }

    private function create_menu_item( $items, $menu, $markup, $array, $parentId = 0 )
    {
        $startOrder = 91;
        foreach ( $menu as $value ) {
            if ( $value == 'menu_cart' || in_array( str_replace('menu_', '', $value), $markup ) ) {
                $item    = $array[$value];
                $items[] = $this->_custom_nav_menu_item( $item["name"], $item["url"], $item["id"], [$item["id"]], $startOrder, $parentId );
            }
            $startOrder++;
        }
        return $items;
    }

    private function create_sub_menu( $items, $menu, $markup, $array )
    {
        $page       = $this->get_page( $this->nipa['order_page'] );
        $menu_order = $this->_custom_nav_menu_item( $page->post_title, '#', $page->post_name, [$page->post_name], 90 );

        $items[] = $menu_order;
        $cart    = false;

        if ( in_array( 'menu_cart', $menu ) ) {
            $cart = $array['menu_cart'];
            $key  = array_search( 'menu_cart', $menu );
            unset( $menu[$key] );
        }

        $items = $this->create_menu_item( $items, $menu, $markup, $array, $menu_order->ID );

        if ( !empty($cart) ) {
            $items[] = $this->_custom_nav_menu_item( $cart["name"], $cart["url"], $cart["id"], [$cart["id"]], 97 );
        }

        return $items;
    }

    private function _custom_nav_menu_item( $title, $url, $id, $class = array(), $order, $parent = 0 )
    {
        $item                   = new \stdClass();
        $item->ID               = $id;
        $item->db_id            = $item->ID;
        $item->title            = $title;
        $item->url              = $url;
        $item->menu_order       = $order;
        $item->menu_item_parent = $parent;
        $item->type             = 'custom';
        $item->object           = 'custom';
        $item->object_id        = $id;
        $item->classes          = $class;
        $item->target           = '';
        $item->attr_title       = '';
        $item->description      = '';
        $item->xfn              = '';
        $item->status           = '';

        return $item;
    }

    private function get_page( $pageName )
    {
        foreach ( get_posts( array( 'post_type' => 'page' ) ) as $page ) {
            if ( $page->post_name == $pageName ) {
                return $page;
            }
        }
        return NULL;
    }

    private function array_nav_menu()
    {
        return array(
            'menu_cart' => array(
                'url' => $this->url_cart,
                'id' => 'menu-nipa-cart',
                'name' => 'Cart'
            ),
            'menu_hosting' => array(
                'url' => $this->url_order,
                'id' => 'menu-nipa-order',
                'name' => 'Hosting'
            ),
            'menu_mailhosting' => array(
                'url' => $this->url_order . '?type=mail-hosting',
                'id' => 'menu-nipa-order-mail-hosting',
                'name' => 'Email Hosting'
            ),
            'menu_domain' => array(
                'url' => $this->url_order . '?type=domain',
                'id' => 'menu-nipa-order-domain',
                'name' => 'Domain'
            ),
            'menu_vpsme' => array(
                'url' => $this->url_order . '?type=vpsme',
                'id' => 'menu-nipa-order-vpsme',
                'name' => 'VPS'
            )
        );
    }

    private function get_product_markup()
    {
        $products = array();
        if ( empty( $this->api->get_products()['result'] ) ) {
            return $products;
        }
        foreach ($this->api->get_products()['result'] as $value) {
            if ( in_array( $value['product_id_nh'], array( 53, 54, 55 ) ) && !in_array( 'mailhosting', $products ) ) {
                $products[] = 'mailhosting';
            } elseif ( !in_array( $value['type'], $products ) ) {
                $products[] = $value['type'];
            }
        }
        return $products;
    }

    private function get_order_menu()
    {
        $menu = [];
        foreach ( $this->nipa as $key => $value ) {
            if ( strpos( $key, 'menu_' ) !== false && $value ) {
                $menu[$value] = $key;
            }
        }
        ksort( $menu );
        return $menu;
    }
}