<?php
/**
 * Plugin Name: WP Shortcode Control
 * Plugin URI: https://rarus.io/wp-shortcode-control/?utm_source=rarus-plugin-wpsc&utm_medium=plugin-page&utm_campaign=Plugin%20to%20Plugin
 * Description: The easiest way to manage your shortcodes.
 * Author: Rarus
 * Author URI: https://rarus.io/?utm_source=rarus-plugin-wpsc&utm_medium=plugin-page&utm_campaign=Plugin%20to%20Rarus
 * Version: 1.0.2
 * Text Domain: wp-shortcode-control
 * Domain Path: languages
 *
 * You should have received a copy of the GNU General Public License
 * along with WP Shortcode Control. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Plugin name.
define( 'WPSCONT_NAME', 'WP Shortcode Control' );

// Plugin version.
define( 'WPSCONT_VERSION', '1.0.2' );

// Define Rarus privacy if not defined.
//Commonly it is loaded by our plugin handler
if(!defined('RARUS_PRIVACY'))
    define( 'RARUS_PRIVACY', 'rarus_privacy' );

// Plugin Root File.
define( 'WPSCONT_PLUGIN_FILE', __FILE__ );

// Plugin nbabse.
define( 'WPSCONT_PLUGIN_BASE', plugin_basename( WPSCONT_PLUGIN_FILE ) );

// Plugin Folder Path.
define( 'WPSCONT_PLUGIN_DIR', plugin_dir_path( WPSCONT_PLUGIN_FILE ) );

// Plugin Folder URL.
define( 'WPSCONT_PLUGIN_URL', plugin_dir_url( WPSCONT_PLUGIN_FILE ) );

// Plugin Root File.
define( 'WPSCONT_TEXTDOMAIN', 'wp-shortcode-control' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/deactivation.php
 */
function deactivate_wpscont() {
    require_once WPSCONT_PLUGIN_DIR . 'includes/deactivation.php';
    WPSC_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_wpscont' );

/**
 * Load our main instance for the helper function
 */
require_once WPSCONT_PLUGIN_DIR . 'core/class-wpscont-core.php';

/**
 * Our helper object class
 *
 * @return object|WP_Shortcode_Control
 */
function WPSCONT() {
    return WP_Shortcode_Control::instance();
}

// Run WP Shortcode Control helper object
WPSCONT();