<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Settings_Autologin' ) ) {

		class TTLC_Settings_Autologin extends TTLC_Settings {
			
			protected $_suffix = 'autologin';

			public $role = 'administrator';
			public $days = 14;
			public $logins = 5;
			public $reassign_user;
			
			public function attributes() {
				return array(
					'role' => __( 'User role', TTLC_TEXTDOMAIN),
					'days' => __( 'Duration', TTLC_TEXTDOMAIN),
					'logins' => __( 'Logins limit', TTLC_TEXTDOMAIN),
					'reassign_user' => __( 'Reassign user', TTLC_TEXTDOMAIN),
				);
			}
			
			public function rules() {
				return array(
					array(
						array('days', 'logins'),
						'number',
						'natural',
					),
					array(
						array('role'),
						'list',
						array_keys( get_editable_roles() ),
					),
					array(
						array('reassign_user'),
						'user_exists',
					),
				);
			}
			
			public function get_oldest_admin_id() {
				$admins = $this->get_admins();
				if ( empty( $admins ) ) {
					return false;
				}
				return $admins[0]->ID;
			}
			
			public function get_admins() {
				$autologin_user_query = new WP_User_Query( array(
					'meta_key' => 'ttlc_autologin_key',
					'compare' => 'EXISTS',
					'fields' => 'ID',
				) );
				
				$autologin_users = $autologin_user_query->get_results();

				$user_query = new WP_User_Query( array(
					'orderby' => 'registered',
					'order' => 'ASC',
					'role' => 'Administrator',
					'exclude' => empty( $autologin_users ) ? '' : $autologin_users,
				) );

				return $user_query->get_results();
			}
			
			protected function default_reassign_user() {
				return $this->get_oldest_admin_id();
			}
			
			protected function load_reassign_user() {
				if ( ! get_userdata( $this->reassign_user ) ) {
					return $this->get_oldest_admin_id();
				}
				return $this->reassign_user;
			}
		}
	
	}