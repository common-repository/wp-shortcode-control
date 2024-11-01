<?php
if ( ! class_exists( 'WP_Shortcode_Control' ) ) :

    /**
     * Main WP_Shortcode_Control Class.
     *
     * @since 1.0.0
     * @package WPSCONT
     * @author Rarus <info@rarus.io>
     */
    final class WP_Shortcode_Control {

        /**
         * @var WP_Shortcode_Control
         * @since 1.0.0
         */
        private static $instance;

        /**
         * WPSCONT settings Object.
         *
         * @var object|WP_Shortcode_Control_Settings
         * @since 1.0.0
         */
        public $settings;

        /**
         * WPSCONT helpers Object.
         *
         * @var object|WP_Shortcode_Control_Helpers
         * @since 1.0.0
         */
        public $helpers;

        /**
         * WPSCONT shortcodes Object.
         *
         * @var object|WP_Shortcode_Control_Shortcode
         * @since 1.0.0
         */
        public $shortcodes;

        /**
         * WPSCONT run Object.
         *
         * @var object|WP_Shortcode_Control_Run
         * @since 1.0.0
         */
        public $run;

        /**
         * WPSCONT tools Object.
         *
         * @var object|WP_Shortcode_Control_Tools
         * @since 1.0.0
         */
        public $tools;

        /**
         * Throw error on object clone.
         *
         * Cloning instances of the class is forbidden.
         *
         * @since 1.0.0
         * @return void
         */
        public function __clone() {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'rarus' ), '1.0.0' );
        }

        /**
         * Disable unserializing of the class.
         *
         * @since 1.0.0
         * @return void
         */
        public function __wakeup() {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'rarus' ), '1.0.0' );
        }

        /**
         * Main WP_Shortcode_Control Instance.
         *
         * Insures that only one instance of WP_Automate exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 1.0.0
         * @static
         * @staticvar array $instance
         * @return object|WP_Shortcode_Control The one true WP_Shortcode_Control
         */
        public static function instance() {
            if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WP_Shortcode_Control ) ) {
                self::$instance =                new WP_Shortcode_Control;
                self::$instance->base_hooks();
                self::$instance->includes();
                self::$instance->settings        = new WP_Shortcode_Control_Settings();
                self::$instance->helpers         = new WP_Shortcode_Control_Helpers();
                self::$instance->shortcodes      = new WP_Shortcode_Control_Shortcode();
                self::$instance->tools           = new WP_Shortcode_Control_Tools();

                new WP_Shortcode_Control_Run();
            }

            return self::$instance;
        }

        /**
         * Include required files.
         *
         * @access private
         * @since 1.0.0
         * @return void
         */
        private function includes() {
            require_once WPSCONT_PLUGIN_DIR . 'core/includes/classes/helpers.php';
            require_once WPSCONT_PLUGIN_DIR . 'core/includes/classes/settings.php';
            require_once WPSCONT_PLUGIN_DIR . 'core/includes/classes/shortcodes.php';
            require_once WPSCONT_PLUGIN_DIR . 'core/includes/classes/features.php';
            require_once WPSCONT_PLUGIN_DIR . 'core/includes/classes/tools.php';

            require_once WPSCONT_PLUGIN_DIR . 'core/includes/classes/run.php';
        }

        /**
         * Include required files.
         *
         * @access private
         * @since 1.0.0
         * @return void
         */
        private function base_hooks() {
            add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain') );
        }

        /**
         * Loads the plugin language files.
         *
         * @access public
         * @since 1.0.0
         * @return void
         */
        public function load_textdomain() {
            load_plugin_textdomain( WPSCONT_TEXTDOMAIN, FALSE, dirname( plugin_basename( WPSCONT_PLUGIN_FILE ) ) . '/language/' );
        }

    }

endif; // End if class_exists check.