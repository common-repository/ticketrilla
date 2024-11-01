<?php
	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Product_Available' ) ) {

		class TTLC_Product_Available extends TTLC_Model {
			
			const PREFIX = '';
			
			public $title;
			public $slug;
			public $description;
			public $author;
			public $author_uri;
			public $thumbnail;
			public $type;
			public $server;
			public $license;
			public $service_terms;
			public $privacy_statement;
			public $manual;
			public $registration = 'y';
			
			public function attributes() {
				return array(
					'title' => 'Title',
					'slug' => 'Slug',
					'description' => 'Description',
					'author' => 'Author',
					'author_uri' => 'Author URI',
					'thumbnail' => 'Thumbnail',
					'type' => 'Type',
					'server' => 'Server',
					'license' => 'License',
					'service_terms' => 'Service Terms',
					'privacy_statement' => 'Privacy Statement',
					'manual' => 'Manual',
					'registration' => 'Registration Opened',
				);
			}

			public function rules() {
				return array(
				);
			}
		
			private static $list = array();
			
			public static function get_list( $type = false ) {
				if ( empty( self::$list ) ) {
					foreach( TTLC()->support()->get_list( $type ) as $product ) {
						if ( isset( $product['slug'] ) ) {
							$connected = TTLC_Product::find_one( array('post_name__in' => array($product['slug'], $product['slug'] . '__trashed'), 'post_status' => array( 'publish', 'trash' ) ) );
							if( empty( $connected['items'] ) ) {
								if ( $product['alt_description'] ) {
									$product['description'] = $product['alt_description'];
								}
								self::$list[] = new self( $product );
							}
						}
					}
				}
				return self::$list;
			}

		}
	}