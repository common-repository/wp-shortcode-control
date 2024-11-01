<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://rarus.io
 * @since      1.0.0
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @author     Rarus <info@rarus.io>
 */
class WPSC_Deactivator {

    /**
     * The main deactivation logic
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        if(get_option( 'wpscont_remove_data_on_deactivation' ) !== 'yes')
            return;

        WPSC_Deactivator::delete_settings();
    }

    public static function delete_settings(){
        delete_option('wpscont_shortcode_list');
        delete_option('wpscont_disable_frontend_crawl');
        delete_option('wpscont_disable_backend_crawl');
        delete_option('_wpscont_disable_ssl_verification');
        delete_option('wpscont_manage_errors');
        delete_option('wpscont_control_translations');
        delete_option('wpscont_remove_data_on_deactivation');
        delete_option('wpscont_tour_completed');
        delete_option('_wpscont_disable_wpml_translate_functions');
        delete_option('wpsc_tour_completed');
        delete_option('wpsc_rarus_remote_api');

        // The privacy key stays deactivated because it is a users choice
        // to keep data safe. If you still want to deactivate it, remove
        // the hashtag below.
        #delete_option('rarus_privacy');
    }

}
