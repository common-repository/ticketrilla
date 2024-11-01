<?php
	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Enqueue' ) ) {

		class TTLC_Enqueue {

			function __construct() {

				add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );

			}

			/**
			 * @register scripts and styles
			 */
			public function admin_enqueue_scripts( $hook ) {
				$ttl_hooks = array(
					'toplevel_page_' . TTLC_Page::SLUG,
					'ticketrilla_page_' . TTLC_Page::SETTINGS_SLUG,
					'ticketrilla_page_' . TTLC_Page::NEWSLETTERS_SLUG,
					
				);
				if ( in_array( $hook, $ttl_hooks ) ) {
					$this->register_css();
					$this->register_js();
				} else {
					$this->register_ttlc_js( array('jquery', 'heartbeat') );
				}
			}

			/**
			 * @register Admin Page Scripts
			 */
			private function register_js() {
				
				// Enqueue TTLC Scripts
				
				$this->register_ttlc_js();

				wp_enqueue_script( 'bootstrap', TTLC_URL . 'assets/js/bootstrap.min.js', array(
					'jquery',
				), filemtime(TTLC_PATH . '/assets/js/bootstrap.min.js'), true );

				wp_enqueue_script( 'ckeditor', TTLC_URL . 'assets/js/ckeditor/ckeditor.js', array(
					'jquery',
				), filemtime(TTLC_PATH . '/assets/js/ckeditor/ckeditor.js'), true );
				
				wp_localize_script( 'ttlc', 'ttlcText', array(
					'waiting_save' => esc_html__( 'Waiting for save', TTLC_TEXTDOMAIN),
				) );
				
			}
			
			private function register_ttlc_js( $spec_deps = false ) {
				$deps_array = array(
					'jquery',
					'bootstrap',
					'ckeditor'
				);

				if ( $spec_deps && is_array( $spec_deps ) ) {
					$deps_array = $spec_deps;
				}
				
				wp_enqueue_script( 'ttlc', TTLC_URL . 'assets/js/ttlc-script.js', $deps_array, filemtime(TTLC_PATH . '/assets/js/ttlc-script.js'), true );
			}

			/**
			 * @register Admin Page Styles
			 */
			private function register_css() {

				wp_enqueue_style( 'ttlc_main', TTLC_URL . 'assets/css/main.css', array(), filemtime(TTLC_PATH . '/assets/css/main.css') );

			}
		}
	}