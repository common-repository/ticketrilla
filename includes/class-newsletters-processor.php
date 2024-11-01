<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Newsletters_Processor' ) ) {

		class TTLC_Newsletters_Processor {
		
			function __construct( TTLC_Product $product ) {
				$rest_newsletters = new TTLC_Rest_Newsletters( array(
					'server' => $product->server,
					'login' => $product->login,
					'password' => $product->password,
					'data' => array_merge( array(
						'license_type' => $product->license,
					), $product->license_data ),
				) );
				$rest_newsletters->get_list();

				$response = $rest_newsletters->get_response_body();
				if ( is_object( $response ) && ! empty( $response->newsletters ) ) {
					foreach( $response->newsletters as $_newsletter ) {
						$newsletter = new TTLC_Newsletter();
						$newsletter->title = $_newsletter->title;
						$newsletter->content = $_newsletter->content;
						$newsletter->date = $_newsletter->date;
						$newsletter->external_id = $_newsletter->id;
						$newsletter->product_id = $product->id;
						$newsletter->server = $product->server;
						if ( ! empty( $_newsletter->status ) ) {
							$newsletter->status = $_newsletter->status;
						}

						$local_newsletter = TTLC_Newsletter::find_one( array(
							'meta_query' => array(
								'relation' => 'AND',
								array(
									'key' => TTLC_Newsletter::PREFIX . 'external_id',
									'value' => $newsletter->external_id,
								),
								array(
									'key' => TTLC_Newsletter::PREFIX . 'server',
									'value' => $product->server
								),
							),
							'post_status' => array('publish', 'trash'),
						) );
						
						if ( empty( $local_newsletter['items'] ) ) {
							$newsletter->save();
						}
					}
				}
			}
		}
	}