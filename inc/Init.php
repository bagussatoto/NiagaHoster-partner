<?php

/**
 * @package  Niagahoster Partner
 */

namespace Inc;

final class Init
{
    /**
     * Store all the classes inside an array
     * @return array Full list of classes
     */
    public static function get_services()
    {
        return [
            Hook\Enqueue::class,
            Hook\Nav_Menu::class,
            Settings_Links::class,
            Domain_Checker::class,
            Controller\Cart_Controller::class,
            Controller\Nipa_Api_Controller::class,
            Pages\Front\Cart::class,
            Pages\Front\Order::class,
            Pages\Admin\Dashboard::class,
            Hook\Plugin_Info_Update::class,
        ];
    }

    /**
     * Loop through the classes, initialize them,
     * and call the register() method if it exists
     * @return
     */
    public static function registerServices()
    {
        foreach ( self::get_services() as $class ) {
            $service = self::instantiate( $class );
            if ( method_exists( $service, 'register' ) ) {
                $service->register();
            }
        }
    }

    /**
     * Initialize the class
     * @param  class $class    class from the services array
     * @return class instance  new instance of the class
     */
    private static function instantiate( $class )
    {
        return new $class();
    }
}
