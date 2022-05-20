<?php

/**
 * @package  Niagahoster Partner
 */

namespace Inc;

final class Custom_Table
{
    /**
     * Store all the classes inside an array
     * @return array Full list of classes
     */
    public static function get_models()
    {
        return [
            \Inc\Model\Cart::class,
            \Inc\Model\Cart_Item::class
        ];
    }

    /**
     * Loop through the classes, initialize them,
     * and call the register() method if it exists
     * @return
     */
    public static function setup_tables()
    {
        foreach ( self::get_models() as $class ) {
            $model = self::instantiate( $class );
            if ( method_exists( $model, 'setup_table' ) ) {
                $model->setup_table();
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
