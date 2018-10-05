<?php
/**
 * Render class fot the about page
 */

class TI_About_Render {

	/**
	 * @var array - theme args
	 */
	private $theme = array();

	/**
	 * Regular tabs, any theme should have this information in the About Page.
	 */
	private $tabs = array();

	/**
	 * @var Ti_About_Page
	 */
	private $about_page = null;

	/**
	 * Custom tabs based on theme's particularities
	 */
	private $custom_tabs = array();

	/**
	 * TI_About_Render constructor.
	 *
	 * @param array         $theme_args - current theme args.
	 * @param array         $data       - about page content.
	 * @param Ti_About_Page $about_page - about page content.
	 */
	public function __construct( $theme_args, $data, $about_page ) {
		$this->theme      = $theme_args;
		$this->tabs       = $data;
		$this->about_page = $about_page;
		if ( isset( $this->tabs['custom_tabs'] ) ) {
			$this->custom_tabs = $data['custom_tabs'];
			unset( $this->tabs['custom_tabs'] );
		}

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

		echo '<div id="about-tabs">';

		$this->render_tabs_list();
		$this->render_tabs_content();
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Render the header
	 */
	private function render_header() {

		?>
		<div class="about-loading loading">
			<div class="about-loader">
				<div class="loader-content">
					<p><i class="dashicons dashicons-update"></i><span><?php echo __( 'Loading...', 'neve' ); ?></span>
					</p>
				</div>
			</div>
		</div>
		<div class="header">
			<div class="info"><h1>Welcome to <?php echo esc_html( $this->theme['name'] ); ?>! - Version <span
							class="version-container"><?php echo esc_html( $this->theme['version'] ); ?></span></h1>
				<div class="ti-about-header-text about-text"><?php echo esc_html( $this->theme['description'] ); ?></div>
			</div>
			<a href="https://themeisle.com/" target="_blank" class="wp-badge epsilon-welcome-logo"></a></div>
		<?php
	}

	/**
	 * Render tabs list
	 */
	private function render_tabs_list() {

		echo '<ul class="nav-tab-wrapper wp-clearfix">';
		foreach ( $this->tabs as $slug => $tab_data ) {
			if ( $tab_data['type'] === 'recommended_actions' && $this->about_page->get_recommended_actions_left() === 0 ) {
				continue;
			}
			echo '<li data-tab-id="' . esc_attr( $slug ) . '">';
			echo '<a class="nav-tab';
			if ( $tab_data['type'] === 'recommended_actions' ) {
				echo ' recommended_actions';
			}
			echo '" href="#' . esc_attr( $slug ) . '">' . esc_html( $tab_data['title'] ) . '</a>';
			echo '</li>';
		}

		foreach ( $this->custom_tabs as $slug => $tab_data ) {
			echo '<li data-tab-id="' . esc_attr( $slug ) . '">';
			echo '<a class="nav-tab" href="#' . esc_attr( $slug ) . '">' . esc_html( $tab_data['title'] ) . '</a>';
			echo '</li>';
		}
		echo '</ul>';
	}

	/**
	 * Render tab content
	 */
	private function render_tabs_content() {
		foreach ( $this->tabs as $slug => $tab_data ) {
		    if( $slug === 'recommended_actions' && $this->about_page->get_recommended_actions_left() === 0 ) {
		        continue;
            }
			echo '<div id="' . esc_attr( $slug ) . '" class="' . esc_attr( $tab_data['type'] ) . '">';

			switch ( $tab_data['type'] ) {

				case 'recommended_actions' :
					$this->render_recommended_actions( $tab_data['plugins'] );
					break;
				case 'plugins' :
					$this->render_plugins_tab( $tab_data['plugins'] );
					break;
				case 'changelog' :
					$this->render_changelog();
					break;
				default :
					$this->render_default_tab( $tab_data['content'] );
					break;
			}

			echo '</div>';
		}
		foreach ( $this->custom_tabs as $slug => $tab_data ) {

			echo '<div id="' . esc_attr( $slug ) . '" class="custom">';
			call_user_func( $tab_data['render_callback'] );
			echo '</div>';
		}
	}

	/**
	 * Render recommended actions
	 */
	private function render_recommended_actions( $plugins_list ) {
		if ( empty( $plugins_list ) || $this->about_page->get_recommended_actions_left() === 0 ) {
			return;
		}

		$recommended_plugins_visbility = get_option( 'recommended_plugins' );

		foreach ( $plugins_list as $slug => $plugin ) {
			if ( $recommended_plugins_visbility[ $slug ] === 'hidden' || Ti_About_Plugin_Helper::instance()->check_plugin_state( $slug ) === 'deactivate' ) {
				continue;
			}

			echo '<div class="ti-about-page-action-required-box ' . esc_attr( $slug ) . '">';
			echo '<span class="dashicons dashicons-visibility ti-about-page-required-action-button" data-slug="' . esc_attr( $slug ) . '"></span>';
			echo '<h3>' . $plugin['name'] . '</h3>';
			echo '<p>' . $plugin['description'] . '</p>';
			echo Ti_About_Plugin_Helper::instance()->get_button_html( $slug, array( 'redirect' => add_query_arg( 'page', $this->theme['slug'] . '-welcome', admin_url( 'themes.php#recommended_actions' ) ) ) );
			echo '</div>';
		}
	}

	/**
	 * Render plugins tab content
	 */
	private function render_plugins_tab( $plugins_list ) {

		if ( empty( $plugins_list ) ) {
			return;
		}

		echo '<div class="recommended-plugins">';

		foreach ( $plugins_list as $plugin ) {
			$current_plugin = $this->call_plugin_api( $plugin );

			echo '<div class="plugin_box">';
			echo '<img class="plugin-banner" src="' . esc_attr( $current_plugin->banners['low'] ) . '">';
			echo '<div class="title-action-wrapper">';
			echo '<span class="plugin-name">' . esc_html( $current_plugin->name ) . '</span>';
			echo '<span class="plugin-desc">' . esc_html( $current_plugin->short_description ) . '</span>';
			echo '</div>';
			echo '<div class="plugin-box-footer">';
			echo '<div class="button-wrap">';
			echo Ti_About_Plugin_Helper::instance()->get_button_html( $plugin );
			echo '</div>';
			echo '<div class="version-wrapper"><span class="version">' . esc_html( $current_plugin->version ) . '</span><span class="separator"> | </span>' . strtok( strip_tags( $current_plugin->author ), ',' ) . '</div>';
			echo '</div>';
			echo '</div>';
		}

		echo '</div>';
	}

	/**
	 * Call plugin api
	 *
	 * @param string $slug plugin slug.
	 *
	 * @return array|mixed|object
	 */
	private function call_plugin_api( $slug ) {
		include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

		$call_api = get_transient( 'ti_about_plugin_info_' . $slug );

		if ( false === $call_api ) {
			$call_api = plugins_api(
				'plugin_information',
				array(
					'slug'   => $slug,
					'fields' => array(
						'downloaded'        => false,
						'rating'            => false,
						'description'       => false,
						'short_description' => true,
						'donate_link'       => false,
						'tags'              => false,
						'sections'          => true,
						'homepage'          => true,
						'added'             => false,
						'last_updated'      => false,
						'compatibility'     => false,
						'tested'            => false,
						'requires'          => false,
						'downloadlink'      => false,
						'icons'             => true,
						'banners'           => true,
					),
				)
			);
			set_transient( 'ti_about_plugin_info_' . $slug, $call_api, 30 * MINUTE_IN_SECONDS );
		}

		return $call_api;
	}

	/**
	 * Render changelog
	 */
	private function render_changelog() {
		$changelog = $this->parse_changelog();
		if ( ! empty( $changelog ) ) {
			echo '<div class="featured-section changelog">';
			foreach ( $changelog as $release ) {
				if ( ! empty( $release['title'] ) ) {
					echo '<h2>' . str_replace( '#', '', $release['title'] ) . ' </h2 > ';
				}
				if ( ! empty( $release['changes'] ) ) {
					echo implode( '<br/>', $release['changes'] );
				}
			}
			echo '</div>';
		}
	}

	/**
	 * Return the releases changes array.
	 */
	private function parse_changelog() {
		WP_Filesystem();
		global $wp_filesystem;
		$changelog = $wp_filesystem->get_contents( get_template_directory() . '/CHANGELOG.md' );
		if ( is_wp_error( $changelog ) ) {
			$changelog = '';
		}
		$changelog = explode( PHP_EOL, $changelog );
		$releases  = array();
		foreach ( $changelog as $changelog_line ) {
			if ( strpos( $changelog_line, '**Changes:**' ) !== false || empty( $changelog_line ) ) {
				continue;
			}
			if ( substr( $changelog_line, 0, 3 ) === '###' || substr( $changelog_line, 1, 3 ) === '###' ) {
				if ( isset( $release ) ) {
					$releases[] = $release;
				}
				$release = array(
					'title'   => substr( $changelog_line, 3 ),
					'changes' => array(),
				);
			} else {
				$release['changes'][] = $changelog_line;
			}
		}

		return $releases;
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

	/**
	 * Render button
	 */
	private function render_button( $button ) {
		if ( empty( $button ) ) {
			return;
		}

		if ( $button['link'] === '#recommended_actions' && $this->about_page->get_recommended_actions_left() === 0 ) {
		    echo '<span>' . esc_html__( 'Recommended actions', 'textdomain' ) . '</span>';
		    return;
        }

		echo '<a href="' . esc_url( $button['link'] ) . '"';
		echo $button['is_button'] ? 'class="button button-primary"' : '';
		echo $button['blank'] ? 'target="_blank"' : '';
		echo '>';
		echo $button['label'];
		echo '</a>';
	}
}