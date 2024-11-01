<?php
	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Rest' ) ) {

		abstract class  TTLC_Rest {

			protected $header = array();
			protected $server;
			protected $path = '?rest_route=/ttls/v1/';
			protected $endpoint;
			protected $response;
			protected $response_body;
			protected $data = array();

			function __construct( $args ) {
				if ( isset( $args['server'] ) ) {
					$this->set_server( $args['server'] );
				}
				
				if ( isset( $args['login'] ) && isset( $args['password'] ) ) {
					$this->set_header( 'Authorization', 'Basic ' . base64_encode( $args['login'] . ':' . $args['password'] ) );
				}
				
				$this->set_data( empty( $args['data'] ) ? array() : $args['data'] );
			}

			protected function set_server( $server_url ) {
				$this->server = esc_url( $server_url );
			}

			public function get_server() {
				return $this->server;
			}

			protected function set_header( $name, $value ) {
				$this->header[ $name ] = $value;
			}

			public function get_header() {
				return $this->header;
			}

			protected function set_endpoint( $endpoint ) {
				$this->endpoint = $endpoint;
			}

			public function get_endpoint() {
				return $this->endpoint;
			}

			protected function set_path( $path ) {
				$this->path = $path;
			}

			public function get_path() {
				return $this->path;
			}

			protected function set_data( $data ) {
				if ( empty( $data['ttlc_ver'] ) ) {
					$data['ttlc_ver'] = TTLC_PLUGIN_VERSION;
				}
				$this->data = $data;
			}
			
			protected function set_mode( $mode ) {
				$this->data['mode'] = $mode;
			}

			/**
			 * @return mixed
			 */
			public function get_data() {
				return $this->data;
			}

			/**
			 * @param mixed $response
			 */
			protected function set_response( $response ) {
				$this->response = $response;

				$response = wp_remote_retrieve_body( $response );
				$response = json_decode( $response );
				$this->set_response_body( $response );
			}

			public function get_response() {
				return $this->response;
			}

			protected function set_response_body( $response_body ) {
				$this->response_body = $response_body;
			}

			public function get_response_body() {
				return $this->response_body;
			}

			public function get_endpoint_path() {
				$url = trailingslashit( $this->get_server() );
				$url .= trailingslashit( $this->get_path() );
				$url .= $this->get_endpoint();

				return $url;
			}
			
			protected function send_request() {

				$response = wp_remote_post( $this->get_endpoint_path(), array(
					'body'    => apply_filters( 'ttlc_rest_send_request_data', $this->get_data() ),
					'headers' => $this->get_header()
				) );
				$this->set_response( $response );
			}
			
			public function check_response() {
				return isset( $this->response['response'] ) && $this->response['response']['code'] === 200;
			}
			
			public function get_message() {
				if ( isset( $this->response_body ) && isset( $this->response_body->message ) ) {
					if ( empty( $this->response_body->additional_errors ) ) {
						return $this->response_body->message;
					} else {
						$messages = array($this->response_body->message);
						foreach( $this->response_body->additional_errors as $error ) {
							$messages[] = $error->message;
						}
						return $messages;
					}
				}
				return false;
			}

			public function get_code() {
				return isset( $this->response_body ) && isset( $this->response_body->code ) ? $this->response_body->code : false;
			}
		}
	}
