<?php
	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Newsletters' ) ) {

		class TTLC_Newsletter extends TTLC_Post {

			const PREFIX = 'ttlc_newsletters_';

			public $id;
			public $external_id;
			public $product_id;
			public $server;
			public $title;
			public $content;
			public $date;
			public $status = 'new';
			public $product;

			public function attributes() {
				return array(
					'id' => __( 'ID', TTLC_TEXTDOMAIN),
					'external_id' => __( 'External ID', TTLC_TEXTDOMAIN),
					'product_id' => __( 'Product ID', TTLC_TEXTDOMAIN),
					'server' => '',
					'title' => __( 'Title', TTLC_TEXTDOMAIN),
					'content' => __( 'Content', TTLC_TEXTDOMAIN),
					'date' => __( 'Date', TTLC_TEXTDOMAIN ),
					'status' => __( 'Status', TTLC_TEXTDOMAIN ),
					'product' => __( 'Product', TTLC_TEXTDOMAIN ),
				);
			}

			public function meta_attributes() {
				return array('external_id', 'product_id', 'server', 'status');
			}

			public function rules() {
				return array(
					array(
						array('title', 'content', 'external_id', 'product_id', 'server', 'date'),
						'required', 'on' => self::SCENARIO_DEFAULT
					),
				);
			}
			
			public static function statuses() {
				return array(
					'new' => __( 'New', TTLC_TEXTDOMAIN ),
					'read' => __( 'Read', TTLC_TEXTDOMAIN ),
				);
			}
			
			protected function load_product() {
				$product = get_post( $this->product_id );
				if ( $product ) {
					return $product->post_title;
				}
				return null;
			}

			protected function filter_meta( $meta_attribute, $value ) {
				if ( $meta_attribute == 'status' && empty( $value ) ) {
					$value = 'new';
				}
				return $value;
			}

		}
	}
