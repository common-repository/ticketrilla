<?php
	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Breadcrumbs' ) ) {

		class TTLC_Breadcrumbs {
			private static $links = array();
			private static $head;
			
			public static function add_link( $title, $url ) {
				self::$links[] = array(
					'title' => $title,
					'url' => $url,
				);
			}
			
			public static function add_head( $title ) {
				self::$head = $title;
			}
			
			public static function get_links() {
				return self::$links;
			}
			
			public static function get_head() {
				return self::$head;
			}
		}
	}