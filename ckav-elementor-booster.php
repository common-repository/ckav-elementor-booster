<?php
/**
 * Plugin Name: C-Kav Elementor Booster
 * Description: Ckav Elementor Booster add unique features to enhance elementor builder experience.
 * Version: 1.0.1
 * Author: C-Kav
 * Author URI: https://c-kav.com
 * Text Domain: ckav-booster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main C-Kav Elementor Booster Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
final class CKav_Elementor_Booster {

	/**
	 * Plugin Version
	 *
	 * @since 1.0.0
	 *
	 * @var string The plugin version.
	 */
	const VERSION = '1.0.1';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.0.0
	 *
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 *
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '7.0';

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @static
	 *
	 * @var CKav_Elementor_Booster The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @static
	 *
	 * @return CKav_Elementor_Booster An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {

		add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );

    }
    
    /**
    * Define Plugin Constants
    * @since 1.0.0
    */
    public function define_constants() {
        define( 'CKAV_PLUGIN_URL', trailingslashit( plugins_url( '/', __FILE__ ) ) );
        define( 'CKAV_PLUGIN_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
    }

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 *
	 * Fired by `init` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function i18n() {

        load_plugin_textdomain( 'ckav-booster', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	}

	/**
	 * On Plugins Loaded
	 *
	 * Checks if Elementor has loaded, and performs some compatibility checks.
	 * If All checks pass, inits the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function on_plugins_loaded() {

		if ( $this->is_compatible() ) {
			add_action( 'elementor/init', [ $this, 'init' ] );
		}

	}

	/**
	 * Compatibility Checks
	 *
	 * Checks if the installed version of Elementor meets the plugin's minimum requirement.
	 * Checks if the installed PHP version meets the plugin's minimum requirement.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function is_compatible() {

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return false;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return false;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return false;
		}

		return true;

	}

	/**
	 * Initialize the plugin
	 *
	 * Load the plugin only after Elementor (and other plugins) are loaded.
	 * Load the files required to run the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function init() {
	
        $this->i18n();
        $this->define_constants();
        add_action( 'init', [ $this, 'ckav_init_boosters' ] );
        
        // Register/Enqueue Scripts
        add_action( 'elementor/frontend/after_register_scripts', [ $this, 'ckav_register_scripts_front' ] );
        add_action( 'elementor/frontend/after_register_styles', [ $this, 'ckav_register_styles' ] );

        add_action( 'elementor/frontend/after_enqueue_styles', function() { 
            wp_enqueue_style( 'ckav-booster-styles' ); 
            wp_enqueue_script( 'ckav-booster-script' ); 
		} );

    }

    /**
     * Register all front-end styles
     * @since 1.0.0
     */
	public function ckav_register_styles() {
		
        wp_register_style( 'ckav-booster-styles', CKAV_PLUGIN_URL . 'assets/dist/css/public.min.css', [], self::VERSION .'-'. rand(), 'all' );

	}
    
    /**
     * Register all front-end scripts
     * @since 1.0.0
     */
	public function ckav_register_scripts_front() {
		
        wp_register_script( 'ckav-booster-script', CKAV_PLUGIN_URL . 'assets/dist/js/public.min.js', [ 'jquery' ], self::VERSION .'-'. rand(), true );

    }
    
    /**
     * Init booster fuctions
     * @since 1.0.0
     */
    public function ckav_init_boosters() {
        
        // Include extension classes
        self::ckav_inc_boosters();
        
        Ckav_Imgmask::init();
		Ckav_Stickyheader::init();
    }

    /**
     * Include booster control files
     * @since 1.0.0
     */
    public static function ckav_inc_boosters() {
        include_once CKAV_PLUGIN_PATH . 'controls/ckav-imgmask.php'; // Image Mask
		include_once CKAV_PLUGIN_PATH . 'controls/ckav-stickyheader.php'; // Sticky section
    }

	
	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'ckav-booster' ),
			'<strong>' . esc_html__( 'C-Kav Elementor Booster', 'ckav-booster' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'ckav-booster' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'ckav-booster' ),
			'<strong>' . esc_html__( 'C-Kav Elementor Booster', 'ckav-booster' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'ckav-booster' ) . '</strong>',
			 self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'ckav-booster' ),
			'<strong>' . esc_html__( 'C-Kav Elementor Booster', 'ckav-booster' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'ckav-booster' ) . '</strong>',
			 self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

}

CKav_Elementor_Booster::instance();