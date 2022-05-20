<?php

/**
 * @package NiagahosterPartner
 */

namespace Inc\Pages\Front;

class Cart
{
    public function register()
    {
        add_filter( 'template_include', array( $this, 'set_template' ));
    }

    public function set_template( $pageTemplate )
    {
        global $post;
        $pageName = get_option( 'nipa' )['cart_page'];
        if ( $post->post_name == $pageName ) {
            $file = NIPA_PLUGIN_PATH . 'templates/front/cart-page.php';
            if ( file_exists( $file ) ) {
                $pageTemplate = $file;
            }
        }
        return $pageTemplate;
    }
}
