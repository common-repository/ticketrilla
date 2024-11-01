<?php
	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Rest_Newsletters' ) ) {

		class TTLC_Rest_Newsletters extends TTLC_Rest {
			
			/**
			 * The Endpoint name.
			 * Used to form request URL
			 *
			 * @var string
			 */
			
			protected $endpoint = 'newsletters';

			public function get_list() {
				$this->set_mode( 'list' );
				$this->send_request();
			}

		}
	}
