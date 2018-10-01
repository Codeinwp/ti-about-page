<?php
/**
 * ThemeIsle - About page class
 *
 * @package ti-about-page
 */

/**
 * Class Ti_About_Page_Main
 *
 * @package Themeisle
 */
class Ti_About_Page {

	/**
	 * Current theme args
	 */
	private $theme_args = array();

	/**
	 * About page content that shuold be rendered
	 */
    private $config = array();

	/**
	 * About Page instance
	 */
    private static $instance;

	/**
	 * The Main Themeisle_About_Page instance.
	 *
	 * We make sure that only one instance of Themeisle_About_Page exists in the memory at one time.
	 * @param array $config The configuration array.
	 */
	public static function init( $config ) {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Ti_About_Page ) ) {
			self::$instance = new Ti_About_Page();
			if ( ! empty( $config ) && is_array( $config ) ) {
				self::$instance->config = $config;
				self::$instance->setup_config();
				self::$instance->setup_actions();
			}
		}
	}

	/**
	 * Setup the class props based on current theme
	 */
	private function setup_config() {

		$theme = wp_get_theme();

		$this->theme_args['name']        = $theme->__get( 'Name' );
		$this->theme_args['version']     = $theme->__get( 'Version' );
		$this->theme_args['description'] = $theme->__get( 'Description' );
		$this->theme_args['slug']        = $theme->__get( 'stylesheet' );
	}

	/**
	 * Setup the actions used for this page.
	 */
	public function setup_actions() {

		add_action( 'admin_menu', array( $this, 'register' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Register the menu page under Appearance menu.
	 */
	public function register() {
		$theme = $this->theme_args;

		if ( empty( $theme['name'] ) || empty( $theme['slug'] ) ) {
			return;
		}

		$menu_name = __( 'About', 'text-domain') . ' ' . $theme['name'];

			add_theme_page(
				$menu_name,
				$menu_name,
				'activate_plugins',
				$theme['slug'] . '-welcome',
				array(
					$this,
					'render',
				)
			);
	}

	/**
	 * Instantiate the render class which will render all the tabs based on config
	 */
	public function render() {
		require_once 'class-ti-about-render.php';
		new TI_About_Render( $this->theme_args, $this->config );
	}

	/**
	 * Load css and scripts for the about page
	 */
	public function enqueue() {
		$screen = get_current_screen();
		
		if ( ! isset( $screen->id ) ) {
			return;
		}

		if ( $screen->id !== 'appearance_page_' . $this->theme_args['slug'] . '-welcome' ) {
			return;
		}

		$handle = $this->theme_args['slug'] . '-about-style';
		$src = get_stylesheet_directory_uri() . '/vendor/codeinwp/ti-about-page/css/style.css';
		$version = $this->theme_args['version'];

		$scripts_handle = $this->theme_args['slug'] . '-about-scripts';
		$scripts_src = get_stylesheet_directory_uri() . '/vendor/codeinwp/ti-about-page/js/ti_about_page_scripts.js';

		wp_enqueue_style( $handle, $src, array(), $version );
		wp_enqueue_script( $scripts_handle, $scripts_src, array( 'jquery', 'jquery-ui-tabs' ), $version, true );
	}
}