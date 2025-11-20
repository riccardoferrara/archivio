<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! class_exists( 'Valeska_Welcome_Page' ) ) {
	/**
	 * Welcome page theme class
	 */
	class Valeska_Welcome_Page {
		private static $instance;

		/**
		 * Instance of module class
		 *
		 * @return Valeska_Welcome_Page
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		private function __construct() {
			// theme activation hook.
			add_action( 'after_switch_theme', array( $this, 'init_activation_hook' ) );

			// welcome page redirect on theme activation.
			add_action( 'admin_init', array( $this, 'welcome_page_redirect' ) );

			// add welcome page into theme options.
			add_action( 'admin_menu', array( $this, 'create_welcome_page' ), 12 );

			// enqueue theme welcome page scripts.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		}

		/**
		 * Init hooks on theme activation
		 */
		public function init_activation_hook() {
			if ( ! is_network_admin() ) {
				set_transient( '_valeska_welcome_page_redirect', 1, 30 );
			}
		}

		/**
		 * Redirect to welcome page on theme activation
		 */
		public function welcome_page_redirect() {
			// fix deprecated notice caused by null $title.
			global $title;
			if ( empty( $title ) ) {
				// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$title = '';
			}
			// if no activation redirect, bail.
			if ( ! get_transient( '_valeska_welcome_page_redirect' ) ) {
				return;
			}

			// delete the redirect transient.
			delete_transient( '_valeska_welcome_page_redirect' );

			// if activating from network, or bulk, bail.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
				return;
			}

			// redirect to welcome page.
			wp_safe_redirect( add_query_arg( array( 'page' => 'valeska_welcome_page' ), esc_url( admin_url( 'themes.php' ) ) ) );
			exit;
		}

		/**
		 * Add welcome page
		 */
		public function create_welcome_page() {
			add_theme_page(
				esc_html__( 'About', 'valeska' ),
				esc_html__( 'About', 'valeska' ),
				'edit_theme_options',
				'valeska_welcome_page',
				array( $this, 'welcome_page_content' )
			);

			remove_submenu_page( 'themes.php', 'valeska_welcome_page' );
		}

		/**
		 * Render welcome page content
		 */
		public function welcome_page_content() {
			$params = array();

			$theme                       = wp_get_theme();
			$params['theme']             = $theme;
			$params['theme_name']        = esc_html( $theme->get( 'Name' ) );
			$params['theme_description'] = esc_html( $theme->get( 'Description' ) );
			$params['theme_version']     = $theme->get( 'Version' );
			$params['theme_screenshot']  = file_exists( VALESKA_ROOT_DIR . '/screenshot.png' ) ? VALESKA_ROOT . '/screenshot.png' : VALESKA_ROOT . '/screenshot.jpg';

			valeska_template_part( 'welcome', 'templates/welcome', '', $params );
		}

		/**
		 * Enqueue welcome page scripts
		 *
		 * @param string $hook
		 */
		public function enqueue_styles( $hook ) {

			if ( 'appearance_page_valeska_welcome_page' === $hook ) {
				// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
				wp_enqueue_style( 'valeska-welcome-page-style', VALESKA_INC_ROOT . '/welcome/assets/admin/css/welcome.min.css' );
			}
		}
	}

	Valeska_Welcome_Page::get_instance();
}
