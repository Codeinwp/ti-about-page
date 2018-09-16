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

	/**
	 * The main render function
	 */
	private function render() {

	    if ( empty( $this->tabs ) ) {
	        return;
        }

		echo '<div class="wrap about-wrap">';
		$this->render_header();

		$this->render_tabs_list();
		$this->render_tabs_content();
		echo '</div>';
	}

	/**
	 * Render tabs list
	 */
	private function render_tabs_list() {

	    echo '<ul class="nav-tab-wrapper wp-clearfix">';
		foreach( $this->tabs as $slug => $tab_data ) {
            echo '<li data-tab-id="' . esc_attr( $slug ) . '">';
            echo esc_html( $tab_data['title'] );
            echo '</li>';
		}
		echo '</ul>';
    }

	/**
     * Render tab content
	 */
	public function render_tabs_content() {
		foreach( $this->tabs as $slug => $tab_data ) {
		    echo '<div id="' . esc_attr( $slug ) . '" class="' . esc_attr( $tab_data['type'] ) . '">';

		        switch( $tab_data['type'] ) {

                    case 'plugins' :
                        $this->render_plugins_tab();
                        break;
                    default :
                         $this->render_default_tab( $tab_data['content'] );
                         break;
                }

			echo '</div>';
		}
	}


	/**
	 * Render button
	 */
	private function render_button( $button ) {
        if ( empty( $button ) ) {
            return;
        }

        echo '<a href="' . esc_url( $button['link'] ) . '"';
        echo $button['is_button'] ? 'class="button button-primary"' : '';
        echo $button['blank'] ? 'target="_blank"' : '';
        echo '>';
        echo $button['label'];
        echo '</a>';
    }

	/**
     * Render the header
	 */
	public function render_header() {

		?>
        <div class="header">
            <div class="info"><h1>Welcome to <?php echo esc_html( $this->theme['name'] ); ?>! - Version <span
                            class="version-container"><?php echo esc_html( $this->theme['version'] ); ?></span></h1>
                <div class="hestia-about-text about-text"><?php echo esc_html( $this->theme['description'] ); ?></div>
            </div>
            <a href="https://themeisle.com/" target="_blank" class="wp-badge epsilon-welcome-logo"></a></div>
		<?php
	}

	/**
     * Render plugins tab content
	 * TODO
	 */
	private function render_plugins_tab() {

    }

	/**
     * Render default tab content
	 */
	private function render_default_tab( $tab_content ) {
		foreach ( $tab_content as $content ) {
			echo '<div class="about-col">';
			echo '<h3>';
			if ( ! empty( $content['icon'] ) ) {
				echo '<i class="dashicons dashicons-' . esc_attr( $content['icon'] ) . '"></i>';
			}
			echo esc_html( $content['title'] ) . '</h3>';
			echo '<p>' . esc_html( $content['text'] ) . '</p>';
			$this->render_button( $content['button'] );
			echo '</div>';
		}
	}


}