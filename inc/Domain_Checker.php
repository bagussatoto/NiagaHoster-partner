<?php

/**
 * @package  NiagahosterPartner
 */

namespace Inc;

class Domain_Checker
{
    public function register()
    {
        add_shortcode( 'nipa_domaincheck', array($this,'nipa_shortcode'));
    }

    function nipa_shortcode($atts)
    {
        ob_start();
        include(NIPA_PLUGIN_PATH . 'templates/front/domain-checker.php');
        return ob_get_clean();
    }

}