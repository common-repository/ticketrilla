<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Settings_Attachments' ) ) {

		class TTLC_Settings_Attachments extends TTLC_Settings {
			
			protected $_suffix = 'attachments';

			public $size = 5;
			public $time = 30;
			public $autoload = false;
			
			public function attributes() {
				return array(
					'size' => __( 'Max attachment size', TTLC_TEXTDOMAIN),
					'time' => __( 'Max load time', TTLC_TEXTDOMAIN),
					'autoload' => __( 'Autoload attachments', TTLC_TEXTDOMAIN),
				);
			}
			
			public function rules() {
				return array(
					array(
						array('size', 'time'),
						'number',
						'natural',
					),
				);
			}
			
		}
	
	}