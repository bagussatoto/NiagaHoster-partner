<?php

/**
 * @package NiagahosterPartner
 */

namespace Inc\Pages\Front;

class Order
{
    public function register()
    {
        add_filter( 'template_include', array( $this, 'set_template' ) );
    }

    public function set_template($pageTemplate)
    {
        global $post;
        global $typeOrder;

        $nipa         = get_option( 'nipa' );
        $file         = NIPA_PLUGIN_PATH . 'templates/front/';
        $fileOrder    = $file . 'order-page.php';
        $fileWebOrder = $file . 'order-website-page.php';

        if (
            isset($nipa['order_page'])
            && $nipa['order_page'] == $post->post_name
            && file_exists( $fileOrder )
        ) {
            $pageTemplate = $fileOrder;
            $typeOrder    = 'hosting';
        } else if (
            isset($nipa['website_instant_page'])
            && $nipa['website_instant_page'] == $post->post_name
            && file_exists( $fileWebOrder )
        ) {
            $pageTemplate = $fileWebOrder;
            $typeOrder    = 'website';
        }

        return $pageTemplate;
    }
}
