<?php

/**
 * Class WP_Shortcode_Control_Features
 *
 * @since 1.0.0
 * @package WPSCONT
 * @author Rarus <info@rarus.io>
 */
class WP_Shortcode_Control_Features{

    /**
     * WP_Shortcode_Control_Features constructor.
     */
    function __construct(){
        $this->skipped_shortcodes = array();
        $this->add_features();
    }

    public function add_features(){
        $priority = 100;

        //Pre Do Shortcode
        $pre_tags = array(
            'feature_exclude_posts' => array( 'priority' => $priority++, 'values' => 3 ),
            'feature_include_posts' => array( 'priority' => $priority++, 'values' => 3 ),
            'feature_is_activated' => array( 'priority' => $priority++, 'values' => 5 ),
            'display_default_text' => array( 'priority' => $priority++, 'values' => 3 )
        );

        /**
         * Filter the defined pre tags based on your needs
         */
        $pre_tags = apply_filters('wpscont/shortcodes/features/list/pre_do_shortcode_tag', $pre_tags);

        foreach($pre_tags as $function => $data){
            add_filter('wpscont/shortcodes/features/pre_do_shortcode_tag', array($this, $function), $data['priority'], $data['values']);
        }

        //Do Shortcode
        $tags = array(
            'limit_response_length' => array( 'priority' => $priority++, 'values' => 5 ),
            'replace_text' => array( 'priority' => $priority++, 'values' => 3 )
        );

        /**
         * Filter the defined tags based on your needs
         */
        $tags = apply_filters('wpscont/shortcodes/features/list/do_shortcode_tag', $tags);

        foreach($tags as $function => $data){
            add_filter('wpscont/shortcodes/features/do_shortcode_tag', array($this, $function), $data['priority'], $data['values']);
        }

        //Filter shortcode attributes
        $attributes = array(
            'feature_add_attributes' => array( 'priority' => $priority++, 'values' => 4 )
        );

        /**
         * Filter the defined shortcode attributes
         */
        $pre_atts = apply_filters('wpscont/shortcodes/features/list/filter_attributes', $attributes);

        foreach($pre_atts as $function => $data){
            add_filter('wpscont/shortcodes/features/filter_attributes', array($this, $function), $data['priority'], $data['values']);
        }
    }

    /**
     * #####################
     * ###
     * #### SKIP SHORTCODE FUNCTION
     * ###
     * #####################
     */

    /**
     * Defines wether a shortcode can be skipped or not.
     *
     * Because this is an internal function that depends on plugins, we do
     * not allow editing directly this function.
     * Please use the settings filter and the feature filter to handle a problem.
     *
     * @param $shortcode
     * @param bool $set
     * @return bool
     */
    public function skip_shortcode($shortcode, $set = false){
        $return = false;

        if($set){
            if(!in_array($shortcode, $this->skipped_shortcodes))
                $this->skipped_shortcodes[] = $shortcode;

            $return = true;
        } else {
            if(in_array($shortcode, $this->skipped_shortcodes))
                $return = true;
        }

        return $return;
    }

    /**
     * Remove the skip flag from a shortcode.
     *
     * This function should just be used for extended functionality.
     * It may makes sense, if you want to skip a specific functionality
     * to start after it again.
     *
     * @param $shortcode
     * @return bool - true if shortcode was deleted
     */
    public function unskip_shortcode($shortcode){
        $return = false;
        if(in_array($shortcode, $this->skipped_shortcodes)){
            $key = array_search($shortcode, $this->skipped_shortcodes);
            unset($this->skipped_shortcodes[$key]);
            $return = true;
        }

        return $return;
    }

    /**
     * #####################
     * ###
     * #### PRE DO SHORTCODE TAG
     * ###
     * #####################
     */

    /**
     * Activate or deactivate the shortcode
     *
     * (We included all possible values for demonstration purpose)
     *
     * @param $bool - FALSE by default, otherwise string
     * @param $shortcode_data - The configurational shortcode data
     * @param $tag - The shortcode tag name
     * @param $attr - The parsed attributes
     * @param $m - Shortcode regex
     * @return string - The shortcode response
     */
    public function feature_is_activated($bool, $shortcode_data, $tag, $attr, $m){
       if(isset($shortcode_data['toggle_on_off'])){
            $toggle = WPSCONT()->helpers->validate_yes_no_to_bool($shortcode_data['toggle_on_off']);
            if(!empty($toggle)){
                $bool = '';
                $this->skip_shortcode($tag, true);
            }
        }

        return $bool;
    }

    /**
     * Exclude functionality for custom posts.
     *
     * You can include specific posts comma separated into the forntend box to
     * remove the rest of the logic for it.
     *
     * @param $bool - FALSE by default, otherwise string
     * @param $shortcode_data - The configurational shortcode data
     * @param $tag - The shortcode tag name
     * @return string - The shortcode response
     */
    public function feature_exclude_posts($bool, $shortcode_data, $tag){
        if($this->skip_shortcode($tag))
            return $bool;

        if(isset($shortcode_data['exclude_posts']) && !empty($shortcode_data['exclude_posts'])){
            $ids = explode(',', $shortcode_data['exclude_posts']);
            $current_id = get_the_ID();
            if(!empty($current_id) && is_numeric($current_id)){

                //Validate the ids first
                foreach($ids as $key => $single){
                    $ids[$key] = trim($single, ' ');
                }

                if(in_array($current_id, $ids)){
                    $this->skip_shortcode($tag, true);
                }
            }
        }

        return $bool;
    }

    /**
     * Include functionality for custom posts.
     *
     * You can include specific posts comma separated into the forntend box to
     * apply the rest of the logic to it. (If it is used,
     * the exclude logic is ignored and vice versa)
     *
     * @param $bool - FALSE by default, otherwise string
     * @param $shortcode_data - The configurational shortcode data
     * @param $tag - The shortcode tag name
     * @return string - The shortcode response
     */
    public function feature_include_posts($bool, $shortcode_data, $tag){
        if($this->skip_shortcode($tag))
            return $bool;

        if(isset($shortcode_data['include_posts']) && !empty($shortcode_data['include_posts'])){
            $ids = explode(',', $shortcode_data['include_posts']);
            $current_id = get_the_ID();
            if(!empty($current_id) && is_numeric($current_id)){

                //Validate the ids first
                foreach($ids as $key => $single){
                    $ids[$key] = trim($single, ' ');
                }

                if(!in_array($current_id, $ids)){
                    $this->skip_shortcode($tag, true);
                }
            }
        }

        return $bool;
    }

    /**
     * Output a default text
     *
     * @param $bool - FALSE by default, otherwise string (In this hook the default text)
     * @param $shortcode_data - The configurational shortcode data
     * @return mixed - The changed response
     */
    public function display_default_text($bool, $shortcode_data, $tag){
        if($this->skip_shortcode($tag))
            return $bool;

        if(isset($shortcode_data['display_default_text'])){
            if(!empty($shortcode_data['display_default_text']))
                $bool = $shortcode_data['display_default_text'];
        }

        return $bool;
    }

    /**
     * #####################
     * ###
     * #### DO SHORTCODE TAG
     * ###
     * #####################
     */

    /**
     * Limit the length of a shortcode response
     *
     * (We included all possible values for demonstration purpose)
     *
     * @param $response - The content of the shortcode
     * @param $shortcode_data - The configurational shortcode data
     * @param $tag - The shortcode tag name
     * @param $attr - The parsed attributes
     * @param $m - Shortcode regex
     * @return mixed - The shortcode response
     */
    public function limit_response_length($response, $shortcode_data, $tag, $attr, $m){
        if($this->skip_shortcode($tag))
            return $response;

        //Shorten circuit
        $limit = !empty($shortcode_data['limit_response']) ? $shortcode_data['limit_response'] : false;
        $toggle = WPSCONT()->helpers->validate_yes_no_to_bool($limit);
        if(empty($toggle))
            return $response;

        if(isset($shortcode_data['limit_response_length'])){
            if(!empty($shortcode_data['limit_response_length_offset'])){
                $response = substr($response,intval($shortcode_data['limit_response_length_offset']),intval($shortcode_data['limit_response_length']));
            } else {
                $response = substr($response, 0, $shortcode_data['limit_response_length']);
            }

            $limit_dots = !empty($shortcode_data['limit_response_length_dots']) ? $shortcode_data['limit_response_length_dots'] : false;
            $show_dots = WPSCONT()->helpers->validate_yes_no_to_bool($limit_dots);
            //Show dots just if response is longer than the response length
            if(!empty($show_dots) && strlen($response) <= intval($shortcode_data['limit_response_length'])){
                $response = $response . '...';
            }
        }

        return $response;
    }

    /**
     * Replace specified content inside of the shortcode response
     *
     * @param $response - The content of the shortcode
     * @param $shortcode_data - The configurational shortcode data
     * @return mixed - The (replaced) shortcode response
     */
    public function replace_text($response, $shortcode_data, $tag){
        if($this->skip_shortcode($tag))
            return $response;

        if(!empty($shortcode_data['replace_shortcode_content_string'])){
            foreach($shortcode_data['replace_shortcode_content_string'] as $replace){
                if(isset($replace['replace_content_origin']) && isset($replace['replace_content_value'])){
                    $response = str_replace($replace['replace_content_origin'], $replace['replace_content_value'], $response);
                }
            }
        }

        return $response;
    }

    /**
     * #####################
     * ###
     * #### FILTER ATTRIBUTES
     * ###
     * #####################
     */

    /**
     * Filter or add custom shortcode attributes to the existing ones.
     *
     * @param $attr - An array of shortcode attributes
     * @param $shortcode_data - The array of our custom shortcode settings
     * @param $tag - The original shortcode tag
     * @param $m - Pregmatch
     * @return array - An array of the available shortcode attributes
     */
    public function feature_add_attributes($attr, $shortcode_data, $tag, $m){
        if($this->skip_shortcode($tag))
            return $attr;

        if(!empty($shortcode_data['add_custom_shortcode_attributes_values'])){
            foreach($shortcode_data['add_custom_shortcode_attributes_values'] as $attributes){
                if(isset($attributes['add_custom_shortcode_attributes_key']) && isset($attributes['add_custom_shortcode_attributes_value'])){
                    $toggle = WPSCONT()->helpers->validate_yes_no_to_bool($attributes['add_custom_shortcode_attributes_overwrite']);
                    if(!empty($toggle)){
                        $attr[$attributes['add_custom_shortcode_attributes_key']] = $attributes['add_custom_shortcode_attributes_value'];
                    } else {
                        //Apply value just if the attribute is not set
                        if(!isset($attr[$attributes['add_custom_shortcode_attributes_key']]))
                            $attr[$attributes['add_custom_shortcode_attributes_key']] = $attributes['add_custom_shortcode_attributes_value'];
                    }
                }
            }
        }

        return $attr;
    }



}
new WP_Shortcode_Control_Features();