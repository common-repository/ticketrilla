<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Settings' ) ) {

		class TTLC_Settings extends TTLC_Model {

			protected $_suffix = '';
			protected $_state = false;
			
			public function save() {
				$result = true;
				$not_saved = array();
				foreach( $this->attributes() as $attribute => $label ) {
					$update = update_option( 'ttlc_' . $this->_suffix . '_' . $attribute, $this->$attribute );
					if ( ! $update ) {
						$not_saved[] = $label;
						$result = false;
					}
					$method_on_save = 'save_' . $attribute;
					if ( is_callable( array($this, $method_on_save ) ) ) {
						call_user_func_array( array($this, $method_on_save), array($this->$attribute) );
					}
				}
				
				 
				$this->_state = array('status' => 'success', 'message' => __( 'Settings successfully saved', TTLC_TEXTDOMAIN ) );

				return $result ? true : new WP_Error( 'ttlc_' . $this->_suffix . '_settings', sprintf( __('%s: options values have not changed', TTLC_TEXTDOMAIN ), implode( ', ', $not_saved ) ) );
			}
			
			public function load() {
				foreach( $this->attributes() as $attribute => $label ) {
					$this->$attribute = get_option( 'ttlc_' . $this->_suffix . '_' . $attribute, isset( $this->$attribute ) ? $this->$attribute : ( is_callable( array($this, 'default_' . $attribute) ) ? call_user_func( array($this, 'default_' . $attribute) ) : false ) );
					if ( is_callable( array($this, 'load_' . $attribute) ) ) {
						$this->$attribute = call_user_func( array($this, 'load_' . $attribute) );
					}
				}
			}
			
			public function get_state() {
				return $this->_state;
			}
		}
	
	}