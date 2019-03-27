<?php
use \Plugin_Name\Includes\i18n;
use \Plugin_Name\Core\Registry\Controller as Controller_Registry;
use \Plugin_Name\Core\Registry\Controller as Model_Registry;

require_once plugin_dir_path( __FILE__ ) . '/trait-dependency-loader.php';

/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 */
if ( ! class_exists( 'Plugin_Name' ) ) {

	class Plugin_Name {

		use Plugin_Name\Includes\Dependency_Loader;

		/**
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      Plugin_Name    $instance    Instance of this class.
		 */
		private static $instance;

		/**
		 * The modules variable holds all modules of the plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      object    $modules    Maintains all modules of the plugin.
		 */
		private static $modules = array();

		/**
		 * Main plugin path /wp-content/plugins/<plugin-folder>/.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $plugin_path    Main path.
		 */
		private static $plugin_path;

		/**
		 * Absolute plugin url <wordpress-root-folder>/wp-content/plugins/<plugin-folder>/.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $plugin_url    Main path.
		 */
		private static $plugin_url;


		/**
		 * The unique identifier of this plugin.
		 *
		 * @since    1.0.0
		 */
		const PLUGIN_ID         = 'plugin-name';

		/**
		 * The name identifier of this plugin.
		 *
		 * @since    1.0.0
		 */
		const PLUGIN_NAME       = 'Plugin Name';


		/**
		 * The current version of the plugin.
		 *
		 * @since    1.0.0
		 */
		const PLUGIN_VERSION    = '1.0.0';

		/**
		 * Define the core functionality of the plugin.
		 *
		 * Load the dependencies, define the locale, and set the hooks for the admin area and
		 * the frontend-facing side of the site.
		 *
		 * @since    1.0.0
		 */
		public function __construct( $router_class_name, $routes ) {
			self::$plugin_path = plugin_dir_path( dirname( __FILE__ ) );
			self::$plugin_url  = plugin_dir_url( dirname( __FILE__ ) );

			$this->autoload_dependencies();
			$this->set_locale();
			$this->init_router( $router_class_name, $routes );

			$this->controllers = $this->get_all_controllers();
			$this->models = $this->get_all_models();
		}

		/**
		 * Get plugin's absolute path.
		 *
		 * @since    1.0.0
		 */
		public static function get_plugin_path() {
			return isset( self::$plugin_path ) ? self::$plugin_path : plugin_dir_path( dirname( __FILE__ ) );
		}

		/**
		 * Get plugin's absolute url.
		 *
		 * @since    1.0.0
		 */
		public static function get_plugin_url() {
			return isset( self::$plugin_url ) ? self::$plugin_url : plugin_dir_url( dirname( __FILE__ ) );
		}

		/**
		 * Define the locale for this plugin for internationalization.
		 *
		 * Uses the i18n class in order to set the domain and to register the hook
		 * with WordPress.
		 *
		 * @since    1.0.0.0
		 */
		private function set_locale() {
			$plugin_i18n = new i18n();
			$plugin_i18n->set_domain( Plugin_Name::PLUGIN_ID );

			add_action( 'plugins_loaded', array( $plugin_i18n, 'load_plugin_textdomain' ) );
		}

		/**
		 * Init Router
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		private function init_router( $router_class_name, $routes ) {
			if ( ! class_exists( $router_class_name ) ) {
				throw new \InvalidArgumentException( "Could not load {$router_class_name} class!" );
			}

			if ( ! file_exists( $routes ) ) {
				throw new \InvalidArgumentException( "Routes file {$routes} not found! Please pass a valid file." );
			}

			$this->router = $router = new $router_class_name();
			add_action(
				'plugins_loaded', function() use ( $router, $routes ) {
					include_once( $routes );
				}
			);
		}

		private function get_all_controllers() {
			return (object) Controller_Registry::get_all_objects();
		}

		private function get_all_models() {
			return (object) Model_Registry::get_all_objects();
		}
	}

}
