<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Pagination' ) ) {

		class TTLC_Pagination {
			
			public $count;
			public $pages_count;
			public $ppp = TTLC_PPP;
			public $paged = false;
			public $active;
			public $prev;
			public $next;
			
			function __construct( $count ) {
				
				$this->count = is_numeric( $count ) ? $count : null;

				if ( $this->count > $this->ppp ) {
					$this->paged = true;
					$this->pages_count = (int) ceil( $this->count / $this->ppp );
					$page_num = isset( $_GET['page_num'] ) && is_numeric( $_GET['page_num'] ) ? (int) sanitize_key( $_GET['page_num'] ) : 1;
					$this->active = $page_num;
					
					if ( $page_num > 1 ) {
						$this->prev = add_query_arg( 'page_num', $page_num - 1 );
					}
					
					if ( $page_num < $this->pages_count ) {
						$this->next = add_query_arg( 'page_num', $page_num + 1 );
					}
				}
				
			}
			
			public function render( $template = false ) {
				if ( $this->paged ) {
					$default_template = 'pagination';
					$template = $template ? $default_template . '-' . $template : $default_template;
					TTLC()->page()->render_template( $template, $this );
				}
			}
			
		}
	}