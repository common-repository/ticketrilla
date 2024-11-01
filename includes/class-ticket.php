<?php
	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Ticket' ) ) {

		class TTLC_Ticket extends TTLC_Post {

			const PREFIX = 'ttlc_ticket_';
			const SCENARIO_ADD = 'add';

			public $id;
			public $external_id;
			public $client_id;
			public $title;
			public $content;
			public $date;
			public $responses = array();
			public $responses_count;
			public $last_response;
			public $ticket_attachments = array();
			public $all_attachments;
			public $developer;
			public $status;
			public $support_until;
			public $extend_support_url;
			
			public $server;
			public $login;
			public $password;
			public $license;
			public $license_data = array();

			public function attributes() {
				return array(
					'id' => __( 'ID', TTLC_TEXTDOMAIN),
					'product_id' => __( 'Product ID', TTLC_TEXTDOMAIN),
					'external_id' => __( 'External ID', TTLC_TEXTDOMAIN),
					'client_id' => __( 'Client ID', TTLC_TEXTDOMAIN),
					'title' => __( 'Title', TTLC_TEXTDOMAIN),
					'content' => __( 'Content', TTLC_TEXTDOMAIN),
					'date' => __( 'Date', TTLC_TEXTDOMAIN ),
					'ticket_attachments' => __( 'Ticket Attachments', TTLC_TEXTDOMAIN),
					'responses' => __( 'Responses', TTLC_TEXTDOMAIN),
					'responses_count' => __( 'Responses Count', TTLC_TEXTDOMAIN),
					'last_response' => __( 'Last Response', TTLC_TEXTDOMAIN),
					'developer' => __( 'Developer', TTLC_TEXTDOMAIN),
					'status' => __( 'Status', TTLC_TEXTDOMAIN),
					'support_until' => __( 'Support Until', TTLC_TEXTDOMAIN ),
					'extend_support_url' => __( 'Extend Support URL', TTLC_TEXTDOMAIN ),
					'server' => __( 'Server', TTLC_TEXTDOMAIN),
					'login' => __( 'Login', TTLC_TEXTDOMAIN),
					'password' => __( 'Password', TTLC_TEXTDOMAIN),
					'license' => __( 'License', TTLC_TEXTDOMAIN),
					'license_data' => __( 'License Data', TTLC_TEXTDOMAIN),
				);
			}
			
			public static function statuses() {
				return array(
					'replied' => __( 'needs attention', TTLC_TEXTDOMAIN),
					'pending' => __( 'pending', TTLC_TEXTDOMAIN),
					'closed' => __( 'closed', TTLC_TEXTDOMAIN),
				);

			}

			public function responses_statuses() {
				return array(
					'take' => __( '%s takes ticket for work', TTLC_TEXTDOMAIN ),
					'redirect' => __( '%s now working with this ticket', TTLC_TEXTDOMAIN),
					'closed' => __( '%s has closed this ticket. Reason — %s.', TTLC_TEXTDOMAIN),
					'closed_reason' => array(
						'client_solved' => __( 'solved', TTLC_TEXTDOMAIN ),
						'client_cancel' => __( 'client cancel', TTLC_TEXTDOMAIN ),
						'client_refund' => __( 'client refund', TTLC_TEXTDOMAIN ),
					),
					'reopen' => __( '%s has reopened this ticket.', TTLC_TEXTDOMAIN),
					'csir' => __( '%s has requested your server information.', TTLC_TEXTDOMAIN),
					'csirc' => __( '%s has requested your server information.', TTLC_TEXTDOMAIN),
					'csirr' => __( '%s has rejected the server information request.', TTLC_TEXTDOMAIN),
					'csirs' => __( '%s has sent the server information.', TTLC_TEXTDOMAIN),
					'cslr' => __( '%s has requested your server login.', TTLC_TEXTDOMAIN),
					'cslrc' => __( '%s has requested your server login.', TTLC_TEXTDOMAIN),
					'cslrr' => __( '%s has rejected the server login request.', TTLC_TEXTDOMAIN),
					'cslrs' => __( '%s has sent the server login link.', TTLC_TEXTDOMAIN),
					'csle' => __( '%s Client server login link has expired.', TTLC_TEXTDOMAIN),
				);

			}

			public function meta_attributes() {
				return array('product_id', 'external_id', 'client_id', 'developer', 'status', 'support_until');
			}

			public function rules() {
				return array(
					array(
						array('title', 'product_id', 'external_id', 'client_id', 'developer', 'status'),
						'required', 'on' => self::SCENARIO_DEFAULT
					),
					array(
						array_merge( $this->required_hidden_attributes(), array('title') ),
						'required', 'on' => self::SCENARIO_ADD
					),
				);
			}

			public function required_hidden_attributes() {
				return array('server', 'login', 'password', 'license', 'license_data' );
			}
			
			public function load_responses( $attribute ) {
				$query_args = array('post_parent' => $this->id);
				if ( isset( $_GET['action'] ) && $_GET['action'] === 'ticket' ) {
					if ( isset( $_GET['page_num'] ) && is_numeric( $_GET['page_num'] ) ) {
						$query_args['paged'] = (int) sanitize_key( $_GET['page_num'] );
					}
					$query_args['order'] = isset( $_GET['order'] ) && in_array( $_GET['order'], array('ASC', 'DESC') ) ? sanitize_text_field( $_GET['order'] ) : get_option( 'ttlc_responses_order', 'ASC' );
				}
				$responses = TTLC_Ticket_Response::find( $query_args );
				$this->responses_count = $responses['total'];
				return $responses['items'];
			}
			
			public function load_last_response() {
				if ( empty( $this->responses ) ) {
					return $this->date;
				}
				$responses = isset( $_GET['order'] ) && $_GET['order'] === 'ASC' ? array_values( array_slice( $this->responses, -1 ) ) : $this->responses;
				$last_response = $responses[0];
				return isset( $last_response->date ) ? $last_response->date : $this->date;
			}

			public function load_ticket_attachments( $attribute ) {
				$attachments = TTLC_Attachment::find_all( array(
					'meta_key' => TTLC_Attachment::PREFIX . 'ticket_id',
					'meta_value' => $this->id,
				) );
				return $attachments['items'];
			}
			
			public function get_attachments_count() {
				if ( ! isset( $this->attachments_count ) ) {
					if ( empty( $this->ticket_attachments ) ) {
						$this->attachments_count = 0;
					} else {
						$this->attachments_count = count( $this->ticket_attachments );
					}
				}
				return $this->attachments_count;
			}

			
			public function get_all_attachments() {
				if ( ! isset( $this->all_attachments ) ) {
					$responses_attachments = array();
					foreach( $this->responses as $response ) {
						$responses_attachments = array_merge( $responses_attachments, $response->attachments );
					}
					$this->all_attachments = array_merge( $this->ticket_attachments, $responses_attachments );
				}
				return $this->all_attachments;
			}

		}
	}
