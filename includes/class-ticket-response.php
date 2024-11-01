<?php
	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Ticket_Response' ) ) {

		class TTLC_Ticket_Response extends TTLC_Ticket {
			
			const SCENARIO_ADD = 'add';
			
			public $id;
			public $parent_id;
			public $external_id;
			public $title;
			public $author_id;
			public $author;
			public $author_pos;
			public $person;
			public $person_pos;
			public $type;
			public $reason;
			public $date;
			public $content;
			public $attachments = array();
			
			public function attributes() {
				return array(
					'id' => __( 'ID', TTLC_TEXTDOMAIN),
					'parent_id' => __( 'Parent ID', TTLC_TEXTDOMAIN),
					'external_id' => __( 'External ID', TTLC_TEXTDOMAIN),
					'title' => __( 'Title', TTLC_TEXTDOMAIN),
					'author_id' => __( 'Author ID', TTLC_TEXTDOMAIN),
					'author' => __( 'Author', TTLC_TEXTDOMAIN),
					'author_pos' => __( 'Author Position', TTLC_TEXTDOMAIN),
					'person' => __( 'Person', TTLC_TEXTDOMAIN),
					'person_pos' => __( 'Person Position', TTLC_TEXTDOMAIN),
					'type' => __( 'Type', TTLC_TEXTDOMAIN),
					'reason' => __( 'Reason', TTLC_TEXTDOMAIN),
					'date' => __( 'Date', TTLC_TEXTDOMAIN),
					'content' => __( 'Content', TTLC_TEXTDOMAIN),
					'attachments' => __( 'Attachments', TTLC_TEXTDOMAIN),
					'server' => __( 'Server', TTLC_TEXTDOMAIN),
					'login' => __( 'Login', TTLC_TEXTDOMAIN),
					'password' => __( 'Password', TTLC_TEXTDOMAIN),
					'license' => __( 'License', TTLC_TEXTDOMAIN),
					'license_data' => __( 'License Data', TTLC_TEXTDOMAIN),
				);
			}

			public function meta_attributes() {
				return array('external_id', 'author_id', 'author', 'author_pos', 'type', 'person', 'person_pos');
			}

			public function rules() {
				return array(
					array(
						array('external_id', 'author_id', 'author', 'author_pos', 'type', 'date'),
						'required', 'on' => self::SCENARIO_DEFAULT
					),
					array(
						$this->required_hidden_attributes(),
						'required', 'on' => self::SCENARIO_ADD
					),
				);
			}
			
			public function required_hidden_attributes() {
				return array_merge( parent::required_hidden_attributes(), array('parent_id') );
			}

			public static function post_type() {
				return strtolower( get_parent_class() );
			}

			public function load_attachments( $attribute ) {
				$query = TTLC_Attachment::find_all( array(
					'meta_key' => TTLC_Attachment::PREFIX . 'ticket_id',
					'meta_value' => $this->id,
				) );
				return $query['items'];
			}

		}
	}
