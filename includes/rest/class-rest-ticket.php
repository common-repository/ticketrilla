<?php
	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Rest_Ticket' ) ) {

		class TTLC_Rest_Ticket extends TTLC_Rest {
			
			/**
			 * The Endpoint name.
			 * Used to form request URL
			 *
			 * @var string
			 */
			
			protected $endpoint = 'ticket';

			public function get_list() {
				$this->set_mode( 'list' );
				$this->send_request();
			}

			public function get() {
				$this->set_mode( 'get' );
				$this->send_request();
			}

			public function add() {
				$this->set_mode( 'add' );
				$this->send_request();
			}
			
			public function edit() {
				$this->set_mode( 'edit' );
				$this->send_request();
			}

		}
	}
