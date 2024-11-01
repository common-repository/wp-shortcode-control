<?php

/**
 * Class WP_Shortcode_Control_Settings
 *
 * This class contains all of our important settings
 *
 * @since 1.0.0
 * @package WPSCONT
 * @author Rarus <info@rarus.io>
 */
class WP_Shortcode_Control_Settings{

    /**
     * Our globally used handling capability
     *
     * @var string
     * @since 1.0
     */
    public $admin_cap;

    /**
     * WP_Shortcode_Control_Settings constructor.
     */
    function __construct(){
        $this->pagename = 'wpscont-settings';
        $this->admin_cap = 'manage_options';
        $this->general_settings = array();
    }

    /**
     * Our admin cap handler function
     *
     * This functionn handles the admin capability throughout
     * the whole plugin.
     *
     * $target - With the target function you can make a more precised filtering
     * by chaning it for specific actions.
     *
     * @param string $target - A identifier where the call comes from
     * @return mixed
     */
    public function get_admin_cap($target = 'main'){
        /**
         * Customize the globally uses capability for this plugin
         *
         * This filter is called every time a capability is needed.
         */
        return apply_filters('wpscont/admin/capability', $this->admin_cap, $target);
    }

    /**
     * The main settings field handler
     *
     * These are the settings we load for every shortcode
     *
     * @param array $args - Currently used to parse the shortcode tag
     * @return mixed - The array of all the currently available settings
     */
    public function get_shortcode_fields($args = array()){
        $fields = array(

            /**
             * DEACTIVATE SHORTCODE
             */
            'field-toggle_on_off' => array(
                'id'          => 'field-toggle_on_off',
                'type'        => 'boolean',
                'label'       => WPSCONT()->helpers->translate('Deactivate Shortcode', 'wpscont-fields-toggle-on-off-label'),
                'placeholder' => '',
                'value'       => 'no',
                'required'    => false,
                'tip'         => WPSCONT()->helpers->translate('You can deactivate the shortcode globally by activating this button. All the filters below get ignored.', 'wpscont-fields-toggle-on-off-tip'),
                'isValid'     => null
            ),

            /**
             * DISPLAY DEFAULT TEXT
             */
            'field-display_default_text' => array(
                'id'          => 'field-display_default_text',
                'type'        => 'textarea',
                'label'       => WPSCONT()->helpers->translate('Display Default Text', 'wpscont-fields-display-default-text-label'),
                'placeholder' => WPSCONT()->helpers->translate('Include your default text here...', 'wpscont-fields-display-default-text-placeholder'),
                'value'       => '',
                'required'    => false,
                'tip'         => WPSCONT()->helpers->translate('Replace the complete content of this shortcode. You can use HTML tags.', 'wpscont-fields-display-default-text-tip'),
                'isValid'     => null
            ),

            /**
             * Exclude posts
             */
            'field-exclude_posts' => array(
                'id'          => 'field-exclude_posts',
                'type'        => 'text',
                'label'       => WPSCONT()->helpers->translate('Exclude Post IDs', 'wpscont-fields-explude-posts-label'),
                'placeholder' => WPSCONT()->helpers->translate('e.g, 100,101,102', 'wpscont-fields-explude-posts-placeholder'),
                'value'       => '',
                'required'    => false,
                'tip'         => WPSCONT()->helpers->translate('Include comma separated post ids to exclude them from the whole logic. The rest applies to the logic.', 'wpscont-fields-explude-posts-tip'),
                'isValid'     => null,
            ),
            'field-include_posts' => array(
                'id'          => 'field-include_posts',
                'type'        => 'text',
                'label'       => WPSCONT()->helpers->translate('Include Post IDs', 'wpscont-fields-include-posts-label'),
                'placeholder' => WPSCONT()->helpers->translate('e.g, 100,101,102', 'wpscont-fields-include-posts-placeholder'),
                'value'       => '',
                'required'    => false,
                'tip'         => WPSCONT()->helpers->translate('Include comma separated post ids to include them for the whole logic. The rest of the posts are ignored.', 'wpscont-fields-include-posts-tip'),
                'isValid'     => null,
            ),

            /**
             * Replace Shortcode Content Strings
             */
            'field-replace_shortcode_content_strings_group' => array(
                'id'          => 'field-replace_shortcode_content_strings_group',
                'type'        => 'group',
                'label'       => WPSCONT()->helpers->translate('Replace Shortcode Content Strings', 'wpscont-fields-replace-shortcode-content-strings-group-label'),
                'placeholder' => '',
                'value'       => '',
                'required'    => false,
                'tip'         => '',
                'isValid'     => null
            ),
            'field-replace_shortcode_content_string' => array(
                'id'          => 'field-replace_shortcode_content_string',
                'type'        => 'repeater',
                'label'       => WPSCONT()->helpers->translate('Replace Multiple Content', 'wpscont-fields-replace-shortcode-content-string-label'),
                // 'max'         => '1',
                // 'min'         => '',
                'subFields'      => array(
                    'field-replace_content_origin' => array(
                        'id'          => 'field-replace_content_origin',
                        'type'        => 'text',
                        'label'       => WPSCONT()->helpers->translate('Content to replace', 'wpscont-fields-replace-content-origin-label'),
                        'placeholder' => WPSCONT()->helpers->translate('e.g, Hello', 'wpscont-fields-replace-content-origin-placeholder'),
                        'value'       => '',
                        'required'    => false,
                        'tip'         => WPSCONT()->helpers->translate('Include the content you want to replace inside of the shortcode.', 'wpscont-fields-replace-content-origin-tip'),
                        'isValid'     => null,
                    ),'field-replace_content_value' => array(
                        'id'          => 'field-replace_content_value',
                        'type'        => 'text',
                        'label'       => WPSCONT()->helpers->translate('New content', 'wpscont-fields-replace-content-value-label'),
                        'placeholder' => WPSCONT()->helpers->translate('e.g, Hi', 'wpscont-fields-replace-content-value-placeholder'),
                        'value'       => '',
                        'required'    => false,
                        'tip'         => WPSCONT()->helpers->translate('This is the content that gets inserted for the above one.', 'wpscont-fields-replace-content-value-tip'),
                        'isValid'     => null,
                    ),
                ),
                'required'    => false,
                'tip'         => WPSCONT()->helpers->translate('Include comma separated post ids to include them for the whole logic. The rest of the posts are ignored.', 'wpscont-fields-replace-shortcode-content-string-tip'),
                'isValid'     => null,
            ),

            /**
             * LIMIT RESPONSE CONTENT LENGTH
             */
            'field-limit_response_group' => array(
                'id'          => 'field-limit_response_group',
                'type'        => 'group',
                'label'       => WPSCONT()->helpers->translate('Limit Shortcode Content Field', 'wpscont-fields-limit-response-group-label'),
                'placeholder' => '',
                'value'       => '',
                'required'    => false,
                'tip'         => '',
                'isValid'     => null
            ),
            'field-limit_response' => array(
                'id'          => 'field-limit_response',
                'type'        => 'boolean',
                'label'       => WPSCONT()->helpers->translate('Limit Response?', 'wpscont-fields-limit-response-label'),
                'placeholder' => '',
                'value'       => 'no',
                'required'    => false,
                'tip'         => WPSCONT()->helpers->translate('Do you want to limit the shortcode\'s response length?', 'wpscont-fields-limit-response-tip'),
                'isValid'     => null
            ),
            'field-limit_response_length' => array(
                'id'          => 'field-limit_response_length',
                'type'        => 'number',
                'min'         => '-1',
                'label'       => WPSCONT()->helpers->translate('Response Length Limit', 'wpscont-fields-limit-response-length-label'),
                'placeholder' => WPSCONT()->helpers->translate('e.g, 100', 'wpscont-fields-limit-response-length-placeholder'),
                'value'       => '',
                'required'    => false,
                'tip'         => WPSCONT()->helpers->translate('Limit the content length of the shortcode content. Numbers only. Setting -1 removes the limit.', 'wpscont-fields-limit-response-length-tip'),
                'isValid'     => null,
                'conditions'  => array(
                    'field-limit_response' => array(
                        'value' => 'yes'
                    )
                )
            ),
            'field-limit_response_length_offset' => array(
                'id'          => 'field-limit_response_length_offset',
                'type'        => 'number',
                'min'         => '-1',
                'label'       => WPSCONT()->helpers->translate('Response Length Limit Offset', 'wpscont-fields-limit-response-length-offset-label'),
                'placeholder' => WPSCONT()->helpers->translate('e.g, 100', 'wpscont-fields-limit-response-length-offset-placeholder'),
                'value'       => '',
                'required'    => false,
                'tip'         => WPSCONT()->helpers->translate('Set an offset to start the limited string from a different position. Numbers only. Setting -1 removes the limit.', 'wpscont-fields-limit-response-length-offset-tip'),
                'isValid'     => null,
                'conditions'  => array(
                    'field-limit_response' => array(
                        'value' => 'yes'
                    )
                )
            ),
            'field-limit_response_length_dots' => array(
                'id'          => 'field-limit_response_length_dots',
                'type'        => 'boolean',
                'label'       => WPSCONT()->helpers->translate('Show Ellipsis (Three dots)', 'wpscont-fields-limit-response-length-dots-label'),
                'placeholder' => '',
                'value'       => 'yes',
                'required'    => false,
                'tip'         => WPSCONT()->helpers->translate('Display an ellipsis at the end of the text. This one is just shown if the length of the text is bigger or the same than the defined one.', 'wpscont-fields-limit-response-length-dots-tip'),
                'isValid'     => null,
                'conditions'  => array(
                    'field-limit_response' => array(
                        'value' => 'yes'
                    )
                )
            ),

            /**
             * Custom Shortcode Attributes
             */
            'field-add_custom_shortcode_attributes' => array(
                'id'          => 'field-add_custom_shortcode_attributes',
                'type'        => 'group',
                'label'       => WPSCONT()->helpers->translate('Add Custom Shortcode Attributes', 'wpscont-fields-add-custom-shortcode-attributes-label'),
                'placeholder' => '',
                'value'       => '',
                'required'    => false,
                'tip'         => '',
                'isValid'     => null
            ),
            'field-add_custom_shortcode_attributes_values' => array(
                'id'          => 'field-add_custom_shortcode_attributes_values',
                'type'        => 'repeater',
                'label'       => WPSCONT()->helpers->translate('Add Custom Attribute', 'wpscont-fields-add-custom-shortcode-attributes-values-label'),
                // 'max'         => '1',
                // 'min'         => '',
                'subFields'      => array(
                    'field-add_custom_shortcode_attributes_key' => array(
                        'id'          => 'field-add_custom_shortcode_attributes_key',
                        'type'        => 'text',
                        'label'       => WPSCONT()->helpers->translate('Name/Key of Attribute', 'wpscont-fields-add-custom-shortcode-attributes-key-label'),
                        'placeholder' => WPSCONT()->helpers->translate('e.g, category', 'wpscont-fields-add-custom-shortcode-attributes-key-placeholder'),
                        'value'       => '',
                        'required'    => false,
                        'tip'         => WPSCONT()->helpers->translate('This will later on be interpreted as the key of the attribute.', 'wpscont-fields-add-custom-shortcode-attributes-key-tip'),
                        'isValid'     => null,
                    ),'field-add_custom_shortcode_attributes_value' => array(
                        'id'          => 'field-add_custom_shortcode_attributes_value',
                        'type'        => 'text',
                        'label'       => WPSCONT()->helpers->translate('Attribute Value', 'wpscont-fields-add-custom-shortcode-attributes-value-label'),
                        'placeholder' => WPSCONT()->helpers->translate('e.g, Hi', 'wpscont-fields-add-custom-shortcode-attributes-value-placeholder'),
                        'value'       => '',
                        'required'    => false,
                        'tip'         => WPSCONT()->helpers->translate('This is the value that shoudl get parsed to the shortcodes function.', 'wpscont-fields-replace-content-value-tip'),
                        'isValid'     => null,
                    ),
                    'field-add_custom_shortcode_attributes_overwrite' => array(
                        'id'          => 'field-add_custom_shortcode_attributes_overwrite',
                        'type'        => 'boolean',
                        'label'       => WPSCONT()->helpers->translate('Overwrite existing values', 'wpscont-fields-replace-content-overwrite-label'),
                        'placeholder' => '',
                        'value'       => 'no',
                        'required'    => false,
                        'tip'         => WPSCONT()->helpers->translate('Check the button if you want to overwrite existing values.', 'wpscont-fields-replace-content-overwrite-tip'),
                        'isValid'     => null,
                        'conditions'  => array(
                            'field-limit_response' => array(
                                'value' => 'yes'
                            )
                        )
                    ),
                ),
                'required'    => false,
                'tip'         => WPSCONT()->helpers->translate('Create custom values to hook them into the parsed attributes of a shortcodes callback function.', 'wpscont-fields-add-custom-shortcode-attributes-values-tip'),
                'isValid'     => null,
            ),

        );
        //Set a global shortcode tag to add settings based on the shortcode
        $single_shortcode_tag = !empty($args['shortcode_tag']) ? $args['shortcode_tag'] : 'global';

        /**
         * Customize the globally uses capability for this plugin
         *
         * This filter is called every time a capability is needed.
         *
         * $single_shortcode_tag just gets called from our main field settigns function. If the tag is global,
         * then a custom plugin hooks into it to make changes
         */
        return apply_filters('wpscont/shortcode/settings/fields', $fields, $single_shortcode_tag, $args);
    }

    /**
     * Get Settings fields
     *
     * This is the same handling as for the shortcode fields
     *
     * @param array $args - Currently just used by custom plugins
     * @return mixed - An array of all available settings
     */
    public function get_settings_fields($args = array()) {
        $fields = array(

            /**
             * ACTIVATE TRANSLATIONS
             */
            'setting-activate_translations' => array(
                'id'           => 'setting-activate_translations',
                'type'         => 'boolean',
                'label'        => WPSCONT()->helpers->translate('Activate Translations', 'wpscont-settings-activate-translation-label'),
                'placeholder'  => '',
                'wp_option'    => 'wpscont_control_translations',
                'value'        => 'no',
                'required'     => false,
                'tip'          => WPSCONT()->helpers->translate('Activate this button to enable pluginwide translations. (If you don’t need translations, leave it deactivated and save performance.)', 'wpscont-settings-activate-translation-tip'),
                'isValid'      => null
            ),

            /**
             * Display remote content
             */
            'setting-rarus_display_remote_content' => array(
                'id'           => 'setting-rarus_display_remote_content',
                'type'         => 'boolean',
                'label'        => WPSCONT()->helpers->translate('Display Remote Content', 'wpscont-settings-activate-error-handling-label'),
                'placeholder'  => '',
                'wp_option'    => RARUS_PRIVACY,
                'value'        => 'no',
                'required'     => false,
                'tip'          => WPSCONT()->helpers->translate('If you want to display our remote content of awesomeness, you can check this box. If you want to know more about our plugin privacy, you can go to https://rarus.io/plugin-privacy', 'wpscont-settings-activate-error-handling-tip'),
                'isValid'      => null
            ),

            /**
             * ACTIVATE Error Handling
             */
            'setting-activate_error_handling' => array(
                'id'           => 'setting-activate_error_handling',
                'type'         => 'boolean',
                'label'        => WPSCONT()->helpers->translate('Activate Error Handling', 'wpscont-settings-activate-error-handling-label'),
                'placeholder'  => '',
                'wp_option'    => 'wpscont_manage_errors',
                'value'        => 'no',
                'required'     => false,
                'tip'          => WPSCONT()->helpers->translate('Activate this button to enable possible errors inside wordpress\' debug log.', 'wpscont-settings-activate-error-handling-tip'),
                'isValid'      => null
            ),

            /**
             * Disable Frontend Crawl Button
             */
            'setting-wpscont_disable_frontend_crawl' => array(
                'id'           => 'setting-wpscont_disable_frontend_crawl',
                'type'         => 'boolean',
                'label'        => WPSCONT()->helpers->translate('Disable frontend crawl button', 'wpscont-settings-disable-frontend-crawl-label'),
                'placeholder'  => '',
                'wp_option'    => 'wpscont_disable_frontend_crawl',
                'value'        => 'no',
                'required'     => false,
                'tip'          => WPSCONT()->helpers->translate('By activating this button, you remove the frontend crawl button from the sidebar.', 'wpscont-settings-disable-frontend-crawl-tip'),
                'isValid'      => null
            ),

            /**
             * Disable Backend Topbar item
             */
            'setting-wpscont_disable_backend_crawl' => array(
                'id'           => 'setting-wpscont_disable_backend_crawl',
                'type'         => 'boolean',
                'label'        => WPSCONT()->helpers->translate('Disable backend topbar item', 'wpscont-settings-disable-backend-crawl-label'),
                'placeholder'  => '',
                'wp_option'    => 'wpscont_disable_backend_crawl',
                'value'        => 'no',
                'required'     => false,
                'tip'          => WPSCONT()->helpers->translate('By activating this button, you remove the WP Shortcode Control menu item from the topbar.', 'wpscont-settings-disable-backend-crawl-tip'),
                'isValid'      => null
            ),

            /**
             * Remove data on deactivation
             */
            'setting-wpscont_remove_data_on_deactivation' => array(
                'id'           => 'setting-wpscont_remove_data_on_deactivation',
                'type'         => 'boolean',
                'label'        => WPSCONT()->helpers->translate('Remove plugin data on deactivation', 'wpscont-settings-remove-data-deactivation'),
                'placeholder'  => '',
                'wp_option'    => 'wpscont_remove_data_on_deactivation',
                'value'        => 'no',
                'required'     => false,
                'tip'          => WPSCONT()->helpers->translate('Activating this button will delete all of the plugin related data from the database on deactivation.', 'wpscont-settings-remove-data-deactivation-tip'),
                'isValid'      => null
            )

        );

        /**
         * Customize the globally uses capability for this plugin
         *
         * This filter is called every time a capability is needed.
         */
        return apply_filters('wpscont/settings/fields', $fields, $args);
    }

    public function get_general_options($args = array()){

        if(empty($this->general_settings)){
            $main_url = strtok((isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '?');
            $optin_url = $main_url . '?' . http_build_query(array_merge($_GET, array('rarus_privacy' => 'yes')));
            $optout_url = $main_url . '?' . http_build_query(array_merge($_GET, array('rarus_privacy' => 'no')));
            $admin_optin_url = admin_url('admin.php?page=' . $this->pagename) . '&' . http_build_query(array_merge($_GET, array('rarus_privacy' => 'yes')));
            $admin_optout_url = admin_url('admin.php?page=' . $this->pagename) . '&' . http_build_query(array_merge($_GET, array('rarus_privacy' => 'no')));

            $fields = array(
                'wpsc_tour_completed' => get_option('wpsc_tour_completed'),
                'wpsc_rarus_remote_api' => 'https://rarus.io/wp-json/rarus/v1/plugin_info/load/wp-shortcode-control-main', //get_option('wpsc_rarus_remote_api')
                'rarus_privacy' => get_option('rarus_privacy', 'not-set'),
                'site_url' => site_url(),
                'main_url' => $main_url,
                'optin_url' => $optin_url,
                'optout_url' => $optout_url,
                'admin_optin_url' => $admin_optin_url,
                'admin_optout_url' => $admin_optout_url,
            );

            //Save the fields also to our class to save performance
            $this->general_settings = $fields;
        } else {
            $fields = $this->general_settings;
        }

        return $fields;
    }

    /**
     * Returns an array for our management page (app) to
     * display custom translated strings in the frontend
     *
     * @return array - An array of our frontend texts
     */
    public function get_strings(){
        $strings = array(
            'settings_main_headline' => WPSCONT()->helpers->translate('WP Shortcode Control', 'wpscont-settings-strings-main-headline'),
            'settings_main_description' => WPSCONT()->helpers->translate('Manage all of your available shortcodes from this page by choosing one from the list and edit the settings on the right. If you can\'t find your shortcode, you can add it manually or you can crawl the site for it by using the topbar functionality in the backend or the side button in the frontend.', 'wpscont-settings-strings-main-description'),
            'settings_main_tab_shortcodes' => WPSCONT()->helpers->translate('Shortcodes', 'wpscont-settings-strings-main-tab-shortcodes'),
            'settings_main_tab_settings' => WPSCONT()->helpers->translate('Settings', 'wpscont-settings-strings-main-tab-settings'),
            'settings_shortcode_save_button' => WPSCONT()->helpers->translate('Save', 'wpscont-settings-strings-shortcode-save-button'),
            'settings_search_placeholder' => WPSCONT()->helpers->translate('Search Shortcode', 'wpscont-settings-strings-search-placeholder'),
            'settings_popup_add_headline' => WPSCONT()->helpers->translate('Add custom shortcode to list', 'wpscont-settings-strings-popup-add-headline'),
            'settings_popup_add_description' => WPSCONT()->helpers->translate('Add custom shortcode to the list if you can\'t find it in the left sidebar. You can easily add a new custom shortcode below or you can crawl the page where the shortcode is loaded. For more informations, read our documentation.', 'wpscont-settings-strings-popup-add-description'),
            'settings_popup_add_input_label' => WPSCONT()->helpers->translate('Shortcode tag', 'wpscont-settings-strings-popup-input-label'),
            'settings_popup_add_input_label_popup' => WPSCONT()->helpers->translate('Should not contain any spaces', 'wpscont-settings-strings-popup-input-label-shortcode'),
            'settings_popup_add_input_placeholder' => WPSCONT()->helpers->translate('e.g., siteorigin_widget', 'wpscont-settings-strings-popup-input-placeholder'),
            'settings_popup_add_button' => WPSCONT()->helpers->translate('Add Shortcode', 'wpscont-settings-strings-popup-add-button'),
            'settings_popup_delete_message' => WPSCONT()->helpers->translate('Are you sure you want to remove the custom shortcode \'%s\'?', 'wpscont-settings-strings-popup-delete-message'),

            //Settings
            'settings_tab_description' => WPSCONT()->helpers->translate('Manage all your settings here.', 'wpscont-settings-strings-tab-description'),
            'settings_tab_import_export_title' => WPSCONT()->helpers->translate('Import / Export', 'wpscont-settings-strings-tab-import-export-title'),
            'settings_tab_import_description' => WPSCONT()->helpers->translate('Choose a WPSC file or drag it here to import shortcodes.', 'wpscont-settings-strings-tab-description'),
            'settings_tab_import_export_spacer' => WPSCONT()->helpers->translate('OR', 'wpscont-settings-strings-tab-export-spacer'),
            'settings_tab_export_title' => WPSCONT()->helpers->translate('Export Shortcodes', 'wpscont-settings-strings-tab-export-title'),
            'settings_tab_import_success_message' => WPSCONT()->helpers->translate('Your settings have been imported successfully', 'wpscont-settings-strings-tab-import-success-message'),
            'settings_tab_activate_translations' => WPSCONT()->helpers->translate('Activate Translations', 'wpscont-settings-strings-tab-activate-translations'),
            'settings_tab_activate_translations_description' => WPSCONT()->helpers->translate('Activate this button to enable pluginwide translations. (If you don’t need translations, leave it deactivated and save performance.)', 'wpscont-settings-strings-tab-activate-translation-description'),
            'settings_tab_activate_error_handling' => WPSCONT()->helpers->translate('Activate Error Handling', 'wpscont-settings-strings-tab-activate-error-handling'),
            'settings_tab_activate_error_handling_description' => WPSCONT()->helpers->translate('Activate this button to enable possible errors inside WordPress\' debug log.', 'wpscont-settings-strings-tab-activate-error-handling-description'),

            // Fields
            'settings_field_repeater_add_item' => WPSCONT()->helpers->translate('Add Item', 'wpscont-settings-strings-field-repeater-add-item'),

            //General
            'settings_general_label' => WPSCONT()->helpers->translate('Label', 'wpscont-settings-strings-general-label'),
            'settings_general_value' => WPSCONT()->helpers->translate('Value', 'wpscont-settings-strings-general-value'),

            //Survey extended
            'settings_plugin_tour_button' => WPSCONT()->helpers->translate('Start Plugin Tour', 'wpscont-settings-plugin-tour-button'),

            //Welcome Box Privacy Content (when not set)
            'settings_privacy_content_not_set' => WPSCONT()->helpers->translate('<p>In order to show you the content from our API, we need your permission. This content will contain many helpful resources and new features for this plugin. You can accept or reject.  If you want to know more about our plugin privacy, you can go to <a href="https://rarus.io/plugin-privacy?utm_source=wpshortcode-admin&utm_medium=top-bar-privacy&utm_content=privacy&utm_campaign=WPSCont-Backlinks" title="Rarus Plugin Privacy" target="_blank" >https://rarus.io/plugin-privacy</a></p><p><a href="{admin_optin_url}" class="rarus-button rarus-button--is-small rarus-button--primary">Accept</a> <a href="{admin_optout_url}" class="rarus-button rarus-button--is-small rarus-button--grey">Reject</a></p><p><em>Don\'t worry, if you want to enable/disable the option later, you can do that under "<a href="#settings">Settings > Display Remote Content</a>" tab.</em></p>', 'wpscont-settings-plugin-tour-button'), );

        /**
         * Filter translations based on your needs.
         */
        return apply_filters('wpscont/shortcode/settings/strings', $strings);
    }

    /**
     * Returns an array for our management page (app) to
     * display a custom tour for users
     *
     * @return array - An array of our frontend texts
     */
    public function get_tour_strings(){

        $strings = array(
            array(
                'target' => '.tabs-component',
                'content' => WPSCONT()->helpers->translate('Welcome to our tour. This is the main admin page of WP Shortcode Control.', 'wpscont-survey'),
                'params' => array(
                    'placement' => 'bottom',
                    'modifiers' => array(
                        'flip' => array(
                            'behaviour' => array( 'bottom' )
                        ),
                        'preventOverflow' => array(
                            'enabled' => true,
                            'boundariesElement' => 'viewport',
                            'priority' => array( 'top' ),
                        ),
                    ),
                )
            ),
            array(
                'target' => '.wpsc-sidebar',
                'content' => WPSCONT()->helpers->translate('In here you have a list of all available shortcodes, that are registered by your site.', 'wpscont-survey'),
                'tab' => 'shortcodes',
                'params' => array(
                    'placement' => 'bottom',
                    'modifiers' => array(
                        'flip' => array(
                            'behaviour' => array( 'bottom' )
                        ),
                        'preventOverflow' => array(
                            'enabled' => true,
                            'boundariesElement' => 'viewport',
                            'priority' => array( 'top' ),
                        ),
                    ),
                )
            ),
            array(
                'target' => '.wpsc-content',
                'content' => WPSCONT()->helpers->translate('After you selected a shortcode, you can filter/edit it in this area. The name in the purple bar represents the current shortcode.', 'wpscont-survey'),
                'tab' => 'shortcodes',
                'params' => array(
                    'placement' => 'bottom',
                    'modifiers' => array(
                        'flip' => array(
                            'behaviour' => array( 'bottom' )
                        ),
                        'preventOverflow' => array(
                            'enabled' => true,
                            'boundariesElement' => 'viewport',
                            'priority' => array( 'top' ),
                        ),
                    ),
                )
            ),
            array(
                'target' => '.wpsc-shortcode-nav__item--add-new',
                'content' => WPSCONT()->helpers->translate('If you can\'t find a shortcode in the list, you can add it manually here, or you can crawl the site where the shortcode gets loaded.', 'wpscont-survey'),
                'tab' => 'shortcodes',
                'params' => array(
                    'placement' => 'bottom',
                    'modifiers' => array(
                        'flip' => array(
                            'behaviour' => array( 'bottom' )
                        ),
                        'preventOverflow' => array(
                            'enabled' => true,
                            'boundariesElement' => 'viewport',
                            'priority' => array( 'top' ),
                        ),
                    ),
                )
            ),
            array(
                'target' => '.tabs-component-tabs',
                'content' => WPSCONT()->helpers->translate('Here you can switch between the shortcode and the settings tab.', 'wpscont-survey'),
                'tab' => 'settings',
                'params' => array(
                    'placement' => 'bottom',
                    'modifiers' => array(
                        'flip' => array(
                            'behaviour' => array( 'bottom' )
                        ),
                        'preventOverflow' => array(
                            'enabled' => true,
                            'boundariesElement' => 'viewport',
                            'priority' => array( 'bottom', 'top' ),
                        ),
                    ),
                )
            ),
            array(
                'target' => '.wpsc-settings',
                'content' => WPSCONT()->helpers->translate('In here, we offer you various customizations and more features, so just check it out. :)', 'wpscont-survey'),
                'tab' => 'settings',
                'params' => array(
                    'placement' => 'bottom',
                    'modifiers' => array(
                        'flip' => array(
                            'behaviour' => array( 'bottom' )
                        ),
                        'preventOverflow' => array(
                            'enabled' => true,
                            'boundariesElement' => 'viewport',
                            'priority' => array( 'bottom', 'top' ),
                        ),
                    ),
                )
            ),
            array(
                'target' => '.wpsc-welcome',
                'content' => WPSCONT()->helpers->translate('Thats it! If you want to know more about this plugin and us, checkout this area.', 'wpscont-survey'),
                'tab' => 'settings',
                'params' => array(
                    'placement' => 'bottom',
                    'modifiers' => array(
                        'flip' => array(
                            'behaviour' => array( 'bottom' )
                        ),
                        'preventOverflow' => array(
                            'enabled' => true,
                            'boundariesElement' => 'viewport',
                            'priority' => array( 'top' ),
                        ),
                    ),
                )
            )
        );

        /**
         * Filter translations based on your needs.
         */
        return apply_filters('wpscont/settings/survey_strings', $strings);
    }

}