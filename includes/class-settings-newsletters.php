<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Settings_Newsletters' ) ) {

		class TTLC_Settings_Newsletters {
			
			protected $_suffix = 'newsletters';
			protected $_state = false;
			public $products = array();
			
			public function __construct( $data = array() ) {
				if ( empty( $this->products ) ) {
					$this->load();
				}
				
				if ( ! empty( $data ) ) {
					foreach ( $this->products as $product ) {
						$id = $product->id;
						if ( array_key_exists( $id, $data ) ) {
							$value = $data[$id];
							if ( empty( $value ) ) {
								$value = '';
							}
							$product->newsletters = $value;
						}
					}
				}
			}
			
			public function validate() {
				return true;
			}
			
			public function load() {
				$products = TTLC_Product::find_all();
				$this->products = $products['items'];
			}
			
			public function has_errors() {
				return false;
			}

			public function attributes() {
				return array();
			}
			
			public function save() {
				$result = true;
				$not_saved = array();
				
				foreach ( $this->products as $product ) {
					$save = update_post_meta( $product->id, $product::PREFIX . 'newsletters', $product->newsletters );
				}
				
				if ( ! empty( $not_saved ) ) {
					$result = false;
				}
							
				$this->_state = array('status' => 'success', 'message' => __( 'Settings successfully saved', TTLC_TEXTDOMAIN ) );

				return $result ? true : new WP_Error( 'ttlc_' . $this->_suffix . '_settings', sprintf( __('%s: options values have not changed', TTLC_TEXTDOMAIN ), implode( ', ', $not_saved ) ) );
			}
						
			public function get_state() {
				return $this->_state;
			}
			
		}
	
	}