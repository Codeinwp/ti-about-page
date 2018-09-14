<?php
/**
 * Render class fot the about page
 */

class TI_About_Render {

	private $theme = array();

	private $tabs = array();

	public function __construct( $theme_args, $data ) {
		$this->tabs  = $data;
		$this->theme = $theme_args;

		$this->render();
	}

	private function render() {
		echo '<div class="wrap about-wrap">';
		$this->render_header( $this->theme );


		echo '</div>';
	}

	public function add_tab( $slug, $type, $content ) {
		array_push( $this->tabs, array( $slug, $type, $content ) );
	}

	public function render_header( $args ) {

		?>
        <div class="header">
            <div class="info"><h1>Welcome to <?php echo esc_html( $args['name'] ); ?>! - Version <span
                            class="version-container"><?php echo esc_html( $args['version'] ); ?></span></h1>
                <div class="hestia-about-text about-text"><?php echo esc_html( $args['description'] ); ?></div>
            </div>
            <a href="https://themeisle.com/" target="_blank" class="wp-badge epsilon-welcome-logo"></a></div>
		<?php
	}


}