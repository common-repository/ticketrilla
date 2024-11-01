<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Settings_Updates' ) ) {

		class TTLC_Settings_Updates extends TTLC_Settings {
			
			protected $_suffix = 'updates';

			public $on = true;
			public $interval = TTLC_DBUI;
			
			public function attributes() {
				return array(
					'on' => __( 'Allow Updates', TTLC_TEXTDOMAIN),
					'interval' => __( 'Minimum Updates Interval', TTLC_TEXTDOMAIN),
				);
			}
			
			public function rules() {
				return array(
					array(
						array('interval'),
						'number',
						'natural',
					),
				);
			}
			
			protected function save_on( $value ) {
				if ( $value ) {
					if ( ! wp_next_scheduled( 'ttlc_background_update' ) ) {
						wp_schedule_event( time() + TTLC()->get_bg_update_interval(), 'ttlc_interval', 'ttlc_background_update' );
					}
				} else {
					if ( wp_next_scheduled( 'ttlc_background_update' ) ) {
						wp_unschedule_event( wp_next_scheduled( 'ttlc_background_update' ), 'ttlc_background_update' );
					}
				}
			}
			
		}
	
	}