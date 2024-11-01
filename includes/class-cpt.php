<?php


	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_CPT' ) ) {

		class TTLC_CPT {

			function __construct() {
				add_action( 'init', array( &$this, 'register_post_types' ), 1 );
			}


			public function register_post_types() {

				register_post_type( 'ttlc_product', array(
					'description'  => '',
					'public'       => false,
					'hierarchical' => true,
					'supports'     => array( 'title', 'editor' ),
				) );

				register_post_type( 'ttlc_ticket', array(
					'description'  => '',
					'public'       => false,
					'hierarchical' => true,
					'supports'     => array( 'title', 'editor' ),
				) );

				register_post_type( 'ttlc_attachment', array(
					'description'  => '',
					'public'       => false,
					'hierarchical' => false,
					'supports'     => array( 'title' ),
				) );

				register_post_type( 'ttlc_newsletters', array(
					'description'  => '',
					'public'       => false,
					'hierarchical' => false,
					'supports'     => array( 'title', 'editor' ),
				) );
			}
		}
	}