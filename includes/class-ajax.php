<?php
	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Ajax' ) ) {

		class TTLC_Ajax {

			function __construct() {
				
				if ( current_user_can( 'manage_ttlc' ) ) {

					add_action( 'wp_ajax_ttlc/server/check', array($this, 'server_check') );
					add_action( 'wp_ajax_ttlc/product/save', array($this, 'product_save') );
					add_action( 'wp_ajax_ttlc/product/trash', array($this, 'product_trash') );
					add_action( 'wp_ajax_ttlc/product/untrash', array($this, 'product_untrash') );
					add_action( 'wp_ajax_ttlc/password/reset', array($this, 'password_reset') );
					add_action( 'wp_ajax_ttlc/add/ticket', array($this, 'add_ticket') );
					add_action( 'wp_ajax_ttlc/edit/ticket', array($this, 'edit_ticket') );
					add_action( 'wp_ajax_ttlc/attachment/download', array($this, 'attachment_download') );
					add_action( 'wp_ajax_ttlc/settings', array($this, 'settings') );
					add_action( 'wp_ajax_ttlc/newsletter/read', array($this, 'newsletter_read') );
					add_action( 'wp_ajax_ttlc/newsletter/trash', array($this, 'newsletter_trash') );
				}				
			}
			
			public function password_reset() {
				$_post_filtered = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
				check_ajax_referer( 'ttlc-product-password-reset-' . ( isset( $_post_filtered['slug'] ) ? $_post_filtered['slug'] : '' ), '_wpnonce' );
				$errors = false;
				$data = array();
				$password_reset = new TTLC_Password_Reset( $_post_filtered );
				$product = new TTLC_Product_Available( $_post_filtered );
				if ( isset( $_post_filtered['secure_key'] ) ) {
					$password_reset->set_scenario( TTLC_Password_Reset::SCENARIO_KEY );
				}

				if ( $password_reset->validate() ) {
					$user = new TTLC_Rest_User( array(
						'server' => $product->server,
						'data' => array(
							'login' => $password_reset->email_login,
							'reset_key' => isset( $password_reset->secure_key ) ? $password_reset->secure_key : '',
						),
					) );
					$user->password_reset();

					$response = $user->get_response();
					$response_body = $user->get_response_body();
					
					if ( $response['response']['code'] === 200 ) {
						if ( isset( $response_body->password ) && isset( $password_reset->autosubstitution ) ) {
							$data['value'] = $response_body->password;
							$data['selector'] = '#ttlc-product-login-' . $_post_filtered['_product_uniqid'] . '-password';
						}
					} else {
						$errors = true;
						$password_reset->add_error( isset( $password_reset->secure_key ) ? 'secure_key' : 'email_login', isset( $response_body->message ) ? $response_body->message : __( 'Unknown Rest Server Error', TTLC_TEXTDOMAIN ) );
					}
					
				} else {
					$errors = true;
				}
				
				if ( $errors ) {
					$html = TTLC()->page()->buffer_template( 'product-settings-password-reset', array(
						'product' => $product,
						'product_uniqid' => $_post_filtered['_product_uniqid'],
						'password_reset' => $password_reset,
					) );
					$data = '<div>' . $html . '</div>';
				}
				
				wp_send_json( array(
					'status' => ! $errors,
					'data' => $data,
				) );
				
			}

			public function server_check() {
				check_ajax_referer( 'ttlc_server_check', '_wpnonce' );
				$error_message = false;
				$html = '';
				$data = array();
				$server_products = array();
				$_post_filtered = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
				$_post_filtered['server'] = esc_url( $_post_filtered['server'] );

				// If Product ID is send — load product from DB
				
				if ( isset( $_post_filtered['id'] ) ) {
					$product_loaded = get_post( sanitize_key( $_post_filtered['id'] ) );
				}
				
				$product = empty( $product_loaded ) ? new TTLC_Product( $_post_filtered ) : new TTLC_Product( $product_loaded );
				
				$server = $this->rest_server_check( array(
					'server' => $product->server,
				) );

				$response_body = $server->get_response_body();
				if ( is_wp_error( $server->get_response() ) ) {
					$error_message = __( 'Wrong Server URL', TTLC_TEXTDOMAIN );
				} elseif( $server->check_response() && ! empty( $response_body->product_list ) && is_array( $response_body->product_list ) ) {
					$current_user = wp_get_current_user();
					if ( is_null( $product->name ) && $current_user instanceof WP_User ) {
						$product->name = trim( $current_user->user_firstname . ' ' . $current_user->user_lastname );
					}
					foreach( $response_body->product_list as $server_product ) {
						$server_product_slug = TTLC_Support::format_slug( $product->server, $server_product->post_name );

						$server_product_model = isset( $product->slug ) && $product->slug === $server_product_slug ? $product : new TTLC_Product(); // If new/existing product
						$server_product_model->external_id = $server_product->ID;
						$server_product_model->name = $product->name; // Client name
						$server_product_model->server = $product->server;
						$server_product_model->registration = ! empty( $server_product->open_registration );
						$server_product_model->slug = $server_product_slug;
						$server_product_model->type = $server_product->type;
						$server_product_model->title = $server_product->post_title;
						$server_product_model->content = $server_product->post_content;
						$server_product_model->thumbnail = $server_product->image;
						$server_product_model->author = $server_product->author_name;
						$server_product_model->author_uri = $server_product->author_link;
						$server_product_model->manual = $server_product->manual;
						$server_product_model->service_terms = $server_product->terms;
						$server_product_model->privacy_statement = $server_product->privacy;
						$server_product_model->license_fields = json_encode( $server_product->license_list );
						$server_products[] = $server_product_model;
					}
					
				} else {
					$response_code = $server->get_code();
					if ( $response_code ) {
						if ( $ttls_error_message = TTLC()->get_ttls_error( $response_code ) ) {
							$error_message = $ttls_error_message;
						} else {
							$error_message = __( 'Unknown TTLS Error', TTLC_TEXTDOMAIN );
						}
					} else {
						$error_message = __( 'Wrong Server URL', TTLC_TEXTDOMAIN );
					}
					
				}
				
				if ( $error_message ) {
					$product->add_error( 'server', $error_message );
					$template = 'product-settings-server';
				} else {
					$template = 'product-settings';
				}
				
				$data = array(
					'product' => $product,
					'server_products' => $server_products,
					'product_uniqid' => $_post_filtered['_product_uniqid'],
				);
				$html = TTLC()->page()->buffer_template( $template, $data );

				wp_send_json( $html );
			}
			
			protected function rest_server_check( $args ) {				
				$server = new TTLC_Rest_Server( $args );
				$server->check(); 
				return $server;
			}
			
			protected function rest_user_register( TTLC_Product $product ) {

				$user = new TTLC_Rest_User( array(
					'server' => $product->server,
					'data' => array(
						'login' => $product->login,
						'password' => $product->password,
						'email' => $product->email,
						'name' => $product->name,
					),
				) );

				$user->register();
				
				if ( $user->check_response() ) {
					
					// Registration OK — Bind License

					$rest_user_license = $this->rest_user_license( $product );
					$product = $rest_user_license['product'];
					$user = $rest_user_license['user'];
					
				}

				return array(
					'user' => $user,
					'product' => $product,
				);
			}
			
			protected function rest_user_license( TTLC_Product $product ) {
				$licenses = (Array)json_decode( $product->license_fields);
				$user = new TTLC_Rest_User( array(
					'server' => $product->server,
					'login' => $product->login,
					'password' => $product->password,
					'data' => array_merge( array(
						'license_type' => $product->license,
						'product_id' => $product->external_id,
					), is_array( $product->license_data ) ? $product->license_data : array() ),
				) );
	
				$user->license();
				
				if ( $user->check_response() ) {
					
					$license_response_body = $user->get_response_body();
					
					foreach( $licenses[$product->license]->fields as $license_field_name => $license_field_data ) {
						if ( isset($license_response_body->$license_field_name) ) {
							$product->license_data[$license_field_name] = $license_response_body->$license_field_name;
						}
					}
				}
				
				return array(
					'product' => $product,
					'user' => $user,
				);				
			}

			public function product_save() {
				$_post_filtered = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
				check_ajax_referer( 'ttlc_product_save_' . ( isset( $_post_filtered['slug'] ) ? $_post_filtered['slug'] : '' ), '_wpnonce' );
				$product = new TTLC_Product( $_post_filtered );
				$licenses = TTLC()->sanitize_string_array( (array) json_decode( stripslashes( $_POST['license_fields'] ) ) );
				$product->license_fields = json_encode( $licenses );
				$form = isset( $_post_filtered['form'] ) && in_array( $_post_filtered['form'], array('login', TTLC_Product::SCENARIO_REGISTRATION) ) ? $_post_filtered['form'] : 'login';
				$html = '';
				$errors = false;
				
				if ( $form === TTLC_Product::SCENARIO_REGISTRATION ) {
					$product->set_scenario( TTLC_Product::SCENARIO_REGISTRATION );
				} elseif( isset( $product->id ) ) {
					$product->set_scenario( TTLC_Product::SCENARIO_UPDATE );
				}
				
				// Process License Fields

				if ( isset( $product->license ) ) {
					foreach( $licenses[$product->license]['fields'] as $license_field_name => $license_field_data ) {
						if ( ( $form === 'login' && $license_field_data['login'] ) || ( $form === 'registration' && $license_field_data['register'] ) ) {
							$product->license_data[$license_field_name] = isset( $_post_filtered[$license_field_name] ) ? $_post_filtered[$license_field_name] : null;
						}
					}
				}
				
				// TTLC Level Validation
				
				if ( $product->validate() ) {
					
					if ( $product->get_scenario() === TTLC_Product::SCENARIO_REGISTRATION ) {

						if ( in_array( $product->license, array('standard', 'ticketrilla') ) && empty( $product->license_data['license_token'] ) ) {
							$rest_user_register = $this->rest_user_register( $product );
							$user = $rest_user_register['user'];
							$product = $rest_user_register['product'];
						} else {
							$user = TTLC_Rest_User::can_license( $product, false );
							if ( $user->check_response() ) {
								$rest_user_register = $this->rest_user_register( $product );
								$user = $rest_user_register['user'];
								$product = $rest_user_register['product'];
							}
						}

					} else {
						
					// Login or Settings Update

						if ( $product->get_scenario() === TTLC_Product::SCENARIO_UPDATE ) {

							// Update User Name
							
							$user_name = new TTLC_Rest_User( array(
								'server' => $product->server,
								'login' => $product->login,
								'password' => $product->password,
								'data' => array(
									'name' => $product->name,
								),
							) );
							$user_name->set_name();
							
						}

						// Login / Product Settings Update

						// No Token Sent or Not Your License or Not Found License — Try Bind License

						if ( empty( $product->license_data['license_token'] ) ) {
							
							$rest_user_license = $this->rest_user_license( $product );
							$product = $rest_user_license['product'];
							$user = $rest_user_license['user'];
						
						} else {
							$user = TTLC_Rest_User::can_license( $product );
							if ( $user->get_code() === 'ttls_license_notyour' || $user->get_code() === 'ttls_license_notfound' ) {
								$rest_user_license = $this->rest_user_license( $product );
								$product = $rest_user_license['product'];
								$user = $rest_user_license['user'];
							}
						}
					}

					
					// TTLS Level Validation (License/Registration)

					
					if ( $user->check_response() ) {

						// License/Registration is Valid - Save Product
						
						$save = $product->save();

						if ( ! $save['status'] ) {
							$product->add_error( '_global', $save['message'] );
							$errors = true;
						}
						
					} else {

						// License/Registration is Invalid
						
						$user_message = $user->get_message();
						
						if ( empty( $user_message ) ) {
							$product->add_error( '_global', __( 'Unknown Rest Server Error', TTLC_TEXTDOMAIN ) );
						} elseif( is_array( $user_message ) ) {
							foreach ( $user_message as $_user_message ) {
								$product->add_error( '_global', $_user_message );
							}
						} else {
							$product->add_error( '_global', $user_message );
						}
						
						
						$errors = true;

					}

				} else {
					foreach( $product->required_hidden_attributes() as $hidden_attribute ) {
						if ( $product->has_errors( $hidden_attribute ) ) {
							foreach ( $product->get_errors( $hidden_attribute ) as $message ) {
								$product->add_error( '_global', $message );
							}
							break;
						}
					}
					
					$errors = true;
					
				}

				
				if ( $errors ) {
					
					$html = TTLC()->page()->buffer_template( 'product-settings-form', array(
						'product' => $product,
						'form' => $form,
						'product_uniqid' => $_post_filtered['_product_uniqid'],
						'server_product_uniqid' => $_post_filtered['_product_uniqid'] . '-' . $product->slug,
					) );					
				} else {
					$html = TTLC()->page()->buffer_template( 'main' );
				}

				wp_send_json( array(
					'status' => ! $errors,
					'data' => $html,
				) );
				
			}
			
			public function product_trash( $trash = false ) {
				$action = $trash ? 'untrash' : 'trash';
				$errors = false;
				$message = '';
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], $action . '_post_' . sanitize_key( $_POST['id'] ) ) ) {
				    $errors = true;
				} else {
					$product = new TTLC_Product( filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING ) );
					$result = $product->$action();
					if ( is_wp_error( $result ) ) {
						$errors = true;
					    $message = $result->get_error_message();
					}
				}
				wp_send_json( array('status' => ! $errors, 'data' => $message) );
			}

			public function product_untrash() {
				$this->product_trash( true );
			}			

			public function add_ticket() {
				define( 'ALLOW_UNFILTERED_UPLOADS', true );
				check_ajax_referer( 'ttlc_add_ticket', '_wpnonce' );				
				$errors = false;
				$data = '';
				$class = isset( $_POST['parent_id'] ) ? 'TTLC_Ticket_Response' : 'TTLC_Ticket';
				$ticket = new $class( filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING ) );
				$ticket_license_data = TTLC()->sanitize_string_array( (array)json_decode( stripslashes( $_POST['license_data'] ) ) );
				$ticket->license_data = json_encode( $ticket_license_data );
				if ( ! empty( $ticket->type ) ) {
					if ( $ticket->type === 'csirs' ) {
						$ticket->content = wp_kses_post( TTLC()->get_csi() );
					} elseif( $ticket->type === 'cslrs' ) {
						$csl_link = TTLC()->get_csl( $ticket->parent_id );
						if ( is_wp_error( $csl_link ) ) {
							$errors = true;
							$ticket->add_error( '_global', __( 'Error on generating auto login link', TTLC_TEXTDOMAIN ) );
						} else {
							$ticket->content = wp_kses_post( $csl_link );
						}
					}
				} else {
					$ticket->content = wp_kses_post( $_POST['content'] );
				}
				$ticket->set_scenario( $class::SCENARIO_ADD );
				
				if ( $ticket->validate() ) {
					if ( $class === 'TTLC_Ticket_Response' && ! empty( $ticket->type ) ) { // To Send Response Status Only
						if ( ! array_key_exists( $ticket->type, $ticket->responses_statuses() ) ) {
							$errors = true;
							$ticket->add_error('status', __( 'Wrong status', TTLC_TEXTDOMAIN ) );
						}
					} elseif ( empty( $_FILES['attachment']  ) ) {
						if ( empty( $ticket->content ) ) {
							$errors = true;
							$ticket->add_error('content', __( 'This field is required', TTLC_TEXTDOMAIN ) );
						}
					} else {
						foreach ( $_FILES['attachment']['error'] as $key => $error ) {
							
							if ( $error == UPLOAD_ERR_OK ) {
			
								$name = sanitize_file_name( basename( $_FILES['attachment']['name'][$key] ) );
								$attachment = new TTLC_Attachment();
								$result = $attachment->upload( $_FILES['attachment']['tmp_name'][$key], $name );
								if ( $result['status'] ) {
									$ticket->attachments[] = $attachment->export_data();
								} else {
									$errors = true;
									$ticket->add_error('attachments', $name . ': ' . $result['message'] );
								}
							}
						}
					}
					
					if ( ! $errors ) {

						$rest_data = array_merge( array(
								'license_type' => $ticket->license,
								'title' => $ticket->title,
								'content' => $ticket->content,
						), $ticket_license_data );
						
						if ( isset( $ticket->parent_id ) ) {
							$rest_data['parent'] = $ticket->parent_id;
						}
	
						if ( ! empty( $ticket->attachments ) ) {
							$rest_data['attachments'] = $ticket->attachments;
						}
						
						if ( $class === 'TTLC_Ticket_Response' && ! empty( $ticket->type ) && in_array( $ticket->type, array('csirr', 'cslrr', 'cslrs') ) ) {
							$rest_data['response_status'] = $ticket->type;
						}
						
						if ( isset( $_POST['csirID'] ) && is_numeric( $_POST['csirID'] ) ) {
							$rest_data['csir_id'] = sanitize_key( $_POST['csirID'] );
						}
		
						$rest_ticket = new TTLC_Rest_Ticket( array(
							'server' => $ticket->server,
							'login' => $ticket->login,
							'password' => $ticket->password,
							'data' => $rest_data,
						) );
		
						$rest_ticket->add();
						$response = $rest_ticket->get_response();
						$response_body = $rest_ticket->get_response_body();
						if ( $response['response']['code'] === 200 ) {
							$data = $response_body;
						} else {
							$errors = true;
							$ticket->add_error( '_global', isset( $response_body->message ) ? $response_body->message : __( 'Unknown Rest Server Error', TTLC_TEXTDOMAIN ) );
						}
					}

					
				} else {
					$errors = true;
				}
				
				if ( $errors ) {
					$data = TTLC()->page()->buffer_template( 'add-ticket-form', array('ticket' => $ticket) );
				}

				wp_send_json( array(
					'status' => ! $errors,
					'data' => $data,
				) );
			}
			
			public function edit_ticket() {
				check_ajax_referer( 'ttlc_edit_ticket', '_wpnonce' );
				$_post_filtered = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
				$result = array();
				$ticket = new TTLC_Rest_Ticket( array(
					'server' => $_post_filtered['server'],
					'login' => $_post_filtered['login'],
					'password' => $_post_filtered['password'],
					'data' => array_merge( array(
						'license_type' => $_post_filtered['license'],
						'parent' => $_post_filtered['external_id'],
						'status' => $_post_filtered['status'],
					), TTLC()->sanitize_string_array( (array)json_decode( stripslashes( $_POST['license_data'] ) ) ) ),
				) );
				$ticket->edit();
				$response = $ticket->get_response();
				if ( is_wp_error( $response ) ) {
					$result['status'] = false;
					$result['message'] = $response->get_error_message();
				} else {
					$result['status'] = true;
					$result['data'] = $ticket->get_response_body();
				}
				wp_send_json( $result );
			}
			
			public function attachment_download() {				
				check_ajax_referer( 'ttlc_attachment_download', '_wpnonce' );				
				$data = '';
				$attachment = new TTLC_Attachment( filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING ) );
				$result = $attachment->download();
				if ( $result ) {
					$data = TTLC()->page()->buffer_template( 'attachment', $attachment );
				}			
				wp_send_json( array('status' => $result, 'data' => $data) );
			}

			public function settings() {
				check_ajax_referer( 'ttlc_settings', '_wpnonce' );
				
				if ( isset( $_POST['section'] ) && in_array( $_POST['section'], array('attachments', 'autologin', 'updates', 'newsletters') ) ) {
					$section = sanitize_text_field( $_POST['section'] );							
					$settings_class = 'TTLC_Settings_' . $section;
					$settings_obj = new $settings_class( filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING ) );
					if ( $settings_obj->validate() ) {
						$result = $settings_obj->save();
					}
					
					wp_send_json( array(
						'data' => TTLC()->page()->buffer_template( 'settings-' . $section . '-form', array($section => $settings_obj ) ),
					) );
				}
				
				wp_send_json_error();
			}
			
			public function newsletter_read() {
				$errors = false;
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'read_newsletter_' . sanitize_key( $_POST['id'] ) ) ) {
				    $errors = true;
				} else {
					update_post_meta( sanitize_key( $_POST['id'] ), TTLC_Newsletter::PREFIX . 'status', 'read' );
				}
				wp_send_json( array('status' => ! $errors ) );
			}

			public function newsletter_trash() {
				$errors = false;
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'trash_newsletter_' . sanitize_key( $_POST['id'] ) ) ) {
				    $errors = true;
				} else {
					if ( ! wp_trash_post( sanitize_key( $_POST['id'] ) ) ) {
						$errors = true;
					}
				}
				wp_send_json( array('status' => ! $errors ) );
			}

		}
	}
