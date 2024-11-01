<?php

/**
 * Class WP_Shortcode_Control_Tools
 *
 * @since 1.0.0
 * @package WPSCONT
 * @author Rarus <info@rarus.io>
 */
class WP_Shortcode_Control_Tools{

    /**
     * Export your shortcode settings
     *
     * @param $shortcodes - the shortcode tag as array values
     * @param bool $overwrite - Wether to overwrite existing shortcodes or not
     * @return string - returns a json with all of the defined shortcode settings
     */
    public function export($shortcode = array(), $overwrite = false){
        $shortcodes = array(
            'overwrite' => $overwrite
        );

        if(is_array($shortcode)){
            foreach($shortcode as $shortcode_tag){
                //Apply the single shortcodes dynamically to create a multi import file
                $shortcodes['data'][] = WPSCONT()->shortcodes->get_data($shortcode_tag);
            }
        } else {
            $shortcodes['data'] = WPSCONT()->shortcodes->get_data($shortcode);
        }

        $shortcodes = json_encode($shortcodes);

        return $shortcodes;
    }

    /**
     * Import the current shortcode settings
     *
     * 1. you can send an empty array which means you download all
     * 2. you can send an array with the shortcode tags as values to download just specific ones
     *
     * e.g.,
     * array(
     *  'shortcode_1',
     *  'shortcode_2'
     * )
     *
     * $overwrite means if you want to overwrite a shortcode setting if it already exists
     * (This logic gets probably effected by the user input on the settings page)
     *
     * @param $shortcodes - the shortcode tag as array values
     * @param bool $overwrite - Wether to overwrite existing shortcodes or not
     * @return bool - true if import was successful
     */
    public function import($shortcodes, $overwrite = false){
        $return = false;
        $errors = array();

        $shortcodes = json_decode($shortcodes, true);

        if(!is_array($shortcodes) || empty($shortcodes))
            return $return;

        if(empty($shortcodes['data']))
            return $return;

        $shortcode_data = $shortcodes['data'];

        if(!empty($overwrite)){
            $overwrite = true;
        } else {
            $overwrite = (!empty($shortcodes['overwrite']) && $shortcodes['overwrite'] !== false) ? true : false;
        }

        foreach($shortcode_data as $shortcode => $shortcode_data_single){
            $check = WPSCONT()->shortcodes->set_data($shortcode, $shortcode_data_single, $overwrite);
            if(!$check)
                $errors[] = WPSCONT()->helpers->translate('The following shortcode could not be imported properly: ', 'tools-import-error') . $shortcode;
            $return = true;
        }

        //Throw errors if available
        if(!empty($errors))
            WPSCONT()->helpers->throw_errors($errors);

        return $return;
    }

    /**
     * Grab the current url as a crawl url for shortcodes
     *
     * @return string - The url
     */
    public function current_page_url_crawl(){
        $main_url = strtok((isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '?');
        $url_renew = $main_url . '?' . http_build_query(array_merge($_GET, array('wpsc_crawl_shortcodes' => '1')));
        return $url_renew;
    }

    /**
     * Grab the current url for deleting shortcodes
     *
     * @return string - The url
     */
    public function current_page_url_delete(){
        $main_url = strtok((isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '?');
        $url_renew = $main_url . '?' . http_build_query(array_merge($_GET, array('wpsc_crawl_shortcodes' => '1')));
        return $url_renew;
    }

}