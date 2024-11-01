<?php
	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Support' ) ) {

		class TTLC_Support {
				
			private $product_list;

			public function get_list( $type = false ) {
				
				
				if ( $this->product_list === null ) {
					
					$this->product_list = array();
					
					$default_headers = array(
						'description' => 'Description',
						'alt_description' => 'TTL Description',
						'author' => 'Author',
						'author_uri' => 'Author URI',
						'server' => 'TTL Server',
						'_slug' => 'TTL Slug',
					);
					
					foreach ( array_keys( get_plugins() ) as $path ) {
						$data = get_file_data( trailingslashit( WP_PLUGIN_DIR ) . $path, array_merge( $default_headers, array('title' => 'Plugin Name') ) );
						$data['type'] = 'plugin';
						$this->add_product( $data );
					}
					
					$themes = wp_get_themes( array(
						'errors' => null,
						'allowed' => null,
						'blog_id' => 0,
					) );

					foreach ( $themes as $theme ) {
						$data = get_file_data( trailingslashit( $theme->get_template_directory() ) . 'style.css', array_merge( $default_headers, array('title' => 'Theme Name') ) );
						
						$data['type'] = 'theme';
						$this->add_product( $data );
					}
				}

				$list = $this->product_list;
				
				if ( $type ) {
					$filtered_list = array();
					foreach ( $list  as $product ) {
						if ( $product['type'] === $type ) {
							$filtered_list[] = $product;
						}
					}
					return $filtered_list;
				}
				
				return $list;
			}
			
			private function add_product( $data ) {
				$data = $this->validatate( $data );
				if ( is_array( $data ) ) {
					$data['slug'] = self::format_slug( $data['server'], $data['_slug'] );
					$this->product_list[] = $data;
				}									
			}
			
			private function validatate( $data ) {
				foreach( array('title', 'author', '_slug', 'server' ) as $field ) {
					if ( empty( $data[$field] ) ) {
						return false;
					}
				}
				return $data;
			}
			
			public static function format_slug( $server_url, $raw_slug ) {
				return preg_replace( '/[\.\/]/', '-', preg_replace( '#^https?://#', '', $server_url ) ) . '-' . $raw_slug;
			}

		}
	}