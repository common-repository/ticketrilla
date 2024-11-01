<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Ticket_Processor' ) ) {

		class TTLC_Ticket_Processor {
			
			public $result;
			public $fallback = false;
		
			function __construct( TTLC_Product $product, $params ) {
				$ticket_external_id = $params['ticket_external_id'];
				$paged = $params['page_num'];
				$order = $params['order'];
				
				$rest_ticket = new TTLC_Rest_Ticket( array(
					'server' => $product->server,
					'login' => $product->login,
					'password' => $product->password,
					'data' => array_merge( array(
						'license_type' => $product->license,
						'ticket_id' => $ticket_external_id,
						'paged' => $paged,
						'order' => $order,
					), $product->license_data ),
				) );
				$rest_ticket->get();
				$response = $rest_ticket->get_response();
				$response_body = $rest_ticket->get_response_body();
				$local_fallback = TTLC_Ticket::find_one( array(
					'meta_key' => TTLC_Ticket::PREFIX . 'external_id',
					'meta_value' => $ticket_external_id,
				) );
				if ( $response['response']['code'] === 200 && is_object( $response_body ) ) {
					
					$_ticket = $response_body;
					
					$ticket = new TTLC_Ticket();
					$ticket->external_id = $_ticket->id;
					$ticket->title = $_ticket->title;
					$ticket->content = $_ticket->content;
					$ticket->developer = $_ticket->developer;
					$ticket->status = $_ticket->status;
					$ticket->client_id = $_ticket->client_id;
					$ticket->client_name = $_ticket->client;
					$ticket->support_until = $_ticket->client_license_have_support && isset( $_ticket->client_license_have_support_until ) ? $_ticket->client_license_have_support_until : null;
					$ticket->extend_support_url = $_ticket->client_license_support_link;
					$ticket->product_id = $product->id;
					
					// If ticket doesn't exist — save it in client DB
					
					if ( ! empty( $local_fallback['items'] ) ) {
						$ticket->id = $local_fallback['items'][0]->id;
						
						// If ticket exists — update dynamic data in client DB
						
					}
					
					$ticket->save();

					// Process attachments after ticket id and responses (responses attachments) set
					
					$ticket->responses_count = $_ticket->response_count;

					if ( $ticket->responses_count ) {
						$ticket_responses = $this->process_responses( $_ticket->response_list, $ticket );
						$ticket->responses = $ticket_responses;						
					}

					if ( isset( $_ticket->ticket_attachments ) ) {

						// Ticket Attachments Only
	 					$ticket->ticket_attachments = $this->process_attachments( $_ticket->ticket_attachments, $ticket );
					}

					$this->result = $ticket;

				} elseif ( ! empty( $local_fallback['items'] ) ) {

					$this->result = $local_fallback['items'][0];
					$this->fallback = true;
				}
			}
			
			private function process_responses( $_responses, $ticket ) {
				$responses = array();
				foreach ( $_responses as $_response ) {	

					$response = new TTLC_Ticket_Response();
					$response->parent_id = $ticket->id;
					$response->external_id = $_response->id;				
					$response->title = $_response->title;
					$response->content = $_response->content;
					$response->date = $_response->time;
					$response->type = $_response->type;
					if ( isset( $_response->reason ) ) {
						$response->reason = $_response->reason;
					}
					$response->author_id = $_response->author_id;
					$response->author = $_response->author;
					$response->author_pos = $_response->author_pos;
					
					if ( $response->type === 'redirect' ) {
						$response->person = $_response->person;
						$response->person_pos = $_response->person_pos;
					}

					$local_response = TTLC_Ticket_Response::find_one( array(
						'meta_key' => TTLC_Ticket::PREFIX . 'external_id',
						'meta_value' => $_response->id,
					) );
					
					if ( empty( $local_response['items'] ) ) {
	
						$response->save();
							
					} else {
						$response->id = $local_response['items'][0]->id;
					}

					// Process attachments after response id set
					
					if ( isset( $_response->attachment_list ) ) {
						$response->attachments = $this->process_attachments( $_response->attachment_list, $response );
					}
					
					$responses[] = $response;					
					
				}
				return $responses;
			}

			private function process_attachments( $_attachments, $parent ) {
				$attachments = array();
				foreach ( $_attachments as $_attachment ) {
					$attachment = new TTLC_Attachment();
					$attachment->ticket_id = $parent->id;
					$attachment->title = $_attachment->name;
					$attachment->size = $_attachment->size;
					$attachment->type = $_attachment->type;
					$attachment->url = $_attachment->link;
					$attachment->md5 = $_attachment->md5;
					
					if ( $_attachment->external_id ) {
						
						// Attachment is from ticket/client response and exists on client server
						
						$attachment->id = $_attachment->external_id;
						
						// Assign ticket id to attachment only once
						
						$attachment->assign_ticket();
											
					} else {
						
						// Attachment is external
						
						$attachment->external_id = $_attachment->id;
						
						
						// Check if attachment not yet downloaded to local server
	
						$downloaded = TTLC_Attachment::find_one( array(
							'meta_key' => TTLC_Attachment::PREFIX . 'external_id',
							'meta_value' => $attachment->external_id,
						) );
						
						if ( empty( $downloaded['items'] ) || ! $downloaded['items'][0]->local_file_exists() ) {
	
							// If autoload option is on — download external attachment
		
							if ( get_option('ttlc_attachments_autoload', false) ) {
								$attachment->download();
							
							// Otherwise mark attachment as available for manual download
							
							} else {
								$attachment->external_url = $attachment->url;
							}
							
						} else {
							$attachment->id = $downloaded['items'][0]->id;
							$attachment->url = $downloaded['items'][0]->url;
						}
						
					}
								
					$attachments[] = $attachment;

				}
				return $attachments;
			}
			
		}
	}
?>