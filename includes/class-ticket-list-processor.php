<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Ticket_List_Processor' ) ) {

		class TTLC_Ticket_List_Processor {
			
			public $result;
		
			function __construct( TTLC_Product $product ) {
				$paged = isset( $_GET['page_num'] ) && is_numeric( $_GET['page_num'] ) ? (int) sanitize_key( $_GET['page_num'] ) : 1;
				$status = isset( $_GET['filter'] ) && array_key_exists( $_GET['filter'], TTLC_Ticket::statuses() ) ? sanitize_text_field( $_GET['filter'] ) : false;
				$rest_ticket = new TTLC_Rest_Ticket( array(
					'server' => $product->server,
					'login' => $product->login,
					'password' => $product->password,
					'data' => array_merge( array(
						'license_type' => $product->license,
						'paged' => $paged,
						'status' => $status,
					), $product->license_data ),
				) );
				$rest_ticket->get_list();

				$response = $rest_ticket->get_response_body();
				if ( is_object( $response ) && isset( $response->tickets ) && isset( $response->count_tickets ) ) {

					foreach ( $response->tickets as $_rest_ticket ) {
						$ticket = new TTLC_Ticket();
						$ticket->status = $_rest_ticket->status;
						$ticket->external_id = $_rest_ticket->id;
						$ticket->title = $_rest_ticket->title;
						$ticket->responses_count = $_rest_ticket->response_count;
						$ticket->last_response = $_rest_ticket->response_last_date;
						$ticket->attachments_count = $_rest_ticket->attachment_count;
						$this->result['items'][] = $ticket;
					}
					$this->result['total'] = $response->count_tickets;

				} else {
					
					$meta_query = array(
						'relation' => 'AND',
						array(
							'key' => TTLC_Ticket::PREFIX . 'product_id',
							'value' => $product->id,
						),
					);
					
					if ( $status ) {
						$meta_query[] = array(
							'key' => TTLC_Ticket::PREFIX . 'status',
							'value' => $status,
						);
					}

					$this->result = TTLC_Ticket::find( array(
						'paged' => $paged,
						'meta_query' => $meta_query,
					) );
				}
			}
			
			
		}
	}
?>