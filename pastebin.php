<?php
/**
 * @package   Awesome Support Pastebin
 * @author    ThemeAvenue <web@themeavenue.net>
 * @license   GPL-2.0+
 * @link      http://themeavenue.net
 * @copyright 2015 ThemeAvenue
 * 
 * @boilerplate-version   0.1.3
 *
 * Plugin Name:       Awesome Support: Pastebin
 * Plugin URI:        http://getawesomesupport.com/addons/?utm_source=internal&utm_medium=plugin_meta&utm_campaign=Addons_Boilerplate
 * Description:       Use Pastebin to share code snippets from any ticket.
 * Version:           1.0
 * Author:            ThemeAvenue
 * Author URI:        http://themeavenue.net/?utm_source=internal&utm_medium=plugin_meta&utm_campaign=Addons_Boilerplate
 * Text Domain:       as-pastebin
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Instantiate the plugin
 *----------------------------------------------------------------------------*/

/**
 * Register the activation hook
 */
register_activation_hook( __FILE__, array( 'AS_Pastebin_Loader', 'activate' ) );

add_action( 'plugins_loaded', array( 'AS_Pastebin_Loader', 'get_instance' ) );
/**
 * Instanciate the addon.
 *
 * This method runs a few checks to make sure that the addon
 * can indeed be used in the current context, after what it
 * registers the addon to the core plugin for it to be loaded
 * when the entire core is ready.
 *
 * @since  0.1.0
 * @return void
 */
class AS_Pastebin_Loader {

	/**
	 * ID of the item.
	 *
	 * The item ID must match teh post ID on the e-commerce site.
	 * Using the item ID instead of its name has the huge advantage of
	 * allowing changes in the item name.
	 *
	 * If the ID is not set the class will fall back on the plugin name instead.
	 *
	 * @since 0.1.3
	 * @var int
	 */
	protected $item_id;

	/**
	 * Required version of the core.
	 *
	 * The minimum version of the core that's required
	 * to properly run this addon. If the minimum version
	 * requirement isn't met an error message is displayed
	 * and the addon isn't registered.
	 *
	 * @since  0.1.0
	 * @var    string
	 */
	protected $version_required = '3.1.6';

	/**
	 * Required version of PHP.
	 *
	 * Follow WordPress latest requirements and require
	 * PHP version 5.4 at least.
	 * 
	 * @var string
	 */
	protected $php_version_required = '5.4';

	/**
	 * Plugin slug.
	 *
	 * @since  0.1.0
	 * @var    string
	 */
	protected $slug = 'pastebin';

	/**
	 * Instance of this loader class.
	 *
	 * @since    0.1.0
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Instance of the addon itself.
	 *
	 * @since  0.1.0
	 * @var    object
	 */
	public $addon = null;

	/**
	 * Possible error message.
	 * 
	 * @var null|WP_Error
	 */
	protected $error = null;

	public function __construct() {
		$this->declare_constants();
		$this->init();
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     3.0.0
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Return an instance of the addon.
	 *
	 * @since  0.1.0
	 * @return object
	 */
	public function scope() {
		return $this->addon;
	}

	public function declare_constants() {
		define( 'AS_PASTEBIN_VERSION', '0.1.0' );
		define( 'AS_PASTEBIN_URL',     trailingslashit( plugin_dir_url( __FILE__ ) ) );
		define( 'AS_PASTEBIN_PATH',    trailingslashit( plugin_dir_path( __FILE__ ) ) );
	}

	/**
	 * Activate the plugin.
	 *
	 * The activation method just checks if the main plugin
	 * Awesome Support is installed (active or inactive) on the site.
	 * If not, the addon installation is aborted and an error message is displayed.
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public static function activate() {

		if ( ! class_exists( 'Awesome_Support' ) ) {
			deactivate_plugins( basename( __FILE__ ) );
			wp_die(
				sprintf( __( 'You need Awesome Support to activate this addon. Please <a href="%s" target="_blank">install Awesome Support</a> before continuing.', 'as-pastebin' ), esc_url( 'http://getawesomesupport.com/?utm_source=internal&utm_medium=addon_loader&utm_campaign=Pastebin' ) )
			);
		}

	}

	/**
	 * Initialize the addon.
	 *
	 * This method is the one running the checks and
	 * registering the addon to the core.
	 *
	 * @since  0.1.0
	 * @return boolean Whether or not the addon was registered
	 */
	public function init() {

		$plugin_name = $this->plugin_data( 'Name' );

		if ( ! $this->is_core_active() ) {
			$this->add_error( sprintf( __( '%s requires Awesome Support to be active. Please activate the core plugin first.', 'as-pastebin' ), $plugin_name ) );
		}

		if ( ! $this->is_php_version_enough() ) {
			$this->add_error( sprintf( __( 'Unfortunately, %s can not run on PHP versions older than %s. Read more information about <a href="%s" target="_blank">how you can update</a>.', 'as-pastebin' ), $plugin_name, $this->php_version_required, esc_url( 'http://www.wpupdatephp.com/update/' ) ) );
		}

		if ( ! $this->is_version_compatible() ) {
			$this->add_error( sprintf( __( '%s requires Awesome Support version %s or greater. Please update the core plugin first.', 'as-pastebin' ), $plugin_name, $this->version_required ) );
		}

		if ( is_a( $this->error, 'WP_Error' ) ) {
			add_action( 'admin_notices', array( $this, 'display_error' ), 10, 0 );
			add_action( 'admin_init',    array( $this, 'deactivate' ),    10, 0 );
			return false;
		}

		/**
		 * Register the addon
		 */
		wpas_register_addon( $this->slug, array( $this, 'load' ) );

		return true;

	}

	/**
	 * Get the plugin data.
	 *
	 * @since  0.1.0
	 * @param  string $data Plugin data to retrieve
	 * @return string       Data value
	 */
	protected function plugin_data( $data ) {

		if ( ! function_exists( 'get_plugin_data' ) ) {
			$admin_path = str_replace( get_site_url() . '/', ABSPATH, get_admin_url() );
			require_once( $admin_path . 'includes/plugin.php' );
		}

		$plugin = get_plugin_data( __FILE__, false, false );

		if ( array_key_exists( $data, $plugin ) ){
			return $plugin[$data];
		} else {
			return '';
		}

	}

	/**
	 * Check if core is active.
	 *
	 * Checks if the core plugin is listed in the acitve
	 * plugins in the WordPress database.
	 *
	 * @since  0.1.0
	 * @return boolean Whether or not the core is active
	 */
	protected function is_core_active() {
		if ( in_array( 'awesome-support/awesome-support.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check if the core version is compatible with this addon.
	 *
	 * @since  0.1.0
	 * @return boolean
	 */
	protected function is_version_compatible() {

		/**
		 * Return true if the core is not active so that this message won't show.
		 * We already have the error saying the plugin is disabled, no need to add this one.
		 */
		if ( ! $this->is_core_active() ) {
			return true;
		}

		if ( empty( $this->version_required ) ) {
			return true;
		}

		if ( ! defined( 'WPAS_VERSION' ) ) {
			return false;
		}

		if ( version_compare( WPAS_VERSION, $this->version_required, '<' ) ) {
			return false;
		}

		return true;

	}

	/**
	 * Check if the version of PHP is compatible with this addon.
	 *
	 * @since  0.1.0
	 * @return boolean
	 */
	protected function is_php_version_enough() {

		/**
		 * No version set, we assume everything is fine.
		 */
		if ( empty( $this->php_version_required ) ) {
			return true;
		}

		if ( version_compare( phpversion(), $this->php_version_required, '<' ) ) {
			return false;
		}

		return true;

	}

	/**
	 * Add error.
	 *
	 * Add a new error to the WP_Error object
	 * and create the object if it doesn't exist yet.
	 *
	 * @since  0.1.0
	 * @param string $message Error message to add
	 * @return void
	 */
	public function add_error( $message ) {

		if ( ! is_object( $this->error ) || ! is_a( $this->error, 'WP_Error' ) ) {
			$this->error = new WP_Error();
		}

		$this->error->add( 'addon_error', $message );

	}

	/**
	 * Display error.
	 *
	 * Get all the error messages and display them
	 * in the admin notices.
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function display_error() {

		if ( ! is_a( $this->error, 'WP_Error' ) ) {
			return;
		}

		$message = $this->error->get_error_messages(); ?>
		<div class="error">
			<p>
				<?php
				if ( count( $message ) > 1 ) {

					echo '<ul>';

					foreach ( $message as $msg ) {
						echo "<li>$msg</li>";
					}

					echo '</li>';

				} else {
					echo $message[0];
				}
				?>
			</p>
		</div>
	<?php

	}

	/**
	 * Deactivate the addon.
	 *
	 * If the requirements aren't met we try to
	 * deactivate the addon completely.
	 * 
	 * @return void
	 */
	public function deactivate() {
		if ( function_exists( 'deactivate_plugins' ) ) {
			deactivate_plugins( basename( __FILE__ ) );
		}
	}

	/**
	 * Load the addon.
	 *
	 * Include all necessary files and instanciate the addon.
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function load() {

		require( AS_PASTEBIN_PATH . 'includes/settings.php' );
		require( AS_PASTEBIN_PATH . 'includes/functions-pastebin.php' );

	}

}
