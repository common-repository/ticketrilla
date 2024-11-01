<?php
	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Functions' ) ) {

		class TTLC_Functions {

			/**
			 * What type of request is this?
			 *
			 * @param string $type String containing name of request type (ajax, frontend, cron or admin)
			 *
			 * @return bool
			 */
			public function is_request( $type ) {
				switch ( $type ) {
					case 'admin' :
						return is_admin();
					case 'ajax' :
						return defined( 'DOING_AJAX' );
					case 'cron' :
						return defined( 'DOING_CRON' );
				}

				return false;
			}

			/**
			 * Locate a template and return the path for inclusion.
			 *
			 * @access public
			 *
			 * @param string $template_name
			 * @param string $path (default: '')
			 *
			 * @return string
			 */
			public function locate_template( $template_name, $path = '' ) {
				// check if there is template at theme folder
				$template = locate_template( array(
					trailingslashit( 'ttlc' . DIRECTORY_SEPARATOR . $path ) . $template_name
				) );

				if ( ! $template ) {
					$template = TTLC_TEMPLATES;
					if ( $path ) {
						$template .= trailingslashit( $path );
					}
					$template .= $template_name;
				}

				// Return what we found.
				return apply_filters( 'ttlc_locate_template', $template, $template_name, $path );
			}
			
			public function sanitize_string_array( array $array ) {
				$sanitized_array = array();
				foreach ( $array as $key => $value ) {
					if ( is_array( $value ) || is_object( $value ) ) {
						$sanitized_array[$key] = $this->sanitize_string_array( (Array) $value );
					} elseif( is_string( $value ) || is_numeric( $value ) ) {
						$sanitized_array[$key] = sanitize_text_field( $value );
					}
				}
				return $sanitized_array;
			}
			
			public function get_bg_update_interval() {
				$interval = get_option( 'ttlc_updates_interval', TTLC_DBUI ); // Minutes
				return $interval ? $interval * 60 : false; // Seconds
			}

		}

	}