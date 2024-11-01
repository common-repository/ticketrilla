<?php
	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Product' ) ) {

		class TTLC_Product extends TTLC_Post {
			
			const PREFIX = 'ttlc_product_';
			
			const SCENARIO_UPDATE = 'update';
			const SCENARIO_REGISTRATION = 'registration';
			
			public $id;
			public $external_id;
			public $title;
			public $slug;
			public $type;
			public $content;
			public $author;
			public $author_uri;
			public $thumbnail;
			public $service_terms;
			public $privacy_statement;
			public $manual;
			public $registration;
			public $support_until;

			public $server;
			public $login;
			public $password;
			public $email;
			public $name;
			public $terms; // Agree with terms checkbox

			public $license_fields;
			public $license;
			public $license_data = array();
			public $update_time;
			public $newsletters;

			public function attributes() {
				return array(
					'id' => __( 'ID', TTLC_TEXTDOMAIN),
					'external_id' => '',
					'update_time' => '',
					'title' => __( 'Title', TTLC_TEXTDOMAIN),
					'slug' => __( 'Slug', TTLC_TEXTDOMAIN),
					'type' => __( 'Type', TTLC_TEXTDOMAIN),
					'content' => __( 'Description', TTLC_TEXTDOMAIN),
					'author' => __( 'Author', TTLC_TEXTDOMAIN),
					'author_uri' => __( 'Author URI', TTLC_TEXTDOMAIN),
					'thumbnail' => __( 'Thumbnail', TTLC_TEXTDOMAIN),
					'service_terms' => __( 'Service Terms', TTLC_TEXTDOMAIN),
					'privacy_statement' => __( 'Privacy Statement', TTLC_TEXTDOMAIN),
					'manual' => __( 'Manual', TTLC_TEXTDOMAIN),
					'registration' => __( 'Registration Opened', TTLC_TEXTDOMAIN),

					'server' => __( 'Server', TTLC_TEXTDOMAIN),
					'login' => __( 'Login', TTLC_TEXTDOMAIN),
					'email' => __( 'E-mail', TTLC_TEXTDOMAIN),
					'name' => __( 'Name', TTLC_TEXTDOMAIN),
					'password' => __( 'Password', TTLC_TEXTDOMAIN),
					'terms' => __( 'Terms', TTLC_TEXTDOMAIN),
					
					'license_fields' => __( 'License Fields', TTLC_TEXTDOMAIN),
					'license' => __( 'License', TTLC_TEXTDOMAIN),
					'license_data' => __( 'License Data', TTLC_TEXTDOMAIN),
					'newsletters' => __( 'Newsletters', TTLC_TEXTDOMAIN),
				);
			}

			public function rules() {
				return array(
					array(
						array_merge( $this->required_hidden_attributes(), array('license', 'login', 'password') ),
						'required'
					),
					array(
						array('id'),
						'exist', 'on' => self::SCENARIO_UPDATE,
					),
					array(
						array('name'),
						'required', 'on' => self::SCENARIO_UPDATE,
					),
					array(
						array('terms', 'name'),
						'required', 'on' => self::SCENARIO_REGISTRATION,
					),
					array(
						array('email'),
						'email', 'on' => self::SCENARIO_REGISTRATION,
					),
					array(
						array('slug'),
						'unique',
						'error_message' => __( 'This product is registered', TTLC_TEXTDOMAIN ),
					),
				);
			}
			
			public function required_hidden_attributes() {
				return array('title', 'slug', 'type', 'author', 'server');
			}
			
			public function meta_attributes() {
				return array('type', 'author', 'author_uri', 'thumbnail', 'server', 'license', 'login', 'password', 'license_data', 'name', 'email', 'license_fields', 'manual', 'update_time', 'newsletters', 'external_id');
			}
			
			protected function meta_base64_attributes() {
				return array('login', 'password', 'email');
			}
			
			protected function process_meta( $meta_attribute, $value ) {
				if ( in_array( $meta_attribute, $this->meta_base64_attributes() ) ) {
					$value = base64_encode( $value );
				}
				return $value;
			}
			
			/**
			 * Filter meta attribute value on populate() method execution.
			 * Decode meta attributes encoded in base64.
			 *
			 * @param $meta_attribute string Name of the meta attribute
			 * @param $value mixed Value of the meta attribute
			 * @return mixed Filtered value of the meta attribute
			 * @uses TTLC_Product::meta_base64_attributes()
			 */
			protected function filter_meta( $meta_attribute, $value ) {
				if ( in_array( $meta_attribute, $this->meta_base64_attributes() ) ) {
					$value = base64_decode( $value );
				}
				return $value;
			}
			
		}
	}