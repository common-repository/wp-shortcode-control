<?php

/**
 * WP_Shortcode_Control_Helpers Class
 *
 * This class handles the role creation and assignment of capabilities for those roles.
 *
 * @since 1.0.0
 */

/**
 * The helpers of the plugin.
 *
 * @since 1.0.0
 * @package WPSCONT
 * @author Rarus <info@rarus.io>
 */
class WP_Shortcode_Control_Helpers {

    /**
     * The globally defined control translation option
     *
     * @var string
     * @since 1.0.0
     */
    public $translate;

    /**
     * The globally defined manage_errors option
     *
     * @var string
     * @since 1.0.0
     */
    public $manage_errors;

    /**
     * The globally defined disabble ssl verification option
     *
     * @var string
     * @since 1.0.0
     */
    public $disable_ssl;

    /**
     * WP_Shortcode_Control_Helpers constructor.
     */
    function __construct(){

        /**
         * Prebuffer values to make the performance more efficient
         */
        $this->translate = get_option('wpscont_control_translations');
        $this->manage_errors = get_option('wpscont_manage_errors');
        $this->disable_ssl = get_option('_wpscont_disable_ssl_verification');

        /**
         * This is a hidden option key. We just provide it this way for developers
         * to disable the wpml string translationn function in an performance
         * optimized way
         */
        $this->disable_wpml = get_option('_wpscont_disable_wpml_translate_functions');
    }

    /**
     * Translate custom Strings
     *
     * @param $string - The language string
     * @param null $cname - If no custom name is set, return the default one
     * @return string - The translated language string
     */
    public function translate( $string, $cname = null, $prefix = null ){

        //Checkbox for enabling the usage of translateable strings
        if(!empty($this->translate)){
            $enable = true;
        } else {
            $enable = false;
        }

        /**
         * Filter to control the translation and optimize
         * them to a specific output
         */
        $trigger = apply_filters('wpscont/helpers/control_translations', $enable, $string, $cname);
        if(empty($trigger)){
            return $string;
        }

        //If empty, we return the current value as it is
        if(empty($string))
            return $string;

        //Keep our globally available textdomain
        $txtdomain = WPSCONT_TEXTDOMAIN;

        if(!empty($cname)){
            $context = $cname;
        } else {
            $context = 'default';
        }

        //For outputting a prefix on various translations
        if($prefix == 'default'){
            $front = 'WPSCONT: ';
        } elseif (!empty($prefix)){
            $front = $prefix;
        } else {
            $front = '';
        }

        // WPML String Translation Logic
        if(function_exists('icl_t') && empty($this->disable_wpml)){
            // icl_t( TEXTDOMAIN, CONTEXT, STRING )
            return icl_t((string) $txtdomain, $context, $string);
        } else {
            return $front . _x($string, $context, (string) $txtdomain);
        }
    }

    /**
     * Checks if the current user is able to do an action depending
     * on the specified capability
     *
     * @param $caps - an array of capabilities
     * @param $type - wether it is a capability or a role
     * @return bool - wether the user can or not
     */
    public function user_is_able_to($caps, $type = 'capability'){
        if(empty($caps))
            return false;

        //Format single string to array
        if(!is_array($caps))
            $caps = array($caps);

        $user = get_current_user_id();
        $is_able = false;

        if(!empty($user)){
            if($type == 'capability'){
                foreach($caps as $cap){
                    if(current_user_can($cap)){
                        $is_able = true;
                    }
                }
            } else {
                foreach($caps as $cap){
                    $user_meta=get_userdata($user);
                    $user_roles=$user_meta->roles; //array of roles the user is part of.

                    if(in_array($cap, $user_roles))
                        $is_able = true;
                }
            }
        }

        /**
         * Filter for custom handling if a user is able to do something
         */
        return apply_filters('wpscont/helpers/user_is_able_to', $is_able, $caps);

    }

    /**
     * Retrieve the current page url
     *
     * @param array $args - special args as used down below
     * @return string - the url
     */
    public static function current_page_url($args = array()){
        $main_url = strtok((isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '?');

        //Replace unwanted parameters
        $url_params = $_GET;
        if(isset($args['unset'])){
            foreach($args['unset'] as $param){
                if(isset($url_params[$param]))
                    unset($url_params[$param]);
            }
        }

        $current_url = $main_url . '?' . http_build_query($url_params);
        return $current_url;
    }

    /**
     * Pass Array and Generate a New fully Sanitized Array
     *
     * @param $arr
     * @return array
     */
    public static function sanitized_array( $arr ) {
        if ( ! is_array( $arr ) ) return $arr;

        $new_array = array();

        foreach ($arr as $key => $value) {
            // If the $value is also an array,
            // reiterate it through the same array.
            if ( is_array( $value ) ) {
                $new_array[$key] = WP_Shortcode_Control_Helpers::sanitized_array( $value );
            } else {
                $new_array[$key] = wp_kses( nl2br( $value ), array( 'br' ) );
            }
        }

        return $new_array;
    }

    /**
     * Validate a yes or no string to boolean
     *
     * @param $value
     * @return bool
     */
    public static function validate_yes_no_to_bool($value){
        if(!is_string($value) || empty($value))
            return false;

        if($value == 'yes'){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if the parsed param is available on the current site
     *
     * @param $param
     * @return bool
     */
    public function is_page( $param ){
        if(empty($param))
            return false;

        if(isset($_GET['page'])){
            if($_GET['page'] == $param){
                return true;
            }
        }

        //Otherwise return false
        return false;
    }

    /**
     * Checks if the parsed param is available on the current site
     *
     * @param $param - $key of the $_GET var
     * @return bool - true if param is set
     */
    public function is_param_set( $param, $value ){
        if(empty($param))
            return false;

        if(isset($_GET[$param])){
            if(!empty($value)){
                if($_GET[$param] == $value){
                    return true;
                }
            } else {
                //return true if no value is set (This statement can just be reached if no value is set)
                return true;
            }

        }

        //Otherwise return false
        return false;
    }

    /**
     * Writes errors to the wordpress debug log
     *
     * @param $arr - array of debug messages
     * @return bool - true if error can nbe thrown
     */
    public function throw_errors( $arr ){
        if(!is_array($arr) && empty($arr))
            return false;

        /**
         * Backend setting for enabling debugging
         */
        if(!empty($this->manage_errors)){
            $manage_errors = true;
        } else {
            $manage_errors = false;
        }

        /**
         * Decide by yourself if a specific error or errors
         * in general should be thrown to wordpress' error log
         */
        $enable = apply_filters('wpscont/helpers/throw_error', $manage_errors, $arr);

        if($enable){
            foreach($arr as $error){
                error_log($error);
            }
        }

        return true;
    }

    /**
     * Converts any kind of var to a string
     *
     * @param $data
     * @return string
     */
    public function dump_output( $data ){
        ob_start();
        var_dump($data);
        $res = ob_get_clean();
        return $res;
    }

    /**
     * Creates a formatted admin notice
     *
     * @param $content - notice content
     * @param string $type - Status of the specified notice
     * @param bool $is_dismissible - If the message should be dismissible
     * @return string - The formatted admin notice
     */
    public function create_admin_notice($content, $type = 'info', $is_dismissible = true){
        if(empty($content))
            return '';

        /**
         * Block an admin notice based onn the specified values
         */
        $throwit = apply_filters('wpscont/helpers/throw_admin_notice', true, $content, $type, $is_dismissible);
        if(!$throwit)
            return '';

        if($is_dismissible !== true){
            $isit = '';
        } else {
            $isit = 'is-dismissible';
        }


        switch($type){
            case 'info':
                $notice = 'notice-info';
                break;
            case 'success':
                $notice = 'notice-success';
                break;
            case 'warning':
                $notice = 'notice-warning';
                break;
            case 'error':
                $notice = 'notice-error';
                break;
            default:
                $notice = 'notice-info';
                break;
        }

        ob_start();
        ?>
        <div class="notice <?php echo $notice; ?> <?php echo $isit; ?>">
            <p><?php echo $this->translate($content, 'create-admin-notice'); ?></p>
        </div>
        <?php
        $res = ob_get_clean();

        return $res;
    }

    /**
     * Retreives a response from an url
     *
     * @param $url
     * @param $data - specifies a special part of the response
     * @return array|bool|int|string|WP_Error
     */
    public function get_from_api( $url, $data = '' ){

        if(empty($url))
            return false;

        if(!empty($this->disable_ssl)){
            $setting = array(
                'sslverify'     => false,
                'timeout' => 30
            );
        } else {
            $setting = array(
                'timeout' => 30
            );
        }

        $val = wp_remote_get( $url, $setting );

        if($data == 'body'){
            $val = wp_remote_retrieve_body( $val );
        } elseif ($data == 'response'){
            $val = wp_remote_retrieve_response_code( $val );
        }


        return $val;

    }

    /**
     * Function to validate a specified variable of a request
     *
     * @param $key - The request key name of the specified varue
     * @param bool $if_false - Return a custom value if the request is false
     * @return mixed - Value of the specified request
     */
    public function validate_request($key, $if_false = false){
        $check = isset($_REQUEST[$key]) ? $_REQUEST[$key] : $if_false;
        return $check;
    }

}
