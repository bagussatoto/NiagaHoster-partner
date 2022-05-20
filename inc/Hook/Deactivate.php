<?php

/**
 * @package  NiagahosterPartner
 */

namespace Inc\Hook;

class Deactivate
{
    public static function init()
    {
        (new self)->remove_page();
        flush_rewrite_rules();
    }

    public function remove_page()
    {
        $menu_id               = get_option('nipa_menu_id');
        $order_page_id         = get_option('nipa_order_page_id');
        $cart_page_id          = get_option('nipa_cart_page_id');
        $home_page_id          = get_option('nipa_home_page_id') ?? get_option('nipa_domain_checker_page_id') ;

        // remove order page if not modified by user
        if (! $this->is_modified($order_page_id)) {
            wp_delete_post($order_page_id, true);
        }

        // remove cart page if not modified by user
        if (! $this->is_modified($cart_page_id)) {
            wp_delete_post($cart_page_id, true);
        }

        // remove home / domain checker page
        if (! $this->is_modified($home_page_id)) {
            wp_delete_post($home_page_id, true);
            if (get_option('page_on_front') === $home_page_id) {
                update_option('show_on_front', 'posts');
                update_option('page_on_front', 0);
            }
        }

        // remove nav menu
        if (is_nav_menu($menu_id)) {
            wp_delete_nav_menu($menu_id);
        }
    }

    public function is_modified($page_id)
    {
        return (get_the_modified_time('', $order_page_id ) != get_the_time('', $order_page_id));
    }
}
