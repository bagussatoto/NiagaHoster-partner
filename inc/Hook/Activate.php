<?php

/**
 * @package  NiagahosterPartner
 */

namespace Inc\Hook;

class Activate
{
    public static function init()
    {
        flush_rewrite_rules();

        $default = array();

        if ( ! get_option('nipa') ) {
            update_option( 'nipa', $default );
        }

        if ( ! get_option('nipa_plugin_onactivate') ) {
            update_option( 'nipa_plugin_onactivate', true );
        }

        \Inc\Custom_Table::setup_tables();
        (new self)->create_page();
    }

    public function create_page()
    {
        $content      = ['home' => '[nipa_domaincheck]'];
        $title        = ['home', 'website instant'];
        $nipa_options = get_option('nipa');

        if (! get_page_by_path('order')) {
            $this->wp_insert_page('order', '', 'publish');
            $nipa_options['order_page'] = 'order';
        }

        if (! get_page_by_path('cart')) {
            $this->wp_insert_page('cart', '', 'publish');
            $nipa_options['cart_page'] = 'cart';
        }

        if (! get_page_by_path('website-instant')) {
            $this->wp_insert_page('website instant', '', 'publish');
            $nipa_options['website_instant'] = 'website-instant';
        }

        if (! get_page_by_path('home') ) {
            $this->wp_insert_page('home', $content['home'], 'publish');
        } else {
            $this->wp_insert_page('domain checker', $content['home'], 'publish');
        }

        update_option('nipa', $nipa_options);
        $this->create_menu($title);

    }

    public function wp_insert_page($title, $content, $status)
    {
        $new_page = array(
            'post_title'    => ucwords($title),
            'post_content'  => $content,
            'post_status'   => $status,
            'post_author'   => get_current_user_id(),
            'post_type'     => 'page',
        );

        $option  = 'nipa_' . str_replace(' ', '_', $title). '_page_id';
        $page_id = wp_insert_post( $new_page );

        update_option($option, $page_id);

        if ($title == 'home' || $title == 'domain checker') {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $page_id);

            $list_plugins = get_plugins();

            add_post_meta($page_id, '_elementor_edit_mode', 'builder');
            add_post_meta($page_id, '_elementor_template_type', 'wp-page');
            add_post_meta($page_id, '_elementor_version', $list_plugins['elementor/elementor.php']['Version'] ?? '3.1.0');
            add_post_meta($page_id, '_wp_page_template', 'elementor_header_footer');
            add_post_meta($page_id, '_elementor_data', '[{\"id\":\"4839edf\",\"elType\":\"section\",\"settings\":[],\"elements\":[{\"id\":\"3ffa503\",\"elType\":\"column\",\"settings\":{\"_column_size\":100,\"_inline_size\":null},\"elements\":[{\"id\":\"ff3c7c7\",\"elType\":\"widget\",\"settings\":{\"shortcode\":\"[nipa_domaincheck]\"},\"elements\":[],\"widgetType\":\"shortcode\"}],\"isInner\":false}],\"isInner\":false}]');
            add_post_meta($page_id, '_elementor_css', '');
        }
    }

    public function create_menu($title)
    {
        $menu_name   = 'NiPa Navigation Menu';
        $menu_exists = wp_get_nav_menu_object( $menu_name );

        if( !$menu_exists){
            $menu_id = wp_create_nav_menu($menu_name);

            for ($i=0; $i < count($title); $i++) {
                if (get_option('nipa_' . str_replace(' ', '_', $title[$i]). '_page_id')) {
                    if ($title[$i] == 'home') {
                        wp_update_nav_menu_item($menu_id, 0, array(
                            'menu-item-title' => 'Home',
                            'menu-item-url' => get_home_url(),
                            'menu-item-status' => 'publish',
                            'menu-item-type' => 'custom',
                            )
                        );
                    } else {
                        wp_update_nav_menu_item($menu_id, 0, array(
                                'menu-item-title' => ucwords($title[$i]),
                                'menu-item-object' => 'page',
                                'menu-item-object-id' => get_option('nipa_' . str_replace(' ', '_', $title[$i]). '_page_id'),
                                'menu-item-type' => 'post_type',
                                'menu-item-status' => 'publish'
                            )
                        );
                    }
                }
            }

            $locations            = get_theme_mod('nav_menu_locations');
            $locations['primary'] = $menu_id;
            set_theme_mod( 'nav_menu_locations', $locations );

            update_option('nipa_menu_id', $menu_id);
        }
    }
}
