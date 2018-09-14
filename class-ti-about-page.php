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

	private $theme_args = array();

    private $config = array();

    private static $instance;

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

	private function setup_config() {

		$theme = wp_get_theme();

		$this->theme_args['name']        = $theme->__get( 'Name' );
		$this->theme_args['version']     = $theme->__get( 'Version' );
		$this->theme_args['description'] = $theme->__get( 'Description' );
		$this->theme_args['slug']        = $theme->__get( 'stylesheet' );

	}

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

	public function render() {
		require_once 'class-ti-about-render.php';
		new TI_About_Render( $this->theme_args, $this->config );
	}

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

		wp_enqueue_style( $handle, $src, array(), $version );
	}

}