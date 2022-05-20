<?php

/**
 * @package  NiagahosterPartner
 */

namespace Inc\Callbacks;

class Nipa_Settings_Callbacks
{
    public $nav_menu = array();

    public function set_nav_menu(array $nav_menu)
    {
        $this->nav_menu = $nav_menu;
        return $this;
    }

    public function input_sanitize( $input )
    {
        return $input;
    }

    public function admin_section_manager( $args )
    {
        $this->content_element( $args['id'], 'Niagahoster Partner Settings' );
    }

    public function admin_section_nav_menu( $args )
    {
        $this->content_element( $args['id'], 'Urutan Nav Menu' );
    }

    public function admin_section_custom_text( $args )
    {
        $this->content_element( $args['id'], 'Costum Title Product' );
    }

    private function content_element( $id, $description )
    {
        echo '<div class="nipa-tab__content hidden ' . $id . '">
            <h2>' . $description . '</h2>';
    }

    public function section_tab_menu()
    {
        if (empty($this->nav_menu)) {
            return;
        }
        echo '<div class="nipa-tab">';
        foreach ($this->nav_menu as $id => $menu) {
            echo '<button class="nipa-tab__links" data-id="' . $id . '">' . $menu . '</button>';
        }
        echo '</div>';
    }

    public function end_section()
    {
        echo '</div>';
    }

    public function text_field( $args )
    {
        $required = empty($args['optional']) ? 'required' : '';
        $name = $args['label_for'];
        $option_name = $args['option_name'];
        $value = get_option( $option_name )[$name];
        echo '<input type="text" class="regular-text" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="' . $value . '" placeholder="' . $args['placeholder'] . '" ' . $required . '>';
    }

    public function select_box( $args )
    {
        $name = $args['label_for'];
        $option_name = $args['option_name'];
        $value = get_option( $option_name )[$name];
        $selectOptions = $args['select_options'];
        $select = '<select id="' . $name . '" name="' . $option_name . '[' . $name . ']">';
        foreach ($selectOptions as $option) {
            $select .= '<option ' . ( $value == $option ? 'selected' : '' ) . ' value="' . $option . '">' . ucfirst( $option ) . '</option>';
        }
        echo $select .= '</select>';
    }

    public function checkbox( $args )
    {
        $name = $args['label_for'];
        $option_name = $args['option_name'];
        $text = $args['text'];

        $checked = '';
        if ( ! empty( get_option( $option_name )[$name] ) ) {
            $checked = 'checked';
        }
        echo '<input type="checkbox" name="' . $option_name . '[' . $name . ']" value="true" ' . $checked . ' /> ' . $text;
    }
}
