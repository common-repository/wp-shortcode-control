<?php

/**
 * Class WP_Shortcode_Control_Shortcode
 *
 * The handler class for managing our custom shortcodes inside of our array
 *
 * @since 1.0.0
 * @package WPSCONT
 * @author Rarus <info@rarus.io>
 */
class WP_Shortcode_Control_Shortcode{

    /**
     * WP_Shortcode_Control_Shortcode constructor.
     */
    function __construct(){
        $this->shortcode_data_option_list = 'wpscont_shortcode_list';
        $this->shortcode_settings = array(
            'toggle_on_off' => array(
                //Shortcode settings
                'title' => 'Toggle element',
                'description' => 'This is an activate/deactivate toggle',
                'info' => 'I am a neat info box',

                //Input settings
                'type' => 'checkbox', // Input type
                'value' => '',
                'name' => '', //If nothing is set, the name will be created automatically out of the shortcode_name and wpscont (wpscont_ + SHORTCODE)
                'checked' => false,
                'disabled' => false,
                'max' => '', //maximal possible number or date
                'min' => '', //opposite of max
                'maxlength' => 0, //0 is no maxlength
                'placeholder' => '',
                'readonly' => false,
            )
        );
    }

    /**
     * Grab data for one or all shortcodes
     *
     * Leave $shortcode empty for grabing all. Otherwise
     * you can include the shortcode key.
     *
     * @param string $shortcode - shortcode_tag or noting
     * @return array|mixed - the shortcode settings
     */
    public function get_data($shortcode = ''){
        $return = array();

        $shortcodes = get_option($this->shortcode_data_option_list);
        $shortcodes = json_decode($shortcodes, true);

        //Smaller the circle
        if(empty($shortcodes))
            return $return;

        if(!empty($shortcode)){
            if(isset($shortcodes[$shortcode]))
                //Keep the actual notation alive
                $return = array(
                    $shortcode => $shortcodes[$shortcode]
                );
        } else {
            uksort($shortcodes, array($this, 'sort_non_case_sensitive'));
            $return = $shortcodes;
        }

        return $return;

    }

    /**
     * Set the data for a specific shortcode
     *
     * @param $shortcode - the shortcode_tag
     * @param $data - the shortcode data as an array
     * @param bool $overwrite - wether you want overwrite an existing shortcode or not
     * @return bool - true if data was set or it was already available
     */
    public function set_data($shortcode, $data, $overwrite = false){
        $return = false;

        if(empty($shortcode))
            return $return;

        $option_key = $this->shortcode_data_option_list;
        $shortcode_data = $this->get_data();

        if(!empty($shortcode_data)){
            if(isset($shortcode_data[$shortcode])){
                if($overwrite){
                    $shortcode_data[$shortcode] = $data;

                    update_option($option_key, json_encode($shortcode_data));
                    $return = true;
                }
            } else {
                $shortcode_data[$shortcode] = $data;

                update_option($option_key, json_encode($shortcode_data));
                $return = true;
            }
        } else {
            $shortcode_data = array();
            $shortcode_data[$shortcode] = $data;

            update_option($option_key, json_encode($shortcode_data));
            $return = true;
        }

        return $return;
    }

    /**
     * Delete shortcode specific data
     *
     * @param $shortcode - the shortcode tag
     * @param bool $return_on_success - the value you want to return non success
     * @return bool|mixed - bool or custom value
     */
    public function delete_data($shortcode, $return_on_success = true){
        $return = false;

        if(empty($shortcode))
            return $return;

        $option_key = $this->shortcode_data_option_list;
        $shortcode_data = $this->get_data();

        if(!empty($shortcode_data)){
            if(isset($shortcode_data[$shortcode])){
                unset($shortcode_data[$shortcode]);
                update_option($option_key, json_encode($shortcode_data));
                if($return_on_success === 'value'){
                    $return = $shortcode_data[$shortcode];
                } else {
                    $return = true;
                }
            }
        }

        return $return;
    }

    /**
     * Delete all the shortcodes by removing the whole key (This resets everything)
     *
     * @return mixed - wether the action went fine or not
     */
    public function delete_all(){
        $option_key = $this->shortcode_data_option_list;
        $check = delete_option($option_key);
        return $check;
    }

    /**
     * MAke sure the shortcodes get compared non case sensitive
     *
     * @param $a
     * @param $b
     * @return int
     */
    public function sort_non_case_sensitive($a, $b){
        return strcasecmp($a, $b);
    }
}