<?php

/**
 * Class WP_Shortcode_Control_Run
 *
 * Thats where we bring the plugin to life
 *
 * @since 1.0.0
 * @package WPSCONT
 * @author Rarus <info@rarus.io>
 */

class WP_Shortcode_Control_Run{

    /**
     * Includes a setting to display the frontend crawl button
     *
     * @var string
     * @since 1.0
     */
    public $disable_frontend_crawl;

    /**
     * WPSCONT settings Object.
     *
     * @var string
     * @since 1.5
     */
    public $pagename;

    /**
     * Our WP_Shortcode_Control_Run constructor.
     */
    function __construct(){
        $this->shortcodes = WPSCONT()->shortcodes->get_data();
        $this->pagename = 'wpscont-settings';
        $this->disable_frontend_crawl = get_option('wpscont_disable_frontend_crawl');
        $this->disable_backend_crawl = get_option('wpscont_disable_backend_crawl');
        $this->add_hooks();
    }

    /**
     * Register all of our plugin funnctionality and make the magic happen
     */
    private function add_hooks(){
        add_action('plugin_action_links_' . WPSCONT_PLUGIN_BASE, array($this, 'plugin_action_links') );

        //register deletion first
        add_action( 'init', array( $this, 'reset_all_shortcodes' ), 99 );

        //Include crawling twice for a better frontend &ä backend handling
        if(is_admin()){
            add_action( 'init', array( $this, 'set_shortcode_list' ), 100 );
        } else {
            add_action( 'wp', array( $this, 'set_shortcode_list' ), 100 );
        }

        add_action( 'admin_menu', array( $this, 'wpscont_add_admin_submenu' ), 150 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );
        add_action( 'wp_ajax_wpsc_extension_handler', array( $this, 'wpsc_ajax_extension_handler' ), 10 );

        // Frontend logic
        add_action('wp_head', array($this, 'wpsc_display_frontend_shortode_crawl'));

        //Backend logic
        add_action( 'admin_bar_menu', array( $this, 'wpsc_add_toolbar' ), 100 );

        //Add our awesome feature logic
        add_action( 'pre_do_shortcode_tag', array( $this, 'wpscont_preload_tag_filtering' ), PHP_INT_MAX, 4 );
        add_action( 'do_shortcode_tag', array( $this, 'wpscont_do_shortcode_tag' ), PHP_INT_MAX, 4 );

        //Rarus privacy
        add_action('init', array($this, 'rarus_privacy'));
    }

    /**
     * Plugin action links.
     *
     * Adds action links to the plugin list table
     *
     * Fired by `plugin_action_links` filter.
     *
     * @since 1.0.0
     * @access public
     *
     * @param array $links An array of plugin action links.
     *
     * @return array An array of plugin action links.
     */
    public function plugin_action_links( $links ) {
        $settings_link = sprintf( '<a href="%s">%s</a>', admin_url( 'tools.php?page=' . $this->pagename ), WPSCONT()->helpers->translate('Settings', 'plugin-action-links-header') );

        array_unshift( $links, $settings_link );

        $links['our_shop'] = sprintf( '<a href="%s" target="_blank" style="font-weight:700;color:#6238bf;">%s</a>', 'https://shop.rarus.io/?utm_source=rarus-plugin&utm_medium=plugin-page&utm_campaign=Plugin%20to%20Shop', WPSCONT()->helpers->translate('Our Shop', 'plugin-action-links') );

        return $links;
    }

    /**
     * Adds our custom admin page
     */
    public function wpscont_add_admin_submenu(){
        #add_menu_page( WPSCONT()->helpers->translate('WP Shortcode Control', 'admin-add-submenu-page-title'), WPSCONT()->helpers->translate('WP Shortcode Control', 'admin-add-submenu-menu-title'), WPSCONT()->settings->get_admin_cap('wpscont-main'), $this->pagename, array($this, 'wpscont_render_settings_page'), 'dashicons-layout', 28.28 );
        add_submenu_page('tools.php', WPSCONT()->helpers->translate('WP Shortcode Control', 'admin-add-submenu-page-title'), WPSCONT()->helpers->translate('WP Shortcode Control', 'admin-add-submenu-page-title'), WPSCONT()->settings->get_admin_cap('wpscont-main'), $this->pagename, array($this, 'wpscont_render_settings_page'));
    }

    /**
     * Displays the content for our custom element settings page
     */
    public function wpscont_render_settings_page(){
        //Make sure the specific capability is set
        if(!current_user_can(WPSCONT()->settings->get_admin_cap('wpscont-main')))
            wp_die( WPSCONT()->helpers->translate( 'You do not have sufficient permissions to access this page.', 'render-settings-page-capability-error-message' ) );

        include(WPSCONT_PLUGIN_DIR . 'core/includes/partials/wpscont-settings-display.php');
    }

    /**
     * Enqueue Scripts
     */
    public function enqueue_scripts(){
        if ( ! WPSCONT()->helpers->is_page($this->pagename) )
            return;

        $min = '.min';

        if ( defined( 'RARUS_ENV' ) && RARUS_ENV === 'dev' ) {
            $min = '';
        }

        wp_enqueue_script( 'wpscont-settings-vendor', site_url() . '/wp-content/plugins/wp-shortcode-control/core/assets/js/dist/wpsc-vendor' . $min . '.js', '', '1.0', true );
        wp_enqueue_script( 'wpscont-settings', site_url() . '/wp-content/plugins/wp-shortcode-control/core/assets/js/dist/wpsc-app' . $min . '.js', '', '1.0', true );
        wp_enqueue_style( 'wpscont-settings', site_url() . '/wp-content/plugins/wp-shortcode-control/core/assets/css/wpsc-admin.min.css' );
    }

    /**
     * #############################ä
     * ###
     * #### FRONTEND LOGIC
     * ###
     * #############################ä
     */

    /**
     * Displays a frontend button to crawl the current site for shortcodes
     *
     * You can disable it inside of the settings page.
     */
    public function wpsc_display_frontend_shortode_crawl(){
        $display = true;

        //Check against our setting if the button should be displayed
        if(WPSCONT()->helpers->validate_yes_no_to_bool($this->disable_frontend_crawl))
            return;

        if(!current_user_can(WPSCONT()->settings->get_admin_cap('wpsc-frontend-shortcode-crawl')))
            $display = false;
        /**
         * Use this hook if you want to customize the permission for displaying the frontend button.
         */
        $display_settings = apply_filters('wpscont/frontend/display_settings', $display);

        if($display_settings){

            $main_url = strtok((isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '?');
            $url_renew = $main_url . '?' . http_build_query(array_merge($_GET, array('wpsc_crawl_shortcodes' => '1')));
            $renew_title = WPSCONT()->helpers->translate('Crawl Shortcodes', 'admin-frontend-crawl-title');

            ob_start();
            ?>
            <!-- Fixed Side Actions -->
            <div id="wpsc-side-nav" class="sidenav hidden-xs hidden-sm" onclick="">
                <a href="<?php echo $url_renew; ?>" id="wpsc-crawl">
                    <span class="wpsc-plugin-brand">WP Shortcode Control</span>
                    <span class="wpsc-span"><?php echo $renew_title ?></span>
                </a>
            </div>
            <!-- Fixed Side Actions -->

            <style>
                #wpsc-side-nav a {
                    position: fixed; /* Position them relative to the browser window */
                    right: -180px; /* Position them outside of the screen */
                    transition: 0.3s; /* Add transition on hover */
                    padding: 10px; /* 15px padding */
                    padding-top: 15px;
                    padding-bottom: 5px;
                    width: 190px; /* Set a specific width */
                    text-decoration: none; /* Remove underline */
                    font-size: 20px; /* Increase font size */
                    color: white; /* White text color */
                    border-radius: 5px 0 0 5px; /* Rounded corners on the top right and bottom right side */
                    margin-top: 5%;
                    z-index: 9999;
                }

                #wpsc-side-nav a:hover {
                    right: 0; /* On mouse-over, make the elements appear as they should */
                }
                #wpsc-crawl {
                    top: 20px;
                    background-color: #7d44f4;
                }
                .wpsc-plugin-brand{
                    font-size: 9px;
                    color: #dcf;
                    position: absolute;
                    top: 4px;
                }
            </style>
            <?php
            $html = ob_get_clean();
            echo $html;
        }
    }

    /**
     * #############################ä
     * ###
     * #### BACKEND LOGIC
     * ###
     * #############################ä
     */

    /**
     * Add a custom toolbar item to wordpress.
     *
     * This includes a button to our main plugin management page,
     * as well as for crawling the backend sites for shortcodes
     */
    public function wpsc_add_toolbar(){
        //Check against our setting if the button should be displayed
        if(WPSCONT()->helpers->validate_yes_no_to_bool($this->disable_backend_crawl))
            return;

        global $wp_admin_bar;

        //Renewal link
        $url_renew = WPSCONT()->tools->current_page_url_crawl();

        // Create or add new items into the Admin Toolbar.
        // Main WP Shortcode Control Node
        $wp_admin_bar->add_node( array(
            'id'    => 'wp-shortcode-control',
            'title' => '<span class="wp-shortcode-control">Shortcode Control</span>',
            'href'  => admin_url( 'admin.php?page=' . $this->pagename )
        ));

        // Crawl current page for shortcodes
        $wp_admin_bar->add_node( array(
            'id'     => 'wpsc-crawl-site',
            'title'  => WPSCONT()->helpers->translate('Crawl page for shortcodes', 'admin-backend-topbar-menu-crawl'),
            'parent' => 'wp-shortcode-control',
            'href' => $url_renew
        ));
    }

    /**
     * #############################ä
     * ###
     * #### CORE LOGIC
     * ###
     * #############################ä
     */

    /**
     * Special function to validate our custom settings field
     * for simplicity to our features class
     *
     * @param $settings - The settings from settings.php
     * @return array - The validated settings
     */
    public function validate_setting_fields($settings){
        if(!is_array($settings))
            return $settings;

        $prefix = 'field-';
        $validated_settings = array();

        foreach($settings as $key => $val){

            //Remove field notation from the beginning
            if (substr($key, 0, strlen($prefix)) == $prefix) {
                $key = substr($key, strlen($prefix));
            }

            //validate sub arrays as well
            if(is_array($val)){
                $val = call_user_func(array($this, 'validate_setting_fields'), $val);
            }

            $validated_settings[$key] = $val;
        }

        return $validated_settings;
    }

    public function validate_shortcode_data($values){
        if(!is_array($values))
            return $values;

        $validated_values = array();

        foreach($values as $key => $val){

            //validate sub arrays as well
            if(is_array($val)){
                $val = call_user_func(array($this, 'validate_shortcode_data'), $val);
            } else {
                $val = stripslashes($val);
            }

            $validated_values[$key] = $val;
        }

        return $validated_values;
    }

    /**
     * This function overwrites the default do_shortcode_tag function defined in
     * wp-includes/shortcodes.php
     *
     * The logic applies as soon as there are settings available for a shortcode.
     * This function is necessary to filter the shortcode attributes based on our given values.
     *
     * @param $forward - false by default - comes from the default pre_do_shortcode_tag_filter
     * @param $tag - The shortcode tag
     * @param array $attr - the given attributes
     * @param string $m - the pregmatch
     * @return string - we always return a string of the html since it is our own logic and we don't want to run do_shortcode_tag twice.
     */
    public function wpscont_preload_tag_filtering($forward, $tag, $attr = array(), $m = ''){

        //Return as well if there is nothing set for custom filters
        $shortcode_data = $this->shortcodes;
        if(!is_array($shortcode_data))
            return $forward;

        //Return too if shortcode tag is empty
        if(!isset($shortcode_data[$tag]))
            return $forward;

        //Return too is there are no settings available
        if (!isset($shortcode_data[$tag]['settings']) || empty($shortcode_data[$tag]['settings']))
            return $forward;

        global $shortcode_tags;

        $content = isset( $m[5] ) ? $m[5] : null;

        $forward = apply_filters('wpscont/shortcodes/features/pre_do_shortcode_tag', $forward, $this->validate_setting_fields($shortcode_data[$tag]['settings']), $tag, $attr, $m);
        if ( false !== $forward )
            return $forward;

        //Validate the attribute array to a real array if empty. It's a string if no attributes are defined.
        $attr = (empty($attr) && !is_array($attr)) ? $attr = array() : $attr;

        /**
         * Apply a custom filter if any kind of attribute is set
         * @since 1.0.1
         */
        $attr = apply_filters('wpscont/shortcodes/features/filter_attributes', $attr, $this->validate_setting_fields($shortcode_data[$tag]['settings']), $tag, $m);

        $output = $m[1] . call_user_func( $shortcode_tags[ $tag ], $attr, $content, $tag ) . $m[6];

        /**
         * Filters the output created by a shortcode callback.
         *
         * @wp-since 4.7.0
         * @since 1.0.1
         *
         * @param string       $output Shortcode output.
         * @param string       $tag    Shortcode name.
         * @param array|string $attr   Shortcode attributes array or empty string.
         * @param array        $m      Regular expression match array.
         */
        $html = apply_filters( 'do_shortcode_tag', $output, $tag, $attr, $m );
        //Replace bool to string to not run do_shortcode_tag again
        if($html === false)
            $html = '';

        return $html;
    }

    /**
     * Our main function that links our shortcodes to
     * our features class for pre shortcode tags.
     *
     * That's where the functionality magic happens.
     *
     * @param $output - The outout from the do_shortcode_tag_function
     * @param $tag - The shortcode tag itself
     * @param array $attr - custom attributes
     * @param string $m - the preg filter
     * @return mixed - The customized value/string
     */
    public function wpscont_do_shortcode_tag($output, $tag, $attr = array(), $m = ''){

        $shortcode_data = $this->shortcodes;
        if(!is_array($shortcode_data))
            return $output;

        if(isset($shortcode_data[$tag])){
            if(isset($shortcode_data[$tag]['settings']) && !empty($shortcode_data[$tag]['settings']))
                $output = apply_filters('wpscont/shortcodes/features/do_shortcode_tag', $output, $this->validate_setting_fields($shortcode_data[$tag]['settings']), $tag, $attr, $m);
        }

        return $output;
    }

    /**
     * Reset all available shortcodes to default
     *
     * This loads basically the default list that is included in the shortcode tag global
     */
    public function reset_all_shortcodes(){
        if(!is_user_logged_in())
            return;

        if(!current_user_can(WPSCONT()->settings->get_admin_cap('wpscont-main-delete')))
            return;

        if(!isset($_GET['wpsc_reset_shortcodes']))
            return;

        WPSCONT()->shortcodes->delete_all();
        $args = array(
            'unset' => array(
                'wpsc_reset_shortcodes'
            )
        );
        wp_redirect(WPSCONT()->helpers->current_page_url($args));
    }

    /**
     * Set the shortcode list
     *
     * This function gets called
     * 1. When you are on the admin page with the right permissions
     * 2. Whe nyou crawl a site with the right permissions
     */
    public function set_shortcode_list(){
        if(!is_user_logged_in())
            return;

        //Allow access just for users with permissions
        if(!current_user_can(WPSCONT()->settings->get_admin_cap('wpscont-main-crawl')))
            return;

        //Validate the site if renew_shortcodes is not set
        if(!isset($_GET['wpsc_crawl_shortcodes'])){
            if(!WPSCONT()->helpers->is_page($this->pagename))
                return;
        }

        global $shortcode_tags;
        $shortcodes = $this->shortcodes;
        if(empty($shortcodes))
            $shortcodes = array(); //Validate it to an array

        foreach( $shortcode_tags as $tag => $value ){
            if(!isset($shortcodes[$tag])){
                $custom_values = apply_filters('wpscont/shortcodes/set_custom_values', array(
                    'type' => 'dynamic',
                    'settings' => array(),
                ), $tag);
                WPSCONT()->shortcodes->set_data($tag, $custom_values);
            }
        }
    }

    /**
     * ################################
     * ###
     * ##### --- AJAX SETTINGS ---
     * ###
     * ################################
     */


    /**
     * Main constructor for ajax handling
     *
     * This is a mai nhandler for our ajax function (single call principle)
     */
    public function wpsc_ajax_extension_handler(){
        // Make sure the user is able to handle data
        if ( isset( $_REQUEST ) && WPSCONT()->helpers->user_is_able_to( array(WPSCONT()->settings->get_admin_cap('wpsc-page-ajax')) ) ){
            $action = isset($_REQUEST['wpsc_handler']) ? $_REQUEST['wpsc_handler'] : '';
            $output = array();

            switch($action){
                // Fetch a list of all available shortcodes
                case 'fetch_settings':
                    $output = $this->ajax_fetch_settings();
                    break;
                // Fetch a list of all available shortcodes
                case 'fetch_general_options':
                    $output = $this->ajax_fetch_general_options();
                    break;
                // Fetch a list of all available shortcodes
                case 'fetch_shortcodes':
                    $output = $this->ajax_fetch_shortcodes();
                    break;
                // Update a existing shortcode
                case 'update_shortcode':
                    $output = $this->ajax_update_shortcode();
                    break;
                // Add a custom shortcode
                case 'add_custom_shortcode':
                    $output = $this->ajax_add_custom_shortcode();
                    break;
                // Delete a custom shortcode
                case 'delete_custom_shortcode':
                    $output = $this->ajax_delete_custom_shortcode();
                    break;
                // Fetch custom texts
                case 'fetch_custom_texts':
                    $output = $this->fetch_custom_texts();
                    break;
                // Fetch custom tour texts
                case 'fetch_tour':
                    $output = $this->fetch_tour_texts();
                    break;
                // Fetch custom tour texts
                case 'complete_tour':
                    $output = $this->wpsc_settings_switch_option( 'wpscont_tour_completed', array(
                        'success_true' => '"Complete Custom Survey" option is enabled.',
                        'success_false' => '"Complete Custom Survey" option is disabled.',
                        'error' => 'There was an error. Option could not be updated.',
                    ));
                    break;

                // Fetch custom texts
                case 'save_settings':
                    $output = $this->ajax_save_settings();
                    break;

                //Settings
                case 'settings_activate_translations':
                    $output = $this->wpsc_settings_switch_option( 'wpscont_control_translations', array(
                        'success_true' => '"Activate Translations" option is enabled.',
                        'success_false' => '"Activate Translations" option is disabled.',
                        'error' => 'There was an error. Option could not be updated.',
                    ));
                    break;
                case 'settings_activate_error_handling':
                    $output = $this->wpsc_settings_switch_option( 'wpscont_manage_errors', array(
                        'success_true' => '"Activate Error Handling" option is enabled.',
                        'success_false' => '"Activate Error Handling" option is disabled.',
                        'error' => 'There was an error. Option could not be updated.',
                    ));
                    break;
                case 'settings_export_shortcodes':
                    $output = $this->wpsc_settings_export_data();
                    break;
                case 'settings_import_shortcodes':
                    $output = $this->wpsc_settings_import_data();
                    break;
                case 'wpsc_welcome_box':
                    $output = $this->wpsc_welcome_box();
                    break;
            }

            echo json_encode($output);
        }

        // Always die in functions echoing ajax content
        die();
    }

    /**
     * Settings: Switch
     * This can be worked with any checkbox based input.
     *
     * @param $option_name - Name of the option
     * @param array $messages - Custom action message
     * @param null $trigger - validate the request if not set
     * @return array
     */
    public function wpsc_settings_switch_option( $option_name, $messages = array(), $trigger = null ){

        if ( $trigger === null ) {
            $trigger = sanitize_text_field( WPSCONT()->helpers->validate_request('trigger') );
        }

        $content = array( 'trigger' => $trigger );

        $msg_success_true  = isset( $messages['success_true'] ) ? $messages['success_true'] : WPSCONT()->helpers->translate('Option has been enabled', 'backend-ajax-option-notice-enable');
        $msg_success_false = isset( $messages['success_false'] ) ? $messages['success_false'] : WPSCONT()->helpers->translate('Option has been disabled', 'backend-ajax-option-notice-disable');
        $msg_error         = isset( $messages['error'] ) ? $messages['error'] : WPSCONT()->helpers->translate('There was an error. Option could not be updated.', 'backend-ajax-option-notice-update-error');

        if ( $trigger !== '' ) {
            $response = update_option( $option_name, $trigger );

            if ( $response ) {
                $content['status'] = 'success';

                if ( $trigger === 'yes' ) {
                    $content['msg'] = array( $msg_success_true );
                } else {
                    $content['msg'] = array( $msg_success_false );
                }
            } else {
                $content['status'] = 'error';
                $content['msg'] = array( $msg_error );
            }
        } else {
            $content['status'] = 'error';
            $content['msg'] = array( $msg_error );
        }

        return $content;

    }

    /**
     * Fetch the settings fields
     *
     * @return array - The settings array
     */
    private function ajax_fetch_general_options() {
        $return = WPSCONT()->settings->get_general_options();

        return $return;
    }

    /**
     * Fetch the settings fields
     *
     * @return array - The settings array
     */
    private function ajax_fetch_settings() {
        $return = array(
            'success' => false
        );

        $settings_fields = WPSCONT()->settings->get_settings_fields();
        $settings = array();

        if ( !empty($settings_fields)) {
            foreach ($settings_fields as $setting_id => $setting_value ) {
                $new_setting = $setting_value;
                if ( $setting_value['wp_option'] ) {
                    $new_setting['value'] = get_option( $setting_value['wp_option'], 'no' );
                }
                $settings[$setting_id] = $new_setting;
            }
        }

        if(!empty($settings)){
            $return = array(
                'success' => true,
                'settings' => $settings
            );
        }

        return $return;
    }

    /**
     * Fetch shortcode list
     *
     * @return array - An array of all available shortcodes with settings
     */
    private function ajax_fetch_shortcodes() {
        $return = array(
            'success' => false
        );

        $shortcodes_list = $this->shortcodes;
        $total = count( $shortcodes_list );
        $shortcodes = array();

        if ( ! empty( $shortcodes_list ) && is_array( $shortcodes_list ) ) {
            foreach( $shortcodes_list as $tag => $value ){
                $shortcodes[] = array(
                    'tag' => $tag,
                    'name' => $tag,
                    'settings' => $value['settings'],
                    'type' => !empty($value['type']) ? $value['type'] : 'dynamic',
                    'fields' => WPSCONT()->settings->get_shortcode_fields(array('shortcode_tag' => $tag)) //Include all available shortcode fields (This function includes a hook)
                );
            }
        }

        if(!empty($shortcodes) && !empty($total)){
            $return = array(
                'total' => $total,
                'success' => true,
                'shortcodes' => $shortcodes,
            );
        }

        return $return;
    }

    /**
     * Update a specific shortcode
     *
     * @return array - wether the update was successfull or not
     */
    private function ajax_update_shortcode(){
        $shortcode_tag       = sanitize_text_field(WPSCONT()->helpers->validate_request('shortcode'));
        $shortcode_settings       = WPSCONT()->helpers->sanitized_array( WPSCONT()->helpers->validate_request('shortcode_settings') );
        $return = array(
            'success' => false
        );

        if(empty($shortcode_settings) || empty($shortcode_tag))
            return $return;

        $shortcodes_list = $this->shortcodes;

        if(isset($shortcodes_list[$shortcode_tag])){
            $single_shortcode = $shortcodes_list[$shortcode_tag];
            $single_shortcode['settings'] = $this->validate_shortcode_data($shortcode_settings);

            $check = WPSCONT()->shortcodes->set_data($shortcode_tag, $single_shortcode, true);

            if($check)
                $return['success'] = true;
        }

        return $return;
    }

    /**
     * Adds a custom shortcode
     *
     * You can add custom shortcodes for example if you can't find yours inside of the list
     *
     * We also offer a frontend crawl function to crawl the specific site, so that you don't have to remove them
     *
     * @return array - wether the update was successfull or not
     */
    private function ajax_add_custom_shortcode(){
        $shortcode_tag       = sanitize_title(WPSCONT()->helpers->validate_request('shortcode'));

        $return = array(
            'success' => false
        );

        $shortcodes_list = $this->shortcodes;
        if(!isset($shortcodes_list[$shortcode_tag])){
            $single_shortcode_data = array(
                'type' => 'custom',
                'settings' => array()
            );
            $check = WPSCONT()->shortcodes->set_data($shortcode_tag, $single_shortcode_data);

            if($check) {
                $return = array(
                    'success' => true,
                    'type' => 'custom',
                    'shortcode' => array(
                        'tag' => $shortcode_tag,
                        'name' => $shortcode_tag,
                        'settings' => array(),
                        'fields' => WPSCONT()->settings->get_shortcode_fields(array('shortcode_tag' => $shortcode_tag)) //Include all available shortcode fields (This function includes a hook)
                    )
                );
            }
        }

        return $return;
    }

    /**
     * Deletes a custom shortcode
     *
     * With this function you can remove a custom shortcode
     * Please note, that the removal of a shortcode does not mean that the shortcode itself gets removed.
     * It just removes it from our settings screen
     *
     * @return array - wether the update was successfull or not
     */
    private function ajax_delete_custom_shortcode(){
        $shortcode_tag       = sanitize_text_field(WPSCONT()->helpers->validate_request('shortcode'));
        $return = array(
            'success' => false
        );

        $shortcodes_list = $this->shortcodes;
        if(isset($shortcodes_list[$shortcode_tag])){

            if($shortcodes_list[$shortcode_tag]['type'] == 'custom'){
                $check = WPSCONT()->shortcodes->delete_data($shortcode_tag);

                if($check)
                    $return['success'] = true;
            }

        }

        return $return;
    }

    /**
     * Fetch custom text strings
     *
     * We use this function to display various texts on our management page
     * All of them get translated through our default backend procedure
     *
     * @return array - The translated strings
     */
    private function fetch_custom_texts(){
        return WPSCONT()->settings->get_strings();
    }


    /**
     * Fetch custom tour strings
     *
     * This function is used for translating our tour strings separately.
     *
     * @return array - The translated strings
     */
    private function fetch_tour_texts(){
        $completed = get_option( 'wpscont_tour_completed' );

        $return = array(
            'strings' => WPSCONT()->settings->get_tour_strings(),
            'show_on_load' => ( $completed === 'yes' ? false : true )
        );

        return $return;
    }

    /**
     * Save the current shortcode settings from the frontend side
     *
     * @return array - wether the update was successfull or not
     */
    private function ajax_save_settings(){
        $settings       = WPSCONT()->helpers->sanitized_array( WPSCONT()->helpers->validate_request('settings') );
        $return = array(
            'success' => false,
            'settings' => array()
        );

        if(empty($settings))
            return $return;

        $return['success'] = true;

        foreach ( $settings as $setting_id => $setting_value ) {
            if ( $setting_id ) {
                $response = update_option( $setting_id, $setting_value );
                if ( $response ) {
                    $return[$setting_id] = array(
                        'success' => true
                    );
                } else {
                    $return[$setting_id] = array(
                        'success' => false
                    );
                }
            }
        }

        return $return;
    }

    /**
     * Ajax function to export data from the frontend
     *
     * This enables you to download all the available data from the frontend
     *
     * @return array - The exported settings data
     */
    private function wpsc_settings_export_data(){
        $overwrite       = WPSCONT()->helpers->validate_request('shortcodes_export_overwrite');
        $return = array(
            'success' => false,
            'overwrite' => $overwrite
        );


        $data = WPSCONT()->tools->export('', $overwrite); //sending an empty string downloads all

        if(!empty($data)){
            $return['success'] = true;
            $return['data'] = $data;
        }

        return $return;
    }

    /**
     * Import the previously exported shortcodes
     *
     * @return array - wether the update was successfull or not
     */
    private function wpsc_settings_import_data(){
        //Overwrite is not needed anymore, because it is included inside of the file (We still use it for user changes outside of the box
        $wpsc_file       = isset($_FILES['shortcodes_import_file']) ? $_FILES['shortcodes_import_file'] : false;
        $overwrite       = WPSCONT()->helpers->validate_request('shortcodes_import_overwrite');
        $return = array(
            'success' => false
        );

        $shortcodes = file_get_contents( $wpsc_file['tmp_name'] );
        $check = WPSCONT()->tools->import($shortcodes, $overwrite);

        if($check){
            $return['success'] = true;
        }

        return $return;
    }

    /**
     * This is our main promotion box
     *
     * In here we display all of our cool offers for more awesomeness
     *
     * @return array
     */
    private function wpsc_welcome_box(){

        $rarus_privacy = get_option(RARUS_PRIVACY);

        ob_start();
        ?>
        <p>First of all thank you a lot for using our plugin.
            <br>We at <a target="_blank" href="https://rarus.io?utm_source=wpshortcode-admin&utm_medium=top-bar-home&utm_content=home&utm_campaign=WPSCont-Backlinks"><strong>Rarus</strong></a> put all of our effort and knowledge into our plugins, to make your experience as smooth as possible. All of our code is completely optimized and performance oriented.</p>


        <?php if(current_user_can(WPSCONT()->settings->get_admin_cap('wpscont-rarus-privacy')) && empty($rarus_privacy)) : ?>
            <p>To make your experience even more amazing, we would like to show you our plugin optimized content from our website. It includes links to the documentation, special offers and more cool stuff.
                Before we display it, we'd like to ask you if you give us the permission for that. (We don't use it for tracking purposes. Only for providing you more value. You can always disable it.)</p>
            <a href="{admin_page_optin_url}" class="button-secondary">Show me your amazing content!</a>
            <a href="{admin_page_optout_url}" class="button-secondary">Don't show it to me.</a>
            <p>If you want to know more about our plugin privacy, you can visit it at <a target="_blank" href="https://rarus.io/plugin-privacy?utm_source=wpshortcode-admin&utm_medium=top-bar-privacy&utm_content=privacy&utm_campaign=WPSCont-Backlinks">https://rarus.io/plugin-privacy</a></p>
        <?php endif;
        $html = ob_get_clean();

        $return = array(
            'heading' => 'Welcome to WP Shortcode Control',
            'content' => $html,
            'rarus_remote' => ''
        );

        if($rarus_privacy == 'yes'){
            $url = 'https://rarus.io/wp-json/rarus/v1/plugin_info/load/wp-shortcode-control-main';
            $return['rarus_remote'] = $url;
        }

        $return['heading'] = $this->replace_welcome_box_tags($return['heading']);
        $return['content'] = $this->replace_welcome_box_tags($return['content']);

        return $return;
    }

    public function replace_welcome_box_tags($content){

        $main_url = strtok((isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '?');
        $optin_url = $main_url . '?' . http_build_query(array_merge($_GET, array('rarus_privacy' => 'yes')));
        $optout_url = $main_url . '?' . http_build_query(array_merge($_GET, array('rarus_privacy' => 'no')));
        $admin_optin_url = admin_url('admin.php?page=' . $this->pagename) . '&' . http_build_query(array_merge($_GET, array('rarus_privacy' => 'yes')));
        $admin_optout_url = admin_url('admin.php?page=' . $this->pagename) . '&' . http_build_query(array_merge($_GET, array('rarus_privacy' => 'no')));

        $tags = array(
            'site_url' => site_url(),
            'privacy_optin_url' => $optin_url,
            'privacy_optout_url' => $optout_url,
            'admin_page_optin_url' => $admin_optin_url,
            'admin_page_optout_url' => $admin_optout_url,
        );

        foreach($tags as $key => $val){
            $content = str_replace('{' . $key . '}', $val, $content);
        }

        return $content;
    }

    public function rarus_privacy(){
        if(!current_user_can(WPSCONT()->settings->get_admin_cap('wpscont-rarus-privacy')))
            return;

        if(!isset($_GET['rarus_privacy']))
            return;

        $privacy = $_GET['rarus_privacy'];

        if($privacy == 'yes'){
            update_option(RARUS_PRIVACY, 'yes');
        } elseif($privacy == 'reset'){
            delete_option(RARUS_PRIVACY);
        } else {
            update_option(RARUS_PRIVACY, 'no');
        }
    }

}
