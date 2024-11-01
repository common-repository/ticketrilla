<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Password_Reset' ) ) {

		class TTLC_Password_Reset extends TTLC_Model {
			
			public $email_login;
			public $secure_key;
			public $autosubstitution;

			const SCENARIO_KEY = 'secure';
			
			public function attributes() {
				return array(
					'email_login' => 'E-mail or Login',
					'secure_key' => 'Secure Key',
					'autosubstitution' => 'Auto-Substitution',
				);
			}
			public function rules() {
				return array(
					array(
						array('email_login'),
						'required', 'on' => self::SCENARIO_DEFAULT
					),
					array(
						array('secure_key'),
						'required', 'on' => self::SCENARIO_KEY
					),
				);
			}
		}
		
	}