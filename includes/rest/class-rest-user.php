<?php
	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Rest_User' ) ) {

		class TTLC_Rest_User extends TTLC_Rest {
			
			protected $endpoint = 'user';

			public function register() {
				$this->set_mode( 'register' );
				$this->send_request();
			}

			public function password_reset() {
				$this->set_mode( 'reset_pass' );
				$this->send_request();
			}

			public function can() {
				$this->set_mode( 'can' );
				$this->send_request();
			}

			public function license() {
				$this->set_mode( 'license' );
				$this->send_request();
			}
			
			public function set_name() {
				$this->set_mode( 'set_name' );
				$this->send_request();
			}

			public static function can_license( $data, $auth = true ) {
				$_data = array(
					'can_mode' => 'license',
					'license_type' => $data->license,
					'product_id' => $data->external_id,
				);
				$args = array(
					'server' => $data->server,
					'data' => array_merge( $_data, $data instanceof TTLC_Product && is_array( $data->license_data ) ? $data->license_data : array() ),
				);
				if ( $auth ) {
					$args['login'] = $data->login;
					$args['password'] = $data->password;
				}
				$user = new self( $args );
				$user->can();
				return $user;
			}

			public static function can_login( $data ) {
				$user = new self( array(
					'server' => $data->server,
					'login' => $data->login,
					'password' => $data->password,
					'data' => array('can_mode' => 'login',),
				) );
				$user->can();
				return $user;
			}

			public static function can_register( $data ) {
				$user = new self( array(
					'server' => $data->server,
					'login' => $data->login,
					'password' => $data->password,
					'data' => array('can_mode' => 'register',),
				) );
				$user->can();
				return $user;
			}

		}
	}
