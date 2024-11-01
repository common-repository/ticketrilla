<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	TTLC_Breadcrumbs::add_link( esc_html__( 'Ticketrilla: Client', TTLC_TEXTDOMAIN ), TTLC_Page::get_url( 'main' ) );
	
	if ( isset( $_GET['product_id'] ) ) {
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'ticket' ) {
			if ( isset( $_GET['ticket_id'] ) ) {
				$this->render_template( 'ticket' );
			} else {
				$this->render_template( 'add-ticket' );
			}
		} else {
			$this->render_template( 'product' );
		}
	} else {
		$this->render_template( 'main' );
	}
