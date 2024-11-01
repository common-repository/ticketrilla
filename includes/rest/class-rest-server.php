<?php
	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Rest_Server' ) ) {

		class TTLC_Rest_Server extends TTLC_Rest {

			public function check() {
				$this->set_endpoint( 'server' );
				$this->send_request();
			}
		}
	}
