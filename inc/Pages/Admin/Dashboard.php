<?php

/**
 * @package  NiagahosterPartner
 */

namespace Inc\Pages\Admin;

use Inc\Api\Settings_Api;
use Inc\Callbacks\Admin_Callbacks;
use Inc\Callbacks\Nipa_Settings_Callbacks;

class Dashboard
{
    public $settings;

    public $callbacks;

    public $settingsCallbacks;

    public $pages = array();

    public function register()
    {
        $this->settings = new Settings_Api();

        $this->callbacks = new Admin_Callbacks();

        $this->settingsCallbacks = new Nipa_Settings_Callbacks;

        $this->set_pages();
        $this->set_settings();
        $this->set_sections();
        $this->set_fields();
        $this->set_nav_menu();

        $this->settings->add_pages( $this->pages )->with_sub_page( 'Settings' )->register();
    }

    public function set_nav_menu()
    {
        $sections = [];
        foreach ($this->sections() as $section) {
            if (!empty($section['tab_title'])) {
                $sections[$section['id']] = $section['tab_title'];
            }
        }

        $this->settingsCallbacks->set_nav_menu($sections);
    }

    public function set_pages()
    {
        $this->pages = array(
            array(
                'page_title' => 'Niagahoster Partner',
                'menu_title' => 'NiPa',
                'capability' => 'manage_options',
                'menu_slug' => 'nipa',
                'callback' => array( $this->callbacks, 'admin_dashboard' ),
                'icon_url' => 'dashicons-store',
                'position' => 110
            )
        );
    }

    public function set_settings()
    {
        $args = array(
            array(
                'option_group' => 'nipa_settings',
                'option_name' => 'nipa',
                'callback' => array( $this->settingsCallbacks, 'input_sanitize' )
            )
        );

        $this->settings->set_settings($args);
    }

    public function set_sections()
    {
        $args = $this->sections();

        $this->settings->set_sections($args);
    }

    private function sections()
    {
        return array(
            array(
                'id' => 'nipa_admin_tab',
                'title' => 'Niagahoster Partner',
                'callback' => array( $this->settingsCallbacks, 'section_tab_menu' ),
                'page' => 'nipa'
            ),
            array(
                'id' => 'nipa_admin_index',
                'title' => '',
                'callback' => array( $this->settingsCallbacks, 'admin_section_manager' ),
                'page' => 'nipa',
                'tab_title' => 'Nipa Setting'
            ),
            array(
                'id' => 'end_nipa_admin_index',
                'title' => '',
                'callback' => array( $this->settingsCallbacks, 'end_section' ),
                'page' => 'nipa'
            ),
            array(
                'id' => 'nipa_admin_index_nav_menu',
                'title' => '',
                'callback' => array( $this->settingsCallbacks, 'admin_section_nav_menu' ),
                'page' => 'nipa',
                'tab_title' => 'Menu Setting'
            ),
            array(
                'id' => 'end_nipa_admin_index_nav_menu',
                'title' => '',
                'callback' => array( $this->settingsCallbacks, 'end_section' ),
                'page' => 'nipa'
            ),
            array(
                'id' => 'nipa_admin_custom_text',
                'title' => '',
                'callback' => array( $this->settingsCallbacks, 'admin_section_custom_text' ),
                'page' => 'nipa',
                'tab_title' => 'Custom Title'
            ),
            array(
                'id' => 'end_nipa_admin_custom_text',
                'title' => '',
                'callback' => array( $this->settingsCallbacks, 'end_section' ),
                'page' => 'nipa'
            ),
        );
    }

    public function set_fields()
    {
        $args = array(
            array(
                'id' => 'api_key',
                'title' => 'Api Key',
                'callback' => array( $this->settingsCallbacks, 'text_field' ),
                'page' => 'nipa',
                'section' => 'nipa_admin_index',
                'args' => array(
                    'option_name' => 'nipa',
                    'label_for' => 'api_key',
                    'placeholder' => 'NiPa API Key'
                )
            ),
            array(
                'id' => 'email',
                'title' => 'Email',
                'callback' => array( $this->settingsCallbacks, 'text_field' ),
                'page' => 'nipa',
                'section' => 'nipa_admin_index',
                'args' => array(
                    'option_name' => 'nipa',
                    'label_for' => 'email',
                    'placeholder' => 'NiPa Email Address'
                )
            ),
            array(
                'id' => 'order_page',
                'title' => 'Set Order Page',
                'callback' => array( $this->settingsCallbacks, 'select_box' ),
                'page' => 'nipa',
                'section' => 'nipa_admin_index',
                'args' => array(
                    'option_name' => 'nipa',
                    'label_for' => 'order_page',
                    'select_options' => $this->get_pages_post_name()
                )
            ),
            array(
                'id' => 'cart_page',
                'title' => 'Set Cart Page',
                'callback' => array( $this->settingsCallbacks, 'select_box' ),
                'page' => 'nipa',
                'section' => 'nipa_admin_index',
                'args' => array(
                    'option_name' => 'nipa',
                    'label_for' => 'cart_page',
                    'select_options' => $this->get_pages_post_name()
                )
            ),
            array(
                'id' => 'website_instant_page',
                'title' => 'Set Website Instant Page',
                'callback' => array( $this->settingsCallbacks, 'select_box' ),
                'page' => 'nipa',
                'section' => 'nipa_admin_index',
                'args' => array(
                    'option_name' => 'nipa',
                    'label_for' => 'website_instant_page',
                    'select_options' => $this->get_pages_post_name()
                )
            ),
            array(
                'id' => 'menu_cart',
                'title' => 'Urutan Menu Cart',
                'callback' => array( $this->settingsCallbacks, 'select_box' ),
                'page' => 'nipa',
                'section' => 'nipa_admin_index_nav_menu',
                'args' => array(
                    'option_name' => 'nipa',
                    'label_for' => 'menu_cart',
                    'select_options' => $this->get_number()
                )
            ),
            array(
                'id' => 'menu_hosting',
                'title' => 'Urutan Menu Hosting',
                'callback' => array( $this->settingsCallbacks, 'select_box' ),
                'page' => 'nipa',
                'section' => 'nipa_admin_index_nav_menu',
                'args' => array(
                    'option_name' => 'nipa',
                    'label_for' => 'menu_hosting',
                    'select_options' => $this->get_number()
                )
            ),
            array(
                'id' => 'menu_mailhosting',
                'title' => 'Urutan Menu Mail Hosting',
                'callback' => array( $this->settingsCallbacks, 'select_box' ),
                'page' => 'nipa',
                'section' => 'nipa_admin_index_nav_menu',
                'args' => array(
                    'option_name' => 'nipa',
                    'label_for' => 'menu_mailhosting',
                    'select_options' => $this->get_number()
                )
            ),
            array(
                'id' => 'menu_domain',
                'title' => 'Urutan Menu Domain',
                'callback' => array( $this->settingsCallbacks, 'select_box' ),
                'page' => 'nipa',
                'section' => 'nipa_admin_index_nav_menu',
                'args' => array(
                    'option_name' => 'nipa',
                    'label_for' => 'menu_domain',
                    'select_options' => $this->get_number()
                )
            ),
            array(
                'id' => 'menu_vpsme',
                'title' => 'Urutan Menu VPS',
                'callback' => array( $this->settingsCallbacks, 'select_box' ),
                'page' => 'nipa',
                'section' => 'nipa_admin_index_nav_menu',
                'args' => array(
                    'option_name' => 'nipa',
                    'label_for' => 'menu_vpsme',
                    'select_options' => $this->get_number()
                )
            ),
            array(
                'id' => 'dropdown_menu',
                'title' => 'Menu Drop Down',
                'callback' => array( $this->settingsCallbacks, 'checkbox' ),
                'page' => 'nipa',
                'section' => 'nipa_admin_index_nav_menu',
                'args' => array(
                    'option_name' => 'nipa',
                    'label_for' => 'dropdown_menu',
                    'text' => 'Buat menu menjadi drop-down / sub-menu.'
                )
            ),
            array(
                'id' => 'title_hosting',
                'title' => 'Title Hosting',
                'callback' => array( $this->settingsCallbacks, 'text_field' ),
                'page' => 'nipa',
                'section' => 'nipa_admin_custom_text',
                'args' => array(
                    'option_name' => 'nipa',
                    'label_for' => 'title_hosting',
                    'placeholder' => 'Web Hosting',
                    'optional' => true
                )
            ),
            array(
                'id' => 'title_mail_hosting',
                'title' => 'Title Mail Hosting',
                'callback' => array( $this->settingsCallbacks, 'text_field' ),
                'page' => 'nipa',
                'section' => 'nipa_admin_custom_text',
                'args' => array(
                    'option_name' => 'nipa',
                    'label_for' => 'title_mail_hosting',
                    'placeholder' => 'Mail Hosting',
                    'optional' => true
                )
            ),
            array(
                'id' => 'title_vpsme',
                'title' => 'Title VPS',
                'callback' => array( $this->settingsCallbacks, 'text_field' ),
                'page' => 'nipa',
                'section' => 'nipa_admin_custom_text',
                'args' => array(
                    'option_name' => 'nipa',
                    'label_for' => 'title_vpsme',
                    'placeholder' => 'Virtual Private Server',
                    'optional' => true
                )
            ),
            array(
                'id' => 'title_domain',
                'title' => 'Title Domain',
                'callback' => array( $this->settingsCallbacks, 'text_field' ),
                'page' => 'nipa',
                'section' => 'nipa_admin_custom_text',
                'args' => array(
                    'option_name' => 'nipa',
                    'label_for' => 'title_domain',
                    'placeholder' => 'Domain',
                    'optional' => true
                )
            ),
            array(
                'id' => 'title_website',
                'title' => 'Title Website Instant',
                'callback' => array( $this->settingsCallbacks, 'text_field' ),
                'page' => 'nipa',
                'section' => 'nipa_admin_custom_text',
                'args' => array(
                    'option_name' => 'nipa',
                    'label_for' => 'title_website',
                    'placeholder' => 'Website Instant',
                    'optional' => true
                )
            ),
        );

        $this->settings->set_fields($args);
    }

    private function get_pages_post_name()
    {
        $pagePostName = array();
        foreach ( get_posts( array( 'post_type' => 'page' ) ) as $page ) {
            $pagePostName[] = $page->post_name;
        }
        return $pagePostName;
    }

    private function get_number()
    {
        return [0, 1, 2, 3, 4, 5];
    }
}
